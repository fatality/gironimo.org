# -*- coding: utf-8 -*-
from django.db import models
from django.contrib.auth.models import User
from django.utils.translation import ugettext_lazy as _
from django.db.models.signals import post_save
from django_model_utils.models import StatMixin


class UserProfile(StatMixin, models.Model):
    user = models.OneToOneField(
        User, 
        related_name='account', 
        verbose_name=_('Benutzer'), 
        help_text=_(u'Benutzer für wen das Profil erstellt wird.')
    )
    about = models.TextField(
        blank=True, 
        null=True, 
        verbose_name=_('Beschreibung'), 
        help_text=_(u'Optional. Kurze Beschreibung über sich selbst.')
    )
    avatar = models.ImageField(
        blank=True, 
        null=True, 
        upload_to='accounts/avatar', 
        verbose_name=_('Avatar'), 
        help_text=_(u'Optional. Wenn angegeben wird dies bei Kommentaren und auf der Profilseite verwendet, wenn nicht wird E-mail Adresse mit Gravatar verglichen.')
    )
    
    def __unicode__(self):
        return self.user.username
    
    class Meta:
        verbose_name = _(u'Account')
        verbose_name_plural = _(u'Accounts')


def create_user_profile(sender, instance, created, **kwargs):
    if created:
        UserProfile.objects.create(user=instance)

post_save.connect(create_user_profile, sender=User)

