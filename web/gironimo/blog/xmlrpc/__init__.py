BLOG_XMLRPC_PINGBACK = [
    ('gironimo.blog.xmlrpc.pingback.pingback_ping',
     'pingback.ping'),
    ('gironimo.blog.xmlrpc.pingback.pingback_extensions_get_pingbacks',
     'pingback.extensions.getPingbacks')
]

BLOG_XMLRPC_METAWEBLOG = [
    ('gironimo.blog.xmlrpc.metaweblog.get_users_blogs',
     'blogger.getUsersBlogs'),
    ('gironimo.blog.xmlrpc.metaweblog.get_user_info',
     'blogger.getUserInfo'),
    ('gironimo.blog.xmlrpc.metaweblog.delete_post',
     'blogger.deletePost'),
    ('gironimo.blog.xmlrpc.metaweblog.get_authors',
     'wp.getAuthors'),
    ('gironimo.blog.xmlrpc.metaweblog.get_categories',
     'metaWeblog.getCategories'),
    ('gironimo.blog.xmlrpc.metaweblog.new_category',
     'wp.newCategory'),
    ('gironimo.blog.xmlrpc.metaweblog.get_recent_posts',
     'metaWeblog.getRecentPosts'),
    ('gironimo.blog.xmlrpc.metaweblog.get_post',
     'metaWeblog.getPost'),
    ('gironimo.blog.xmlrpc.metaweblog.new_post',
     'metaWeblog.newPost'),
    ('gironimo.blog.xmlrpc.metaweblog.edit_post',
     'metaWeblog.editPost'),
    ('gironimo.blog.xmlrpc.metaweblog.new_media_object',
     'metaWeblog.newMediaObject')
]

BLOG_XMLRPC_METHODS = BLOG_XMLRPC_PINGBACK + BLOG_XMLRPC_METAWEBLOG

