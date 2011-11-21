import inspect
from functools import wraps

from django.db.models.signals import post_save

from gironimo.blog import config


def disable_for_loaddata(signal_handler):
    """ Decorator for disabling signals sent by 'post_save' on loaddata command.
    http://code.djangoproject.com/ticket/8399 """
    
    @wraps(signal_handler)
    def wrapper(*args, **kwargs):
        for fr in inspect.stack():
            if inspect.getmodulename(fr[1]) == 'loaddata':
                return
        signal_handler(*args, **kwargs)
    
    return wrapper


@disable_for_loaddata
def ping_directories_handler(sender, **kwargs):
    """ Ping Directories when an entry is saved """
    entry = kwargs['instance']
    
    if entry.is_visible and config.SAVE_PING_DIRECTORIES:
        from gironimo.blog.ping import DirectoryPinger
        
        for directory in settings.PING_DIRECTORIES:
            DirectoryPinger(directory, [entry])


@disable_for_loaddata
def ping_external_urls_handler(sender, **kwargs):
    """ Ping Externals URLS when an entry is saved """
    entry = kwargs['instance']

    if entry.is_visible and config.SAVE_PING_EXTERNAL_URLS:
        from gironimo.blog.ping import ExternalUrlsPinger
        
        ExternalUrlsPinger(entry)


def disconnect_blog_signals():
    """ Disconnect all the signals """
    from gironimo.blog.models import Entry
    
    post_save.disconnect(
        sender=Entry, 
        dispatch_uid='gironimo.blog.entry.post_save.ping_directories')
    post_save.disconnect(
        sender=Entry, 
        dispatch_uid='gironimo.blog.entry.post_save.ping_external_urls')

