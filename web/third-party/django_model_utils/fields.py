# -*- coding: utf-8 -*-
from django.db import models
from django.utils.translation import ugettext_lazy as _
from django.utils.encoding import force_unicode
from django.conf import settings
from django.template import Template, Context
from django.utils.safestring import mark_safe

def south_field_triple(self):
    from south.modelsinspector import introspector
    field_class = self.__class__.__module__ + "." + self.__class__.__name__
    args, kwargs = introspector(self)
    return (field_class, args, kwargs)





# based on http://www.djangosnippets.org/snippets/513/

class SerializedObject(str):
    """A subclass of string so it can be told whether a string is
       a pickled object or not (if the object is an instance of this class
       then it must [well, should] be a pickled one)."""
    pass


class SerializedBaseField(models.Field):
    def to_python(self, value):
        if isinstance(value, SerializedObject):
            # If the value is a definite pickle; and an error is raised in de-pickling
            # it should be allowed to propogate.
            return self.serializer.loads(str(value))
        else:
            try:
                return self.serializer.loads(str(value))
            except:
                # If an error was raised, just return the plain value
                return value
    
    def get_db_prep_save(self, value, connection):
        if value is not None and not isinstance(value, SerializedObject):
            value = SerializedObject(self.serializer.dumps(value))
        return super(SerializedBaseField, self).get_db_prep_save(value, connection=connection)
    
    def get_internal_type(self): 
        return 'TextField'
    
    def get_db_prep_lookup(self, lookup_type, value, connection, prepared=False):
        if lookup_type == 'exact':
            value = self.get_db_prep_save(value)
            return super(SerializedBaseField, self).get_db_prep_lookup(lookup_type, value, connection=connection, prepared=prepared)
        elif lookup_type == 'in':
            value = [self.get_db_prep_save(v) for v in value]
            return super(SerializedBaseField, self).get_db_prep_lookup(lookup_type, value, connection=connection, prepared=prepared)
        else:
            raise TypeError('Lookup type %s is not supported.' % lookup_type)
    
    south_field_triple = south_field_triple


class PickledField(SerializedBaseField):
    __metaclass__ = models.SubfieldBase
    
    try:
        import cPickle as serializer
    except ImportError:
        import pickle as serializer


class JSONField(SerializedBaseField):
    __metaclass__ = models.SubfieldBase
    
    from django.utils import simplejson as serializer





TEMPLATESTRING_LIBS = getattr(settings, 'TEMPLATESTRING_LIBS', ())


class TemplateString(object):
    def __init__(self, content, taglibs=None, context_callback=None, parent_object=None):
        if content is None:
            self.content = None
        else:
            self.content = force_unicode(content)
        self.taglibs = taglibs or ()
        self.context_callback = context_callback
        self.parent_object = parent_object

    def get_template_content(self):
        if self.content is None:
            return u''
        content = force_unicode(self.content)
        taglibs = ' '.join(list(TEMPLATESTRING_LIBS) + list(self.taglibs))
        if taglibs:
            content = (
                '{% load ' + taglibs + ' %}' + content
            )
        return content

    def render(self, context=None, context_instance=None):
        if self.content is None:
            return u''
        if not context_instance:
            _context = Context()
        else:
            _context = context_instance
        if context:
            _context.update(context)
        if self.context_callback:
            _context.update(self.context_callback(self.parent_object))
        content = self.get_template_content()
        rendered_content = Template(content).render(_context)
        return mark_safe(rendered_content)

    def __nonzero__(self):
        return bool(self.content)
    def __unicode__(self):
        if self.content is None:
            return u''
        return self.content
    def __str__(self):
        return str(self.__unicode__())


class TemplateStringValue(object):
    def __init__(self, name, taglibs=None, context_callback=None):
        self.name = name
        self.taglibs = taglibs
        self.context_callback = context_callback

    def __get__(self, obj, type=None):
        if obj is None:
            raise AttributeError('Can only be accessed via an instance.')
        return TemplateString(
            getattr(obj, '_%s_value' % self.name),
            self.taglibs,
            self.context_callback,
            obj,
        )

    def __set__(self, obj, value):
        if obj is None:
            raise AttributeError('Can only be accessed via an instance.')
        setattr(obj, '_%s_value' % self.name, value)


class TemplateStringField(models.TextField):
    def __init__(self, *args, **kwargs):
        self.taglibs = kwargs.pop('taglibs', ())
        self.context_callback = kwargs.pop('context_callback', None)
        # %-operator translates string, so this _()-call will not be lazy any more, this breaks things heavily!!!
        help_text = _(u'You are allowed to use valid Django template code.') # Sie können die Templatetags verwenden die in den folgenden Tagbibliotheken definiert sind %(taglibs)s.')
        #if self.taglibs:
        #   help_text = help_text % {'taglibs': ', '.join(self.taglibs)}
        #else:
        #   help_text = help_text % {'taglibs': ''}
        kwargs.setdefault('help_text', help_text)
        super(TemplateStringField, self).__init__(*args, **kwargs)

    def contribute_to_class(self, cls, name):
        super(TemplateStringField, self).contribute_to_class(cls, name)
        setattr(cls, name, TemplateStringValue(name, self.taglibs, self.context_callback))

    def get_db_prep_value(self, value, connection, prepared=False):
        if isinstance(value, TemplateString):
            value = value.content
        return super(TemplateStringField, self).get_db_prep_value(value, connection=connection, prepared=prepared)

    def formfield(self, **kwargs):
        from django_model_utils.forms import TemplateStringField as TemplateStringFormField
        defaults = {
            'form_class': TemplateStringFormField,
            'taglibs': self.taglibs,
        }
        defaults.update(kwargs)
        return super(TemplateStringField, self).formfield(**defaults)

    south_field_triple = south_field_triple

