from django_assets import Bundle, register


js = Bundle(
    'lib/js/modernizr.js',
    'lib/js/jquery.js',
    'lib/js/jquery.ui.js',
    'lib/js/gironimo.js',
    'lib/js/gironimo.utils.js',
    'lib/js/superfish.js',
    output='lib/js/_packed.js',
)
register('js_base', js)
    
css = Bundle(
    'lib/css/normalize.css',
    'lib/css/general.css',
    'lib/css/clearfix.css',
    'lib/css/header.css',
    'lib/css/sidebar.css',
    'lib/css/paginator.css',
    'lib/css/content.css',
    'lib/css/forms.css',
    output='lib/css/_packed.css',
)
register('css_base', css)

