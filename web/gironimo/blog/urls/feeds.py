from django.conf.urls.defaults import url, patterns
from gironimo.blog.feeds import LatestEntries, EntryDiscussions, EntryComments, EntryTrackbacks, EntryPingbacks, SearchEntries, TagEntries, CategoryEntries, AuthorEntries


urlpatterns = patterns('',
    url(
        r'^latest/$',
        LatestEntries(),
        name='blog_entry_latest_feed'
    ),
    url(
        r'^suche/$',
        SearchEntries(),
        name='blog_entry_search_feed'
    ),
    url(
        r'^tags/(?P<slug>[- \w]+)/$',
        TagEntries(),
        name='blog_tag_feed'
    ),
    url(
        r'^autoren/(?P<username>[.+-@\w]+)/$',
        AuthorEntries(),
        name='blog_author_feed'
    ),
    url(
        r'^kategorien/(?P<path>[-\/\w]+)/$',
        CategoryEntries(),
        name='blog_category_feed'
    ),
    url(
        r'^diskussionen/(?P<path>[-\/\w]+)/(?P<slug>[-\w]+).html',
        EntryDiscussions(),
        name='blog_entry_discussion_feed'
    ),
    url(
        r'^kommentare/(?P<path>[-\/\w]+)/(?P<slug>[-\w]+).html',
        EntryComments(),
        name='blog_entry_comment_feed'
    ),
    url(
        r'^pingbacks/(?P<path>[-\/\w]+)/(?P<slug>[-\w]+).html',
        EntryPingbacks(),
        name='blog_entry_pingback_feed'
    ),
    url(
        r'^trackbacks/(?P<path>[-\/\w]+)/(?P<slug>[-\w]+).html',
        EntryTrackbacks(),
        name='blog_entry_trackback_feed'
    ),
)

