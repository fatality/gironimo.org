from django import template
import django.utils.simplejson as _json

register = template.Library()

class JsonNode(template.Node):
	def __init__(self, variable):
		self.variable = variable

	def render(self, context):
		try:
			obj = self.variable.resolve(context)
		except template.VariableDoesNotExist:
			return ''
		return _json.dumps(obj)

@register.tag
def json(parser, token):
	'''
	{% json object %}
	'''
	tokens = token.split_contents()
	tag_name = tokens[0]
	values = tokens[1:]
	if not values:
		raise template.TemplateSyntaxError(u'%r requires one argument (object to be encoded).' % tag_name)
	variable = parser.compile_filter(values[0])
	return JsonNode(variable)


class AjaxCacheNode(template.Node):
	def __init__(self, variable, cache_func):
		self.variable = variable
		self.cache_func = cache_func

	def render(self, context):
		try:
			obj = self.variable.resolve(context)
			cache_func = self.cache_func.resolve(context)
		except template.VariableDoesNotExist:
			return ''
		if not hasattr(context, '_ajax_cached'):
			context._ajax_cached = {}
		if not cache_func in context._ajax_cached:
			context._ajax_cached[cache_func] = []
		if obj.pk in context._ajax_cached[cache_func]:
			return ''
		context._ajax_cached[cache_func].append(obj.pk)
		if hasattr(obj, 'ajax_data'):
			obj = obj.ajax_data()
		return u'%(cache_func)s(%(dump)s);' % {
			'cache_func': cache_func,
			'dump': _json.dumps(obj),
		}


@register.tag
def ajax_cache(parser, token):
	'''
	{% ajax_cache obj using "some_func.to.call" %}
	'''
	tokens = token.split_contents()
	tag_name = tokens[0]
	values = tokens[1:]
	if len(values) < 3:
		template.TemplateSyntaxError(u'%r requires at least one parameter.' % tag_name)
	variable = parser.compile_filter(values[0])
	if values[1] != 'using':
		template.TemplateSyntaxError(u"%r's second parameter must be 'using'." % tag_name)
	cache_func = template.Variable(values[2])
	return AjaxCacheNode(variable, cache_func)

