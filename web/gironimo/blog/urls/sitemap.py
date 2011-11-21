from django.conf.urls.defaults import url
from django.conf.urls.defaults import patterns


urlpatterns = patterns('zinnia.views.sitemap',
    url(r'^$',
        'sitemap', {
            'template': 'blog/sitemap.html'
        }, name='blog_sitemap'),
)

