# encoding: utf-8
import datetime
from south.db import db
from south.v2 import SchemaMigration
from django.db import models

class Migration(SchemaMigration):
    
    def forwards(self, orm):
        
        # Adding model 'Hit'
        db.create_table('django_hits_hit', (
            ('visits', self.gf('django.db.models.fields.PositiveIntegerField')(default=0)),
            ('object_pk', self.gf('django.db.models.fields.CharField')(max_length=255)),
            ('id', self.gf('django.db.models.fields.AutoField')(primary_key=True)),
            ('content_type', self.gf('django.db.models.fields.related.ForeignKey')(to=orm['contenttypes.ContentType'], null=True)),
            ('views', self.gf('django.db.models.fields.PositiveIntegerField')(default=0)),
        ))
        db.send_create_signal('django_hits', ['Hit'])

        # Adding unique constraint on 'Hit', fields ['content_type', 'object_pk']
        db.create_unique('django_hits_hit', ['content_type_id', 'object_pk'])

        # Adding model 'HitLog'
        db.create_table('django_hits_hitlog', (
            ('ip', self.gf('django.db.models.fields.IPAddressField')(max_length=15, null=True)),
            ('when', self.gf('django.db.models.fields.DateTimeField')(default=datetime.datetime.now)),
            ('hit', self.gf('django.db.models.fields.related.ForeignKey')(related_name='log', to=orm['django_hits.Hit'])),
            ('id', self.gf('django.db.models.fields.AutoField')(primary_key=True)),
            ('user', self.gf('django.db.models.fields.related.ForeignKey')(related_name='hits_log', null=True, to=orm['auth.User'])),
        ))
        db.send_create_signal('django_hits', ['HitLog'])

        # Adding unique constraint on 'HitLog', fields ['hit', 'user', 'ip']
        db.create_unique('django_hits_hitlog', ['hit_id', 'user_id', 'ip'])
    
    
    def backwards(self, orm):
        
        # Deleting model 'Hit'
        db.delete_table('django_hits_hit')

        # Removing unique constraint on 'Hit', fields ['content_type', 'object_pk']
        db.delete_unique('django_hits_hit', ['content_type_id', 'object_pk'])

        # Deleting model 'HitLog'
        db.delete_table('django_hits_hitlog')

        # Removing unique constraint on 'HitLog', fields ['hit', 'user', 'ip']
        db.delete_unique('django_hits_hitlog', ['hit_id', 'user_id', 'ip'])
    
    
    models = {
        'auth.group': {
            'Meta': {'object_name': 'Group'},
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'name': ('django.db.models.fields.CharField', [], {'unique': 'True', 'max_length': '80'}),
            'permissions': ('django.db.models.fields.related.ManyToManyField', [], {'to': "orm['auth.Permission']", 'symmetrical': 'False', 'blank': 'True'})
        },
        'auth.permission': {
            'Meta': {'unique_together': "(('content_type', 'codename'),)", 'object_name': 'Permission'},
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
            'is_active': ('django.db.models.fields.BooleanField', [], {'default': 'True', 'blank': 'True'}),
            'is_staff': ('django.db.models.fields.BooleanField', [], {'default': 'False', 'blank': 'True'}),
            'is_superuser': ('django.db.models.fields.BooleanField', [], {'default': 'False', 'blank': 'True'}),
            'last_login': ('django.db.models.fields.DateTimeField', [], {'default': 'datetime.datetime.now'}),
            'last_name': ('django.db.models.fields.CharField', [], {'max_length': '30', 'blank': 'True'}),
            'password': ('django.db.models.fields.CharField', [], {'max_length': '128'}),
            'user_permissions': ('django.db.models.fields.related.ManyToManyField', [], {'to': "orm['auth.Permission']", 'symmetrical': 'False', 'blank': 'True'}),
            'username': ('django.db.models.fields.CharField', [], {'unique': 'True', 'max_length': '30'})
        },
        'contenttypes.contenttype': {
            'Meta': {'unique_together': "(('app_label', 'model'),)", 'object_name': 'ContentType', 'db_table': "'django_content_type'"},
            'app_label': ('django.db.models.fields.CharField', [], {'max_length': '100'}),
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'model': ('django.db.models.fields.CharField', [], {'max_length': '100'}),
            'name': ('django.db.models.fields.CharField', [], {'max_length': '100'})
        },
        'django_hits.hit': {
            'Meta': {'unique_together': "(('content_type', 'object_pk'),)", 'object_name': 'Hit'},
            'content_type': ('django.db.models.fields.related.ForeignKey', [], {'to': "orm['contenttypes.ContentType']", 'null': 'True'}),
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'object_pk': ('django.db.models.fields.CharField', [], {'max_length': '255'}),
            'views': ('django.db.models.fields.PositiveIntegerField', [], {'default': '0'}),
            'visits': ('django.db.models.fields.PositiveIntegerField', [], {'default': '0'})
        },
        'django_hits.hitlog': {
            'Meta': {'unique_together': "(('hit', 'user', 'ip'),)", 'object_name': 'HitLog'},
            'hit': ('django.db.models.fields.related.ForeignKey', [], {'related_name': "'log'", 'to': "orm['django_hits.Hit']"}),
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'ip': ('django.db.models.fields.IPAddressField', [], {'max_length': '15', 'null': 'True'}),
            'user': ('django.db.models.fields.related.ForeignKey', [], {'related_name': "'hits_log'", 'null': 'True', 'to': "orm['auth.User']"}),
            'when': ('django.db.models.fields.DateTimeField', [], {'default': 'datetime.datetime.now'})
        }
    }
    
    complete_apps = ['django_hits']
