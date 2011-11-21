from django.conf.urls.defaults import url
from django.conf.urls.defaults import patterns


urlpatterns = patterns('gironimo.blog.views.quick_entry',
    url(r'^quick_entry/$', 'view_quick_entry', name='blog_entry_quick_post')
)

