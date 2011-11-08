from django.conf.urls.defaults import url, patterns

urlpatterns = patterns('django.views.generic.simple',
    url(
        r'^erfolg/$',
        'direct_to_template',
        {
            'template': 'comments/blog/entry/posted.html'
        },
        name='blog_discussion_success'
    ),
)

