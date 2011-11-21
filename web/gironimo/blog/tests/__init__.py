from unittest import TestSuite
from unittest import TestLoader
from django.conf import settings

from gironimo.blog.tests.entry import EntryTestCase  # ~0.2s
from gironimo.blog.tests.entry import EntryHtmlContentTestCase  # ~0.5s
from gironimo.blog.tests.entry import EntryGetBaseModelTestCase
from gironimo.blog.tests.signals import SignalsTestCase
from gironimo.blog.tests.category import CategoryTestCase
from gironimo.blog.tests.admin import EntryAdminTestCase
from gironimo.blog.tests.admin import CategoryAdminTestCase
from gironimo.blog.tests.managers import ManagersTestCase  # ~1.2s
from gironimo.blog.tests.feeds import BlogFeedsTestCase  # ~0.4s
from gironimo.blog.tests.views import BlogViewsTestCase  # ~1.5s ouch...
from gironimo.blog.tests.views import BlogCustomDetailViews  # ~0.3s
from gironimo.blog.tests.pingback import PingBackTestCase  # ~0.3s
from gironimo.blog.tests.metaweblog import MetaWeblogTestCase  # ~0.6s
from gironimo.blog.tests.comparison import ComparisonTestCase
from gironimo.blog.tests.quick_entry import QuickEntryTestCase  # ~0.4s
from gironimo.blog.tests.sitemaps import BlogSitemapsTestCase  # ~0.3s
from gironimo.blog.tests.ping import DirectoryPingerTestCase
from gironimo.blog.tests.ping import ExternalUrlsPingerTestCase
from gironimo.blog.tests.templatetags import TemplateTagsTestCase  # ~0.4s
from gironimo.blog.tests.moderator import EntryCommentModeratorTestCase  # ~0.1s
from gironimo.blog.tests.spam_checker import SpamCheckerTestCase
from gironimo.blog.tests.url_shortener import URLShortenerTestCase
from gironimo.blog.signals import disconnect_blog_signals
# TOTAL ~ 6.6s


def suite():
    """Suite of TestCases for Django"""
    suite = TestSuite()
    loader = TestLoader()
    
    test_cases = (ManagersTestCase, EntryTestCase,
                  EntryGetBaseModelTestCase, SignalsTestCase,
                  EntryHtmlContentTestCase, CategoryTestCase,
                  BlogViewsTestCase, BlogFeedsTestCase,
                  BlogSitemapsTestCase, ComparisonTestCase,
                  DirectoryPingerTestCase, ExternalUrlsPingerTestCase,
                  TemplateTagsTestCase, QuickEntryTestCase,
                  URLShortenerTestCase, EntryCommentModeratorTestCase,
                  BlogCustomDetailViews, SpamCheckerTestCase,
                  EntryAdminTestCase, CategoryAdminTestCase)
    
    if 'django_xmlrpc' in settings.INSTALLED_APPS:
        test_cases += (PingBackTestCase, MetaWeblogTestCase)
    
    for test_class in test_cases:
        tests = loader.loadTestsFromTestCase(test_class)
        suite.addTests(tests)
    
    return suite

disconnect_blog_signals()

