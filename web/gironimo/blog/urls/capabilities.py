from django.conf.urls.defaults import url, patterns
from django.contrib.sites.models import Site
from gironimo.blog.config import PROTOCOL, COPYRIGHT, FEEDS_FORMAT


extra_context = {
    'protocol': PROTOCOL,
    'site': Site.objects.get_current()
}

extra_context_opensearch = extra_context.copy()
extra_context_opensearch.update({
    'copyright': COPYRIGHT,
    'feeds_format': FEEDS_FORMAT,
})

urlpatterns = patterns('django.views.generic.simple',
    url(
        r'^rsd.xml$',
        'direct_to_template',
        {
            'template': 'blog/rsd.xml',
            'mimetype': 'application/rsd+xml',
            'extra_context': extra_context
        },
        name='blog_rsd'
    ),
    url(
        r'^wlwmanifest.xml$',
        'direct_to_template',
        {
            'template': 'blog/wlwmanifest.xml',
            'mimetype': 'application/wlwmanifest+xml',
            'extra_context': extra_context
        },
        name='blog_wlwmanifest'
    ),
    url(
        r'^opensearch.xml$',
        'direct_to_template',
        {
            'template': 'blog/opensearch.xml',
            'mimetype': 'application/opensearchdescription.xml',
            'extra_context': extra_context_opensearch
        },
        name='blog_opensearch'
    ),
)

