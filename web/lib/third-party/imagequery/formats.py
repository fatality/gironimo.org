class FormatDoesNotExist(Exception):
    pass

_formats = {}

def register(name, format):
    _formats[name] = format

def get(name):
    try:
        return _formats[name]
    except KeyError:
        raise FormatDoesNotExist()


class Format(object):
    """
    A Format represents a fixed image manipulation
    
    Format's allow you to define an image manipulation based on ImageQuery. You
    can write your own Format's just by extending the Format class and implement
    the execute() method.
    
    Example:
    from imagequery import formats
    
    class MyShinyNewFormat(formats.Format):
        def execute(self, imagequery):
            # it's always good to privide a query name, so you can easily
            # empty the cache for the format
            # (the name will be created as a path in storage)
            return imagequery.operation1().operation2().query_name('shiny')
    
    After defining your format you can register it, this is mainly useful when
    using the format inside the templates:
    formats.register('shiny', MyShinyNewFormat)
    Inside the template (outputs the url):
    {% load imagequery_tags %}{% image_format "shiny" obj.image %}
    
    Note:
    When using Format's yourself (without the templatetags) you should be aware
    that you have to pass it an existing ImageQuery. This is needed to simplify
    the storage handling, as Format's don't need to care about storage.
    
    Format's mainly provide some methods to be used in your code, like returning
    the URL/path of the generated image.
    """
    
    # we don't allow passing filenames here, as this would need us to
    # repeat big parts of the storage-logic
    def __init__(self, imagequery):
        self._query = imagequery
    
    def execute(self, query):
        ''' needs to be filled by derivates '''
        return query
    
    def _execute(self):
        try:
            return self._executed
        except AttributeError:
            self._executed = self.execute(self._query)
            return self._executed
    
    def name(self):
        """ like Imagequery: return the name of the associated file """
        return self._execute().name()
    
    def path(self):
        """ like Imagequery: return the full path of the associated file """
        return self._execute().path()
    
    def url(self):
        """ like Imagequery: return the URL of the associated file """
        return self._execute().url()
    
    def height(self):
        return self._execute().height()
    
    def width(self):
        return self._execute().width()

