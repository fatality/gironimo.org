import warnings
import mptt
from datetime import datetime
from django.db import models
from django.db.models import Q
from django.utils.html import strip_tags, linebreaks
from django.contrib.auth.models import User
from django.contrib.sites.models import Site
from django.db.models.signals import post_save
from django.utils.importlib import import_module
from django.contrib import comments
from django.contrib.comments.models import CommentFlag
from django.contrib.comments.moderation import moderator
from django.utils.translation import ugettext_lazy as _
from django.contrib.markup.templatetags.markup import markdown, textile, restructeredtext
from gironimo.utils.models import StatMixin, PageMixin
from tagging.fields import TagField
from gironimo.blog.config import UPLOAD_TO, MARKUP_LANGUAGE, ENTRY_TEMPLATES, ENTRY_BASE_MODEL, MARKDOWN_EXTENSIONS, AUTO_CLOSE_COMMENTS_AFTER
from gironimo.blog.managers import entries_published, EntryPublishedManager, AuthorPublishedManager, DRAFT, HIDDEN, PUBLISHED
from gironimo.blog.moderator import EntryCommentModerator
from gironimo.blog.url_shortener import get_url_shortener
from gironimo.blog.signals import ping_directories_handler, ping_external_urls_handler


class Author(User):
    """ Proxy Model around User """
    
    objects = models.Manager()
    published = models.AuthorPublishedManager()
    
    def entries_published(self):
        """ Return only the entries published """
        return entries_published(self.entries)
    
    @models.permalink
    def get_absolute_url(self):
        """ Return author's URL """
        return ('blog_author_detail', (self.username,))
    
    class Meta:
        proxy = True


class Category(StatMixin, PageMixin, models.Model):
    """ Category object for Entry """
    
    title = models.CharField(
        _('title'), 
        max_length=255
    )
    slug = models.SlugField(
        help_text=_('used for publication'), 
        unique=True, 
        max_length=255
    )
    description = models.TextField(
        _('description'), 
        blank=True, 
        null=True
    )
    image = modelsImageField(
        _('category image'), 
        blank=True, 
        null=True, 
        upload_to=UPLOAD_TO
    )
    parent = models.ForeignKey(
        'self', 
        null=True, 
        blank=True, 
        verbose_name=_('parent category'), 
        related_name=_('children')
    )
    
    def entries_published(self):
        """ Return only the entries published """
        return entries_published(self.entries)
    
    @property
    def tree_path(self):
        """ Return categories tree path, by his ancestors """
        if self.parent:
            return '%s/%s' % (self.parent.tree_path, self.slug)
        return self.slug
    
    def __unicode__(self):
        return self.title
    
    @models.permalink
    def get_absolute_url(self):
        """ Return categories URL """
        return ('blog_category_detail', (self.tree_path,))
    
    class Meta:
        ordering = ['title',]
        verbose_name = _('category')
        verbose_name_plural = _('categories')


class EntryAbstractClass(StatMixin, PageMixin, models.Model):
    """ Base Model design for publishing entries """
    STATUS_CHOICES = (
        (DRAFT, _('draft')),
        (HIDDEN, _('hidden')),
        (PUBLISHED, _('published'))
    )
    
    title = models.CharField(
        _('title'), 
        max_length=255
    )
    image = models.ImageField(
        _('image'), 
        upload_to=UPLOAD_TO, 
        blank=True, 
        null=True, 
        help_text=_('used for illustration')
    )
    content = models.TextField(
        _('content')
    )
    excerpt = models.TextField(
        _('excerpt'), 
        blank=True, 
        null=True, 
        helpt_text=_('optional element')
    )
    tags = TagField(
        _('tags')
    )
    categories = models.ManyToManyField(
        Category, 
        verbose_name=_('categories'), 
        related_name='entries', 
        blank=True, 
        null=True
    )
    related = models.ManyToManyField(
        'self', 
        verbose_name=_('related_entries'), 
        blank=True, 
        null=True
    )
    slug = models.SlugField(
        help_text=_('used for publication'), 
        unique_for_date='created', 
        max_length=255
    )
    authors = models.ManyToManyField(
        User, 
        verbose_name=_('authors'), 
        related_name='entries', 
        blank=True, 
        null=True
    )
    status = models.IntegerField(
        choices=STATUS_CHOICES, 
        default=DRAFT
    )
    feateured = models.BooleanField(
        _('featured'), 
        default=False
    )
    comment_enabled = models.BooleanField(
        _('comment enabled'), 
        default=True
    )
    pingback_enabled = models.BooleanField(
        _('linkback enabled'),
        default=True
    )
    start_publication = models.DateTimeField(
        _('start publication'), 
        help_text=_('date start publish')
        default=datetime.now
    )
    end_publication = models.DateTimeField(
        _('end publication'), 
        helpt_text=_('date end publish'), 
        default=datetime(2042, 11, 9)
    )
    sites = models.ManyToManyField(
        Site, 
        verbose_name=_('sites publication'), 
        related_name='entries'
    )
    login_required = models.BooleanField(
        _('login required'), 
        default=False, 
        help_text=_('only authenticated users can view the entry')
    )
    password = models.CharField(
        _('password'), 
        max_length=50, 
        blank=True, 
        null=True, 
        help_text=_('protect the entry with a password')
    )
    template = models.CharField(
        _('template'), 
        max_length=250, 
        default='blog/entry_detail.html', 
        choices=[('blog/entry_detail.html', _('Default template'))] + ENTRY_TEMPLATES, 
        help_text=_('template used to display the entry')
    )
    
    objects = models.Manager()
    published = EntryPublishedManager()
    
    @property
    def html_content(self):
        """ Return the content correctly formatted """
        if MARKUP_LANGUAGE == 'markdown':
            return markdown(self.content, MARKDOWN_EXTENSIONS)
        elif MARKUP_LANGUAGE == 'textile':
            return textile(self.content)
        elif MARKUP_LANGUAGE == 'restructedtext':
            return restructeredtext(self.content)
        elif not '</p>' in self.content:
            return linebreaks(self.content)
        return self.content
    
    @property
    def previous_entry(self):
        """ Return the previous entry """
        entries = Entry.published.filter(created__lt=self.created)[:1]
        if entries:
            return entries[0]
    
    @property
    def next_entry(self):
        """ Return the next entry """
        entries = Entry.published.filter(created__gt=self.created).order_by('created')[:1]
        if entries:
            return entries[0]
    
    @property
    def word_count(self):
        """ Count the words of an entry """
        return len(strip_tags(self.html_content).split())
    
    @property
    def is_actual(self):
        """ Check if an entry is within publication period """
        now = datetime.now()
        return now >= self.start_publication and now < self.end_publication
    
    @property
    def is_visible(self):
        """ Check if an entry is visible on site """
        return self.is_actual and self.status == PUBLISHED
    
    @property
    def related_published(self):
        """ Return only related entries published """
        return entries_published(self.related)
    
    @property
    def discussions(self):
        """ Return published discussions """
        return comments.get_model().objects.for_model(self).filter(is_public=True)
    
    @property
    def comments(self):
        """ Return published comments """
        return self.discussions.filter(Q(flags=None) | Q(flags__flag=CommentFlag.MODERATOR_APPROVAL))
    
    @property
    def pingbacks(self):
        """ Return published pingbacks """
        return self.discussions.filter(flags__flag='pingback')
    
    @property
    def trackbacks(self):
        """ Return published trackbacks """
        return self.discussions.filter(flags__flag='trackbacks')
    
    @property
    def comments_are_open(self):
        """ Check if comments are open """
        if AUTO_CLOSE_COMMENTS_AFTER and self.comment_enabled:
            return (datetime.now() - self.start_publication).days < AUTO_CLOSE_COMMENTS_AFTER
        return self.comment_enabled
    
    @property
    def short_url(self):
        """ Return the entries short url """
        return get_url_shortener()(self)
    
    def __unicode__(self):
        return '%s: %s' % (self.title, self.get_status_display())
    
    @models.permalink
    def get_absolute_url(self):
        """ Return entries URL """
        return ('blog_entry_detail', (), {
                'category': self.categories.all()[0],
                'slug': self.slug})
    
    class Meta:
        abstract = True


def get_base_model():
    """ Determine the base Model to inherit in the Entry Model, this allow to overload it. """
    if not ENTRY_BASE_MODEL:
        return EntryAbastractClass
    dot = ENTRY_BASE_MODEL.rindex('.')
    module_name = ENTRY_BASE_MODEL[:dot]
    class_name = ENTRY_BASE_MODEL[dot + 1:]
    
    try:
        _class = getattr(import_module(module_name), class_name)
        return _class
    except (ImportError, AttributeError):
        warnings.warn('%s cannot be imported' % ENTRY_BASE_MODEL, RuntimeWarning)
    return EntryAbstractClass


class Entry(get_base_model()):
    """ Final Entry model """
    
    class Meta:
        ordering = ['-created',]
        verbose_name = _('entry')
        verbose_name_plural = _('entries')
        permissions = (
            ('can_view_all', 'Can view all'),
            ('can_change_author', 'Can change author'),
        )


moderator.register(Entry, EntryCommentModerator)
mptt.register(Category, order_insertion_by=['title'])
post_save.connect(ping_directories_handler, sender=Entry, dispatch_uid='blog.entry.post_save.ping_directories')
post_save.connect(ping_external_urls_handler, sender=Entry, dispatch_uid='blog.entry.post_save.ping_external_urls')

