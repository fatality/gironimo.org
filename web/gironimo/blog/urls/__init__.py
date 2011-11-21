from django.conf.urls.defaults import url
from django.conf.urls.defaults import include
from django.conf.urls.defaults import patterns


urlpatterns = patterns('',
    url(r'^tags/', include('gironimo.blog.urls.tags',)),
    url(r'^feeds/', include('gironimo.blog.urls.feeds')),
    url(r'^authors/', include('gironimo.blog.urls.authors')),
    url(r'^categories/', include('gironimo.blog.urls.categories')),
    url(r'^search/', include('gironimo.blog.urls.search')),
    url(r'^sitemap/', include('gironimo.blog.urls.sitemap')),
    url(r'^trackback/', include('gironimo.blog.urls.trackback')),
    url(r'^discussions/', include('gironimo.blog.urls.discussions')),
    url(r'^', include('gironimo.blog.urls.quick_entry')),
    url(r'^', include('gironimo.blog.urls.capabilities')),
    url(r'^', include('gironimo.blog.urls.entries')),
)

