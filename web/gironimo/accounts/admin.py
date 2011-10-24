from django.contrib import admin
from django.utils.translation import ugettext_lazy as _
from gironimo.accounts.models import UserProfile


class AccountAdmin(admin.ModelAdmin):
    fieldsets = [
        (None,               {'fields': ['user']}),
        (_('Benutzerinformationen'), {'fields': ['about', 'avatar', 'website',]}),
    ]
    list_display = ('user', 'created', 'website',)
    list_filter = ['created', 'modified',]
    search_fields = ['user', 'website']
    date_hierarchy = 'created'


admin.site.register(UserProfile, AccountAdmin)

