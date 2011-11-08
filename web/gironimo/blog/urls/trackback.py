from django.conf.urls.defaults import url, patterns


urlpatterns = patterns('gironimo.blog.views.trackback',
    url(
        r'^(?P<object_id>\d+)/$',
        'entry_trackback',
        name='blog_entry_trackback'
    ),
)

