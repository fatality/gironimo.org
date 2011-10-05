def copy_fields(from_obj, to_obj, model=None):
    if model is None:
        model = from_obj.__class__
    for f in model._meta.fields:
        if f.primary_key:
            continue
        attname = f.attname
        setattr(
            to_obj,
            attname,
            getattr(from_obj, attname)
        )
    return to_obj

