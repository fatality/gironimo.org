from unittest import TestSuite
from unittest import TestLoader
from django.conf import settings

from gironimo.blog.tests.entry import EntryTestCase
from gironimo.blog.tests.entry import EntryHtmlContentTestCase
from gironimo.blog.tests.entry import EntryGetBaseModelTestCase
from gironimo.blog.tests.signals import SignalsTestCase
from gironimo.blog.tests.category import CategoryTestCase
from gironimo.blog.tests.admin import EntryAdminTestCase
from gironimo.blog.tests.admin import CategoryAdminTestCase
from gironimo.blog.tests.managers import ManagersTestCase
from gironimo.blog.tests.feeds import BlogFeedsTestCase
from gironimo.blog.tests.views import BlogViewsTestCase
from gironimo.blog.tests.views import BlogCustomDetailViews
from gironimo.blog.tests.pingback import PingBackTestCase
from gironimo.blog.tests.metaweblog import MetaWeblogTestCase
from gironimo.blog.tests.comparison import ComparisonTestCase
from gironimo.blog.tests.quick_entry import QuickEntryTestCase
from gironimo.blog.tests.sitemaps import BlogSitemapsTestCase
from gironimo.blog.tests.ping import DirectoryPingerTestCase
from gironimo.blog.tests.ping import ExternalUrlsPingerTestCase
from gironimo.blog.tests.templatetags import TemplateTagsTestCase
from gironimo.blog.tests.moderator import EntryCommentModeratorTestCase
from gironimo.blog.tests.spam_checker import SpamCheckerTestCase
from gironimo.blog.tests.url_shortener import URLShortenerTestCase
from gironimo.blog.signals import disconnect_blog_signals


def suite():
    """Suite of TestCases for Django"""
    suite = TestSuite()
    loader = TestLoader()
    
    test_cases = (
        ManagersTestCase, # 24 tests, ~0.912s
        EntryTestCase, # 25 tests, ~0.498s
        EntryGetBaseModelTestCase, # 16 tests, ~0.421s
        SignalsTestCase, # 18 tests, ~0.458s
        EntryHtmlContentTestCase, # 19 tests, ~0.421s
        CategoryTestCase, # 17 tests, ~0.443s
        BlogViewsTestCase, # 34 tests, fail,, ~0.828s
        BlogFeedsTestCase, # 29 tests, ~0.578s
        BlogSitemapsTestCase, # 20 tests, ~0.500s
        ComparisonTestCase, # 18 tests, ~0.432s
        DirectoryPingerTestCase, # 16 tests, 0.420s
        ExternalUrlsPingerTestCase, # 20 tests, ~0.437s
        TemplateTagsTestCase, # 32 tests, ~0.587s
        QuickEntryTestCase, # 16 tests, fail, ~0.429s
        URLShortenerTestCase, # 16 tests, 0.427s
        EntryCommentModeratorTestCase, # 20 tests, ~0.464s
        BlogCustomDetailViews, # 18 tests, ~0.466s
        SpamCheckerTestCase, # 16 tests, ~0.420s
        EntryAdminTestCase, # 16 tests, ~0.541s
        CategoryAdminTestCase, # 16 tests, ~0.511s
    )
    
    # Total: 121 tests, ~2.159s
    
    if 'django_xmlrpc' in settings.INSTALLED_APPS:
        test_cases += (PingBackTestCase, MetaWeblogTestCase)
    
    for test_class in test_cases:
        tests = loader.loadTestsFromTestCase(test_class)
        suite.addTests(tests)
    
    return suite

disconnect_blog_signals()

