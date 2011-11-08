BLOG_XMLRPC_PINGBACK = [
    ('gironimo.blog.xmlrpc.pingback.pingback_ping', 'pingback.ping'),
    ('gironimo.blog.xmlrpc.pingback.pingback_extensions_get_pingbacks', 'pingback.extensions.getPingbacks'),
]

BLOG_XMLRPC_METAWEBLOG = [
    ('gironimo.blog.xmlrpc.mataweblog.get_users_blogs', 'blogger.getUsersBlogs'),
    ('gironimo.blog.xmlrpc.mataweblog.get_user_info', 'blogger.getUserInfo'),
    ('gironimo.blog.xmlrpc.mataweblog.delete_post', 'blogger.deletePost'),
    ('gironimo.blog.xmlrpc.mataweblog.get_authors', 'wp.getAuthors'),
    ('gironimo.blog.xmlrpc.mataweblog.get_categories', 'metaWeblog.getCategories'),
    ('gironimo.blog.xmlrpc.mataweblog.new_category', 'wp.newCategory'),
    ('gironimo.blog.xmlrpc.mataweblog.get_recent_posts', 'metaWeblog.getRecentPosts'),
    ('gironimo.blog.xmlrpc.mataweblog.get_post', 'metaWeblog.getPost'),
    ('gironimo.blog.xmlrpc.mataweblog.new_post', 'metaWeblog.newPost'),
    ('gironimo.blog.xmlrpc.mataweblog.editPost', 'metaWeblog.editPost'),
    ('gironimo.blog.xmlrpc.mataweblog.new_media_object', 'metaWeblog.newMediaObject'),
]

BLOG_XMLRPF_METHODS = BLOG_XMLRPC_PINGBACK + BLOG_XMLRPC_METAWEBLOG

