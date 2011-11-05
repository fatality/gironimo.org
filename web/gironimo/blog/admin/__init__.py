""" Admin of Blog """
from django.contrib import admin
from gironimo.blog.models import Entry, Category
from gironimo.blog.admin.entry import EntryAdmin
from gironimo.blog.admin.category import CategoryAdmin


admin.site.register(Entry, EntryAdmin)
admin.site.register(Category, CategoryAdmin)

