from django.conf.urls.defaults import url
from django.conf.urls.defaults import patterns

from gironimo.blog.feeds import LatestEntries
from gironimo.blog.feeds import EntryDiscussions
from gironimo.blog.feeds import EntryComments
from gironimo.blog.feeds import EntryTrackbacks
from gironimo.blog.feeds import EntryPingbacks
from gironimo.blog.feeds import SearchEntries
from gironimo.blog.feeds import TagEntries
from gironimo.blog.feeds import CategoryEntries
from gironimo.blog.feeds import AuthorEntries


urlpatterns = patterns('',
    url(r'^latest/$',
        LatestEntries(),
        name='blog_entry_latest_feed'),
    url(r'^search/$',
        SearchEntries(),
        name='blog_entry_search_feed'),
    url(r'^tags/(?P<slug>[- \w]+)/$',
        TagEntries(),
        name='blog_tag_feed'),
    url(r'^authors/(?P<username>[.+-@\w]+)/$',
        AuthorEntries(),
        name='blog_author_feed'),
    url(r'^categories/(?P<path>[-\/\w]+)/$',
        CategoryEntries(),
        name='blog_category_feed'),
    url(r'^discussions/(?P<year>\d{4})/(?P<month>\d{2})/' \
        '(?P<day>\d{2})/(?P<slug>[-\w]+)/$',
        EntryDiscussions(),
        name='blog_entry_discussion_feed'),
    url(r'^comments/(?P<year>\d{4})/(?P<month>\d{2})/' \
        '(?P<day>\d{2})/(?P<slug>[-\w]+)/$',
        EntryComments(),
        name='blog_entry_comment_feed'),
    url(r'^pingbacks/(?P<year>\d{4})/(?P<month>\d{2})/' \
        '(?P<day>\d{2})/(?P<slug>[-\w]+)/$',
        EntryPingbacks(),
        name='blog_entry_pingback_feed'),
    url(r'^trackbacks/(?P<year>\d{4})/(?P<month>\d{2})/' \
        '(?P<day>\d{2})/(?P<slug>[-\w]+)/$',
        EntryTrackbacks(),
        name='blog_entry_trackback_feed'),
)

