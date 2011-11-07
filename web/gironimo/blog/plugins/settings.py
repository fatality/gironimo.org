""" Settings for Blog CMS """
import warnings
from django.conf import settings
from django.utils.importlib import import_module


HIDE_ENTRY_MENU = getattr(settings, 'BLOG_HIDE_ENTRY_MENU', True)

PLUGINS_TEMPLATES = getattr(settings, 'BLOG_PLUGINS_TEMPLATES', [])

APP_MENUS = []

DEFAULT_APP_MENUS = [
    'gironimo.blog.plugins.menu.EntryMenu',
    'gironimo.blog.plugins.menu.CategoryMenu',
    'gironimo.blog.plugins.menu.TagMenu',
    'gironimo.blog.plugins.menu.AuthorMenu'
]

for menu_string in getattr(settings, 'BLOG_APP_MENUS', DEFAULT_APP_MENUS):
    try:
        dot = menu_string.rindex('.')
        menu_module = menu_string[:dot]
        menu_name = menu_string[dot + 1:]
        APP_MENUS.append(getattr(import_module(menu_module), menu_name))
    except (ImportError, AttributeError):
        warnings.warn('%s menu cannot be imported' % menu_string, RuntimeWarning)

