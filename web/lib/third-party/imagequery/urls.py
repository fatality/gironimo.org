# -*- coding: utf-8 -*-
from django.conf.urls.defaults import *

urlpatterns = patterns('imagequery.views',
    url(r'^generate/(?P<pk>[0-9]+)?$', 'generate_lazy', name='imagequery_generate_lazy'),
)
