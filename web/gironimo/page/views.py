from django.http import *
from django.shortcuts import *
from django.template import Context, RequestContext
from gironimo.page.models import Page


def view_page(request, url=''):
    clean_url = url.split('.', 1)
    ext = clean_url[1]
    url = clean_url[0]
    try:
        page = Page.objects.get(url=u'/%s' % url)
    except Page.DoesNotExist:
        raise Http404()
    
    context = {
        'page': page,
    }
    
    return render_to_response(
        'common/page/page_base.html',
        context,
        context_instance=RequestContext(request)
    )

