# -*- coding: utf-8 -*-
import os
import shutil
try:
    from PIL import Image
except ImportError:
    import Image
from django.test import TestCase
from django.conf import settings
from django.core.files.storage import FileSystemStorage
from django.db import models
from imagequery.query import ImageQuery, RawImageQuery, NewImageQuery
from imagequery import formats


class ImageModel(models.Model):
    name = models.CharField(max_length=50)
    image = models.ImageField(upload_to='.', max_length=255)

    def __unicode__(self):
        return self.name


class TestFormat(formats.Format):
    def execute(self, qs):
        return qs.grayscale().query_name('test_format')


class ImageQueryTest(TestCase):
    def setUp(self):
        import tempfile
        self.tmp_dir = os.path.join(settings.MEDIA_ROOT, 'test', 'imagequery')
        self.sample_dir = os.path.join(self.tmp_dir, 'sampleimages')
        self.font_dir = os.path.join(os.path.dirname(__file__), 'samplefonts')
        shutil.copytree(
            os.path.join(os.path.dirname(__file__), 'sampleimages'),
            self.sample_dir)
        self.tmpstorage_dir = tempfile.mkdtemp()
        self.tmpstorage = FileSystemStorage(location=self.tmpstorage_dir)
        self.tmpstorage_save_dir = tempfile.mkdtemp()
        self.tmpstorage_save = FileSystemStorage(location=self.tmpstorage_save_dir)
        self.registered_formats = formats._formats
        formats._formats = {}
        formats.register('test', TestFormat)

    def tearDown(self):
        shutil.rmtree(self.tmp_dir)
        formats._formats = self.registered_formats
    
    def sample(self, path):
        return os.path.join(self.sample_dir, path)
    def tmp(self, path):
        return os.path.join(self.tmp_dir, path)
    def compare(self, im1, im2):
        import hashlib
        f1hash = hashlib.md5()
        f1hash.update(Image.open(im1).tostring())
        f2hash = hashlib.md5()
        f2hash.update(Image.open(im2).tostring())
        return f1hash.hexdigest() == f2hash.hexdigest()

    def test_load_simple_filename(self):
        iq = ImageQuery(self.sample('django_colors.jpg'))
        iq.grayscale().save(self.tmp('test.jpg'))
        self.assert_(self.compare(self.tmp('test.jpg'), self.sample('results/django_colors_gray.jpg')))

    def test_load_open_image_file(self):
        iq = RawImageQuery(Image.open(self.sample('django_colors.jpg')))
        iq.grayscale().save(self.tmp('test.jpg'))
        self.assert_(self.compare(self.tmp('test.jpg'), self.sample('results/django_colors_gray.jpg')))

    def test_load_blank_image(self):
        blank = NewImageQuery(x=100,y=100,color=(250,200,150,100))
        blank.save(self.tmp('test.png'))
        self.assert_(self.compare(self.tmp('test.png'), self.sample('results/blank_100x100_250,200,150,100.png')))

    def test_load_model_field(self):
        instance = ImageModel.objects.create(name='Hi', image=self.sample('django_colors.jpg'))
        instance = ImageModel.objects.get(pk=instance.pk)
        iq = ImageQuery(instance.image)
        iq.grayscale().save(self.tmp('test.jpg'))
        self.assert_(self.compare(self.tmp('test.jpg'), self.sample('results/django_colors_gray.jpg')))

    def test_custom_storage(self):
        shutil.copyfile(self.sample('django_colors.jpg'), os.path.join(self.tmpstorage_dir, 'customstorage.jpg'))
        # load from custom tmp storage
        iq = ImageQuery('customstorage.jpg', storage=self.tmpstorage)
        
        iq.grayscale().save('save.jpg')
        self.assert_(self.compare(os.path.join(self.tmpstorage_dir, 'save.jpg'), self.sample('results/django_colors_gray.jpg')))
        
        iq.grayscale().save('save.jpg', storage=self.tmpstorage_save)
        self.assert_(self.compare(os.path.join(self.tmpstorage_save_dir, 'save.jpg'), self.sample('results/django_colors_gray.jpg')))
        
        iq = ImageQuery('customstorage.jpg', storage=self.tmpstorage, cache_storage=self.tmpstorage_save)
        iq.grayscale().save('save.jpg')
        self.assert_(self.compare(os.path.join(self.tmpstorage_save_dir, 'save.jpg'), self.sample('results/django_colors_gray.jpg')))

    def test_operations(self):
        dj = ImageQuery(self.sample('django_colors.jpg'))
        tux = ImageQuery(self.sample('tux_transparent.png'))
        lynx = ImageQuery(self.sample('lynx_kitten.jpg'))

        dj.grayscale().save(self.tmp('test.jpg'))
        self.assert_(self.compare(self.tmp('test.jpg'), self.sample('results/django_colors_gray.jpg')))

        dj.paste(tux, 'center', 'bottom').save(self.tmp('test.jpg'))
        self.assert_(self.compare(self.tmp('test.jpg'), self.sample('results/django_colors_with_tux_center_bottom.jpg')))

        lynx.mirror().flip().invert().resize(400,300).save(self.tmp('test.jpg'))
        self.assert_(self.compare(self.tmp('test.jpg'), self.sample('results/lynx_kitten_mirror_flip_invert_resize_400_300.jpg')))

        lynx.fit(400,160).save(self.tmp('test.jpg'))
        self.assert_(self.compare(self.tmp('test.jpg'), self.sample('results/lynx_fit_400_160.jpg')))

        tux_blank = tux.blank(color='#000088').save(self.tmp('test.png'))
        self.assert_(self.compare(self.tmp('test.png'), self.sample('results/tux_blank_000088.png')))
        self.assertEqual(tux.size(), tux_blank.size())

        lynx.resize(400).save(self.tmp('test.jpg'))
        lynx.resize(400).sharpness(3).save(self.tmp('test2.jpg'))
        lynx.resize(400).sharpness(-1).save(self.tmp('test3.jpg'))
        self.assert_(self.compare(self.tmp('test.jpg'), self.sample('results/lynx_resize_400.jpg')))
        self.assert_(self.compare(self.tmp('test2.jpg'), self.sample('results/lynx_resize_400_sharpness_3.jpg')))
        self.assert_(self.compare(self.tmp('test3.jpg'), self.sample('results/lynx_resize_400_sharpness_-1.jpg')))
        self.assert_(not self.compare(self.tmp('test.jpg'), self.tmp('test2.jpg')))

        dj.text('Django ImageQuery', 'center', 10, os.path.join(self.font_dir, 'Vera.ttf'), 20, '#000000').save(self.tmp('test.jpg'))
        self.assert_(self.compare(self.tmp('test.jpg'), self.sample('results/django_colors_text_center_10.jpg')))

        self.assertEqual(dj.mimetype(), 'image/jpeg')
        self.assertEqual(tux.mimetype(), 'image/png')

    def test_hash_calculation(self):
        dj = ImageQuery(self.sample('django_colors.jpg'))
        dj1 = dj.scale(100,100)
        self.assertNotEqual(dj1._name(), dj._name())
        dj2 = ImageQuery(self.sample('django_colors.jpg')).scale(100,100)

        self.assertEqual(dj1._name(), dj2._name())
        self.assertNotEqual(dj._name(), dj2._name())
        dj3 = dj.scale(101,101)
        self.assertNotEqual(dj1._name(), dj3._name())
    
    def test_format(self):
        iq = ImageQuery(self.sample('django_colors.jpg'))
        f = TestFormat(iq)
        self.assert_(self.compare(f.path(), self.sample('results/django_colors_gray.jpg')))
    
    def test_template_format(self):
        from django import template
        tpl = template.Template('{% load imagequery_tags %}{% image_format "test" image %}')
        ctx = template.Context({
            'image': ImageQuery(self.sample('django_colors.jpg')),
        })
        result = tpl.render(ctx)
        self.assertEqual(result, 'cache/test_format/django_colors.jpg')

