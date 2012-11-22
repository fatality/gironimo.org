/**
 * SyntaxHighlighter 3.0 brush for DOS Batch files
 *
 * @version
 * 1.0.0 (August 24 2011)
 * 
 * @copyright
 * Copyright (C) 2011 Andreas Breitschopp.
 * http://www.ab-weblog.com
 *
 * @license
 * Dual licensed under the MIT and GPL licenses.
 */
;(function()
{
	// CommonJS
	typeof(require) != 'undefined' ? SyntaxHighlighter = require('shCore').SyntaxHighlighter : null;

        function Brush()
        {
                var keywordsnocase =  'goto call exit if else for exist defined errorlevel cmdextversion';
                var keywordscase   =  'EQU NEQ LSS LEQ GTR GEQ';
                var commands       =  'append assoc at attrib break cacls cd chcp chdir chkdsk chkntfs cls cmd color comp compact ' +
                                      'convert copy date del dir diskcomp diskcopy doskey echo endlocal erase fc find findstr ' +
                                      'format ftype graftabl help keyb label md mkdir mode more move path pause popd print ' +
                                      'prompt pushd rd recover rem ren rename replace restore rmdir set setlocal shift sort ' +
                                      'start subst time title tree type ver verify vol xcopy'
                                      ;

                this.regexList = [
                        { regex: /^@.*$/gm,                                                      css: 'preprocessor bold' },
                        { regex: /\/[\w-\/]+/gm,                                                 css: 'plain' },
                        { regex: /\:[^\:\r\n]*$/gm,                                              css: 'variable' },              // jump labels
                        { regex: /\:\:.*$/gm,                                                    css: 'comments' },              // one line comments
                        { regex: /rem.*$/gim,                                                    css: 'comments' },              // one line comments
                        { regex: SyntaxHighlighter.regexLib.doubleQuotedString,                  css: 'string' },                // double quoted strings
                        { regex: SyntaxHighlighter.regexLib.singleQuotedString,                  css: 'string' },                // single quoted strings
                        { regex: new RegExp(this.getKeywords(keywordsnocase), 'gim'),           css: 'keyword' },               // keywords (case insensitive)
                        { regex: new RegExp(this.getKeywords(keywordscase), 'gm'),              css: 'keyword' },               // keywords (case sensitive)
                        { regex: new RegExp(this.getKeywords(commands), 'gim'),                 css: 'functions' }              // commands (case insensitive)
                        ];
        }

	Brush.prototype	= new SyntaxHighlighter.Highlighter();
	Brush.aliases	= ['dosbatch', 'batch'];

	SyntaxHighlighter.brushes.DosBatch = Brush;

	// CommonJS
	typeof(exports) != 'undefined' ? exports.Brush = Brush : null;
})();
