from django.contrib.sites.models import Site


def gironimo(request):
    site_name = Site.objects.get_current()
    return {'site_name': site_name}

