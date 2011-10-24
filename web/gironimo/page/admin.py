from django.contrib import admin
from django import forms
from django.utils.translation import ugettext_lazy as _
from gironimo.page.models import Page
from tinymce.widgets import TinyMCE


class PageAdminForm(forms.ModelForm):
    content = forms.CharField(
        widget=TinyMCE(attrs={'cols': 80, 'rows': 30})
    )
    
    class Meta:
        model = Page


class PageAdmin(admin.ModelAdmin):
    form = PageAdminForm
    
    fieldsets = [
        (None, {
            'classes': ['wide', 'extrapretty'],
            'fields': ['title', 'url', 'base_template', 'content',],
        }),
        (_('HTML Metainformationen'), {
                'classes': ['collapse', 'extrapretty'],
                'fields': ['html_title', 'html_description', 'html_keywords',],
        }),
    ]
    
    list_display = ('title', 'created', 'modified',)
    list_filter = ['created', 'modified', 'base_template',]
    search_fields = ['title',]
    date_hierarchy = 'created'


admin.site.register(Page, PageAdmin)
