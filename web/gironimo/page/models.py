# -*- coding: utf-8 -*-
from datetime import datetime
from django.db import models
from django.utils.translation import ugettext_lazy as _
from django.contrib.sites.models import Site


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


class Page(StatMixin, PageMixin, models.Model):
    TEMPLATE_BASE = 0,
    TEMPLATE_PAGE = 1
    TEMPLATE_CHOICES = (
        (TEMPLATE_BASE, 'base.html'),
        (TEMPLATE_PAGE, 'common/page/page_base.html'),
    )
    title = models.CharField(
        max_length=150, 
        verbose_name=_(u'Überschrift'), 
        help_text=_(u'Der Titel der Seite.')
    )
    url = models.CharField(
        verbose_name=_('URL'), 
        max_length=150, 
        db_index=True, 
        unique=True, 
        help_text=_(u'Zum Beispiel "/about/contact/".')
    )
    base_template = models.IntegerField(
        verbose_name=_('Basis Template'), 
        choices=TEMPLATE_CHOICES, 
        default=TEMPLATE_BASE, 
        help_text=_(u'Auswahl des Templates.')
    )
    content = models.TextField(
        verbose_name=_('Inhalt'), 
        help_text=_(u'Der Inhalt der Seite.')
    )
    
    def __unicode__(self):
        return u"%s -- %s" % (self.url, self.title)
    
    class Meta:
        ordering = ('url',)
        verbose_name = _(u'Statische Seite')
        verbose_name_plural = _(u'Statische Seiten')

