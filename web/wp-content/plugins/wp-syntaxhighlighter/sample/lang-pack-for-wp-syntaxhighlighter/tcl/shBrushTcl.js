/**
 * SyntaxHighlighter brush for Tcl
 *
 * more info:
 * http://www.henix-blog.co.cc/blog/tcl-syntaxhighlighter-brush.html
 *
 * @copyright
 * Copyright (C) 2011 henix.
 *
 * @license
 * Dual licensed under the MIT and GPL licenses.
 */

/**
 * ChangeLog
 *
 * 2011-4-16 henix
 *     Version 1.0
 */

SyntaxHighlighter.brushes.Tcl = function()
{
	// According to: http://www.tcl.tk/man/tcl8.5/TclCmd/contents.htm
	var funcs = "after append apply array bgerror binary break catch cd chan clock close concat continue dde dict encoding eof error eval exec exit expr fblocked fconfigure fcopy file fileevent filename flush for foreach format gets glob global history http if incr info interp join lappend lassign lindex linsert list llength load lrange lrepeat lreplace lreverse lsearch lset lsort mathfunc mathop memory msgcat namespace open package parray pid platform proc puts pwd read refchan regexp registry regsub rename return scan seek set socket source split string subst switch tcltest tclvars tell time tm trace unknown unload unset update uplevel upvar variable vwait while";

	this.regexList = [
		{ regex: new RegExp('^\\s*(#.*$)', 'gm'), css: 'comments' },
		{ regex: new RegExp(';\\s*(#.*$)', 'gm'), css: 'comments' },
		{ regex: SyntaxHighlighter.regexLib.doubleQuotedString, css: 'string' },
		{ regex: new RegExp('\\$[A-Za-z]\\w*', 'g'), css: 'variable'},
		{ regex: new RegExp('\\b\\d+\\b', 'g'), css: 'constants' },
		{ regex: new RegExp(this.getKeywords(funcs), 'g'), css: 'functions bold' }
		];
};
SyntaxHighlighter.brushes.Tcl.prototype	= new SyntaxHighlighter.Highlighter();
SyntaxHighlighter.brushes.Tcl.aliases	= ['tcl'];
