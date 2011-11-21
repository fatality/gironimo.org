from django.views.generic.simple import direct_to_template

from gironimo.blog.models import Entry
from gironimo.blog.models import Category


def sitemap(*ka, **kw):
    """ Wrapper around the direct to template generic view to force the update 
    of the extra context """
    kw['extra_context'] = {'entries': Entry.published.all(),
                           'categories': Category.tree.all()}
    return direct_to_template(*ka, **kw)

