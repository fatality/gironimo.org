import os.path


DEBUG = False
TEMPLATE_DEBUG = DEBUG

gettext_noop = lambda s: s

ADMINS = (
    (u'Marc Rochow', 'marc.rochow@gironimo.org'),
)

MANAGERS = ADMINS

DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.sqlite3',
        'NAME': os.path.join(os.path.dirname(__file__), 'sqlite.db'),
        'USER': '',
        'PASSWORD': '',
        'HOST': '',
        'PORT': '',
    }
}

TIME_ZONE = 'Europe/Berlin'

LANGUAGE_CODE = 'de'

LANGUAGES = (
    ('de', gettext_noop('German')),
)

SITE_ID = 1

USE_I18N = True

USE_L10N = True

USE_THOUSAND_SEPARATOR = False

LOCALE_PATHS = (
    os.path.join(os.path.dirname(os.path.dirname(os.path.realpath(__file__))), 'locale'),
)

MEDIA_ROOT = os.path.join(os.path.dirname(os.path.dirname(__file__)), 'htdocs', 'media')

LIB_MEDIA_ROOT = MEDIA_ROOT

MEDIA_URL = '/media/'

STATIC_ROOT = os.path.join(os.path.dirname(os.path.dirname(__file__)), 'htdocs', 'static')

STATIC_URL = '/static/'

STATICFILES_DIRS = (
    os.path.join(os.path.dirname(os.path.dirname(__file__)), 'static'),
)

ADMIN_MEDIA_PREFIX = '/static/admin/'

SECRET_KEY = 'so9qu(s8+^-n#j*m!(6n@a&l&!)+&lj=))r#(wb)3soec2x3zu'

TEMPLATE_LOADERS = (
    'django.template.loaders.filesystem.Loader',
    'django.template.loaders.app_directories.Loader',
    #'django.template.loaders.eggs.Loader',
)

MIDDLEWARE_CLASSES = (
    'django.middleware.common.CommonMiddleware',
    'django.contrib.sessions.middleware.SessionMiddleware',
    'django.middleware.csrf.CsrfViewMiddleware',
    'django.contrib.auth.middleware.AuthenticationMiddleware',
    'django.contrib.messages.middleware.MessageMiddleware',
)

ROOT_URLCONF = 'gironimo.urls'

TEMPLATE_DIRS = (
    os.path.join(os.path.dirname(os.path.dirname(__file__)), 'templates'),
)

INSTALLED_APPS = (
    #Django,
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.sites',
    'django.contrib.messages',
    'django.contrib.staticfiles',
    'django.contrib.admin',
    'django.contrib.admindocs',
    
    #Gironimo,
    'gironimo.accounts',
    
    #Third-Party,
    'django_assets',
    'django_ajax',
    'imagequery',
    'django_model_utils',
    'tagging',
    'south',
    'django_hits',
    'registration',
)

TEMPLATE_CONTEXT_PROCESSORS = (
	'django.core.context_processors.request',
	'django.contrib.auth.context_processors.auth',
	'django.core.context_processors.i18n',
	'django.core.context_processors.media',
	'django.core.context_processors.static',
	'django.contrib.messages.context_processors.messages',
)

IMAGEQUERY_DEFAULT_OPTIONS = {'quality': 92}

AJAX_CONFIG_PROCESSORS = (
	'django_ajax.ajax_processors.media',
	'django_ajax.ajax_processors.static',
	#'django_ajax.ajax_processors.session',
)

ASSETS_DEBUG = False

AUTH_PROFILE_MODULE = 'gironimo.accounts.UserProfile'
ACCOUNT_ACTIVATION_DAYS = 3

try:
    from local_settings import *
except ImportError:
    try:
        from gironimo.local_settings import *
    except ImportError:
        pass

