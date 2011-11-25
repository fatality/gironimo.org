# encoding: utf-8
import datetime
from south.db import db
from south.v2 import SchemaMigration
from django.db import models

class Migration(SchemaMigration):

    def forwards(self, orm):
        
        # Adding model 'Category'
        db.create_table('blog_category', (
            ('id', self.gf('django.db.models.fields.AutoField')(primary_key=True)),
            ('title', self.gf('django.db.models.fields.CharField')(max_length=255)),
            ('slug', self.gf('django.db.models.fields.SlugField')(unique=True, max_length=255, db_index=True)),
            ('description', self.gf('django.db.models.fields.TextField')(blank=True)),
            ('image', self.gf('django.db.models.fields.files.ImageField')(max_length=100, null=True, blank=True)),
            ('parent', self.gf('django.db.models.fields.related.ForeignKey')(blank=True, related_name='children', null=True, to=orm['blog.Category'])),
            ('lft', self.gf('django.db.models.fields.PositiveIntegerField')(db_index=True)),
            ('rght', self.gf('django.db.models.fields.PositiveIntegerField')(db_index=True)),
            ('tree_id', self.gf('django.db.models.fields.PositiveIntegerField')(db_index=True)),
            ('level', self.gf('django.db.models.fields.PositiveIntegerField')(db_index=True)),
        ))
        db.send_create_signal('blog', ['Category'])

        # Adding model 'Entry'
        db.create_table('blog_entry', (
            ('id', self.gf('django.db.models.fields.AutoField')(primary_key=True)),
            ('title', self.gf('django.db.models.fields.CharField')(max_length=255)),
            ('image', self.gf('django.db.models.fields.files.ImageField')(max_length=100, blank=True)),
            ('content', self.gf('django.db.models.fields.TextField')()),
            ('excerpt', self.gf('django.db.models.fields.TextField')(blank=True)),
            ('tags', self.gf('tagging.fields.TagField')()),
            ('slug', self.gf('django.db.models.fields.SlugField')(max_length=255, db_index=True)),
            ('status', self.gf('django.db.models.fields.IntegerField')(default=0)),
            ('featured', self.gf('django.db.models.fields.BooleanField')(default=False)),
            ('comment_enabled', self.gf('django.db.models.fields.BooleanField')(default=True)),
            ('pingback_enabled', self.gf('django.db.models.fields.BooleanField')(default=True)),
            ('creation_date', self.gf('django.db.models.fields.DateTimeField')(default=datetime.datetime.now)),
            ('last_update', self.gf('django.db.models.fields.DateTimeField')(default=datetime.datetime.now)),
            ('start_publication', self.gf('django.db.models.fields.DateTimeField')(default=datetime.datetime.now)),
            ('end_publication', self.gf('django.db.models.fields.DateTimeField')(default=datetime.datetime(2042, 3, 15, 0, 0))),
            ('login_required', self.gf('django.db.models.fields.BooleanField')(default=False)),
            ('password', self.gf('django.db.models.fields.CharField')(max_length=50, blank=True)),
            ('template', self.gf('django.db.models.fields.CharField')(default='blog/entry_detail.html', max_length=250)),
        ))
        db.send_create_signal('blog', ['Entry'])

        # Adding M2M table for field categories on 'Entry'
        db.create_table('blog_entry_categories', (
            ('id', models.AutoField(verbose_name='ID', primary_key=True, auto_created=True)),
            ('entry', models.ForeignKey(orm['blog.entry'], null=False)),
            ('category', models.ForeignKey(orm['blog.category'], null=False))
        ))
        db.create_unique('blog_entry_categories', ['entry_id', 'category_id'])

        # Adding M2M table for field related on 'Entry'
        db.create_table('blog_entry_related', (
            ('id', models.AutoField(verbose_name='ID', primary_key=True, auto_created=True)),
            ('from_entry', models.ForeignKey(orm['blog.entry'], null=False)),
            ('to_entry', models.ForeignKey(orm['blog.entry'], null=False))
        ))
        db.create_unique('blog_entry_related', ['from_entry_id', 'to_entry_id'])

        # Adding M2M table for field authors on 'Entry'
        db.create_table('blog_entry_authors', (
            ('id', models.AutoField(verbose_name='ID', primary_key=True, auto_created=True)),
            ('entry', models.ForeignKey(orm['blog.entry'], null=False)),
            ('user', models.ForeignKey(orm['auth.user'], null=False))
        ))
        db.create_unique('blog_entry_authors', ['entry_id', 'user_id'])

        # Adding M2M table for field sites on 'Entry'
        db.create_table('blog_entry_sites', (
            ('id', models.AutoField(verbose_name='ID', primary_key=True, auto_created=True)),
            ('entry', models.ForeignKey(orm['blog.entry'], null=False)),
            ('site', models.ForeignKey(orm['sites.site'], null=False))
        ))
        db.create_unique('blog_entry_sites', ['entry_id', 'site_id'])


    def backwards(self, orm):
        
        # Deleting model 'Category'
        db.delete_table('blog_category')

        # Deleting model 'Entry'
        db.delete_table('blog_entry')

        # Removing M2M table for field categories on 'Entry'
        db.delete_table('blog_entry_categories')

        # Removing M2M table for field related on 'Entry'
        db.delete_table('blog_entry_related')

        # Removing M2M table for field authors on 'Entry'
        db.delete_table('blog_entry_authors')

        # Removing M2M table for field sites on 'Entry'
        db.delete_table('blog_entry_sites')


    models = {
        'auth.group': {
            'Meta': {'object_name': 'Group'},
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'name': ('django.db.models.fields.CharField', [], {'unique': 'True', 'max_length': '80'}),
            'permissions': ('django.db.models.fields.related.ManyToManyField', [], {'to': "orm['auth.Permission']", 'symmetrical': 'False', 'blank': 'True'})
        },
        'auth.permission': {
            'Meta': {'ordering': "('content_type__app_label', 'content_type__model', 'codename')", 'unique_together': "(('content_type', 'codename'),)", 'object_name': 'Permission'},
            'codename': ('django.db.models.fields.CharField', [], {'max_length': '100'}),
            'content_type': ('django.db.models.fields.related.ForeignKey', [], {'to': "orm['contenttypes.ContentType']"}),
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'name': ('django.db.models.fields.CharField', [], {'max_length': '50'})
        },
        'auth.user': {
            'Meta': {'object_name': 'User'},
            'date_joined': ('django.db.models.fields.DateTimeField', [], {'default': 'datetime.datetime.now'}),
            'email': ('django.db.models.fields.EmailField', [], {'max_length': '75', 'blank': 'True'}),
            'first_name': ('django.db.models.fields.CharField', [], {'max_length': '30', 'blank': 'True'}),
            'groups': ('django.db.models.fields.related.ManyToManyField', [], {'to': "orm['auth.Group']", 'symmetrical': 'False', 'blank': 'True'}),
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'is_active': ('django.db.models.fields.BooleanField', [], {'default': 'True'}),
            'is_staff': ('django.db.models.fields.BooleanField', [], {'default': 'False'}),
            'is_superuser': ('django.db.models.fields.BooleanField', [], {'default': 'False'}),
            'last_login': ('django.db.models.fields.DateTimeField', [], {'default': 'datetime.datetime.now'}),
            'last_name': ('django.db.models.fields.CharField', [], {'max_length': '30', 'blank': 'True'}),
            'password': ('django.db.models.fields.CharField', [], {'max_length': '128'}),
            'user_permissions': ('django.db.models.fields.related.ManyToManyField', [], {'to': "orm['auth.Permission']", 'symmetrical': 'False', 'blank': 'True'}),
            'username': ('django.db.models.fields.CharField', [], {'unique': 'True', 'max_length': '30'})
        },
        'blog.category': {
            'Meta': {'ordering': "['title']", 'object_name': 'Category'},
            'description': ('django.db.models.fields.TextField', [], {'blank': 'True'}),
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'image': ('django.db.models.fields.files.ImageField', [], {'max_length': '100', 'null': 'True', 'blank': 'True'}),
            'level': ('django.db.models.fields.PositiveIntegerField', [], {'db_index': 'True'}),
            'lft': ('django.db.models.fields.PositiveIntegerField', [], {'db_index': 'True'}),
            'parent': ('django.db.models.fields.related.ForeignKey', [], {'blank': 'True', 'related_name': "'children'", 'null': 'True', 'to': "orm['blog.Category']"}),
            'rght': ('django.db.models.fields.PositiveIntegerField', [], {'db_index': 'True'}),
            'slug': ('django.db.models.fields.SlugField', [], {'unique': 'True', 'max_length': '255', 'db_index': 'True'}),
            'title': ('django.db.models.fields.CharField', [], {'max_length': '255'}),
            'tree_id': ('django.db.models.fields.PositiveIntegerField', [], {'db_index': 'True'})
        },
        'blog.entry': {
            'Meta': {'ordering': "['-creation_date']", 'object_name': 'Entry'},
            'authors': ('django.db.models.fields.related.ManyToManyField', [], {'symmetrical': 'False', 'related_name': "'entries'", 'blank': 'True', 'to': "orm['auth.User']"}),
            'categories': ('django.db.models.fields.related.ManyToManyField', [], {'blank': 'True', 'related_name': "'entries'", 'null': 'True', 'symmetrical': 'False', 'to': "orm['blog.Category']"}),
            'comment_enabled': ('django.db.models.fields.BooleanField', [], {'default': 'True'}),
            'content': ('django.db.models.fields.TextField', [], {}),
            'creation_date': ('django.db.models.fields.DateTimeField', [], {'default': 'datetime.datetime.now'}),
            'end_publication': ('django.db.models.fields.DateTimeField', [], {'default': 'datetime.datetime(2042, 3, 15, 0, 0)'}),
            'excerpt': ('django.db.models.fields.TextField', [], {'blank': 'True'}),
            'featured': ('django.db.models.fields.BooleanField', [], {'default': 'False'}),
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'image': ('django.db.models.fields.files.ImageField', [], {'max_length': '100', 'blank': 'True'}),
            'last_update': ('django.db.models.fields.DateTimeField', [], {'default': 'datetime.datetime.now'}),
            'login_required': ('django.db.models.fields.BooleanField', [], {'default': 'False'}),
            'password': ('django.db.models.fields.CharField', [], {'max_length': '50', 'blank': 'True'}),
            'pingback_enabled': ('django.db.models.fields.BooleanField', [], {'default': 'True'}),
            'related': ('django.db.models.fields.related.ManyToManyField', [], {'blank': 'True', 'related_name': "'related_rel_+'", 'null': 'True', 'to': "orm['blog.Entry']"}),
            'sites': ('django.db.models.fields.related.ManyToManyField', [], {'related_name': "'entries'", 'symmetrical': 'False', 'to': "orm['sites.Site']"}),
            'slug': ('django.db.models.fields.SlugField', [], {'max_length': '255', 'db_index': 'True'}),
            'start_publication': ('django.db.models.fields.DateTimeField', [], {'default': 'datetime.datetime.now'}),
            'status': ('django.db.models.fields.IntegerField', [], {'default': '0'}),
            'tags': ('tagging.fields.TagField', [], {}),
            'template': ('django.db.models.fields.CharField', [], {'default': "'blog/entry_detail.html'", 'max_length': '250'}),
            'title': ('django.db.models.fields.CharField', [], {'max_length': '255'})
        },
        'contenttypes.contenttype': {
            'Meta': {'ordering': "('name',)", 'unique_together': "(('app_label', 'model'),)", 'object_name': 'ContentType', 'db_table': "'django_content_type'"},
            'app_label': ('django.db.models.fields.CharField', [], {'max_length': '100'}),
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'model': ('django.db.models.fields.CharField', [], {'max_length': '100'}),
            'name': ('django.db.models.fields.CharField', [], {'max_length': '100'})
        },
        'sites.site': {
            'Meta': {'ordering': "('domain',)", 'object_name': 'Site', 'db_table': "'django_site'"},
            'domain': ('django.db.models.fields.CharField', [], {'max_length': '100'}),
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'name': ('django.db.models.fields.CharField', [], {'max_length': '50'})
        }
    }

    complete_apps = ['blog']
