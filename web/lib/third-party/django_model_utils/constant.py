def model_constant(model, pk):
    ''' Creates a new model instance which has all fields set to deferred except for the pk-value
    
    This allows usage of this instance as a constant value without hitting he database.
    '''
    from django.db.models.query_utils import deferred_class_factory
    deferred_attrs = [f.name for f in model._meta.local_fields if not f.primary_key]
    return deferred_class_factory(model, deferred_attrs)(pk=pk)

