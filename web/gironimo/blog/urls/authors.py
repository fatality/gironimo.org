from django.conf.urls.defaults import url
from django.conf.urls.defaults import patterns


urlpatterns = patterns('gironimo.blog.views.authors',
    url(r'^$', 'author_list',
        name='blog_author_list'),
    url(r'^(?P<username>[.+-@\w]+)/$',
        'author_detail', name='blog_author_detail'),
    url(r'^(?P<username>[.+-@\w]+)/page/(?P<page>\d+)/$',
        'author_detail', name='blog_author_detail_paginated'),
)

