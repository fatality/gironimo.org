from django.http import HttpResponseRedirect, Http404
from django.core.exceptions import ImproperlyConfigured

def generate_lazy(request, pk):
    try:
        from imagequery.models import LazyFormat
    except ImportError:
        raise ImproperlyConfigured('You have to set "IMAGEQUERY_ALLOW_LAZY_FORMAT = True" in order to use this view')
    LazyFormat.objects.cleanup()
    try:
        lazy_format = LazyFormat.objects.get(pk=pk)
    except LazyFormat.DoesNotExist:
        raise Http404()
    return HttpResponseRedirect(lazy_format.generate_image_url())

