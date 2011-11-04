# -*- coding: utf-8 -*-
from datetime import datetime
from django.db import models
from django.utils.translation import ugettext_lazy as _


class StatMixin(models.Model):
    created = models.DateTimeField(
        editable=False, 
        default=datetime.now, 
        verbose_name=_('Erstellungsdatum')
    )
    modified = models.DateTimeField(
        editable=False, 
        default=datetime.now, 
        auto_now=True,
        verbose_name=_('Letzte Aktualisierung')
    )
    
    class Meta:
        abstract = True


class PageMixin(models.Model):
    html_title = models.CharField(
        verbose_name=_(u'Titel <title>'), 
        max_length=150, 
        blank=True, 
        null=True, 
        help_text=_(u'Optional. Wenn nicht angegeben wird Titel - Domainname ausgegeben')
    )
    html_description = models.CharField(
        verbose_name=_('Meta Beschreibung'), 
        max_length= 160, 
        blank=True,
        null=True,
        help_text=_(u'Optional. Wird als Meta Beschreibung für Suchmaschinen angezeigt.')
    )
    html_keywords = models.CharField(
        verbose_name=_('Meta Keywords'),
        max_length=120,
        blank=True,
        null=True,
        help_text=_(u'Optional. Für Suchmaschinen relevant.')
    )
    
    class Meta:
        abstract = True

