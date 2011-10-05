from django import template
from django_hits.models import Hit

register = template.Library()


class HitNode(template.Node):
	def __init__(self, context_var_name, var_name, count):
		self.context_var_name = context_var_name
		self.var_name = var_name
		self.count = count
	
	def render(self, context):
		if not hasattr(context, '_hit_cache_'):
			context._hit_cache_ = {}
		try:
			obj = self.context_var_name.resolve(context)
			user = template.Variable('user').resolve(context)
		except template.VariableDoesNotExist:
			return ''
		if user.is_anonymous():
			user = None
		count = self.count
		ip = None
		if count:
			try:
				request = template.Variable('request').resolve(context)
				if 'REMOTE_ADDR' in request.META:
					ip = request.META['REMOTE_ADDR']
			except template.VariableDoesNotExist:
				pass
		was_counted = False
		if obj in context._hit_cache_:
			hit, was_counted = context._hit_cache_[obj]
		else:
			hit = Hit.objects.get_for(obj)
		if count and not was_counted:
			was_counted = hit.hit(user, ip)
		context._hit_cache_[obj] = (hit, was_counted)
		if self.var_name:
			context[self.var_name] = hit
		return ''


@register.tag
def get_hit(parser, token):
	'''
	{% get_hit for obj as hit %}
	{% get_hit for "static_page" as hit %}
	'''
	tokens = token.split_contents()
	if not len(tokens) in (5,):
		raise template.TemplateSyntaxError, "%r tag requires 4 or 5" % tokens[0]
	if tokens[1] != 'for':
		raise template.TemplateSyntaxError, "Second argument in %r tag must be 'for'" % tokens[0]
	context_var_name = parser.compile_filter(tokens[2])
	if tokens[3] != 'as':
		raise template.TemplateSyntaxError, "Fourth argument in %r must be 'as'" % tokens[0]
	var_name = tokens[4]
	return HitNode(context_var_name, var_name, False)

@register.tag
def count_hit(parser, token):
	'''
	{% count_hit for obj %}
	{% count_hit for obj as hit %}
	{% count_hit for "static_page" %}
	{% count_hit for "static_page" as hit %}
	'''
	tokens = token.split_contents()
	if not len(tokens) in (3, 5,):
		raise template.TemplateSyntaxError, "%r tag requires 4 or 5" % tokens[0]
	if tokens[1] != 'for':
		raise template.TemplateSyntaxError, "Second argument in %r tag must be 'for'" % tokens[0]
	context_var_name = parser.compile_filter(tokens[2])
	var_name = None
	if len(tokens) > 3:
		if tokens[3] != 'as':
			raise template.TemplateSyntaxError, "Fourth argument in %r must be 'as'" % tokens[0]
		var_name = tokens[4]
	return HitNode(context_var_name, var_name, True)

