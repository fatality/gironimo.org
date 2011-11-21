from django.conf.urls.defaults import *
from django.conf import settings
from gironimo.blog.sitemaps import TagSitemap, EntrySitemap, CategorySitemap, AuthorSitemap


sitemaps = {
    'tags': TagSitemap,
    'blog': EntrySitemap,
    'authors': AuthorSitemap,
    'categories': CategorySitemap,
}


urlpatterns = patterns('',
    url(r'^media/(?P<path>lib/.*)$', 'django.views.static.serve', {'document_root': settings.LIB_MEDIA_ROOT}),
    url(r'^media/(?P<path>.*)$', 'django.views.static.serve', {'document_root': settings.MEDIA_ROOT}),
    url(r'^media/(?P<path>.*)$', 'django.views.static.serve', {'document_root': settings.STATIC_ROOT}),
    url(r'^js/i18n\.js$', 'django.views.i18n.javascript_catalog', name='js_i18n'),
    url(r'^admin/', include('gironimo.admin_urls')),
    #url(r'^blog/', include('gironimo.blog.urls')),
    url(r'^kommentare/', include('django.contrib.comments.urls')),
    url(r'^tinymce/', include('tinymce.urls')),
    url(r'^xmlrpc/$', 'django_xmlrpc.views.handle_xmlrpc'),
    url(r'^$', 'gironimo.views.index', name='index'),
    #url(r'^', include('gironimo.page.urls')),
)

urlpatterns += patterns('django.contrib.sitemaps.views',
    url(r'^sitemap.xml$', 'index', {'sitemap': sitemaps}),
    url(r'^sitemap-(?P<section>.+)\.xml$', 'sitemap', {'sitemaps': sitemaps}),
)

