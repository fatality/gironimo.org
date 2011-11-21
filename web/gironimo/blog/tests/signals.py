from django.test import TestCase

from gironimo.blog.models import Entry
from gironimo.blog.managers import DRAFT
from gironimo.blog.managers import PUBLISHED
from gironimo.blog.signals import disable_for_loaddata
from gironimo.blog.signals import ping_directories_handler
from gironimo.blog.signals import ping_external_urls_handler


class SignalsTestCase(TestCase):
    """ Test cases for signals """
    
    def test_disable_for_loaddata(self):
        self.top = 0
        
        @disable_for_loaddata
        def make_top():
            self.top += 1
        
        def call():
            return make_top()
        
        call()
        self.assertEquals(self.top, 1)
        # Okay the command is executed
    
    def test_ping_directories_handler(self):
        # Set up a stub around DirectoryPinger
        self.top = 0
        
        def fake_pinger(*ka, **kw):
            self.top += 1
        
        import gironimo.blog.ping
        from gironimo.blog import config
        self.original_pinger = gironimo.blog.ping.DirectoryPinger
        gironimo.blog.ping.DirectoryPinger = fake_pinger

        params = {'title': 'My entry',
                  'content': 'My content',
                  'status': PUBLISHED,
                  'slug': 'my-entry'}
        entry = Entry.objects.create(**params)
        self.assertEquals(entry.is_visible, True)
        config.PING_DIRECTORIES = ()
        ping_directories_handler('sender', **{'instance': entry})
        self.assertEquals(self.top, 0)
        config.PING_DIRECTORIES = ('toto',)
        config.SAVE_PING_DIRECTORIES = True
        ping_directories_handler('sender', **{'instance': entry})
        self.assertEquals(self.top, 1)
        entry.status = DRAFT
        ping_directories_handler('sender', **{'instance': entry})
        self.assertEquals(self.top, 1)
        
        # Remove stub
        gironimo.blog.ping.DirectoryPinger = self.original_pinger
    
    def test_ping_external_urls_handler(self):
        # Set up a stub around ExternalUrlsPinger
        self.top = 0
        
        def fake_pinger(*ka, **kw):
            self.top += 1
        
        import gironimo.blog.ping
        from gironimo.blog import config
        self.original_pinger = gironimo.blog.ping.ExternalUrlsPinger
        gironimo.blog.ping.ExternalUrlsPinger = fake_pinger
        
        params = {'title': 'My entry',
                  'content': 'My content',
                  'status': PUBLISHED,
                  'slug': 'my-entry'}
        entry = Entry.objects.create(**params)
        self.assertEquals(entry.is_visible, True)
        config.SAVE_PING_EXTERNAL_URLS = False
        ping_external_urls_handler('sender', **{'instance': entry})
        self.assertEquals(self.top, 0)
        config.SAVE_PING_EXTERNAL_URLS = True
        ping_external_urls_handler('sender', **{'instance': entry})
        self.assertEquals(self.top, 1)
        entry.status = 0
        ping_external_urls_handler('sender', **{'instance': entry})
        self.assertEquals(self.top, 1)
        
        # Remove stub
        gironimo.blog.ping.ExternalUrlsPinger = self.original_pinger

