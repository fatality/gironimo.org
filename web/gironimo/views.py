from django.template import RequestContext
from django.shortcuts import *


def index(request):
    return render_to_response('index.html', {
    }, context_instance=RequestContext(request))

