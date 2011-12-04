from django.conf import settings
from django.utils.encoding import smart_str
from django.contrib.sites.models import Site
from django.template.loader import render_to_string
from django.core.management.base import NoArgsCommand

from tagging.models import Tag

from gironimo.blog import __version__
from gironimo.blog.config import PROTOCOL
from gironimo.blog.models import Entry
from gironimo.blog.models import Category


class Command(NoArgsCommand):
    """ Command object for exporting the blog into WordPress via a WordPress 
    eXtended RSS (WXR) file. """
    help = 'Export your Blog to WXR file.'

    def handle_noargs(self, **options):
        site = Site.objects.get_current()
        blog_context = {'entries': Entry.objects.all(),
                        'categories': Category.objects.all(),
                        'tags': Tag.objects.usage_for_model(Entry),
                        'version': __version__,
                        'language': settings.LANGUAGE_CODE,
                        'site': site,
                        'site_url': '%s://%s' % (PROTOCOL, site.domain)}
        export = render_to_string('blog/wxr.xml', blog_context)
        print smart_str(export)

