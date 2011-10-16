try:
    from PIL import Image
    from PIL import ImageFile
    from PIL import ImageFont
except ImportError:
    import Image
    import ImageFile
    import ImageFont
from django.core.cache import cache
from django.core.files.base import File
from imagequery.settings import default_storage

def get_imagequery(value):
    from imagequery import ImageQuery # late import to avoid circular import
    if isinstance(value, ImageQuery):
        return value
    # value must be the path to an image or an image field (model attr)
    return ImageQuery(value)

def _get_image_object(value, storage=default_storage):
    from imagequery import ImageQuery # late import to avoid circular import
    if isinstance(value, (ImageFile.ImageFile, Image.Image)):
        return value
    if isinstance(value, ImageQuery):
        return value.raw()
    if isinstance(value, File):
        value.open('rb')
        return Image.open(value)
    return Image.open(storage.open(value, 'rb'))

def get_image_object(value, storage=default_storage):
    image = _get_image_object(value, storage)
    # PIL Workaround:
    # We avoid lazy loading here as ImageQuery already is lazy enough.
    # In addition PIL does not always check if image is loaded, so not
    # loading it here might break some code (for example ImageQuery.paste)
    if not getattr(image, 'im', True):
        try:
            image.load()
        except AttributeError:
            pass
    return image

def get_font_object(value, size=None):
    if isinstance(value, (ImageFont.ImageFont, ImageFont.FreeTypeFont)):
        return value
    if value[-4:].lower() in ('.ttf', '.otf'):
        return ImageFont.truetype(value, size)
    return ImageFont.load(value)

def get_coords(first, second, align):
    if align in ('left', 'top'):
        return 0
    if align in ('center', 'middle'):
        return (first / 2) - (second / 2)
    if align in ('right', 'bottom'):
        return first - second
    return align

# TODO: Keep this?
# TODO: Add storage support
# TODO: Move to equal_height.py?
def equal_height(images, maxwidth=None):
    """ Allows you to pass in multiple images, which all get resized to
    the same height while allowing you to defina a maximum width.
    
    The maximum height is calculated by resizing every image to the maximum
    width and comparing all resulting heights. maxheight gets to be
    min(heights). Because of the double-resize involved here the function
    caches the heights. But there is room for improvement. """
    from imagequery import ImageQuery # late import to avoid circular import
    minheight = None # infinity
    all_values = ':'.join(images.values())
    for i, value in images.items():
        if not value:
            continue
        try:
            cache_key = 'imagequery_equal_height_%s_%s_%d' % (all_values, value, maxwidth)
            height = cache.get(cache_key, None)
            if height is None:
                height = ImageQuery(value).resize(x=maxwidth).height()
                cache.set(cache_key, height, 604800) # 7 days
            if minheight is None or height < minheight:
                minheight = height
        except IOError:
            pass
    result = {}
    for i, value in images.items():
        try:
            result[i] = ImageQuery(value).scale(x=maxwidth, y=minheight)
        except IOError:
            result[i] = None
    return result

