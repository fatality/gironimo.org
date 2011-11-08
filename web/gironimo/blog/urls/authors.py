from django.conf.urls.defaults import url, patterns


urlpatterns = patterns('gironimo.blog.views.authors',
    url(
        r'^$',
        'author_list',
        name='blog_author_list'
    ),
    url(
        r'^(?P<username>[.+-@\w]+)/$',
        'author_detail',
        name='blog_author_detail'
    ),
    url(
        r'^(?P<username>[.+-@\w]+/seite/(?P<page>\d+)/$',
        'author_detail',
        name='blog_author_detail_paginated'
    ),
)

