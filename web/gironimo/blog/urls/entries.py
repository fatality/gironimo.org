from django.conf.urls.defaults import url, patterns
from gironimo.blog.models import Entry
from gironimo.blog.config import PAGINATION, ALLOW_EMPTY, ALLOW_FUTURE


entry_conf_index = {
    'paginate_by': PAGINATION,
    'template_name': 'blog/entry_archive.html'
}

entry_conf = {
    'date_field': 'created',
    'allow_empty': ALLOW_EMPTY,
    'allow_future': ALLOW_FUTURE,
    'month_format': '%m'
}

entry_conf_year = entry_conf.copy()
entry_conf_year['make_object_list'] = True
del entry_conf_year['month_format']

entry_conf_detail = entry_conf.copy()
del entry_conf_detail['allow_empty']
entry_conf_detail['queryset'] = Entry.published.on_site()

urlpatterns = patterns('gironimo.blog.views.entries',
    url(
        r'^$',
        'entry_index',
        entry_conf_index,
        name='blog_entry_archive_index'
    ),
    url(
        r'^page/(?P<page>\d+)/$',
        'entry_index',
        entry_conf_index,
        name='blog_entry_archive_index_paginated'
    ),
    url(
        r'^(?P<year>\d{4})/$',
        'entry_year',
        entry_conf_year,
        name='blog_entry_archive_year'
    ),
    url(
        r'^(?P<year>\d{4})/(?P<month>\d{2})/$',
        'entry_month',
        entry_conf,
        name='blog_entry_archive_month'
    ),
    url(
        r'^(?P<year>\d{4})/(?P<month>\d{2}/(?P<day>\d{2})/$',
        'entry_day',
        entry_conf,
        name='blog_entry_archive_day'
    ),
    url(
        r'^(?P<year>\d{4})/(?P<month>\d{2})/(?P<day>\d{2})/(?P<slug>[-\w]+)/$',
        'entry_detail',
        entry_conf_detail,
        name='blog_entry_detail'
    ),
    url(
        r'^(?P<object_id>\d+)/$',
        'entry_shortlink',
        name='blog_entry_shortlink'
    ),
)

