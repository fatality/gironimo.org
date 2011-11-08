""" Mollom spam checker backend """
from django.conf import settings
from django.utils.encoding import smart_str
from django.core.exceptions import ImproperlyConfigured


try:
    from pymollom import MollomAPI
    from pymollom import MollomFault
except ImportError:
    raise ImproperlyConfigured('pymollom module is not available')

if not getattr(settings, 'MOLLOM_PUBLIC_KEY', ''):
    raise ImproperlyConfigured('You have to set a MOLLOM_PUBLIC_KEY setting')

if not getattr(settings, 'MOLLOM_PRIVATE_KEY', ''):
    raise ImproperlyConfigured('You have to set a MOLLOM_PRIVATE_KEY setting')

MOLLOM_PUBLIC_KEY = settings.MOLLOM_PUBLIC_KEY
MOLLOM_PRIVATE_KEY = settings.MOLLOM_PRIVATE_KEY


def backend(comment, content_object, request):
    """ Mollom spam checker backend """
    mollom_api = MollomAPI(
        publicKey=MOLLOM_PUBLIC_KEY,
        privateKey=MOLLOM_PRIVATE_KEY
    )
    if not mollom_api.verifyKey():
        raise MollomFault('Your MOLLOM credentials are invalid.')
    
    mollom_data = {
        'authorIP': request.META.get('REMOTE_ADDR', ''),
        'authorName': smart_str(comment.userinfo.get('name', '')),
        'authorMail': smart_str(comment.userinfo.get('email', '')),
        'authorURL': smart_str(comment.userinfo.get('url', ''))
    }
    
    cc = mollom_api.checkContent(
        postTitle=smart_str(content_object.title),
        postBody=smart_str(comment.comment),
        **mollom_data
    )
    
    if cc['spam'] == 2:
        return True
    
    return False
