from django.conf.urls.defaults import *
from django.conf import settings


urlpatterns = patterns('gironimo.page.views',
    url(r'^(?P<url>.*)$', 'view_page', name='page'),
)

