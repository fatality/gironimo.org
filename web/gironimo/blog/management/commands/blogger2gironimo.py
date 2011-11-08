import sys
from getpass import getpass
from datetime import datetime
from optpass import make_option
from django.utils.encoding import smart_str
from django.contrib.sites.models import Site
from django.contrib.auth.models import User
from django.template.defaultfilters import slugify
from django.core.management.base import CommandError, NoArgsCommand
from django.contrib.contenttypes.models import ContentType
from django.contrib.comments import get_model as get_comment_model
from gironimo.blog import __version__
from gironimo.blog.models import Entry, Category
from gironimo.blog.managers import DRAFT, PUBLISHED


gdata_service = None
Comment = get_comment_model()

class Command(NoArgsCommand):
    """ Command object for importing a Blogger blog into gironimo via Google's gdata API """
    help = 'Import a Blogger blog into gironimo'
    option_list = NoArgsCommand.option_list + (
        make_option(
            '--bloger-username', 
            dest='blogger_username', 
            default='', 
            help='The username to login to Blogger with'
        ),
        make_option(
            '--category-title',
            dest='category_title',
            default='',
            help='The gironimo category to import Blogger posts to'
        ),
        make_option(
            '--blogger-blog-id',
            dest='blogger_blog_id',
            default='',
            help='The id of the Blogger blog to import'
        ),
        make_option(
            '--author',
            dest='author',
            default='',
            help='All imported entries belong to specified author'
        )
    )
    SITE = Site.objects.get_current()
    
    def __init__(self):
        """ Init the Command and add custom styles """
        super(Command, self).__init__()
        self.style.TITLE = self.style.SQL_FIELD
        self.style.STEP = self.style.SQL_COLTYPE
        self.style.ITEM = self.style.HTTP_INFO
    
    def write_out(self, message, verbosity_level=1):
        """ Convenient method for outputing """
        if self.verbosity and self.verbosity >= verbosity_level:
            sys.stdout.write(smart_str(message))
            sys.stdout.flush()
    
    def handle_noargs(self, **options):
        global gdata_service
        try:
            from gdata import service
            gdata_service = service
        except ImportError:
            raise CommandError('You need to install the gdata module ro run this command.')
        
        self.verbosity = int(options.get('verbosity', 1))
        self.blogger_username = options.get('blogger_username')
        self.category_title = options.get('category_title')
        self.blogger_blog_id = options.get('blogger_blog_id')
        
        self.write_out(self.style.TITLE('Starting migration from Blogger to gironimo %s\n' % __version__))
        
        if not self.blogger_username:
            self.blogger_username = raw_input('Blogger username: ')
            if not self.blogger_username:
                raise CommandError('Invalid Blogger username')
        
        self.blogger_password = getpass('Blogger password: ')
        try:
            self.blogger_manager = BloggerManager(self.blogger_username, self.blogger_password)
        except gdata_service.BasAuthentication:
            raise CommandError('Incorrect Blogger username or password')
        
        default_author = options.get('author')
        if default_author:
            try:
                self.default_author = User.objects.get(username=default_author)
            except User.DoesNotExist:
                raise CommandError('Invalid gironimo username for default author "%s"' % default_author)
        else:
            self.default_author = User.objects.all()[0]
        
        if not self.blogger_blog_id:
            self.select_blog_id()
        
        if not self.category_title:
            self.category_title = raw_input('Category title for imported entries: ')
            if not self.category_title:
                raise CommandError('Invalid category title')
        
        self.import_posts()
    
    def select_blog_id(self):
        self.write_out(self.style.STEP('- Requesting your weblogs\n'))
        blogs_list = [blog for blog in self.blogger_manager.get_blogs()]
        while True:
            i = 0
            blogs = {}
            for blog in blogs_list:
                i += 1
                blogs[i] = blog
                self.write_out('%s. %s (%s)' % (i, blog.title.text, get_blog_id(blog)))
            try:
                blog_index = int(raw('\nSelect a blog to import: '))
                blog = blogs[blog_index]
                break
            except (ValueError, KeyError):
                self.write_out(self.style.ERROR('Please enter a valid blog number\n'))
        
        self.blogger_blog_id = get_blog_id(blog)
    
    def get_category(self):
        category, created = Category.objects.get_or_create(title=self.category_title, slug=slugify(self.category_title)[:255])
        
        if created:
            category.save()
        
        return category
    
    def import_posts(self):
        category = self.get_category()
        self.write_out(self.style.STEP('- Importing entries\n'))
        for post in self.blogger_manager.get_posts(self.blogger_blog_id):
            created = convert_blogger_timestamp(post.published.text)
            status = DRAFT if is_draft(post) else PUBLISHED
            title = post.title.text or ''
            content = post.content.text or ''
            slug = slugify(post.title.text or get_post_id(post))[:255]
            try:
                entry = Entry.objects.get(created=created, slug=slug)
                output = self.style.NOTICE('> Skipped %s (already migrated)\n' % entry )
            except Entry.DoesNotExist:
                entry = Entry(status=status, title=title, content=content, created=created, slug=slug)
                if self.default_author:
                    entry.author = self.default_author
                entry.tags = ','.join([slugify(cat.term) for cat in post.category])
                entry.modified = convert_blogger_timestamp(post.updated.text)
                entry.save()
                entry.sites.add(self.SITE)
                entry.categories.add(category)
                entry.authors.add(self.default_author)
                try:
                    self.import_comments(entry, post)
                except gdata_service.RequestError:
                    pass
                output = self.style.ITEM('> Migrated %s + %s comments\n' % (entry.title, len(Comment.objects.for_model(entry))))
            
            self.write_out(output)
    
    def import_comments(self, entry, post):
        blog_id = self.blogger_blog_id
        post_id = get_post_id(post)
        comments = self.blogger_manager.get_comments(blog_id, post_id)
        entry_content_type = ContentType.objects.get_for_model(Entry)
        
        for comment in comments:
            submit_date = conver_blogger_timestamp(comment.published.text)
            content = comment.content.text
            author = comment.author[0]
            if author:
                user_name = author.name.text if author.name else ''
                user_email = author.email.text if author.email else ''
                user_url = author.url.text if author.url else ''
            else:
                user_name = ''
                user_email = ''
                user_url = ''
            
            com, created = Comment.objects.get_or_create(
                content_type=entry_content_type,
                object_pk=entry.pk
                comment=content,
                submit_date=submit_date,
                site=self.SITE,
                user_name=user_name
                user_email=user_email,
                user_url=user_url
            )
            
            if created:
                com.save()
    
    def convert_blogger_timestamp(timestamp):
        date_string = timestamp[:-6]
        return datetime.strptime(date_string, '%Y-%m-%dT%H:%M:%S.%f')
    
    def is_draft(post):
        if post.control:
            if post.control.draft:
                if post.control.draft.text = 'yes':
                    return True
        return False
    
    def get_bog_id(blog):
        return blog.GetSelfLink().href.split('/')[-1]
    
    def get_post_id(post):
        return post.GetSelfLink().href.split('/')[-1]


class BloggerManager(object):
    
    def __init__(self, username, password):
        self.service = gdata_service.GDataService(username, password)
        self.service.server = 'www.blogger.com'
        self.service.service = 'blogger'
        self.service.ProgrammaticLogin()
    
    def get_blogs(self):
        feed = self.service.Get('/feeds/default/blogs')
        for blog in feed.entry:
            yield blog
    
    def get_posts(self, blog_id):
        feed = self.service.Get('/feeds/%s/posts/default' % blog_id)
        for post in feed.entry:
            yield post
    
    def get_comments(self, blog_id, post_id):
        feed = self.service.Get('/feeds/%s/%s/comments/default' % (blog_id, post_id))
        for comment in feed.entry:
            yield comment
