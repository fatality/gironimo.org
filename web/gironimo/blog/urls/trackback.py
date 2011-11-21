from django.conf.urls.defaults import url
from django.conf.urls.defaults import patterns


urlpatterns = patterns('gironimo.blog.views.trackback',
    url(r'^(?P<object_id>\d+)/$', 'entry_trackback',
        name='blog_entry_trackback'),
)

