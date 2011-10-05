import os
import weakref
try:
    from PIL import Image
    from PIL import ImageFile
    from PIL import ImageEnhance
    from PIL import ImageDraw
except ImportError:
    import Image
    import ImageFile
    import ImageEnhance
    import ImageDraw
from django.conf import settings
from django.utils.encoding import smart_str
from django.core.files.base import File, ContentFile
from django.db.models.fields.files import FieldFile
from imagequery import operations
from imagequery.settings import CACHE_DIR, DEFAULT_OPTIONS, default_storage,\
    default_cache_storage
from imagequery.utils import get_image_object, get_font_object, get_coords

# stores rendered images
# keys are hashes of image operations
_IMAGE_REGISTRY = weakref.WeakKeyDictionary()

def _set_image_registry(item, image):
    _IMAGE_REGISTRY[item] = image
def _get_image_registry(item):
    return _IMAGE_REGISTRY.get(item, None)


class QueryItem(object):
    """
    An ImageQuery consists of multiple QueryItem's
    
    Each QueryItem might have an associated Operation (like rescaling the image)
    """
    
    def __init__(self, operation=None):
        self._previous = None
        self._evaluated_image = None
        self._name = None
        self._format = None
        self.operation = operation

    def _get_previous(self):
        return self._previous
    def _set_previous(self, athor):
        prev = self._previous
        athor._previous = prev
        self._previous = athor
    previous = property(_get_previous, _set_previous)
    
    def __iter__(self):
        items = [self]
        item = self._previous
        while not item is None:
            items.append(item)
            item = item._previous
        return reversed(items)
    
    def __unicode__(self):
        return u', '.join([unicode(x.operation) for x in self])
    
    def execute(self, image):
        evaluated_image = _get_image_registry(self)
        if evaluated_image is None:
            if self._previous is not None:
                image = self._previous.execute(image)
            if self.operation is not None:
                image = self.operation.execute(image, self)
            evaluated_image = image
            _set_image_registry(self, evaluated_image)
        return evaluated_image
    
    def get_attrs(self):
        attrs = {}
        if self._previous is not None:
            attrs.update(self._previous.get_attrs())
        if self.operation is not None:
            attrs.update(self.operation.attrs)
        return attrs
    
    def format(self, value=None):
        if value:
            self._format = value
            return value
        item = self
        while item:
            if item._format:
                return item._format
            item = item._previous
        return None
    
    def name(self, value=None):
        import hashlib
        if value:
            self._name = value
            return value
        if self._name:
            return self._name
        val = hashlib.sha1()
        altered = False
        item = self
        while item:
            if item._name: # stop on first named operation
                val.update(item._name)
                altered = True
                break
            if item.operation:
                val.update(unicode(item))
                altered = True
            item = item._previous
        if altered:
            return val.hexdigest()
        else:
            return None
    
    def get_first(self):
        first = self
        while first._previous is not None:
            first = first._previous
        return first
    
    def has_operations(self):
        if self.operation:
            return True
        if self._previous:
            return self._previous.has_operations()
        return False


class RawImageQuery(object):
    """ Base class for raw handling of images, needs an loaded PIL image """
    def __init__(self, image, source=None, storage=default_storage, cache_storage=None):
        self.image = get_image_object(image, storage)
        self.source = smart_str(source)
        self.storage = storage
        if cache_storage is None:
            if default_cache_storage is None:
                cache_storage = storage
            else:
                cache_storage = default_cache_storage
        self.cache_storage = cache_storage
        self.query = QueryItem()
    
    def _basename(self):
        if self.source:
            return os.path.basename(self.source)
        else:
            # the image was not loaded from source. create some name
            import hashlib
            md5 = hashlib.md5()
            md5.update(self.image.tostring())
            return '%s.png' % md5.hexdigest()
    
    def _format_extension(self, format):
        try:
            return self._reverse_extensions.get(format, '')
        except AttributeError:
            if not Image.EXTENSION:
                Image.init()
            self._reverse_extensions = {}
            for ext, _format in Image.EXTENSION.iteritems():
                self._reverse_extensions[_format] = ext
            # some defaults for formats with multiple extensions
            self._reverse_extensions.update({
                'JPEG': '.jpg',
                'MPEG': '.mpeg',
                'TIFF': '.tiff',
            })
            return self._reverse_extensions.get(format, '')
    
    def _name(self):
        if self.query.has_operations():
            hashval = self.query.name()
            format = self.query.format()
            # TODO: Support windows?
            # TODO: Remove support for absolute path?
            if not self.source or self.source.startswith('/'):
                name = self._basename()
            else:
                name = self.source
            if format:
                ext = self._format_extension(format)
                name = os.path.splitext(name)[0] + ext
            return os.path.join(CACHE_DIR, hashval, name)
        else:
            return self.source

    def _source(self):
        if self.source:
            return self.storage.path(self.source)

    def _path(self):
        if self.source:
            return self.cache_storage.path(self._name())

    def _url(self):
        if self.query.has_operations():
            return self.cache_storage.url(self._name())
        else:
            return self.storage.url(self._name())

    def _exists(self):
        if self.source and \
            self.cache_storage.exists(self._path()):
                # TODO: Really support local paths this way?
                try:
                    source_path = self.storage.path(self.source)
                    cache_path = self.cache_storage.path(self._path())
                    if os.path.exists(source_path) and \
                        os.path.exists(cache_path) and \
                        os.path.getmtime(source_path) > \
                        os.path.getmtime(cache_path):
                            return False
                except NotImplementedError:
                    pass
                return True
        return False

    def _apply_operations(self, image):
        image = self.query.execute(image)
        return image

    def _create_raw(self, allow_reopen=True):
        if allow_reopen and self._exists(): # Load existing image if possible
            return Image.open(self.cache_storage.open(self._name(), 'rb'))
        return self._apply_operations(self.image)
    
    def _convert_image_mode(self, image, format):
        # TODO: Run this again with all available modes
        # TODO: Find out how to get all available modes ;-)
        # >>> import Image
        # >>> Image.init()
        # >>> MODES = ('RGBA', 'RGB', 'CMYK', 'P', '1')
        # >>> FOMATS = Image.EXTENSION.values()
        # >>> i = Image.open('/some/image')
        # >>> for f in FORMATS:
        # ...     s = []
        # ...     for m in MODES:
        # ...             try:
        # ...                     i.convert(m).save('foo', f)
        # ...                     s.append(m)
        # ...             except:
        # ...                     pass
        # ...     print "'" + f + "': ('" + "', '".join(s) + "'),"
        # ... 
        MODES = {
            'JPEG': ('RGBA', 'RGB', 'CMYK', '1'),
            'PCX': ('RGB', 'P', '1'),
            'EPS': ('RGB', 'CMYK'),
            'TIFF': ('RGBA', 'RGB', 'CMYK', 'P', '1'),
            'GIF': ('RGBA', 'RGB', 'CMYK', 'P', '1'),
            'PALM': ('P', '1'),
            'PPM': ('RGBA', 'RGB', '1'),
            'EPS': ('RGB', 'CMYK'),
            'BMP': ('RGB', 'P', '1'),
            'PPM': ('RGBA', 'RGB', '1'),
            'PNG': ('RGBA', 'RGB', 'P', '1'),
            'MSP': ('1'),
            'IM': ('RGBA', 'RGB', 'CMYK', 'P', '1'),
            'TGA': ('RGBA', 'RGB', 'P', '1'),
            'XBM': ('1'),
            'PDF': ('RGB', 'CMYK', 'P', '1'),
            'TIFF': ('RGBA', 'RGB', 'CMYK', 'P', '1'),
        }
        if format and format in MODES:
            if image.mode not in MODES[format]:
                # convert to first mode in list, should be mode with most
                # features
                image = image.convert(MODES[format][0])
        return image
    
    def _create(self, name=None, **options):
        '''
        Recreate image. Does not check whether the image already exists.
        '''
        if self.query:
            if name is None:
                name = self._path()
            name = smart_str(name)
            image = self._create_raw(allow_reopen=False)
            format = self.query.format()
            if image.format:
                format = image.format
            elif not format:
                format = self.image.format
            if not format:
                if not Image.EXTENSION:
                    Image.init()
                format = Image.EXTENSION[os.path.splitext(name)[1]]
            if not self.cache_storage.exists(name):
                self.cache_storage.save(name, ContentFile(''))
            if DEFAULT_OPTIONS:
                save_options = DEFAULT_OPTIONS.copy()
            else:
                save_options = {}
            save_options.update(options)
            # options may raise errors
            # TODO: Check this
            image = self._convert_image_mode(image, format)
            try:
                image.save(self.cache_storage.open(name, 'wb'), format, **save_options)
            except TypeError:
                image.save(self.cache_storage.open(name, 'wb'), format)

    def _clone(self):
        import copy
        # clone = RawImageQuery(
            # self.image,
            # self.source,
            # self.storage,
            # self.cache_storage,
        # )
        clone = copy.copy(self)
        clone.query = copy.copy(self.query)
        return clone

    def _evaluate(self):
        if not self._exists():
            self._create()
    
    def _append(self, operation):
        query = QueryItem(operation)
        query._previous = self.query
        self.query = query
        return self

    def __unicode__(self):
        return self.url()

    def __repr__(self):
        return '<ImageQuery %s>' % self._name()

    ########################################
    # Query methods ########################
    ########################################

    def append(self, op):
        q = self._clone()
        q = q._append(op)
        return q

    def blank(self,x=None,y=None,color=None):
        return self.append(operations.Blank(x,y,color))

    def paste(self, image, x=0, y=0, storage=None):
        '''
        Pastes the given image above the current one.
        '''
        if storage is None:
            storage = self.storage
        return self.append(operations.Paste(image,x,y,storage))

    def background(self, image, x=0, y=0, storage=None):
        '''
        Same as paste but puts the given image behind the current one.
        '''
        if storage is None:
            storage = self.storage
        return self.append(operations.Background(image,x,y,storage))

    def blend(self, image, alpha=0.5, storage=None):
        if storage is None:
            storage = self.storage
        return self.append(operations.Blend(image,alpha,storage))

    def resize(self, x=None, y=None, filter=Image.ANTIALIAS):
        return self.append(operations.Resize(x,y,filter))

    def scale(self, x, y, filter=Image.ANTIALIAS):
        return self.append(operations.Scale(x,y,filter))

    def crop(self, x, y, w, h):
        return self.append(operations.Crop(x,y,w,h))

    def fit(self, x, y, centering=(0.5,0.5), method=Image.ANTIALIAS):
        return self.append(operations.Fit(x,y,centering,method))

    def enhance(self, enhancer, factor):
        return self.append(operations.Enhance(enhancer, factor))

    def sharpness(self, amount=2.0):
        '''
        amount: 
            < 1 makes the image blur
            1.0 returns the original image
            > 1 increases the sharpness of the image
        '''
        return self.enhance(ImageEnhance.Sharpness, amount)

    def blur(self, amount=1):
        #return self.sharpness(1-(amount-1))
        return self.append(operations.Blur(amount))

    def filter(self, image_filter):
        return self.append(operations.Filter(image_filter))
    
    def truecolor(self):
        return self.append(operations.Convert('RGBA'))

    def invert(self, keep_alpha=True):
        return self.append(operations.Invert(keep_alpha))

    def flip(self):
        return self.append(operations.Flip())

    def mirror(self):
        return self.append(operations.Mirror())

    def grayscale(self):
        return self.append(operations.Grayscale())

    def alpha(self):
        return self.append(operations.GetChannel('alpha'))

    def applyalpha(self, alphamap):
        return self.append(operations.ApplyAlpha(alphamap))

    def composite(self, image, mask, storage=None):
        if storage is None:
            storage = self.storage
        return self.append(operations.Composite(image, mask, storage))

    def offset(self, x, y):
        return self.append(operations.Offset(x, y))

    def padding(self, left, top=None, right=None, bottom=None, color=None):
        return self.append(operations.Padding(left, top, right, bottom, color))

    def opacity(self, opacity):
        return self.append(operations.Opacity(opacity))

    def clip(self, start=None, end=None):
        return self.append(operations.Clip(start, end))

    def shadow(self, color):
        #mask = self.alpha().invert()
        #return self.blank(color=None).composite(self.blank(color=color), mask)
        return self.blank(color=color).applyalpha(self)

    def makeshadow(self, x, y, color, opacity=1, blur=1):
        shadow = self.shadow(color).opacity(opacity).blur(blur)
        return self.background(shadow, x, y)

    def save(self, name=None, storage=None, **options):
        # create a clone for saving (thus we might change its state)
        q = self._clone()
        if storage:
            q.cache_storage = storage
        q._create(name, **options)
        # return new clone
        return self._clone()

    def query_name(self, value):
        q = self._clone()
        q = q._append(None)
        q.query.name(value)
        return q

    def image_format(self, value):
        value = value.upper()
        if not Image.EXTENSION:
            Image.init()
        if not value in Image.EXTENSION.values():
            raise RuntimeError('invalid format')
        q = self._clone()
        q = q._append(None)
        q.query.format(value)
        return q

    # text operations

    def text(self, text, x, y, font, size=None, fill=None):
        return self.append(operations.Text(text, x, y, font, size, fill))

    @staticmethod
    def textbox(text, font, size=None):
        font = get_font_object(font, size)
        text = smart_str(text)
        return font.getsize(text)

    @staticmethod
    def img_textbox(text, font, size=None):
        font = get_font_object(font, size)
        text = smart_str(text)
        try:
            imgsize, offset = font.font.getsize(text)
            if isinstance(imgsize, int) and isinstance(offset, int):
                imgsize = (imgsize, offset)
                offset = (0, 0)
        except AttributeError:
            imgsize = font.getsize(text)
            offset = (0, 0)
        return (
            imgsize[0] - offset[0],
            imgsize[1] - offset[1],
        ), (
            -offset[0],
            -offset[1],
        )

    @staticmethod
    def textimg(text, font, size=None, fill=None, padding=0, mode='RGBA', storage=default_storage):
        font = get_font_object(font, size)
        text = smart_str(text)
        imgsize, offset = ImageQuery.img_textbox(text, font, size)
        bg = [0,0,0,0]
        # Workaround: Image perhaps is converted to RGB before pasting,
        # black background draws dark outline around text
        if fill:
            for i in xrange(0, min(len(fill), 3)):
                bg[i] = fill[i]
        if padding:
            imgsize = (imgsize[0] + padding * 2, imgsize[1] + padding * 2)
            offset = (offset[0] + padding, offset[1] + padding)
        fontimage = Image.new(mode, imgsize, tuple(bg))
        draw = ImageDraw.Draw(fontimage)
        # HACK
        if Image.VERSION == '1.1.5' and isinstance(text, unicode):
            text = text.encode('utf-8')
        draw.text(offset, text, font=font, fill=fill)
        return RawImageQuery(fontimage, storage=storage)
    
    # methods which does not return a new ImageQuery instance
    
    def mimetype(self):
        format = self.raw().format
        try:
            if not Image.MIME:
                Image.init()
            return Image.MIME[format]
        except KeyError:
            return None

    def width(self):
        return self.raw().size[0]
    x = width

    def height(self):
        return self.raw().size[1]
    y = height

    def size(self):
        return self.raw().size

    def raw(self):
        return self._create_raw()

    def name(self):
        self._evaluate()
        return self._name()

    def path(self):
        self._evaluate()
        return self._path()

    def url(self):
        self._evaluate()
        return self._url()


class NewImageQuery(RawImageQuery):
    """ Creates an new (blank) image for you """
    
    def __init__(self, x, y, color=(0,0,0,0), storage=default_storage, cache_storage=None):
        image = Image.new('RGBA', (x, y), color)
        super(NewImageQuery, self).__init__(image, storage=storage, cache_storage=cache_storage)


class ImageQuery(RawImageQuery):
    """ Write your image operations like you would use QuerySet
    
    With ImageQuery you are able to write image manipulations without needing
    to learn some low-level API for the most use cases. It allows you to:
     * simple manipulation like rescaling
     * combining images
     * handling text (note: fonts must be available locally)
     * even more like creating drop shadows (using the alpha mask)
    
    ImageQuery basicly provides an API similar to the well known QuerySet API,
    which means:
     * Most methods just return another ImageQuery
     * Every bit of your image manipulation chain can be used/saved
     * Image manipulations are lazy, they are only evaluated when needed
    
    ImageQuery in addition automaticly stores the results in an cache, so you
    don't need to worry about recreating the same image over and over again.
    It is possible to use a different storage for caching, so you could - for
    example - put all your cached images on an different server while keeping
    the original files locally.
    """
    
    def __init__(self, source, storage=default_storage, cache_storage=None):
        query = None
        self.storage = storage
        if cache_storage is None:
            if default_cache_storage is None:
                cache_storage = storage
            else:
                cache_storage = default_cache_storage
        self.cache_storage = cache_storage
        if isinstance(source, File):
            self.source = source.name
            source.open('rb')
            self.fh = source
            if isinstance(source, FieldFile):
                # we use the field storage, regardless what the constructor
                # get as param, just to be safe
                self.storage = source.storage
        else:
            # assume that image is a filename
            self.source = smart_str(source)
            self.fh = storage.open(self.source, 'rb')
        self.query = QueryItem()
    
    def _get_image(self):
        try:
            return self._image
        except AttributeError:
            self.fh.open('rb') # reset file access
            self._image = Image.open(self.fh)
            return self._image
    def _set_image(self, image):
        self._image = image
    image = property(_get_image, _set_image)

