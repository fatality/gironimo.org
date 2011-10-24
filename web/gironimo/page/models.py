# -*- coding: utf-8 -*-
from django.db import models
from django.utils.translation import ugettext_lazy as _
from gironimo.utils.models import StatMixin, PageMixin


class Page(StatMixin, PageMixin, models.Model):
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
    content = models.TextField(
        verbose_name=_('Inhalt'), 
        help_text=_(u'Der Inhalt der Seite.')
    )
    
    def __unicode__(self):
        return self.title
    
    class Meta:
        ordering = ('url',)
        verbose_name = _(u'Statische Seite')
        verbose_name_plural = _(u'Statische Seiten')

