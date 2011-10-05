import os
try:
    from PIL import Image
    from PIL import ImageChops
    from PIL import ImageOps
    from PIL import ImageFilter
    from PIL import ImageDraw
except ImportError:
    import Image
    import ImageChops
    import ImageOps
    import ImageFilter
    import ImageDraw
from imagequery.utils import get_image_object, get_font_object, get_coords


class Operation(object):
    """
    Image Operation, like scaling
    """
    
    args = ()
    args_defaults = {}
    attrs = {}

    def __init__(self, *args, **kwargs):
        allowed_args = list(self.args)
        allowed_args.reverse()
        for key in self.args_defaults:
            setattr(self, key, self.args_defaults[key])
        for value in args:
            assert allowed_args, 'too many arguments, only accepting %s arguments' % len(self.args)
            key = allowed_args.pop()
            setattr(self, key, value)
        for key in kwargs:
            assert key in allowed_args, '%s is not an accepted keyword argument' % key
            setattr(self, key, kwargs[key])

    def __unicode__(self):
        content = [self.__class__.__name__]
        args = '-'.join([str(getattr(self, key)) for key in self.args])
        if args:
            content.append(args)
        return '_'.join(content)

    def execute(self, image, query):
        return image


class DummyOperation(Operation):
    pass


class CommandOperation(Operation):
    def file_operation(self, image, query, command):
        import tempfile, subprocess
        suffix = '.%s' % os.path.basename(query.source).split('.', -1)[1]
        whfile, wfile = tempfile.mkstemp(suffix)
        image.save(wfile)
        rhfile, rfile = tempfile.mkstemp(suffix)
        proc = subprocess.Popen(command % {'infile': wfile, 'outfile': rfile}, shell=True)
        proc.wait()
        image = Image.open(rfile)
        return image


class Enhance(Operation):
    args = ('enhancer', 'factor')

    def execute(self, image, query):
        enhancer = self.enhancer(image)
        return enhancer.enhance(self.factor)


class Resize(Operation):
    args = ('x', 'y', 'filter')
    args_defaults = {
        'x': None,
        'y': None,
        'filter': Image.ANTIALIAS,
    }

    def execute(self, image, query):
        if self.x is None and self.y is None:
            self.x, self.y = image.size
        elif self.x is None:
            orig_x, orig_y = image.size
            ratio = float(self.y) / float(orig_y)
            self.x = int(orig_x * ratio)
        elif self.y is None:
            orig_x, orig_y = image.size
            ratio = float(self.x) / float(orig_x)
            self.y = int(orig_y * ratio)
        return image.resize((self.x, self.y), self.filter)


class Scale(Operation):
    args = ('x', 'y', 'filter')
    args_defaults = {
        'filter': Image.ANTIALIAS,
    }

    def execute(self, image, query):
        image = image.copy()
        image.thumbnail((self.x, self.y), self.filter)
        return image


class Invert(Operation):
    args = ('keep_alpha',)
    def execute(self, image, query):
        if self.keep_alpha:
            image = image.convert('RGBA')
            channels = list(image.split())
            for i in xrange(0, 3):
                channels[i] = ImageChops.invert(channels[i])
            return Image.merge('RGBA', channels)
        else:
            return ImageChops.invert(image)


class Grayscale(Operation):
    def execute(self, image, query):
        return ImageOps.grayscale(image)


class Flip(Operation):
    def execute(self, image, query):
        return ImageOps.flip(image)


class Mirror(Operation):
    def execute(self, image, query):
        return ImageOps.mirror(image)


class Blur(Operation):
    args = ('amount',)
    def execute(self, image, query):
        for i in xrange(0, self.amount):
            image = image.filter(ImageFilter.BLUR)
        return image


class Filter(Operation):
    args = ('filter',)
    def execute(self, image, query):
        return image.filter(self.filter)


class Crop(Operation):
    args = ('x', 'y', 'w', 'h')
    def execute(self, image, query):
        box = (
            self.x,
            self.y,
            self.x + self.w,
            self.y + self.h,
        )
        return image.crop(box)


class Fit(Operation):
    args = ('x', 'y', 'centering', 'method')
    args_defaults = {
        'method': Image.ANTIALIAS,
        'centering': (0.5, 0.5),
    }
    def execute(self, image, query):
        return ImageOps.fit(image, (self.x, self.y), self.method, centering=self.centering)


class Blank(Operation):
    args = ('x','y','color','mode')
    args_defaults = {
        'x': None,
        'y': None,
        'color': None,
        'mode': 'RGBA',
    }
    def execute(self, image, query):
        x, y = self.x, self.y
        if x is None:
            x = image.size[0]
        if y is None:
            y = image.size[1]
        if self.color:
            return Image.new(self.mode, (x, y), self.color)
        else:
            return Image.new(self.mode, (x, y))


class Paste(Operation):
    args = ('image','x','y','storage')
    def execute(self, image, query):
        athor = get_image_object(self.image, self.storage)
        x2, y2 = athor.size
        x1 = get_coords(image.size[0], athor.size[0], self.x)
        y1 = get_coords(image.size[1], athor.size[1], self.y)
        box = (
            x1,
            y1,
            x1 + x2,
            y1 + y2,
        )
        # Note that if you paste an "RGBA" image, the alpha band is ignored.
        # You can work around this by using the same image as both source image and mask.
        image = image.copy()
        if athor.mode == 'RGBA':
            if image.mode == 'RGBA':
                channels = image.split()
                alpha = channels[3]
                image = Image.merge('RGB', channels[0:3])
                athor_channels = athor.split()
                athor_alpha = athor_channels[3]
                athor = Image.merge('RGB', athor_channels[0:3])
                image.paste(athor, box, mask=athor_alpha)
                # merge alpha
                athor_image_alpha = Image.new('L', image.size, color=0)
                athor_image_alpha.paste(athor_alpha, box)
                new_alpha = ImageChops.add(alpha, athor_image_alpha)
                image = Image.merge('RGBA', image.split() + (new_alpha,))
            else:
                image.paste(athor, box, mask=athor)
        else:
            image.paste(athor, box)
        return image


class Background(Operation):
    args = ('image','x','y','storage')
    def execute(self, image, query):
        background = Image.new('RGBA', image.size, color=(0,0,0,0))
        athor = get_image_object(self.image, self.storage)
        x2,y2 = image.size
        x1 = get_coords(image.size[0], athor.size[0], self.x)
        y1 = get_coords(image.size[1], athor.size[1], self.y)
        box = (
            x1,
            y1,
            x1 + x2,
            y1 + y2,
        )
        background.paste(athor, box, mask=athor)
        background.paste(image, None, mask=image)
        return background


class Convert(Operation):
    args = ('mode', 'matrix')
    args_defaults = {
        'matrix': None,
    }
    def execute(self, image, query):
        if self.matrix:
            return image.convert(self.mode, self.matrix)
        else:
            return image.convert(self.mode)


class GetChannel(Operation):
    args = ('channel',)
    channel_map = {
        'red': 0,
        'green': 1,
        'blue': 2,
        'alpha': 3,
    }
    def execute(self, image, query):
        image = image.convert('RGBA')
        alpha = image.split()[self.channel_map[self.channel]]
        return Image.merge('RGBA', (alpha, alpha, alpha, alpha))


class ApplyAlpha(GetChannel):
    args = ('alphamap',)
    def execute(self, image, query):
        # TODO: Use putalpha(band)?
        image = image.convert('RGBA')
        alphamap = get_image_object(self.alphamap).convert('RGBA')
        data = image.split()[self.channel_map['red']:self.channel_map['alpha']]
        alpha = alphamap.split()[self.channel_map['alpha']]
        alpha = alpha.resize(image.size, Image.ANTIALIAS)
        return Image.merge('RGBA', data + (alpha,))


class Blend(Operation):
    args = ('image','alpha','storage')
    channel_map = {
        'alpha': 0.5,
    }
    def execute(self, image, query):
        athor = get_image_object(self.image, self.storage)
        return Image.blend(image, athor, self.alpha)


class Text(Operation):
    args = ('text','x','y','font','size','fill')
    args_defaults = {
        'size': None,
        'fill': None,
    }
    def execute(self, image, query):
        from imagequery import ImageQuery # late import to avoid circular import
        image = image.copy()
        font = get_font_object(self.font, self.size)
        size, offset = ImageQuery.img_textbox(self.text, self.font, self.size)
        x = get_coords(image.size[0], size[0], self.x) + offset[0]
        y = get_coords(image.size[1], size[1], self.y) + offset[1]
        draw = ImageDraw.Draw(image)
        text = self.text
        # HACK
        if Image.VERSION == '1.1.5' and isinstance(text, unicode):
            text = text.encode('utf-8')
        draw.text((x, y), text, font=font, fill=self.fill)
        return image


# TODO: enhance text operations

class TextImage(Operation):
    args = ('text', 'font', 'size', 'mode')
    args_defaults = {
        'size': None,
        'fill': None,
    }
    def execute(self, image, query):
        font = get_font_object(self.font, self.size)
        font.getmask(self.text)


class FontDefaults(Operation):
    args = ('font', 'size', 'fill')

    @property
    def attrs(self):
        return {
            'font': self.font,
            'size': self.size,
            'fill': self.fill,
        }


class Composite(Operation):
    args = ('image','mask','storage')
    def execute(self, image, query):
        athor = get_image_object(self.image, self.storage)
        mask = get_image_object(self.mask, self.storage)
        return Image.composite(image, athor, mask)


class Offset(Operation):
    args = ('x','y')
    def execute(self, image, query):
        return ImageChops.offset(image, self.x, self.y)


class Padding(Operation):
    args = ('left','top','right','bottom','color')
    def execute(self, image, query):
        left, top, right, bottom = self.left, self.top, self.right, self.bottom
        color = self.color
        if top is None:
            top = left
        if right is None:
            right = left
        if bottom is None:
            bottom = top
        if color is None:
            color = (0,0,0,0)
        new_width = left + right + image.size[0]
        new_height = top + bottom + image.size[1]
        new = Image.new('RGBA', (new_width, new_height), color=color)
        new.paste(image, (left, top))
        return new


class Opacity(Operation):
    args = ('opacity',)
    def execute(self, image, query):
        opacity = int(self.opacity * 255)
        background = Image.new('RGBA', image.size, color=(0,0,0,0))
        mask = Image.new('RGBA', image.size, color=(0,0,0,opacity))
        box = (0,0) + image.size
        background.paste(image, box, mask)
        return background


class Clip(Operation):
    args = ('start','end',)
    args_defaults = {
        'start': None,
        'end': None,
    }
    def execute(self, image, query):
        start = self.start
        if start is None:
            start = (0, 0)
        end = self.end
        if end is None:
            end = image.size
        new = image.crop(self.start + self.end)
        new.load() # crop is a lazy operation, see docs
        return new

