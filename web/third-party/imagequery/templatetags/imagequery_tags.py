from django import template
from imagequery import ImageQuery, formats
from imagequery.utils import get_imagequery
from django.db.models.fields.files import ImageFieldFile
from django.utils.encoding import smart_unicode
from imagequery.settings import ALLOW_LAZY_FORMAT, LAZY_FORMAT_DEFAULT

register = template.Library()

def parse_value(value):
    try:
        if int(value) == float(value):
            return int(value)
        else:
            return float(value)
    except (TypeError, ValueError):
        return value

def parse_attrs(attrs):
    args, kwargs = [], {}
    if attrs:
        for attr in attrs.split(','):
            try:
                key, value = attr.split('=', 1)
                kwargs[key] = parse_value(value)
            except ValueError:
                args.append(parse_value(attr))
    return args, kwargs

def imagequerify(func):
    from functools import wraps
    # Template-filters do not work without funcs that _need_ args
    # because of some inspect-magic (not) working here
    # TODO: Find some way to support optional "attr"
    @wraps(func)
    def newfunc(image, attr):
        try:
            image = get_imagequery(image)
        except IOError:
            return ''
        return func(image, attr)
    return newfunc

def imagequerify_filter(value):
    return get_imagequery(value)
register.filter('imagequerify', imagequerify_filter)

def imagequery_filter(method_name, filter_name=None):
    if not filter_name:
        filter_name = method_name
    def filter(image, attr=None):
        args, kwargs = parse_attrs(attr)
        return getattr(image, method_name)(*args, **kwargs)
    filter = imagequerify(filter)
    filter = register.filter(filter_name, filter)
    return filter

# register all (/most of) the ImageQuery methods as filters
crop = imagequery_filter('crop')
fit = imagequery_filter('fit')
resize = imagequery_filter('resize')
scale = imagequery_filter('scale')
sharpness = imagequery_filter('sharpness')
blur = imagequery_filter('blur')
truecolor = imagequery_filter('truecolor')
invert = imagequery_filter('invert')
flip = imagequery_filter('flip')
mirror = imagequery_filter('mirror')
grayscale = imagequery_filter('grayscale')
offset = imagequery_filter('offset')
padding = imagequery_filter('padding')
opacity = imagequery_filter('opacity')
shadow = imagequery_filter('shadow')
makeshadow = imagequery_filter('makeshadow')
mimetype = imagequery_filter('mimetype')
width = imagequery_filter('width')
height = imagequery_filter('height')
x = imagequery_filter('x')
y = imagequery_filter('y')
size = imagequery_filter('size')
url = imagequery_filter('url')
query_name = imagequery_filter('query_name')


class ImageFormatNode(template.Node):
    def __init__(self, format, image, name, allow_lazy=None):
        self.format = format
        self.image = image
        self.name = name
        if allow_lazy is None:
            self.allow_lazy = LAZY_FORMAT_DEFAULT and ALLOW_LAZY_FORMAT
        else:
            self.allow_lazy = allow_lazy and ALLOW_LAZY_FORMAT

    def render(self, context):
        try:
            formatname = self.format.resolve(context)
            image = self.image.resolve(context)
        except template.VariableDoesNotExist:
            return ''
        try:
            format_cls = formats.get(formatname)
        except formats.FormatDoesNotExist:
            return ''
        try:
            imagequery = get_imagequery(image)
        except IOError: # handle missing files
            return ''
        format = format_cls(imagequery)
        if self.allow_lazy and not self.name and not format._execute()._exists():
            from imagequery.models import LazyFormat
            lazy_format = LazyFormat(format=formatname)
            lazy_format.query = imagequery
            lazy_format.save()
            return lazy_format.get_absolute_url()
        if self.name:
            context[self.name] = format
            return ''
        else:
            try:
                return format.url()
            except:
                return ''


@register.tag
def image_format(parser, token):
    """
    Allows you to use predefined Format's for changing your images according to
    predefined sets of operations. Format's must be registered for using them
    here (using imagequery.formats.register("name", MyFormat).
    
    You can get the resulting Format instance as a context variable.
    
    Examples:
    {% image_format "some_format" foo.image %}
    {% image_format "some_format" foo.image as var %}
    {% image_format "some_format" foo.image lazy %}
    {% image_format "some_format" foo.image nolazy %}
    
    This tag does not support storage by design. If you want to use different
    storage engines here you have to:
     * pass in an ImageQuery instance
     * write your own template filter that constructs an ImageQuery instance
       (including storage settings)
     * pass in an FieldImage
    """
    bits = token.split_contents()
    tag_name = bits[0]
    values = bits[1:]
    if len(values) not in (2, 3, 4):
        raise template.TemplateSyntaxError(u'%r tag needs two, three or four parameters.' % tag_name)
    format = parser.compile_filter(values[0])
    image = parser.compile_filter(values[1])
    name = None
    allow_lazy = None
    i = 2
    while i < len(values):
        if values[i] == 'as':
            name = values[i + 1]
            i = i + 2
        elif values[i] == 'lazy':
            allow_lazy = True
            i = i + 1
        elif values[i] == 'nolazy':
            allow_lazy = False
            i = i + 2
        else:
            raise template.TemplateSyntaxError(u'%r tag: parameter must be "as" or "lazy"/"nolazy"' % tag_name)
    return ImageFormatNode(format, image, name, allow_lazy)

