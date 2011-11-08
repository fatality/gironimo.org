from django.conf.urls.defaults import url, patterns


urlpatterns = patterns('gironimo.blog.views.sitemap',
    url(
        r'^$',
        'sitemap',
        {
            'template': 'blog/sitemap.html'
        },
        name='blog_sitemap'
    ),
)

