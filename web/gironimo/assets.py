from django_assets import Bundle, register


js = Bundle(
    'lib/js/modernizr.js',
    'lib/js/jquery.js',
    'lib/js/gironimo.js',
    'lib/js/gironimo.utils.js',
    output='lib/js/_packed.js',
)
register('js_base', js)
    
css = Bundle(
    'lib/css/normalize.css',
    'lib/css/general.css',
    output='lib/css/_packed.css',
)
register('css_base', css)

