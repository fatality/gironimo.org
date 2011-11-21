from django.conf.urls.defaults import url
from django.conf.urls.defaults import patterns


urlpatterns = patterns('django.views.generic.simple',
    url(r'^success/$',
        'direct_to_template', {
            'template': 'comments/blog/entry/posted.html'
        }, name='blog_discussion_success'),
)

