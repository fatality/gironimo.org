from datetime import datetime

from django.test import TestCase
from django.template import Context
from django.template import Template
from django.template import TemplateSyntaxError
from django.contrib import comments
from django.core.paginator import Paginator
from django.core.urlresolvers import reverse
from django.contrib.auth.models import User
from django.contrib.sites.models import Site
from django.contrib.comments.models import CommentFlag

from tagging.models import Tag

from gironimo.blog.models import Entry
from gironimo.blog.models import Author
from gironimo.blog.models import Category
from gironimo.blog.managers import DRAFT
from gironimo.blog.managers import PUBLISHED
from gironimo.blog.managers import PINGBACK, TRACKBACK
from gironimo.blog.templatetags.blog_tags import get_authors
from gironimo.blog.templatetags.blog_tags import get_gravatar
from gironimo.blog.templatetags.blog_tags import get_tag_cloud
from gironimo.blog.templatetags.blog_tags import get_categories
from gironimo.blog.templatetags.blog_tags import blog_pagination
from gironimo.blog.templatetags.blog_tags import blog_statistics
from gironimo.blog.templatetags.blog_tags import get_draft_entries
from gironimo.blog.templatetags.blog_tags import get_recent_entries
from gironimo.blog.templatetags.blog_tags import get_random_entries
from gironimo.blog.templatetags.blog_tags import get_random_author_entries
from gironimo.blog.templatetags.blog_tags import blog_breadcrumbs
from gironimo.blog.templatetags.blog_tags import get_popular_entries
from gironimo.blog.templatetags.blog_tags import get_similar_entries
from gironimo.blog.templatetags.blog_tags import get_recent_comments
from gironimo.blog.templatetags.blog_tags import get_recent_linkbacks
from gironimo.blog.templatetags.blog_tags import get_calendar_entries
from gironimo.blog.templatetags.blog_tags import get_archives_entries
from gironimo.blog.templatetags.blog_tags import get_featured_entries
from gironimo.blog.templatetags.blog_tags import get_archives_entries_tree


class TemplateTagsTestCase(TestCase):
    """Test cases for Template tags"""
    
    def setUp(self):
        params = {'title': 'My entry',
                  'content': 'My content',
                  'tags': 'blog, test',
                  'creation_date': datetime(2010, 1, 1),
                  'slug': 'my-entry'}
        self.entry = Entry.objects.create(**params)
    
    def publish_entry(self):
        self.entry.status = PUBLISHED
        self.entry.featured = True
        self.entry.sites.add(Site.objects.get_current())
        self.entry.save()
    
    def test_get_categories(self):
        context = get_categories()
        self.assertEquals(len(context['categories']), 0)
        self.assertEquals(context['template'], 'blog/tags/categories.html')
        
        Category.objects.create(title='Category 1', slug='category-1')
        context = get_categories('custom_template.html')
        self.assertEquals(len(context['categories']), 1)
        self.assertEquals(context['template'], 'custom_template.html')
    
    def test_get_authors(self):
        context = get_authors()
        self.assertEquals(len(context['authors']), 0)
        self.assertEquals(context['template'], 'blog/tags/authors.html')
        
        user = User.objects.create_user(username='webmaster',
                                        email='webmaster@example.com')
        self.entry.authors.add(user)
        self.publish_entry()
        context = get_authors('custom_template.html')
        self.assertEquals(len(context['authors']), 1)
        self.assertEquals(context['template'], 'custom_template.html')
    
    def test_get_recent_entries(self):
        context = get_recent_entries()
        self.assertEquals(len(context['entries']), 0)
        self.assertEquals(context['template'],
                          'blog/tags/recent_entries.html')
        
        self.publish_entry()
        context = get_recent_entries(3, 'custom_template.html')
        self.assertEquals(len(context['entries']), 1)
        self.assertEquals(context['template'], 'custom_template.html')
        context = get_recent_entries(0)
        self.assertEquals(len(context['entries']), 0)
    
    def test_get_featured_entries(self):
        context = get_featured_entries()
        self.assertEquals(len(context['entries']), 0)
        self.assertEquals(context['template'],
                          'blog/tags/featured_entries.html')
        
        self.publish_entry()
        context = get_featured_entries(3, 'custom_template.html')
        self.assertEquals(len(context['entries']), 1)
        self.assertEquals(context['template'], 'custom_template.html')
        context = get_featured_entries(0)
        self.assertEquals(len(context['entries']), 0)
    
    def test_draft_entries(self):
        context = get_draft_entries()
        self.assertEquals(len(context['entries']), 1)
        self.assertEquals(context['template'],
                          'blog/tags/draft_entries.html')
        
        self.publish_entry()
        context = get_draft_entries(3, 'custom_template.html')
        self.assertEquals(len(context['entries']), 0)
        self.assertEquals(context['template'], 'custom_template.html')
        context = get_draft_entries(0)
        self.assertEquals(len(context['entries']), 0)
    
    def test_get_random_entries(self):
        context = get_random_entries()
        self.assertEquals(len(context['entries']), 0)
        self.assertEquals(context['template'],
                          'blog/tags/random_entries.html')
        
        self.publish_entry()
        context = get_random_entries(3, 'custom_template.html')
        self.assertEquals(len(context['entries']), 1)
        self.assertEquals(context['template'], 'custom_template.html')
        context = get_random_entries(0)
        self.assertEquals(len(context['entries']), 0)
    
    def test_get_random_author_entries(self):
        user = User.objects.create_user(username='webmaster',
                                        email='webmaster@example.com')
        self.entry.authors.add(user)
        self.publish_entry()
        author = Author.published.all()[0]
        context = get_random_author_entries(author)
        self.assertEquals(context['template'],
                          'blog/tags/random_author_entries.html')
        self.assertEquals(len(context['entries']), 1)
        context = get_random_author_entries(author, 0)
        self.assertEquals(len(context['entries']), 0)
        context = get_random_author_entries(author, 1, 'custom_template.html')
        self.assertEquals(len(context['entries']), 1)
        self.assertEquals(context['template'], 'custom_template.html')
    
    def test_get_popular_entries(self):
        context = get_popular_entries()
        self.assertEquals(len(context['entries']), 0)
        self.assertEquals(context['template'],
                          'blog/tags/popular_entries.html')
        
        self.publish_entry()
        context = get_popular_entries(3, 'custom_template.html')
        self.assertEquals(len(context['entries']), 0)
        self.assertEquals(context['template'], 'custom_template.html')
        
        params = {'title': 'My second entry',
                  'content': 'My second content',
                  'tags': 'blog, test',
                  'status': PUBLISHED,
                  'slug': 'my-second-entry'}
        site = Site.objects.get_current()
        second_entry = Entry.objects.create(**params)
        second_entry.sites.add(site)

        comments.get_model().objects.create(comment='My Comment 1', site=site,
                                            content_object=self.entry)
        comments.get_model().objects.create(comment='My Comment 2', site=site,
                                            content_object=self.entry)
        comments.get_model().objects.create(comment='My Comment 3', site=site,
                                            content_object=second_entry)
        context = get_popular_entries(3)
        self.assertEquals(context['entries'], [self.entry, second_entry])
        self.entry.status = DRAFT
        self.entry.save()
        context = get_popular_entries(3)
        self.assertEquals(context['entries'], [second_entry])
    
    def test_get_similar_entries(self):
        self.publish_entry()
        source_context = Context({'object': self.entry})
        context = get_similar_entries(source_context)
        self.assertEquals(len(context['entries']), 0)
        self.assertEquals(context['template'],
                          'blog/tags/similar_entries.html')
        
        params = {'title': 'My second entry',
                  'content': 'This is the second entry of my tests.',
                  'tags': 'blog, test',
                  'status': PUBLISHED,
                  'slug': 'my-second-entry'}
        site = Site.objects.get_current()
        second_entry = Entry.objects.create(**params)
        second_entry.sites.add(site)
        
        source_context = Context({'object': second_entry})
        context = get_similar_entries(source_context, 3,
                                      'custom_template.html',
                                      flush=True)
        self.assertEquals(len(context['entries']), 1)
        self.assertEquals(context['template'], 'custom_template.html')
    
    def test_get_archives_entries(self):
        context = get_archives_entries()
        self.assertEquals(len(context['archives']), 0)
        self.assertEquals(context['template'],
                          'blog/tags/archives_entries.html')
        
        self.publish_entry()
        params = {'title': 'My second entry',
                  'content': 'My second content',
                  'tags': 'blog, test',
                  'status': PUBLISHED,
                  'creation_date': datetime(2009, 1, 1),
                  'slug': 'my-second-entry'}
        site = Site.objects.get_current()
        second_entry = Entry.objects.create(**params)
        second_entry.sites.add(site)
        
        context = get_archives_entries('custom_template.html')
        self.assertEquals(len(context['archives']), 2)
        self.assertEquals(context['archives'][0], datetime(2010, 1, 1))
        self.assertEquals(context['archives'][1], datetime(2009, 1, 1))
        self.assertEquals(context['template'], 'custom_template.html')
    
    def test_get_archives_tree(self):
        context = get_archives_entries_tree()
        self.assertEquals(len(context['archives']), 0)
        self.assertEquals(context['template'],
                          'blog/tags/archives_entries_tree.html')
        
        self.publish_entry()
        params = {'title': 'My second entry',
                  'content': 'My second content',
                  'tags': 'blog, test',
                  'status': PUBLISHED,
                  'creation_date': datetime(2009, 1, 10),
                  'slug': 'my-second-entry'}
        site = Site.objects.get_current()
        second_entry = Entry.objects.create(**params)
        second_entry.sites.add(site)
        
        context = get_archives_entries_tree('custom_template.html')
        self.assertEquals(len(context['archives']), 2)
        self.assertEquals(context['archives'][0], datetime(2009, 1, 10))
        self.assertEquals(context['archives'][1], datetime(2010, 1, 1))
        self.assertEquals(context['template'], 'custom_template.html')
    
    def test_get_calendar_entries(self):
        source_context = Context()
        context = get_calendar_entries(source_context)
        self.assertEquals(context['previous_month'], None)
        self.assertEquals(context['next_month'], None)
        self.assertEquals(context['template'], 'blog/tags/calendar.html')
        
        self.publish_entry()
        context = get_calendar_entries(source_context,
                                       template='custom_template.html')
        self.assertEquals(context['previous_month'], datetime(2010, 1, 1))
        self.assertEquals(context['next_month'], None)
        self.assertEquals(context['template'], 'custom_template.html')
        
        context = get_calendar_entries(source_context, 2009, 1)
        self.assertEquals(context['previous_month'], None)
        self.assertEquals(context['next_month'], datetime(2010, 1, 1))
        
        source_context = Context({'month': datetime(2009, 1, 1)})
        context = get_calendar_entries(source_context)
        self.assertEquals(context['previous_month'], None)
        self.assertEquals(context['next_month'], datetime(2010, 1, 1))
        
        source_context = Context({'month': datetime(2010, 1, 1)})
        context = get_calendar_entries(source_context)
        self.assertEquals(context['previous_month'], None)
        self.assertEquals(context['next_month'], None)
        
        params = {'title': 'My second entry',
                  'content': 'My second content',
                  'tags': 'blog, test',
                  'status': PUBLISHED,
                  'creation_date': datetime(2008, 1, 1),
                  'slug': 'my-second-entry'}
        site = Site.objects.get_current()
        second_entry = Entry.objects.create(**params)
        second_entry.sites.add(site)
        
        source_context = Context()
        context = get_calendar_entries(source_context, 2009, 1)
        self.assertEquals(context['previous_month'], datetime(2008, 1, 1))
        self.assertEquals(context['next_month'], datetime(2010, 1, 1))
        context = get_calendar_entries(source_context)
        self.assertEquals(context['previous_month'], datetime(2010, 1, 1))
        self.assertEquals(context['next_month'], None)
    
    def test_get_recent_comments(self):
        site = Site.objects.get_current()
        context = get_recent_comments()
        self.assertEquals(len(context['comments']), 0)
        self.assertEquals(context['template'],
                          'blog/tags/recent_comments.html')
        
        comment_1 = comments.get_model().objects.create(
            comment='My Comment 1', site=site,
            content_object=self.entry)
        context = get_recent_comments(3, 'custom_template.html')
        self.assertEquals(len(context['comments']), 0)
        self.assertEquals(context['template'], 'custom_template.html')
        
        self.publish_entry()
        context = get_recent_comments()
        self.assertEquals(len(context['comments']), 1)
        
        author = User.objects.create_user(username='webmaster',
                                          email='webmaster@example.com')
        comment_2 = comments.get_model().objects.create(
            comment='My Comment 2', site=site,
            content_object=self.entry)
        comment_2.flags.create(user=author,
                               flag=CommentFlag.MODERATOR_APPROVAL)
        context = get_recent_comments()
        self.assertEquals(list(context['comments']), [comment_2, comment_1])
    
    def test_get_recent_linkbacks(self):
        user = User.objects.create_user(username='webmaster',
                                        email='webmaster@example.com')
        site = Site.objects.get_current()
        context = get_recent_linkbacks()
        self.assertEquals(len(context['linkbacks']), 0)
        self.assertEquals(context['template'],
                          'blog/tags/recent_linkbacks.html')
        
        linkback_1 = comments.get_model().objects.create(
            comment='My Linkback 1', site=site,
            content_object=self.entry)
        linkback_1.flags.create(user=user, flag=PINGBACK)
        context = get_recent_linkbacks(3, 'custom_template.html')
        self.assertEquals(len(context['linkbacks']), 0)
        self.assertEquals(context['template'], 'custom_template.html')
        
        self.publish_entry()
        context = get_recent_linkbacks()
        self.assertEquals(len(context['linkbacks']), 1)
        
        linkback_2 = comments.get_model().objects.create(
            comment='My Linkback 2', site=site,
            content_object=self.entry)
        linkback_2.flags.create(user=user, flag=TRACKBACK)
        context = get_recent_linkbacks()
        self.assertEquals(list(context['linkbacks']), [linkback_2, linkback_1])
    
    def test_blog_pagination(self):
        class FakeRequest(object):
            def __init__(self, get_dict):
                self.GET = get_dict
        
        source_context = Context({'request': FakeRequest(
            {'page': '1', 'key': 'val'})})
        paginator = Paginator(range(200), 10)
        
        context = blog_pagination(source_context, paginator.page(1))
        self.assertEquals(context['page'].number, 1)
        self.assertEquals(context['begin'], [1, 2, 3])
        self.assertEquals(context['middle'], [])
        self.assertEquals(context['end'], [18, 19, 20])
        self.assertEquals(context['GET_string'], '&key=val')
        self.assertEquals(context['template'], 'blog/tags/pagination.html')
        
        source_context = Context({'request': FakeRequest({})})
        context = blog_pagination(source_context, paginator.page(2))
        self.assertEquals(context['page'].number, 2)
        self.assertEquals(context['begin'], [1, 2, 3, 4])
        self.assertEquals(context['middle'], [])
        self.assertEquals(context['end'], [18, 19, 20])
        self.assertEquals(context['GET_string'], '')
        
        context = blog_pagination(source_context, paginator.page(3))
        self.assertEquals(context['begin'], [1, 2, 3, 4, 5])
        self.assertEquals(context['middle'], [])
        self.assertEquals(context['end'], [18, 19, 20])
        
        context = blog_pagination(source_context, paginator.page(6))
        self.assertEquals(context['begin'], [1, 2, 3, 4, 5, 6, 7, 8])
        self.assertEquals(context['middle'], [])
        self.assertEquals(context['end'], [18, 19, 20])
        
        context = blog_pagination(source_context, paginator.page(11))
        self.assertEquals(context['begin'], [1, 2, 3])
        self.assertEquals(context['middle'], [9, 10, 11, 12, 13])
        self.assertEquals(context['end'], [18, 19, 20])
        
        context = blog_pagination(source_context, paginator.page(15))
        self.assertEquals(context['begin'], [1, 2, 3])
        self.assertEquals(context['middle'], [])
        self.assertEquals(context['end'], [13, 14, 15, 16, 17, 18, 19, 20])
        
        context = blog_pagination(source_context, paginator.page(18))
        self.assertEquals(context['begin'], [1, 2, 3])
        self.assertEquals(context['middle'], [])
        self.assertEquals(context['end'], [16, 17, 18, 19, 20])
        
        context = blog_pagination(source_context, paginator.page(19))
        self.assertEquals(context['begin'], [1, 2, 3])
        self.assertEquals(context['middle'], [])
        self.assertEquals(context['end'], [17, 18, 19, 20])
        
        context = blog_pagination(source_context, paginator.page(20))
        self.assertEquals(context['begin'], [1, 2, 3])
        self.assertEquals(context['middle'], [])
        self.assertEquals(context['end'], [18, 19, 20])
        
        context = blog_pagination(source_context, paginator.page(10),
                                    begin_pages=1, end_pages=3,
                                    before_pages=4, after_pages=3,
                                    template='custom_template.html')
        self.assertEquals(context['begin'], [1])
        self.assertEquals(context['middle'], [6, 7, 8, 9, 10, 11, 12, 13])
        self.assertEquals(context['end'], [18, 19, 20])
        self.assertEquals(context['template'], 'custom_template.html')
        
        paginator = Paginator(range(50), 10)
        context = blog_pagination(source_context, paginator.page(1))
        self.assertEquals(context['begin'], [1, 2, 3, 4, 5])
        self.assertEquals(context['middle'], [])
        self.assertEquals(context['end'], [])
        
        paginator = Paginator(range(60), 10)
        context = blog_pagination(source_context, paginator.page(1))
        self.assertEquals(context['begin'], [1, 2, 3, 4, 5, 6])
        self.assertEquals(context['middle'], [])
        self.assertEquals(context['end'], [])
        
        paginator = Paginator(range(70), 10)
        context = blog_pagination(source_context, paginator.page(1))
        self.assertEquals(context['begin'], [1, 2, 3])
        self.assertEquals(context['middle'], [])
        self.assertEquals(context['end'], [5, 6, 7])
    
    def test_blog_breadcrumbs(self):
        class FakeRequest(object):
            def __init__(self, path):
                self.path = path
        
        source_context = Context({'request': FakeRequest('/')})
        context = blog_breadcrumbs(source_context)
        self.assertEquals(len(context['breadcrumbs']), 1)
        self.assertEquals(context['breadcrumbs'][0].name, 'Blog')
        self.assertEquals(context['breadcrumbs'][0].url,
                          reverse('blog_entry_archive_index'))
        self.assertEquals(context['template'], 'blog/tags/breadcrumbs.html')
        
        context = blog_breadcrumbs(source_context,
                                     'Weblog', 'custom_template.html')
        self.assertEquals(len(context['breadcrumbs']), 1)
        self.assertEquals(context['breadcrumbs'][0].name, 'Weblog')
        self.assertEquals(context['template'], 'custom_template.html')
        
        source_context = Context(
            {'request': FakeRequest(self.entry.get_absolute_url()),
             'object': self.entry})
        context = blog_breadcrumbs(source_context)
        self.assertEquals(len(context['breadcrumbs']), 5)
        
        cat_1 = Category.objects.create(title='Category 1', slug='category-1')
        source_context = Context(
            {'request': FakeRequest(cat_1.get_absolute_url()),
             'object': cat_1})
        context = blog_breadcrumbs(source_context)
        self.assertEquals(len(context['breadcrumbs']), 3)
        cat_2 = Category.objects.create(title='Category 2', slug='category-2',
                                        parent=cat_1)
        source_context = Context(
            {'request': FakeRequest(cat_2.get_absolute_url()),
             'object': cat_2})
        context = blog_breadcrumbs(source_context)
        self.assertEquals(len(context['breadcrumbs']), 4)
        
        tag = Tag.objects.get(name='test')
        source_context = Context(
            {'request': FakeRequest(reverse('blog_tag_detail',
                                            args=['test'])),
             'object': tag})
        context = blog_breadcrumbs(source_context)
        self.assertEquals(len(context['breadcrumbs']), 3)
        
        User.objects.create_user(username='webmaster',
                                 email='webmaster@example.com')
        author = Author.objects.get(username='webmaster')
        source_context = Context(
            {'request': FakeRequest(author.get_absolute_url()),
             'object': author})
        context = blog_breadcrumbs(source_context)
        self.assertEquals(len(context['breadcrumbs']), 3)
        
        source_context = Context(
            {'request': FakeRequest(reverse(
                'blog_entry_archive_year', args=[2011]))})
        context = blog_breadcrumbs(source_context)
        self.assertEquals(len(context['breadcrumbs']), 2)
        
        source_context = Context({'request': FakeRequest(reverse(
            'blog_entry_archive_month', args=[2011, '03']))})
        context = blog_breadcrumbs(source_context)
        self.assertEquals(len(context['breadcrumbs']), 3)
        
        source_context = Context({'request': FakeRequest(reverse(
            'blog_entry_archive_day', args=[2011, '03', 15]))})
        context = blog_breadcrumbs(source_context)
        self.assertEquals(len(context['breadcrumbs']), 4)
        # More tests can be done here, for testing path and objects in context
    
    def test_get_gravatar(self):
        self.assertEquals(
            get_gravatar('webmaster@example.com'),
            'http://www.gravatar.com/avatar/86d4fd4a22de452'
            'a9228298731a0b592.jpg?s=80&amp;r=g')
        self.assertEquals(
            get_gravatar('  WEBMASTER@example.com  ', 15, 'x', '404'),
            'http://www.gravatar.com/avatar/86d4fd4a22de452'
            'a9228298731a0b592.jpg?s=15&amp;r=x&amp;d=404')
    
    def test_get_tags(self):
        Tag.objects.create(name='tag')
        t = Template("""
        {% load blog_tags %}
        {% get_tags as entry_tags %}
        {{ entry_tags|join:", " }}
        """)
        html = t.render(Context())
        self.assertEquals(html.strip(), '')
        self.publish_entry()
        html = t.render(Context())
        self.assertEquals(html.strip(), 'blog, test')
        
        template_error_as = """
        {% load blog_tags %}
        {% get_tags a_s entry_tags %}"""
        self.assertRaises(TemplateSyntaxError, Template, template_error_as)
        
        template_error_args = """
        {% load blog_tags %}
        {% get_tags as entry tags %}"""
        self.assertRaises(TemplateSyntaxError, Template, template_error_args)
    
    def test_get_tag_cloud(self):
        context = get_tag_cloud()
        self.assertEquals(len(context['tags']), 0)
        self.assertEquals(context['template'], 'blog/tags/tag_cloud.html')
        self.publish_entry()
        context = get_tag_cloud(6, 'custom_template.html')
        self.assertEquals(len(context['tags']), 2)
        self.assertEquals(context['template'], 'custom_template.html')
    
    def test_blog_statistics(self):
        context = blog_statistics()
        self.assertEquals(context['template'], 'blog/tags/statistics.html')
        self.assertEquals(context['entries'], 0)
        self.assertEquals(context['categories'], 0)
        self.assertEquals(context['tags'], 0)
        self.assertEquals(context['authors'], 0)
        self.assertEquals(context['comments'], 0)
        self.assertEquals(context['pingbacks'], 0)
        self.assertEquals(context['trackbacks'], 0)
        self.assertEquals(context['rejects'], 0)
        self.assertEquals(context['words_per_entry'], 0)
        self.assertEquals(context['words_per_comment'], 0)
        self.assertEquals(context['entries_per_month'], 0)
        self.assertEquals(context['comments_per_entry'], 0)
        self.assertEquals(context['linkbacks_per_entry'], 0)

        site = Site.objects.get_current()
        Category.objects.create(title='Category 1', slug='category-1')
        user = User.objects.create_user(username='webmaster',
                                        email='webmaster@example.com')
        comments.get_model().objects.create(comment='My Comment 1', site=site,
                                            content_object=self.entry)
        self.entry.authors.add(user)
        self.publish_entry()
        
        context = blog_statistics('custom_template.html')
        self.assertEquals(context['template'], 'custom_template.html')
        self.assertEquals(context['entries'], 1)
        self.assertEquals(context['categories'], 1)
        self.assertEquals(context['tags'], 2)
        self.assertEquals(context['authors'], 1)
        self.assertEquals(context['comments'], 1)
        self.assertEquals(context['pingbacks'], 0)
        self.assertEquals(context['trackbacks'], 0)
        self.assertEquals(context['rejects'], 0)
        self.assertEquals(context['words_per_entry'], 2)
        self.assertEquals(context['words_per_comment'], 3)
        self.assertEquals(context['entries_per_month'], 0)
        self.assertEquals(context['comments_per_entry'], 1)
        self.assertEquals(context['linkbacks_per_entry'], 0)

