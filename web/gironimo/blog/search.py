from pyparsing import Word, alphas, WordEnd, Combine, opAssoc, Optional, OneOrMore, StringEnd, printables, quotedString, removeQuotes, ParseResults, CaselessLiteral, operatorPrecedence
from django.db.models import Q
from gironimo.blog.models import Entry
from gironimo.blog.config import STOP_WORDS


def createQ(token):
    """ Creates the Q() object """
    meta = getattr(token, 'meta', None)
    query = getattr(token, 'query', '')
    wildcards = None
    
    if isinstance(query, basestring): # Unicode -> Quoted String
        search = query
    else: # List -> No quoted string (possible wildcards)
        if len(query) == 1:
            search = query[0]
        elif len(query) == 3:
            wildcards = 'BOTH'
            search = query[1]
        elif len(query) == 2:
            if query[0] == '*':
                wildcards = 'START'
                search = query[1]
            else:
                wildcards = 'END'
                search = query[0]
    
    # Ignore connective words (of, a, an ...) and STOP_WORDS
    if (len(search) < 3 and not search.isdigit()) or search in STOP_WORDS:
        return Q()
    
    if not meta:
        return Q(content__icontains=search) | Q(excerpt__icontains=search) | Q(title__icontains=earch)
    
    if meta == 'category':
        if wildcards == 'BOTH':
            return Q(categories__title__icontains=search) | Q(categories__slug__icontains=search)
        elif wildcards == 'START':
            return Q(categories__title__iendswith=search) | Q(categories__slug__iendswith=search)
            
