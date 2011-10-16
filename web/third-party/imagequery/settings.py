from django.conf import settings
from django.core.files.storage import default_storage as _default_storage,\
    get_storage_class

CACHE_DIR = getattr(settings, 'IMAGEQUERY_CACHE_DIR', 'cache')
# can be used to define quality
# IMAGEQUERY_DEFAULT_OPTIONS = {'quality': 92}
DEFAULT_OPTIONS = getattr(settings, 'IMAGEQUERY_DEFAULT_OPTIONS', None)
# storage options
DEFAULT_STORAGE = getattr(settings, 'IMAGEQUERY_DEFAULT_STORAGE', None)
DEFAULT_CACHE_STORAGE = getattr(settings, 'IMAGEQUERY_DEFAULT_CACHE_STORAGE', None)

if DEFAULT_STORAGE:
    default_storage = get_storage_class(DEFAULT_STORAGE)
else:
    default_storage = _default_storage
if DEFAULT_CACHE_STORAGE:
    default_cache_storage = get_storage_class(DEFAULT_CACHE_STORAGE)
else:
    # we use the image storage if default_cache_storage is None
    default_cache_storage = None

ALLOW_LAZY_FORMAT = getattr(settings, 'IMAGEQUERY_ALLOW_LAZY_FORMAT', False)
LAZY_FORMAT_DEFAULT = getattr(settings, 'IMAGEQUERY_LAZY_FORMAT_DEFAULT', False)
LAZY_FORMAT_CLEANUP_TIME = getattr(settings, 'IMAGEQUERY_LAZY_FORMAT_CLEANUP_TIME', 86400) # one day


