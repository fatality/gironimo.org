import warnings
from django.utils.importlib import import_module
from django.core.exceptions import ImproperlyConfigured
from gironimo.blog.config import URL_SHORTENER_BACKEND
from gironimo.blog.url_shortener.backends.default import backend as default_backend


def get_url_shortener():
    """ Return the selected url shortener backend """
    try:
        backend_module = import_module(URL_SHORTENER_BACKEND)
        backend = getattr(backend_module, 'backend')
    except (ImportError, AttributeError):
        warnings.warn('%s backend cannot be imported' % URL_SHORTENER_BACKEND, RuntimeWarning)
        backend = default_backend
    except ImproperlyConfigured, e:
        warnings.warn(str(e), RuntimeWarning)
        backend = default_backend
    
    return backend

