from django.conf.urls.defaults import *
from django.conf import settings


urlpatterns = patterns('',
    url(r'^media/(?P<path>lib/.*)$', 'django.views.static.serve', {'document_root': settings.LIB_MEDIA_ROOT}),
    url(r'^media/(?P<path>.*)$', 'django.views.static.serve', {'document_root': settings.MEDIA_ROOT}),
    url(r'^media/(?P<path>.*)$', 'django.views.static.serve', {'document_root': settings.STATIC_ROOT}),
    url(r'^js/i18n\.js$', 'django.views.i18n.javascript_catalog', name='js_i18n'),
    url(r'^admin/', include('gironimo.admin_urls')),
    url(r'^tinymce/', include('tinymce.urls')),
    url(r'^$', 'gironimo.views.index', name='index'),
    url(r'^', include('gironimo.page.urls')),
)

