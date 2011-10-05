from django.db import models

def _mimic_queryset_method(method):
    def mimic_method(self, *args, **kwargs):
        return getattr(self.get_query_set(), method)(*args, **kwargs)
    return mimic_method

def queryset_manager(manager, queryset):
    class QuerySetManager(manager):
        def get_query_set(self):
            qs = super(QuerySetManager, self).get_query_set()
            return qs._clone(klass=queryset)
    return QuerySetManager

def mimic_queryset_manager(manager, methods):
    class MimicManager(manager):
        pass
    for method in methods:
        setattr(MimicManager, method, _mimic_queryset_method(method))
    return MimicManager

def forced_queryset_manager(manager, funcname):
    class ForcedManager(manager):
        def get_query_set(self):
            qs = super(ForcedManager, self).get_query_set()
            qs = getattr(qs, funcname)()
            return qs
    return ForcedManager

