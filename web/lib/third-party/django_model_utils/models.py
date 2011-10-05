from datetime import datetime
from django.db import models
from django.utils.translation import ugettext_lazy as _

from django_model_utils.signals import auto_now_on_save


class StatMixin(models.Model):
    created = models.DateTimeField(editable=False, default=datetime.now)
    modified = models.DateTimeField(editable=False, default=datetime.now, auto_now=True)
    
    class Meta:
        abstract = True

#auto_now_on_save(StatMixin, 'modified')


class PageMixin(models.Model):
    html_title = models.CharField(verbose_name=_(u'Titel (<title>)'), max_length=150, blank=True, null=True)
    html_description = models.CharField(verbose_name=_(u'Meta Beschreibung'), max_length=200, blank=True, null=True)
    html_keywords = models.CharField(verbose_name=_(u'Meta Keywords'), max_length=120, blank=True, null=True)
    
    class Meta:
        abstract = True


class StatusMixin(models.Model):
    STATUS_DRAFT = 0
    STATUS_TEST = 10
    STATUS_LIVE = 20
    STATUS_FEATURED = 30
    STATUS_ARCHIVE = -10
    STATUS_DELETED = -20
    STATUS_CHOICES = (
        (STATUS_DRAFT, _('Draft')),
        (STATUS_TEST, _('Test')),
        (STATUS_LIVE, _('Live')),
        (STATUS_FEATURED, _('Featured')),
        (STATUS_ARCHIVE, _('Archive')),
        (STATUS_DELETED, _('Deleted')),
    )
    status = models.IntegerField(verbose_name=_(u'Status'), choices=STATUS_CHOICES, default=STATUS_DRAFT)
    
    class Meta:
        abstract = True


class PublicDateMixin(models.Model):
    public_start = models.DateTimeField(verbose_name=_(u'Start date'), null=True, blank=True, default=datetime.now)
    public_end = models.DateTimeField(verbose_name=_(u'End date'), null=True, blank=True)
    
    class Meta:
        abstract = True


# http://djangosnippets.org/snippets/1271/
class ClonableMixin(object):
    ''' Adds a clone() method to models
    
    Cloning is done by first copying the object using copy.copy. After this
    the primary key (pk) is removed, passed attributes are set
    (obj.clone(**kwargs)) and the object is saved to the database.
    
    Now all m2m relations are cloned, including reverse m2m relations.
    m2m relations using a intermediate model (through) will be cloned, if
    (and only if) the intermediate model itself is cloneable.
    
    The cloned object will be returned.
    
    clone() uses some helper methods, which may be extended/replaced in
    child classes. These include:
     * _clone_copy(): create the copy
     * _clone_prepare(): prepare the obj, so it can be saved
       (this method may be extended to set some attributes, for example
        some created timestamp, see StatClonableMixin)
     * _clone_attrs(): set all attributes passed to clone()
     * _clone_save(): saves the copy
     * _clone_copy_m2m(): clones all m2m relations
     * _clone_copy_reverse_m2m(): clones all reverse m2m relations
    '''
    
    def _clone_copy(self):
        import copy
        """Return an identical copy of the instance"""
        if not self.pk:
            raise ValueError('Instance must be saved before it can be cloned.')
        return copy.copy(self)
    
    def _clone_prepare(self, duplicate):
        # Setting pk to None tricks Django into thinking this is a new object.
        duplicate.pk = None
    
    def _clone_attrs(self, duplicate, **attrs):
        # We allow users to pass attrs to clone(), so set these
        for name in attrs:
            setattr(duplicate, name, attrs[name])
    
    def _clone_save(self, duplicate):
        duplicate.save(force_insert=True)
    
    def _clone_copy_m2m(self, duplicate):
        # copy.copy loses all ManyToMany relations.
        for field in self._meta.many_to_many:
            # handle m2m using through
            if field.rel.through and not field.rel.through._meta.auto_created:
                # through-model must be cloneable
                if hasattr(field.rel.through, 'clone'):
                    for m2m_obj in field.rel.through._default_manager.filter(**{field.m2m_field_name(): self}):
                        m2m_obj.clone(**{field.m2m_field_name(): duplicate})
                else:
                    continue # don't know how to clone these
            # normal m2m, this is easy
            else:
                objs = getattr(self, field.attname).all()
                setattr(duplicate, field.attname, objs)
    
    def _clone_copy_reverse_m2m(self, duplicate):
        for relation in self._meta.get_all_related_many_to_many_objects():
            # handle m2m using through
            if relation.field.rel.through and not relation.field.rel.through._meta.auto_created:
                # through-model must be cloneable
                if hasattr(relation.field.rel.through, 'clone'):
                    for m2m_obj in relation.field.rel.through._default_manager.filter(**{relation.field.m2m_reverse_field_name(): self}):
                        m2m_obj.clone(**{relation.field.m2m_reverse_field_name(): duplicate})
                else:
                    continue # don't know how to clone these
            # normal m2m, this is easy
            else:
                objs = getattr(self, relation.field.rel.related_name).all()
                setattr(duplicate, relation.field.rel.related_name, objs)
    
    def clone(self, **attrs):
        duplicate = self.prepare_clone(**attrs)
        duplicate.save()
        return duplicate
    
    def prepare_clone(self, **attrs):
        duplicate = self._clone_copy()
        self._clone_prepare(duplicate)
        self._clone_attrs(duplicate, **attrs)
        old_save = duplicate.save
        def save_duplicate():
            duplicate.save = old_save
            self._clone_save(duplicate)
            self._clone_copy_m2m(duplicate)
            self._clone_copy_reverse_m2m(duplicate)
        duplicate.save = save_duplicate
        return duplicate


class StatClonableMixin(ClonableMixin):
    def _clone_prepare(self, duplicate):
        super(StatClonableMixin, self)._clone_prepare(duplicate)
        duplicate.created = datetime.now()

