from django.conf.urls.defaults import url
from django.conf.urls.defaults import patterns

from gironimo.blog.models import Category


category_conf = {'queryset': Category.tree.all()}


urlpatterns = patterns('django.views.generic.list_detail',
    url(r'^$',
        'object_list', category_conf, 'blog_category_list'),
)


urlpatterns += patterns('gironimo.blog.views.categories',
    url(r'^(?P<path>[-\/\w]+)/page/(?P<page>\d+)/$',
        'category_detail', name='blog_category_detail_paginated'),
    url(r'^(?P<path>[-\/\w]+)/$',
        'category_detail', name='blog_category_detail'),
)

