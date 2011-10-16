# we need a models.py file to make the django test runner determine the tests
from imagequery.settings import ALLOW_LAZY_FORMAT, LAZY_FORMAT_CLEANUP_TIME

if ALLOW_LAZY_FORMAT:
    from django.db import models
    from django.utils.functional import LazyObject
    from datetime import datetime, timedelta
    try:
        import cPickle as pickle
    except ImportError:
        import pickle
    
    
    def resolve_lazy(obj):
        if isinstance(obj, LazyObject):
            obj._setup()
            return obj._wrapped
        return obj
    
    
    class LazyFormatManager(models.Manager):
        def cleanup(self):
            cleanup_time = datetime.now() - timedelta(seconds=LAZY_FORMAT_CLEANUP_TIME)
            self.filter(created__lt=cleanup_time).delete()
    
    
    class LazyFormat(models.Model):
        format = models.CharField(max_length=100)
        query_data = models.TextField()
        created = models.DateTimeField(default=datetime.now)
        
        objects = LazyFormatManager()
        
        def _set_query(self, imagequery):
            from imagequery.query import ImageQuery
            if not isinstance(imagequery, ImageQuery):
                raise TypeError('this only works for ImageQuery')
            data = {
                'source': imagequery.source,
                'storage': resolve_lazy(imagequery.storage),
                'cache_storage': resolve_lazy(imagequery.cache_storage),
            }
            self.query_data = pickle.dumps(data)
        
        def _get_query(self):
            from imagequery.query import ImageQuery
            try:
                data = pickle.loads(str(self.query_data))
            except pickle.UnpicklingError:
                raise RuntimeError('could not load data')
            return ImageQuery(
                source=data['source'],
                storage=data['storage'],
                cache_storage=data['cache_storage'],
            )
        
        query = property(_get_query, _set_query)
        
        @models.permalink
        def get_absolute_url(self):
            return 'imagequery_generate_lazy', (), {'pk': self.pk}
        
        def generate_image_url(self):
            from imagequery import formats
            format = formats.get(self.format)
            return format(self.query).url()


