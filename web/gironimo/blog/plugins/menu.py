from django.core.urlresolvers import reverse
from django.utils.translation import ugettext_lazy as _
from menus.base import Modifier
from menus.base import NavigationNode
from menus.menu_pool import menu_pool
from cms.menu_bases import CMSAttachMenu
from gironimo.blog.models import Entry
from gironimo.blog.models import Author
from gironimo.blog.models import Category
from gironimo.blog.managers import tags_published
from gironimo.blog.plugins.settings import HIDE_ENTRY_MENU


class EntryMenu(CMSAttachMenu):
    """ Menu for the entries organized by archives dates """
    name = _('Blog Entry Menu')
    
    def get_nodes(self, request):
        """ Return menu's node for entries """
        nodes = []
        archives = []
        attributes = {'hidden': HIDE_ENTRY_MENU}
        for entry in Entry.published.all():
            year = entry.created.strftime('%Y')
            month = entry.created.strftime('%m')
            month_text = entry.created.strftime('%b')
            day = entry.created.strftime('%d')
            
            key_archive_year = 'year-%s' % year
            key_archive_month = 'month-%s-%s' % (year, month)
            key_archive_day = 'day-%s-%s-%s' % (year, month, day)
            
            if not key_archive_year in archives:
                nodes.append(NavigationNode(
                    year,
                    reverse('blog_entry_archive_year', args=[year]),
                    key_archive_year, attr=attributes
                ))
                archives.append(key_archive_year)
            
            if not key_archive_month in archives:
                nodes.append(NavigationNode(
                    month_text,
                    reverse('blog_entry_archive_month', args=[year, month]),
                    key_archive_month,
                    key_archive_year,
                    attr=attributes
                ))
                archives.append(key_archive_month)
            
            if not key_archive_day in archives:
                nodes.append(NavigationNode(
                    day,
                    reverse('blog_entry_archive_day', args=[year, month, day]),
                    key_archive_day,
                    key_archive_month,
                    attr=attributes
                ))
                archives.append(key_archive_day)
            
            nodes.append(NavigationNode(
                entry.title,
                entry.get_absolute_url(),
                entry.pk, key_archive_day
            ))
        return nodes


class CategoryMenu(CMSAttachMenu):
    """ Menu for the categories """
    name = _('Blog Category Menu')
    
    def get_nodes(self, request):
        """ Return menu's node for categories """
        nodes = []
        nodes.append(NavigationNode(
            _('Categories'),
            reverse('blog_category_list'),
            'categories'
        ))
        for category in Category.objects.all():
            nodes.append(NavigationNode(
                category.title,
                category.get_absolute_url(),
                category.pk,
                'categories'
            ))
        return nodes


class AuthorMenu(CMSAttachMenu):
    """ Menu for the authors """
    name = _('Blog Author Menu')
    
    def get_nodes(self, request):
        """ Return menu's node for authors """
        nodes = []
        nodes.append(NavigationNode(
            _('Authors'),
            reverse('blog_author_list'),
            'authors'
        ))
        for author in Author.published.all():
            nodes.append(NavigationNode(
                author.username,
                reverse('blog_author_detail', args=[author.username]),
                author.pk,
                'authors'
            ))
        return nodes


class TagMenu(CMSAttachMenu):
    """ Menu for the tags """
    name = _('Blog Tag Menu')
    
    def get_nodes(self, request):
        """ Return menu's node for tags """
        nodes = []
        nodes.append(NavigationNode(
            _('Tags'),
            reverse('blog_tag_list'),
            'tags'
        ))
        for tag in tags_published():
            nodes.append(NavigationNode(
                tag.name,
                reverse('blog_tag_detail', args=[tag.name]),
                tag.pk,
                'tags'
            ))
        return nodes


class EntryModifier(Modifier):
    """ Menu Modifier for entries, hide the MenuEntry in navigation, not in breadcrumbs """
    
    def modify(self, request, nodes, namespace, root_id, post_cut, breadcrumb):
        """ Modify nodes of a menu """
        if breadcrumb:
            return nodes
        for node in nodes:
            if node.attr.get('hidden'):
                nodes.remove(node)
        return nodes


menu_pool.register_menu(EntryMenu)
menu_pool.register_menu(CategoryMenu)
menu_pool.register_menu(AuthorMenu)
menu_pool.register_menu(TagMenu)
menu_pool.register_modifier(EntryModifier)

