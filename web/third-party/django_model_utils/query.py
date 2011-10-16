from django.db import models
from django.db.models.query import QuerySet
from django_model_utils import models as util_models


class StatusQuerySet(QuerySet):
    STATUS_ATTR = 'status'
    STATUS_DRAFT_VALUE = util_models.StatusMixin.STATUS_DRAFT
    STATUS_LIVE_VALUE = util_models.StatusMixin.STATUS_LIVE
    STATUS_FEATURED_VALUE = util_models.StatusMixin.STATUS_FEATURED
        
    def draft(self):
        return self.filter(
            **{self.STATUS_ATTR: self.STATUS_DRAFT_VALUE}
        )
    
    def live(self):
        return self.filter(
            **{'%s__gte' % self.STATUS_ATTR: self.STATUS_LIVE_VALUE}
        )
    
    def featured(self):
        return self.filter(
            **{'%s__gte' % self.STATUS_ATTR: self.STATUS_FEATURED_VALUE}
        )


class PublicDateQuerySet(QuerySet):
    PUBLIC_START_ATTR = 'public_start'
    PUBLIC_END_ATTR = 'public_end'
    
    def public(self):
        from datetime import datetime
        now = datetime.now()
        return self.filter(
            models.Q(**{'%s__lte' % self.PUBLIC_START_ATTR: now}) |
            models.Q(**{'%s__isnull' % self.PUBLIC_START_ATTR: True}),
            models.Q(**{'%s__gt' % self.PUBLIC_END_ATTR: now}) |
            models.Q(**{'%s__isnull' % self.PUBLIC_END_ATTR: True})
        )


