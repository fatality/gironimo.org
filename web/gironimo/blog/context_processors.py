from gironimo import __version__


def version(request):
    """ Adds version of Blog to the context """
    return {'BLOG_VERSION': __version__}

