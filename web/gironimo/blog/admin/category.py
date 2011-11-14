from django.contrib import admin
from django.core.urlresolvers import NoReverseMatch
from django.utils.translation import ugettext_lazy as _
from gironimo.blog.admin.forms import CategoryAdminForm


class CategoryAdmin(admin.ModelAdmin):
    """ Admin for Category Model """
    form = CategoryAdminForm
    fields = ('title', 'parent', 'description', 'slug', 'image',)
    list_displays = ('title', 'slug', 'get_tree_path', 'description',)
    prepopulated_fields = {'slug': ('title',)}
    search_fields = ('title', 'description',)
    list_filter = ('parent',)
    
    def __init__(self, model, admin_site):
        self.form.admin_site = admin_site
        super(CategoryAdmin, self).__init__(model, admin_site)
    
    def get_tree_path(self, category):
        """ Return the category's three path in HTML """
        try:
            return '<a href"%s" target="blank">/%s/</a>' % (category.get_absolute_url(), category.tree_path)
        except NoReverseMatch:
            return '/%s/' % category.tree_path
    get_tree_path.allow_tags = True
    get_tree_path.short_description = _('tree path')

