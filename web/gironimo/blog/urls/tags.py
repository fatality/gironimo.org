from django.conf.urls.defaults import url, patterns


urlpatterns = patterns('gironimo.blog.views.tags',
    url(
        r'^$',
        'tag_list',
        name='blog_tag_list'
    ),
    url(
        r'^(?P<tag>[^/]+(?u))/$',
        'tag_detail',
        name='blog_tag_detail'
    ),
    url(
        r'^(?<tag>[^/]+(?u))/seite/(?P<page>\d+)/$',
        'tag_detail',
        name='blog_tag_detail_paginated'
    ),
)

