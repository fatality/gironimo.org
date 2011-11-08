from django.conf.urls.defaults import url, patterns


urlpatterns = patterns('gironimo.blog.views.search',
    url(
        r'^$',
        'entry_search',
        name='blog_entry_search'
    ),
)

