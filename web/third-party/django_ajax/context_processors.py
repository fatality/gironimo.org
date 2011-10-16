from django_ajax.config import ajax_context

def config(request):
	return {
		'ajax_config': ajax_context(request)
	}

