from django import forms
from django.utils.translation import ugettext_lazy as _
try:
    from cStringIO import StringIO
except ImportError:
    from StringIO import StringIO
from django.template import TemplateSyntaxError, InvalidTemplateLibrary, Template

from django_model_utils.fields import TemplateString

class TemplateStringTextarea(forms.Textarea):
    def __init__(self, attrs=None):
        if attrs is None:
            attrs = {}
        if 'class' in attrs:
            attrs['class'] = attrs['class'] + ' template-string'
        else:
            attrs['class'] = 'template-string'
        super(TemplateStringTextarea, self).__init__(attrs)


class TemplateStringField(forms.CharField):
    widget = TemplateStringTextarea
    
    def __init__(self, *args, **kwargs):
        self.taglibs = kwargs.setdefault('taglibs', ())
        del kwargs['taglibs']
        super(TemplateStringField, self).__init__(*args, **kwargs)

    def clean(self, value):
        value = super(TemplateStringField, self).clean(value)
        templatestring = TemplateString(value, self.taglibs)
        try:
            Template(templatestring.get_template_content())
        except TemplateSyntaxError, e:
            raise forms.ValidationError(e)
        except InvalidTemplateLibrary, e:
            raise forms.ValidationError(e)
        return value

