from django.utils.translation import ugettext_lazy as _
from cms.app_base import CMSApp
from cms.apphook_pool import apphook_pool
from gironimo.blog.plugins.settings import APP_MENUS


class ZinniaApphook(CMSApp):
    name = _('Gironimo App Hook')
    urls = ['blog.urls']
    menus = APP_MENUS

apphook_pool.register(ZinniaApphook)

