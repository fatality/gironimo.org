from datetime import datetime

from django.forms import Media
from django.contrib import admin
from django.contrib.auth.models import User
from django.utils.html import strip_tags
from django.utils.text import truncate_words
from django.conf.urls.defaults import url
from django.conf.urls.defaults import patterns
from django.conf import settings as project_settings
from django.utils.translation import ugettext_lazy as _
from django.core.urlresolvers import reverse, NoReverseMatch

from tagging.models import Tag

from gironimo.blog import config
from gironimo.blog.managers import HIDDEN
from gironimo.blog.managers import PUBLISHED
from gironimo.blog.ping import DirectoryPinger
from gironimo.blog.admin.forms import EntryAdminForm


class EntryAdmin(admin.ModelAdmin):
    """ Admin for Entry model """
    form = EntryAdminForm
    date_hierarchy = 'creation_date'
    fieldsets = (
        (None, {
            'fields': ('title', 'slug', 'content', 'tags', 'excerpt', 'image',
                       'featured', 'status', 'categories',),
            'classes': ('wide', 'extrapretty',),
        }),
        (_('Options'), {
            'fields': ('authors', 'related', 'template', 'comment_enabled',
                       'pingback_enabled', 'sites',),
            'classes': ('wide', 'extrapretty', 'collapse', 'collapse-closed',),
        }),
        (_('Date options'), {
            'fields': ('creation_date', 'start_publication',
                       'end_publication',),
            'classes': ('wide', 'extrapretty', 'collapse', 'collapse-closed',),
        }),
        (_('Privacy'), {
            'fields': ('password', 'login_required',),
            'classes': ('wide', 'extrapretty', 'collapse', 'collapse-closed',),
        }),
    )
    list_filter = ('categories', 'authors', 'status', 'featured',
                   'login_required', 'comment_enabled', 'pingback_enabled',
                   'creation_date', 'start_publication',
                   'end_publication', 'sites',)
    list_display = ('get_title', 'get_authors', 'get_categories',
                    'get_tags', 'get_sites',
                    'get_comments_are_open', 'pingback_enabled',
                    'get_is_actual', 'get_is_visible', 'get_link',
                    'get_short_url', 'creation_date',)
    radio_fields = {'template': admin.VERTICAL}
    filter_horizontal = ('categories', 'authors', 'related',)
    prepopulated_fields = {'slug': ('title', )}
    search_fields = ('title', 'excerpt', 'content', 'tags',)
    actions = ['make_mine', 'make_published', 'make_hidden',
               'close_comments', 'close_pingbacks',
               'ping_directories', 'make_tweet', 'put_on_top',]
    actions_on_top = True
    actions_on_bottom = True
    
    def __init__(self, model, admin_site):
        self.form.admin_site = admin_site
        super(EntryAdmin, self).__init__(model, admin_site)
    
    # Custom Display
    def get_title(self, entry):
        """ Return the title with word count and number of comments """
        title = _('%(title)s (%(word_count)i words)') % \
                {'title': entry.title, 'word_count': entry.word_count}
        comments = entry.comments.count()
        if comments:
            return _('%(title)s (%(comments)i comments)') % \
                   {'title': title, 'comments': comments}
        return title
    get_title.short_description = _('title')
    
    def get_authors(self, entry):
        """ Return the authors in HTML """
        try:
            authors = ['<a href="%s" target="blank">%s</a>' %
                       (reverse('blog_author_detail',
                                args=[author.username]),
                        author.username) for author in entry.authors.all()]
        except NoReverseMatch:
            authors = [author.username for author in entry.authors.all()]
        return ', '.join(authors)
    get_authors.allow_tags = True
    get_authors.short_description = _('author(s)')
    
    def get_categories(self, entry):
        """ Return the categories linked in HTML """
        try:
            categories = ['<a href="%s" target="blank">%s</a>' %
                          (category.get_absolute_url(), category.title)
                          for category in entry.categories.all()]
        except NoReverseMatch:
            categories = [category.title for category in
                          entry.categories.all()]
        return ', '.join(categories)
    get_categories.allow_tags = True
    get_categories.short_description = _('category(s)')
    
    def get_tags(self, entry):
        """ Return the tags linked in HTML """
        try:
            return ', '.join(['<a href="%s" target="blank">%s</a>' %
                              (reverse('blog_tag_detail',
                                       args=[tag.name]), tag.name)
                              for tag in Tag.objects.get_for_object(entry)])
        except NoReverseMatch:
            return entry.tags
    get_tags.allow_tags = True
    get_tags.short_description = _('tag(s)')
    
    def get_sites(self, entry):
        """ Return the sites linked in HTML """
        return ', '.join(
            ['<a href="http://%(domain)s" target="blank">%(name)s</a>' %
             site.__dict__ for site in entry.sites.all()])
    get_sites.allow_tags = True
    get_sites.short_description = _('site(s)')
    
    def get_comments_are_open(self, entry):
        """ Admin wrapper for entry.comments_are_open """
        return entry.comments_are_open
    get_comments_are_open.boolean = True
    get_comments_are_open.short_description = _('comment enabled')
    
    def get_is_actual(self, entry):
        """ Admin wrapper for entry.is_actual """
        return entry.is_actual
    get_is_actual.boolean = True
    get_is_actual.short_description = _('is actual')
    
    def get_is_visible(self, entry):
        """ Admin wrapper for entry.is_visible """
        return entry.is_visible
    get_is_visible.boolean = True
    get_is_visible.short_description = _('is visible')
    
    def get_link(self, entry):
        """ Return a formated link to the entry """
        return u'<a href="%s" target="blank">%s</a>' % (
            entry.get_absolute_url(), _('View'))
    get_link.allow_tags = True
    get_link.short_description = _('View on site')
    
    def get_short_url(self, entry):
        """ Return the short url in HTML """
        short_url = entry.short_url
        if not short_url:
            return _('Unavailable')
        return '<a href="%(url)s" target="blank">%(url)s</a>' % \
               {'url': short_url}
    get_short_url.allow_tags = True
    get_short_url.short_description = _('short url')
    
    # Custom Methods
    def save_model(self, request, entry, form, change):
        """ Save the authors, update time, make an excerpt """
        if not form.cleaned_data.get('excerpt') and entry.status == PUBLISHED:
            entry.excerpt = truncate_words(strip_tags(entry.content), 50)
        
        if entry.pk and not request.user.has_perm('blog.can_change_author'):
            form.cleaned_data['authors'] = entry.authors.all()
        
        if not form.cleaned_data.get('authors'):
            form.cleaned_data['authors'].append(request.user)
        
        entry.last_update = datetime.now()
        entry.save()
    
    def queryset(self, request):
        """ Make special filtering by user permissions """
        queryset = super(EntryAdmin, self).queryset(request)
        if request.user.has_perm('blog.can_view_all'):
            return queryset
        return request.user.entries.all()
    
    def formfield_for_manytomany(self, db_field, request, **kwargs):
        """ Filters the disposable authors """
        if db_field.name == 'authors':
            if request.user.has_perm('blog.can_change_author'):
                kwargs['queryset'] = User.objects.filter(is_staff=True)
            else:
                kwargs['queryset'] = User.objects.filter(pk=request.user.pk)
        
        return super(EntryAdmin, self).formfield_for_manytomany(
            db_field, request, **kwargs)
    
    def get_actions(self, request):
        """ Define user actions by permissions """
        actions = super(EntryAdmin, self).get_actions(request)
        if not request.user.has_perm('blog.can_change_author') \
           or not request.user.has_perm('blog.can_view_all'):
            del actions['make_mine']
        if not config.PING_DIRECTORIES:
            del actions['ping_directories']
        if not config.USE_TWITTER:
            del actions['make_tweet']
        
        return actions
    
    # Custom Actions
    def make_mine(self, request, queryset):
        """ Set the entries to the user """
        for entry in queryset:
            if request.user not in entry.authors.all():
                entry.authors.add(request.user)
        self.message_user(
            request, _('The selected entries now belong to you.'))
    make_mine.short_description = _('Set the entries to the user')
    
    def make_published(self, request, queryset):
        """ Set entries selected as published """
        queryset.update(status=PUBLISHED)
        self.ping_directories(request, queryset, messages=False)
        self.message_user(
            request, _('The selected entries are now marked as published.'))
    make_published.short_description = _('Set entries selected as published')
    
    def make_hidden(self, request, queryset):
        """ Set entries selected as hidden """
        queryset.update(status=HIDDEN)
        self.message_user(
            request, _('The selected entries are now marked as hidden.'))
    make_hidden.short_description = _('Set entries selected as hidden')
    
    def make_tweet(self, request, queryset):
        """ Post an update on Twitter """
        import tweepy
        auth = tweepy.OAuthHandler(config.TWITTER_CONSUMER_KEY,
                                   config.TWITTER_CONSUMER_SECRET)
        auth.set_access_token(config.TWITTER_ACCESS_KEY,
                              config.TWITTER_ACCESS_SECRET)
        api = tweepy.API(auth)
        for entry in queryset:
            short_url = entry.short_url
            message = '%s %s' % (entry.title[:139 - len(short_url)], short_url)
            api.update_status(message)
        self.message_user(
            request, _('The selected entries have been tweeted.'))
    make_tweet.short_description = _('Tweet entries selected')
    
    def close_comments(self, request, queryset):
        """ Close the comments for selected entries """
        queryset.update(comment_enabled=False)
        self.message_user(
            request, _('Comments are now closed for selected entries.'))
    close_comments.short_description = _('Close the comments for '\
                                         'selected entries')
    
    def close_pingbacks(self, request, queryset):
        """ Close the pingbacks for selected entries """
        queryset.update(pingback_enabled=False)
        self.message_user(
            request, _('Linkbacks are now closed for selected entries.'))
    close_pingbacks.short_description = _(
        'Close the linkbacks for selected entries')
    
    def put_on_top(self, request, queryset):
        """ Put the selected entries on top at the current date """
        queryset.update(creation_date=datetime.now())
        self.ping_directories(request, queryset, messages=False)
        self.message_user(request, _(
            'The selected entries are now set at the current date.'))
    put_on_top.short_description = _(
        'Put the selected entries on top at the current date')
    
    def ping_directories(self, request, queryset, messages=True):
        """ Ping Directories for selected entries """
        for directory in config.PING_DIRECTORIES:
            pinger = DirectoryPinger(directory, queryset)
            pinger.join()
            if messages:
                success = 0
                for result in pinger.results:
                    if not result.get('flerror', True):
                        success += 1
                    else:
                        self.message_user(request,
                                          '%s : %s' % (directory,
                                                       result['message']))
                if success:
                    self.message_user(
                        request,
                        _('%(directory)s directory succesfully ' \
                          'pinged %(success)d entries.') %
                        {'directory': directory, 'success': success})
    ping_directories.short_description = _(
        'Ping Directories for selected entries')
    
    def get_urls(self):
        entry_admin_urls = super(EntryAdmin, self).get_urls()
        urls = patterns(
            'django.views.generic.simple',
            url(r'^autocomplete_tags/$', 'direct_to_template',
                {'template': 'admin/blog/entry/autocomplete_tags.js',
                 'mimetype': 'application/javascript'},
                name='blog_entry_autocomplete_tags'),
            url(r'^wymeditor/$', 'direct_to_template',
                {'template': 'admin/blog/entry/wymeditor.js',
                 'mimetype': 'application/javascript'},
                name='blog_entry_wymeditor'),
            url(r'^markitup/$', 'direct_to_template',
                {'template': 'admin/blog/entry/markitup.js',
                 'mimetype': 'application/javascript'},
                name='blog_entry_markitup'),)
        return urls + entry_admin_urls
    
    def _media(self):
        STATIC_URL = '%sblog/' % project_settings.STATIC_URL
        media = super(EntryAdmin, self).media + Media(
            css={'all': ('%scss/jquery.autocomplete.css' % STATIC_URL,)},
            js=('%sjs/jquery.js' % STATIC_URL,
                '%sjs/jquery.bgiframe.js' % STATIC_URL,
                '%sjs/jquery.autocomplete.js' % STATIC_URL,
                reverse('admin:blog_entry_autocomplete_tags'),))
        
        if config.WYSIWYG == 'wymeditor':
            media += Media(
                js=('%sjs/wymeditor/jquery.wymeditor.pack.js' % STATIC_URL,
                    '%sjs/wymeditor/plugins/hovertools/'
                    'jquery.wymeditor.hovertools.js' % STATIC_URL,
                    reverse('admin:blog_entry_wymeditor')))
        elif config.WYSIWYG == 'tinymce':
            from tinymce.widgets import TinyMCE
            media += TinyMCE().media + Media(
                js=(reverse('tinymce-js', args=('admin/blog/entry',)),))
        elif config.WYSIWYG == 'markitup':
            media += Media(
                js=('%sjs/markitup/jquery.markitup.js' % STATIC_URL,
                    '%sjs/markitup/sets/%s/set.js' % (
                        STATIC_URL, config.MARKUP_LANGUAGE),
                    reverse('admin:blog_entry_markitup')),
                css={'all': (
                    '%sjs/markitup/skins/django/style.css' % STATIC_URL,
                    '%sjs/markitup/sets/%s/style.css' % (
                        STATIC_URL, config.MARKUP_LANGUAGE))})
        return media
    media = property(_media)

