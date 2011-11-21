from django.test import TestCase
from django.contrib.auth.models import User

from gironimo.blog import config
from gironimo.blog.models import Entry
from gironimo.blog.managers import DRAFT


class QuickEntryTestCase(TestCase):
    """ Test cases for quick_entry view """
    urls = 'gironimo.blog.tests.urls'
    
    def setUp(self):
        self.original_wysiwyg = config.WYSIWYG
        config.WYSIWYG = None
    
    def tearDown(self):
        config.WYSIWYG = self.original_wysiwyg
    
    def test_quick_entry(self):
        User.objects.create_user('user', 'user@example.com', 'password')
        User.objects.create_superuser('admin', 'admin@example.com', 'password')
        
        response = self.client.get('/quick_entry/', follow=True)
        self.assertEquals(
            response.redirect_chain,
            [('http://testserver/accounts/login/?next=/quick_entry/', 302)])
        self.client.login(username='user', password='password')
        response = self.client.get('/quick_entry/', follow=True)
        self.assertEquals(
            response.redirect_chain,
            [('http://testserver/accounts/login/?next=/quick_entry/', 302)])
        self.client.logout()
        self.client.login(username='admin', password='password')
        response = self.client.get('/quick_entry/', follow=True)
        self.assertEquals(response.redirect_chain,
                          [('http://testserver/admin/blog/entry/add/', 302)])
        response = self.client.post('/quick_entry/', {'title': 'test'},
                                    follow=True)
        self.assertEquals(response.redirect_chain,
                          [('http://testserver/admin/blog/entry/add/' \
                            '?tags=&title=test&sites=1&content=' \
                            '%3Cp%3E%3C%2Fp%3E&authors=2&slug=test', 302)])
        response = self.client.post('/quick_entry/',
                                    {'title': 'test', 'tags': 'test',
                                     'content': 'Test content',
                                     'save_draft': ''}, follow=True)
        entry = Entry.objects.get(title='test')
        self.assertEquals(response.redirect_chain,
                          [('http://testserver%s' % entry.get_absolute_url(),
                            302)])
        self.assertEquals(entry.status, DRAFT)
        self.assertEquals(entry.title, 'test')
        self.assertEquals(entry.tags, 'test')
        self.assertEquals(entry.content, '<p>Test content</p>')

