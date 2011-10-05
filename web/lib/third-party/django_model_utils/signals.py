from django.db.models import signals


class DisconnectHandlerException(Exception):
    pass


def auto_now_on_save(model, fieldname):
    def _update_datefield(instance):
        from datetime import datetime
        setattr(instance, fieldname, datetime.now())
    execute_on_pre_save(model, _update_datefield)

def execute_on_pre_save(model, callback):
    def _exec(instance, **kwargs):
        try:
            callback(instance)
        except DisconnectHandlerException:
            signals.pre_save.disconnect(_exec, sender=model, weak=False)
    signals.pre_save.connect(_exec, sender=model, weak=False)

def execute_on_post_save(model, callback):
    def _exec(instance, **kwargs):
        try:
            callback(instance)
        except DisconnectHandlerException:
            signals.post_save.disconnect(_exec, sender=model, weak=False)
    signals.post_save.connect(_exec, sender=model, weak=False)

def execute_on_obj_pre_save(obj, callback):
    def _exec(instance, **kwargs):
        if instance is obj:
            try:
                callback(instance)
            except DisconnectHandlerException:
                signals.pre_save.disconnect(_exec, sender=obj.__class__, weak=False)
    signals.pre_save.connect(_exec, sender=obj.__class__, weak=False)

def execute_on_obj_post_save(obj, callback):
    def _exec(instance, **kwargs):
        if instance is obj:
            try:
                callback(instance)
            except DisconnectHandlerException:
                signals.post_save.disconnect(_exec, sender=obj.__class__, weak=False)
    signals.post_save.connect(_exec, sender=obj.__class__, weak=False)

def autosave_on_related_save_setattr(attname):
    """Helper-function for autosave_on_related_save, see example there"""
    def _set_attribute(obj, instance, **kwargs):
        setattr(obj, attname, instance)
    return _set_attribute

def autosave_on_related_save(obj, related, callback=None):
    """Saves a given obj when a related object gehts saved

    This function uses the django signals to autosave some object which depends
    on a related object. This is useful in forms when commit=False is used, as
    only the related object is returned.

    callback can be used to accomplish different tasks before saving the object.
    In most cases you will need to set the foreign key attribute to its right
    value, you may use autosave_on_related_save_setattr() to do so.

    Example:
        def save(commit=True):
            parent = ParentObj() # related
            child = ChildObj() # obj
            # do stuff
            if commit:
                parent.save()
                child.parent = parent
                child.save()
            else:
                autosave_on_related_save(child, parent, autosave_on_related_save_setattr('parent'))
            return parent # child stays in memory until it gets saved
    """
    def _autosave_related_obj(instance, **kwargs):
        if instance is related:
            if callback:
                callback(obj, instance, **kwargs)
            obj.save()
            signals.post_save.disconnect(_autosave_related_obj, sender=related.__class__, weak=False)
    signals.post_save.connect(_autosave_related_obj, sender=related.__class__, weak=False)

def send_signal_on_post_save(model, signal, signal_kwargs=None):
    def _send_signal_on_post_save(instance, **kwargs):
        _signal_kwargs = signal_kwargs
        if _signal_kwargs is None:
            _signal_kwargs = {}
        elif hasattr(_signal_kwargs, '__call__'):
            _signal_kwargs = signal_kwargs(instance)
        signal.send(instance, **_signal_kwargs)
        signals.post_save.disconnect(_send_signal_on_post_save, sender=model, weak=False)
    signals.post_save.connect(_send_signal_on_post_save, sender=model, weak=False)

def send_signal_on_obj_post_save(obj, signal, signal_kwargs=None):
    def _send_signal_on_obj_post_save(instance, **kwargs):
        if instance is obj:
            _signal_kwargs = signal_kwargs
            if _signal_kwargs is None:
                _signal_kwargs = {}
            elif hasattr(_signal_kwargs, '__call__'):
                _signal_kwargs = signal_kwargs(obj)
            signal.send(obj, **_signal_kwargs)
            signals.post_save.disconnect(_send_signal_on_obj_post_save, sender=obj.__class__, weak=False)
    signals.post_save.connect(_send_signal_on_obj_post_save, sender=obj.__class__, weak=False)
