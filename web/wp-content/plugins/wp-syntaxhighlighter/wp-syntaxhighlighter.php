<?php
/*
Plugin Name: WP SyntaxHighlighter
Plugin URI: http://www.near-mint.com/blog/software/wp-syntaxhighlighter
Description: This plugin is code syntax highlighter based on <a href="http://alexgorbatchev.com/SyntaxHighlighter/">SyntaxHighlighter</a> ver. 3.0.83 and ver. 2.1.382. Supported languages: Bash, C++, CSS, Delphi, Java, JavaScript, Perl, PHP, Python, Ruby, SQL, VB, XML, XHTML and HTML etc.
Version: 1.7.3
Author: redcocker
Author URI: http://www.near-mint.com/blog/
Text Domain: wp_sh
Domain Path: /languages
*/
/* 
Last modified: 2012/2/29
License: GPL v2(Except "SyntaxHighlighter" libraries)
*/
/*  Copyright 2011 M. Sumitomo

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/* 
'WP SyntaxHighlighter' uses SyntaxHighlighter ver. 3.0.83 and ver. 2.1.382 by Alex Gorbatchev. 
SyntaxHighlighter ver. 3.0.83 is licensed under the MIT and LGPL v3
SyntaxHighlighter ver. 2.1.382 is licensed under the LGPL v3.
*/

load_plugin_textdomain('wp_sh', false, dirname(plugin_basename(__FILE__)).'/languages');
$wp_sh_ver = "1.7.3";
$wp_sh_db_ver = "1.7";
$wp_sh_plugin_url = plugin_dir_url(__FILE__);
$wp_sh_allowed_str = "3";

// Default setting values
function wp_sh_default_setting_value($val) {
	if ($val == "class") {
		$wp_sh_default_class = 'notranslate';
		return $wp_sh_default_class;
	} else if ($val == "style") {
		$wp_sh_default_addl_style = '.syntaxhighlighter,
.syntaxhighlighter a,
.syntaxhighlighter div,
.syntaxhighlighter code,
.syntaxhighlighter table,
.syntaxhighlighter table td,
.syntaxhighlighter table tr,
.syntaxhighlighter table tbody,
.syntaxhighlighter table thead,
.syntaxhighlighter table caption,
.syntaxhighlighter textarea {
font-size: 12px !important; /* Set the font size in pixels */
font-family: "Consolas", "Bitstream Vera Sans Mono", "Courier New", Courier, monospace !important; /* Set the font type */
}
.syntaxhighlighter table caption {
/* For Title(Caption) */
font-size: 14px !important; /* Set the font size in pixels */
font-family: "Consolas", "Bitstream Vera Sans Mono", "Courier New", Courier, monospace !important; /* Set the font type */
}
.syntaxhighlighter.nogutter td.code .line {
/* Set the left padding space when no-gutter in ver. 3.0 */
padding-left: 3px !important;
}
.syntaxhighlighter {
/* For Chrome/Safari(WebKit) */
/* Hide the superfluous vertical scrollbar in ver. 3.0 */
overflow-y: hidden !important;
padding: 1px !important;
}
.widget-area.syntaxhighlighter a,
.widget-area.syntaxhighlighter div,
.widget-area.syntaxhighlighter code,
.widget-area.syntaxhighlighter table,
.widget-area.syntaxhighlighter table td,
.widget-area.syntaxhighlighter table tr,
.widget-area.syntaxhighlighter table tbody,
.widget-area.syntaxhighlighter table thead,
.widget-area.syntaxhighlighter table caption,
.widget-area.syntaxhighlighter textarea {
/* For Widget */
font-size: 14px !important; /* Set the font size in pixels */
font-family: "Consolas", "Bitstream Vera Sans Mono", "Courier New", Courier, monospace !important; /* Set the font type */
}
.widget-area table caption {
/* For Widget */
/* For Title(Caption) */
font-size: 10px !important; /* Set the font size in pixels */
font-family: "Consolas", "Bitstream Vera Sans Mono", "Courier New", Courier, monospace !important; /* Set the font type */
}';
		return $wp_sh_default_addl_style;
	} else if ($val == "comment_desc") {
		$wp_sh_default_comment_hl_description_before = __("This site supports <a href=\"http://alexgorbatchev.com/SyntaxHighlighter/\">SyntaxHighlighter</a> via <a href=\"http://www.near-mint.com/blog/software/wp-syntaxhighlighter\">WP SyntaxHighlighter</a>. It can highlight your code. <br /><strong>How to highlight your code:</strong> Paste your code in the comment form, select it and then click the language link button below. This will wrap your code in a <pre> tag(or shortcode) and format it when submitted.", "wp_sh");
		return $wp_sh_default_comment_hl_description_before;
	} else if ($val == "comment_style") {
		$wp_sh_default_comment_hl_stylesheet = ".comment_highlight {
margin: 0px 0px 0px 0px;
}
.comment_highlight p {
font-size: 12px;
}
.comment_highlight_button {
margin: 0px 0px 0px 0px;
font-size: 10px;
}";
		return $wp_sh_default_comment_hl_stylesheet;
	} else if ($val == "bbp_desc") {
		$wp_sh_default_bbpress_hl_description_before = __("This site supports <a href=\"http://alexgorbatchev.com/SyntaxHighlighter/\">SyntaxHighlighter</a> via <a href=\"http://www.near-mint.com/blog/software/wp-syntaxhighlighter\">WP SyntaxHighlighter</a>. It can highlight your code. <br /><strong>How to highlight your code:</strong> Paste your code in the topic or reply form, select it and then click the language link button below. This will wrap your code in a <pre> tag(or shortcode) and format it when submitted.", "wp_sh");
		return $wp_sh_default_bbpress_hl_description_before;
	} else if ($val == "bbp_style") {
		$wp_sh_default_bbpress_hl_stylesheet = ".bbpress_highlight {
margin: 0px 0px 0px 0px;
}
.bbpress_highlight p {
font-size: 12px;
}
.bbpress_highlight_button {
margin: 0px 0px 0px 0px;
font-size: 10px;
}";
		return $wp_sh_default_bbpress_hl_stylesheet;
	} else {
		return;
	}
}

// Get setting values
$wp_sh_setting_opt = get_option('wp_sh_setting_opt');

// Get updated setting values
if (isset($_POST['WP_SH_Setting_submit']) && $_POST['wp_sh_hidden_value'] == "true") {
	include_once('get-updated-settings.php');
}

// Create the language list array
function wp_sh_language_array() {
	// Languages for ver. 3.0
	$wp_sh_pre_language3 = get_option('wp_sh_language3');
	if (is_array($wp_sh_pre_language3)) {
		$wp_sh_language3 = $wp_sh_pre_language3;
	}
	$wp_sh_language3 = array(
		"applescript" => array('AppleScript', 'true'),
		"actionscript3" => array('Actionscript3', 'true'),
		"bash" => array('Bash shell', 'true'),
		"coldfusion" => array('ColdFusion', 'true'),
		"c" => array('C', 'true'),
		"cpp" => array('C++', 'true'),
		"csharp" => array('C#', 'true'),
		"css" => array('CSS', 'true'),
		"delphi" => array('Delphi', 'true'),
		"diff" => array('Diff', 'true'),
		"erlang" => array('Erlang', 'true'),
		"groovy" => array('Groovy', 'true'),
		"html" => array('HTML', 'true'),
		"java" => array('Java', 'true'),
		"javafx" => array('JavaFX', 'true'),
		"javascript" => array('JavaScript', 'true'),
		"pascal" => array('Pascal', 'true'),
		"patch" => array('Patch', 'true'),
		"perl" => array('Perl', 'true'),
		"php" => array('PHP', 'true'),
		"text" => array('Plain Text', 'true'),
		"powershell" => array('PowerShell', 'true'),
		"python" => array('Python', 'true'),
		"ruby" => array('Ruby', 'true'),
		"rails" => array('Ruby on Rails', 'true'),
		"sass" => array('Sass', 'true'),
		"scala" => array('Scala', 'true'),
		"scss" => array('Scss', 'true'),
		"shell" => array('Shell', 'true'),
		"sql" => array('SQL', 'true'),
		"vb" => array('Visual Basic', 'true'),
		"vbnet" => array('Visual Basic .NET', 'true'),
		"xhtml" => array('XHTML', 'true'),
		"xml" => array('XML', 'true'),
		"xslt" => array('XSLT', 'true'),
		);
	// Languages for ver. 2.1
	$wp_sh_pre_language2 = get_option('wp_sh_language2');
	if (is_array($wp_sh_pre_language2)) {
		$wp_sh_language2 = $wp_sh_pre_language2;
	}
	$wp_sh_language2 = array(
		"actionscript3" => array('Actionscript3', 'true'),
		"bash" => array('Bash shell', 'true'),
		"coldfusion" => array('ColdFusion', 'true'),
		"c" => array('C', 'true'),
		"cpp" => array('C++', 'true'),
		"csharp" => array('C#', 'true'),
		"css" => array('CSS', 'true'),
		"delphi" => array('Delphi', 'true'),
		"diff" => array('Diff', 'true'),
		"erlang" => array('Erlang', 'true'),
		"groovy" => array('Groovy', 'true'),
		"html" => array('HTML', 'true'),
		"java" => array('Java', 'true'),
		"javafx" => array('JavaFX', 'true'),
		"javascript" => array('JavaScript', 'true'),
		"pascal" => array('Pascal', 'true'),
		"patch" => array('Patch', 'true'),
		"perl" => array('Perl', 'true'),
		"php" => array('PHP', 'true'),
		"text" => array('Plain Text', 'true'),
		"powershell" => array('PowerShell', 'true'),
		"python" => array('Python', 'true'),
		"ruby" => array('Ruby', 'true'),
		"rails" => array('Ruby on Rails', 'true'),
		"scala" => array('Scala', 'true'),
		"shell" => array('Shell', 'true'),
		"sql" => array('SQL', 'true'),
		"vb" => array('Visual Basic', 'true'),
		"vbnet" => array('Visual Basic .NET', 'true'),
		"xhtml" => array('XHTML', 'true'),
		"xml" => array('XML', 'true'),
		"xslt" => array('XSLT', 'true'),
		);
	// Brush files
	$wp_sh_pre_brush_files = get_option('wp_sh_brush_files');
	if (is_array($wp_sh_pre_brush_files)) {
		$wp_sh_brush_files = $wp_sh_pre_brush_files;
	}
	$wp_sh_brush_files = array(
		"AppleScript" => array('shBrushAppleScript.js', 'applescript', '3.0', 'true'),
		"ActionScript3" => array('shBrushAS3.js', 'as3 actionscript3', '2.1', 'true'),
		"Bash" => array('shBrushBash.js', 'bash shell', '2.1', 'true'),
		"ColdFusion" => array('shBrushColdFusion.js', 'cf coldfusion', '2.1', 'true'),
		"C++" => array('shBrushCpp.js', 'cpp c', '1.5', 'true'),
		"C#" => array('shBrushCSharp.js', 'c# c-sharp csharp', '1.5', 'true'),
		"CSS" => array('shBrushCss.js', 'css', '1.5', 'true'),
		"Delphi" => array('shBrushDelphi.js', 'delphi pas pascal', '1.5', 'true'),
		"Diff" => array('shBrushDiff.js', 'diff patch', '2.1', 'true'),
		"Erlang" => array('shBrushErlang.js', 'erl erlang', '2.1', 'true'),
		"Groovy" => array('shBrushGroovy.js', 'groovy', '2.1', 'true'),
		"Java" => array('shBrushJava.js', 'java', '1.5', 'true'),
		"JavaFX" => array('shBrushJavaFX.js', 'jfx javafx', '2.1', 'true'),
		"JavaScript" => array('shBrushJScript.js', 'js jscript javascript', '1.5', 'true'),
		"Perl" => array('shBrushPerl.js', 'perl pl', '2.1', 'true'),
		"PHP" => array('shBrushPhp.js', 'php', '1.5', 'true'),
		"PlainText" => array('shBrushPlain.js', 'plain text', '2.1', 'true'),
		"PowerShell" => array('shBrushPowerShell.js', 'ps powershell', '2.1', 'true'),
		"Python" => array('shBrushPython.js', 'py python', '1.5', 'true'),
		"Ruby" => array('shBrushRuby.js', 'rails ror ruby rb', '1.5', 'true'),
		"Sass" => array('shBrushSass.js', 'sass scss', '3.0', 'true'),
		"Scala" => array('shBrushScala.js', 'scala', '2.1', 'true'),
		"SQL" => array('shBrushSql.js', 'sql', '1.5', 'true'),
		"VisualBasic" => array('shBrushVb.js', 'vb vbnet', '1.5', 'true'),
		"XML" => array('shBrushXml.js', 'xml xhtml xslt html', '1.5', 'true'),
		);
	// Restore in DB
	update_option('wp_sh_language3', $wp_sh_language3);
	update_option('wp_sh_language2', $wp_sh_language2);
	update_option('wp_sh_brush_files', $wp_sh_brush_files);
}

// Create the settings array
function wp_sh_setting_array() {
	$wp_sh_setting_opt = array(
		"highlight_home" => 1,
		"highlight_posts" => 1,
		"highlight_categories" => 1,
		"highlight_archives" => 1,
		"highlight_search" => 1,
		"highlight_comment" => 0,
		"highlight_others" => 0,
		"highlight_widgets" => 0,
		"highlight_bbpress" => 0,
		"lib_version" => "3.0",
		"theme" => "Default",
		"auto_links" => "true",
		"quick_code" => "true",
		"addl_style_enable" => 0,
		"collapse" => "false",
		"gutter" => "true",
		"first_line" => "1",
		"padding_line" => "false",
		"smart_tabs" => "true",
		"tab_size" => "4",
		"toolbar" => "true",
		"wrap" => "false",
		"legacy" => 0,
		"css" => 0,
		"select_insert" => 1,
		"codebox" => 1,
		"button_window_size" => "100",
		"button_row" => "1",
		"quicktag" => 1,
		"quicktag_jquery" => 0,
		"editor_shorcode" => 0,
		"editor_no_unfiltered_html" => 0,
		"comment_hl_bt_enable" => 0,
		"comment_hl_bt_tag" => "pre",
		"comment_hl_bt_shorcode" => 0,
		"comment_hl_description_before_enable" => 0,
		"comment_hl_stylesheet_enable" => 0,
		"comment_quicktag" => 1,
		"comment_jquery" => 0,
		"wiget_tag" => "pre",
		"wiget_shorcode" => 0,
		"bbpress_hl_bt_enable" => 0,
		"bbpress_hl_bt_tag" => "pre",
		"bbpress_hl_bt_shorcode" => 0,
		"bbpress_hl_bt_guest" => 0,
		"bbpress_hl_description_before_enable" => 0,
		"bbpress_hl_stylesheet_enable" => 0,
		);
	// For WordPress 3.0.6 or older
	if (version_compare(get_bloginfo('version'), "3.0.6", "<=")) {
		$wp_sh_setting_opt['quicktag'] = 0;
	}

	// For WordPress 3.2.1 or older
	if (version_compare(get_bloginfo('version'), "3.2.1", "<=")) {
		$wp_sh_setting_opt['comment_quicktag'] = 0;
	}

	// Store in DB
	add_option('wp_sh_version', '3.0'); // For backward compatibility
	add_option('wp_sh_gutter', 1); // For backward compatibility
	add_option('wp_sh_first_line', '1'); // For backward compatibility
	add_option('wp_sh_setting_opt', $wp_sh_setting_opt);
	add_option('wp_sh_code_title', '');
	add_option('wp_sh_class_name', wp_sh_default_setting_value('class'));
	add_option('wp_sh_addl_style', wp_sh_default_setting_value('style'));
	add_option('wp_sh_collapse_lable_text', '');
	add_option('wp_sh_comment_hl_description_before', wp_sh_default_setting_value('comment_desc'));
	add_option('wp_sh_comment_hl_stylesheet', wp_sh_default_setting_value('comment_style'));
	add_option('wp_sh_bbpress_hl_description_before', wp_sh_default_setting_value('bbp_desc'));
	add_option('wp_sh_bbpress_hl_stylesheet', wp_sh_default_setting_value('bbp_style'));
	add_option('wp_sh_updated', 'false');
	add_option('wp_sh_child_plugin');
}

// Check DB version and register settings
add_action('plugins_loaded', 'wp_sh_check_db_ver');

function wp_sh_check_db_ver() {
	global $wp_sh_db_ver, $wp_sh_setting_opt;
	$current_checkver_stamp = get_option('wp_sh_checkver_stamp');
	if (!$current_checkver_stamp || version_compare($current_checkver_stamp, $wp_sh_db_ver, "!=")) {
		$updated_count = 0;
		$migration_count = 0;
		// Register languages when new installation, updated from ver 1.2.3 or older
		if (!$current_checkver_stamp || version_compare($current_checkver_stamp, "1.3", "<")) {
			wp_sh_language_array();
			$updated_count = $updated_count + 1;
		}
		// Register settings when new installation, updated from ver 1.5 or older
		if (!is_array($wp_sh_setting_opt)) {
			wp_sh_setting_array();
			$updated_count = $updated_count + 1;
		}
		// Setting data migration when updated from ver.1.5 or older
		if (!$current_checkver_stamp || version_compare($current_checkver_stamp, "1.5", "<=")) {
			if (get_option('wp_sh_theme')) {
				include_once('data-migration.php');
				$migration_count = $updated_count + 1;
				// Delete un-used options before ver.1.5
				include_once('del-old-options.php');
			}
		}
		// If possible, update to current default values
		if (!$current_checkver_stamp || version_compare($current_checkver_stamp, "1.3.9", "<")) {
			// This option is available since ver 1.0 and updated in ver. 1.3.9
			if (get_option('wp_sh_class_name') == '') {
				update_option('wp_sh_class_name', wp_sh_default_setting_value('class'));
				$updated_count = $updated_count + 1;
			}
		}
		if (version_compare($current_checkver_stamp, "1.3", ">=") && version_compare($current_checkver_stamp, "1.5", "<")) {
			// This option is available since ver. 1.3.5 and updated in ver. 1.5
			if ($wp_sh_setting_opt['addl_style_enable'] == 0) {
				update_option('wp_sh_addl_style', wp_sh_default_setting_value('style'));
				$updated_count = $updated_count + 1;
			}
		}
		if (version_compare($current_checkver_stamp, "1.5", "==")) {
			// This option is available since ver. 1.5 and updated in ver. 1.5.5
			if ($wp_sh_setting_opt['comment_hl_description_before_enable'] == 0) {
				update_option('wp_sh_comment_hl_description_before', wp_sh_default_setting_value('comment_desc'));
				$updated_count = $updated_count + 1;
			}
			if ($wp_sh_setting_opt['comment_hl_stylesheet_enable'] == 0) {
				update_option('wp_sh_comment_hl_stylesheet', wp_sh_default_setting_value('comment_style'));
				$updated_count = $updated_count + 1;
			}
		}
		// Add new setting options when updated from ver.1.5.8 or older
		if ($current_checkver_stamp && version_compare($current_checkver_stamp, "1.5.5", "<=")) {
			if (version_compare(get_bloginfo('version'), "3.0.6", "<=")) {
				$wp_sh_setting_opt['quicktag'] = 0;
			} else {
				$wp_sh_setting_opt['quicktag'] = 1;
			}
			update_option('wp_sh_setting_opt', $wp_sh_setting_opt);
			$updated_count = $updated_count + 1;
		}
		// Add new setting options when updated from ver.1.6 or older
		if ($current_checkver_stamp && version_compare($current_checkver_stamp, "1.6", "<=")) {
			$wp_sh_setting_opt['quicktag_jquery'] = 0;
			// Correct a typo
			if ($wp_sh_setting_opt['theme'] == "Randam") {
				$wp_sh_setting_opt['theme'] = "Random";
			}
			update_option('wp_sh_setting_opt', $wp_sh_setting_opt);
			$updated_count = $updated_count + 1;
		}
		// Add new setting options when updated from ver.1.6.5 or older
		if ($current_checkver_stamp && version_compare($current_checkver_stamp, "1.6.5", "<=")) {
			$wp_sh_setting_opt['quick_code'] = "true";
			update_option('wp_sh_setting_opt', $wp_sh_setting_opt);
			$updated_count = $updated_count + 1;
		}
		// Add new setting options when updated from ver.1.6.7 or older
		if ($current_checkver_stamp && version_compare($current_checkver_stamp, "1.6.7", "<=")) {
			$wp_sh_setting_opt['editor_shorcode'] = 0;
			$wp_sh_setting_opt['editor_no_unfiltered_html'] = 0;
			$wp_sh_setting_opt['comment_hl_bt_tag'] = "pre";
			$wp_sh_setting_opt['comment_hl_bt_shorcode'] = 0;
			if (version_compare(get_bloginfo('version'), "3.2.1", "<=")) {
				$wp_sh_setting_opt['comment_quicktag'] = 0;
			} else {
				$wp_sh_setting_opt['comment_quicktag'] = 1;
			}
			$wp_sh_setting_opt['comment_jquery'] = 0;
			$wp_sh_setting_opt['wiget_tag'] = "pre";
			$wp_sh_setting_opt['wiget_shorcode'] = 0;
			$wp_sh_setting_opt['highlight_bbpress'] = 0;
			$wp_sh_setting_opt['bbpress_hl_bt_enable'] = 0;
			$wp_sh_setting_opt['bbpress_hl_bt_tag'] = "pre";
			$wp_sh_setting_opt['bbpress_hl_bt_shorcode'] = 0;
			$wp_sh_setting_opt['bbpress_hl_bt_guest'] = 0;
			$wp_sh_setting_opt['bbpress_hl_description_before_enable'] = 0;
			$wp_sh_setting_opt['bbpress_hl_stylesheet_enable'] = 0;
			update_option('wp_sh_setting_opt', $wp_sh_setting_opt);
			add_option('wp_sh_bbpress_hl_description_before', wp_sh_default_setting_value('bbp_desc'));
			add_option('wp_sh_bbpress_hl_stylesheet', wp_sh_default_setting_value('bbp_style'));
			$updated_count = $updated_count + 1;
		}
		update_option('wp_sh_checkver_stamp', $wp_sh_db_ver);
		// Stamp for showing messages
		if ($updated_count != 0 && $migration_count == 0) {
			update_option('wp_sh_updated', 'true');
		} elseif ($migration_count != 0) {
			update_option('wp_sh_updated', 'migration');
		}
	}
}

// Show plugin info in the setting panel footer
function wp_sh_add_admin_footer() {
	$wp_sh_plugin_data = get_plugin_data(__FILE__);
	printf('%1$s by %2$s<br />', $wp_sh_plugin_data['Title'].' '.$wp_sh_plugin_data['Version'], $wp_sh_plugin_data['Author']);
}

// Register link to the setting panel
add_filter('plugin_action_links', 'wp_sh_setting_link', 10, 2);

function wp_sh_setting_link($links, $file) {
	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
	if ($file == $this_plugin){
		$settings_link = '<a href="options-general.php?page=wp-syntaxhighlighter-options">'.__('Settings', 'wp_sh').'</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}

// Add SyntaxHighlighter TinyMCE Buttons into Visual Editor
if ($wp_sh_setting_opt['select_insert'] == 1 || $wp_sh_setting_opt['codebox'] == 1) {

	// Load SyntaxHighlighter TinyMCE Buttons
	if ($wp_sh_setting_opt['select_insert'] == 1) {
		include_once('sh-tinymce-button-ins/sh-tinymce-button-ins.php');
	}
	if ($wp_sh_setting_opt['codebox'] == 1) {
		include_once('sh-tinymce-button-box/sh-tinymce-button-box.php');
	}

	// Allow tab to indent in TinyMCE
	add_filter('tiny_mce_before_init', 'wp_sh_shtb_allow_tab');

	function wp_sh_shtb_allow_tab($initArray) {
		$initArray['plugins'] = preg_replace("|[,]+tabfocus|i", "", $initArray['plugins']);
		return $initArray;
	}

	// Register tags and attribtes as TinyMCE valid_elements
	add_filter('tiny_mce_before_init', 'wp_sh_shtb_mce_valid_elements');

	function wp_sh_shtb_mce_valid_elements($init) {
		if (isset($init['extended_valid_elements']) && !empty($init['extended_valid_elements'])) {
			$init['extended_valid_elements'] .= ',' . 'pre[name|class]';
			$init['extended_valid_elements'] .= ',' . 'textarea[name|class|cols|rows]';
		} else {
			$init['extended_valid_elements'] = 'pre[name|class]';
			$init['extended_valid_elements'] .= ',' . 'textarea[name|class|cols|rows]';
		}
		return $init;
	}

	// Load stylesheet for fullscreen mode
	if (version_compare(get_bloginfo('version'), "3.2", ">=")) {
		add_action('admin_print_styles-post.php', 'wp_sh_editor_style');
		add_action('admin_print_styles-post-new.php', 'wp_sh_editor_style');
		add_action('admin_print_styles-page.php', 'wp_sh_editor_style');
		add_action('admin_print_styles-page-new.php', 'wp_sh_editor_style');

		function wp_sh_editor_style() {
			global $wp_sh_plugin_url;
			if (current_user_can('unfiltered_html')) {
				wp_enqueue_style('wpsh-editor', $wp_sh_plugin_url.'css/wpsh_fullscreen.css', false, '1.1');
			}
		}

	}
}

// Filters for editor
add_action('init', 'wp_sh_editor_filters');

function wp_sh_editor_filters() {
	global $wp_sh_setting_opt;
	// Apply shortcode parser
	if ($wp_sh_setting_opt['editor_shorcode'] == 1) {
		add_filter('the_content', 'wp_sh_do_shortcode', 0);
	}
	// For users with unfiltered_html cap
	if (current_user_can('unfiltered_html')) {
		// Escape code to HTML entities
		add_filter('content_save_pre', 'wp_sh_escape_code', 0);
	}
	// For users without unfiltered_html cap
	if (($wp_sh_setting_opt['editor_no_unfiltered_html'] == 1 || $wp_sh_setting_opt['highlight_bbpress'] == 1) && !current_user_can('unfiltered_html')) {
		// Escape code to HTML entities
		add_filter('content_save_pre', 'wp_sh_escape_code', 0);
		// Apply substitute wp_filter_post_kses filter when saved
		remove_filter('content_save_pre', 'wp_filter_post_kses');
		add_filter('content_save_pre', 'wp_sh_wp_filter_kses');
	}
	// Replaced marker with escaped <pre> when saved
	add_filter('content_save_pre', 'wp_sh_replace_marker', 1);
	// Add extra "[]" into shortcode when shown
	if ($wp_sh_setting_opt['editor_shorcode'] == 1) {
		add_filter('the_content', 'wp_sh_add_extra_bracket', -1);
	}
}

// Escape code to HTML entities when saved
function wp_sh_escape_code($content) {
	$content = preg_replace_callback('|(\[sourcecode[^\]]*?\])(.*?)(\[/sourcecode\])|is', 'wp_sh_escape_code_callback', $content);
	$content = preg_replace_callback('|(<pre[^>]*?>)(.*?)(</pre>)|is', 'wp_sh_escape_code_callback', $content);
	return $content;
}

function wp_sh_escape_code_callback($matches) {
	$code = $matches[2];
	if (preg_match('|\[sourcecode[^\]]*?\].*?\[/sourcecode\]|is', $code)) {
		$replaced_code = preg_replace_callback('|^(.*\[sourcecode[^\]]*?\])(.*?)(\[/sourcecode\].*)$|is', 'wp_sh_escape_code_sub_callback', $code);
		return $matches[1].$replaced_code.$matches[3];
	}
	if (strpos($code, "<") !== false || strpos($code, ">") !== false || strpos($code, '"') !== false || strpos($code, "'") !== false || preg_match('/&(?!lt;)(?!gt;)(?!amp;)(?!quot;)(?!#039;)/i', $code)) {
		if (strpos($code, "<") === false && strpos($code, ">") === false && !preg_match('/&(?!lt;)(?!gt;)(?!amp;)(?!quot;)(?!#039;)/i', $code)) {
			$pre_replaced_code = str_replace('"', '&quot;', $code);
			$replaced_code = $matches[1].str_replace("'", '&#039;', $pre_replaced_code).$matches[3];
		} else {
			$replaced_code = $matches[1].htmlspecialchars($code, ENT_QUOTES, 'UTF-8').$matches[3];
		}
	} else {
		$replaced_code = $matches[1].$code.$matches[3];
	}
	return $replaced_code;
}

function wp_sh_escape_code_sub_callback($matches) {
	$element1 = $matches[1];
	$element2 = $matches[2];
	$element3 = $matches[3];
	if (strpos($element1, "<") !== false || strpos($element1, ">") !== false || strpos($element1, '"') !== false || strpos($element1, "'") !== false || preg_match('/&(?!lt;)(?!gt;)(?!amp;)(?!quot;)(?!#039;)/i', $element1)) {
		if (strpos($element1, "<") === false && strpos($element1, ">") === false && !preg_match('/&(?!lt;)(?!gt;)(?!amp;)(?!quot;)(?!#039;)/i', $element1)) {
			$pre_replaced_element1 = str_replace('"', '&quot;', $element1);
			$replaced_element1 = str_replace("'", '&#039;', $pre_replaced_element1);
		} else {
			$replaced_element1 = htmlspecialchars($element1, ENT_QUOTES, 'UTF-8');
		}
	} else {
		$replaced_element1 = $element1;
	}
	if (strpos($element3, "<") !== false || strpos($element3, ">") !== false || strpos($element3, '"') !== false || strpos($element3, "'") !== false || preg_match('/&(?!lt;)(?!gt;)(?!amp;)(?!quot;)(?!#039;)/i', $element3)) {
		if (strpos($element3, "<") === false && strpos($element3, ">") === false && !preg_match('/&(?!lt;)(?!gt;)(?!amp;)(?!quot;)(?!#039;)/i', $element3)) {
			$pre_replaced_element3 = str_replace('"', '&quot;', $element3);
			$replaced_element3 = str_replace("'", '&#039;', $pre_replaced_element3);
		} else {
			$replaced_element3 = htmlspecialchars($element3, ENT_QUOTES, 'UTF-8');
		}
	} else {
		$replaced_element3 = $element3;
	}
	return $replaced_element1.$element2.$replaced_element3;
}

// Substitute wp_filter_kses and wp_filter_post_kses
function wp_sh_wp_filter_kses($content) {
	if(!preg_match('|\[sourcecode[^\]]*?\].*?\[/sourcecode\]|is', $content) && !preg_match('|<pre[^>]*?>.*?</pre>|is', $content)) {
		if(current_filter() == "content_save_pre" || current_filter() == "content_filtered_save_pre") {
			return wp_filter_post_kses($content);
		} else {
			return wp_filter_kses($content);
		}
	}

	$content = preg_replace_callback('|(\[sourcecode[^\]]*?\])(.*?)(\[/sourcecode\])|is', 'wp_sh_wp_filter_kses_before_callback', $content);
	$content = preg_replace_callback('|(<pre[^>]*?>)(.*?)(</pre>)|is', 'wp_sh_wp_filter_kses_before_callback', $content);

	if(current_filter() == "content_save_pre" || current_filter() == "content_filtered_save_pre") {
		$content = wp_filter_post_kses($content);
	} else {
		$content = wp_filter_kses($content);
	}

	$content = preg_replace_callback('|(\[sourcecode[^\]]*?\])(.*?)(\[/sourcecode\])|is', 'wp_sh_wp_filter_kses_after_callback', $content);
	$content = preg_replace_callback('|(<pre[^>]*?>)(.*?)(</pre>)|is', 'wp_sh_wp_filter_kses_after_callback', $content);

	return $content;
}

function wp_sh_wp_filter_kses_before_callback($matches) {
	$replaced_code = preg_replace("/&amp;#([0-9A-Fa-f]{2,4});/is", "&lt;!--[$1]--&gt;", $matches[2]);
	return $matches[1].$replaced_code.$matches[3];
}

function wp_sh_wp_filter_kses_after_callback($matches) {
	$replaced_code = preg_replace('/&lt;!--\[([0-9A-Fa-f]{2,4})\]--&gt;/is', "&amp;#$1;", $matches[2]);
	return $matches[1].$replaced_code.$matches[3];
}

// Substitute make_clickable
function wp_sh_make_clickable($content) {
	if (!preg_match('|(\[sourcecode[^\]]*?\])(.*?)(\[/sourcecode\])|is', $content) && !preg_match('|(<pre[^>]*?>)(.*?)(</pre>)|is', $content)) {
		$content = make_clickable($content);
	}
	return $content;
}

// Replaced marker with escaped <pre> when saved
function wp_sh_replace_marker($content) {
	$content = preg_replace_callback('|(<pre[^>]*?>)(.*?)(</pre>)|is', 'wp_sh_replace_marker_callback', $content);
	return $content;
}

function wp_sh_replace_marker_callback($matches) {
	$code = $matches[2];
	$replaced_code = $matches[1].str_replace('&lt;!--[/pre]--&gt;', '&lt;/pre&gt;', $code).$matches[3];
	return $replaced_code;
}

// Add extra "[]" into shortcode
function wp_sh_add_extra_bracket($content) {
	$content = preg_replace_callback('|(<pre[^>]*?>)(.*?)(</pre>)|is', 'wp_sh_add_extra_bracket_callback', $content);
	return $content;
}

function wp_sh_add_extra_bracket_callback($matches) {
	$code = $matches[2];
	$replaced_code = preg_replace('|(\[source[^\]]*?\].*?\[/source[^\]]*?\])|is', '[$1]', $code);
	$replaced_code = preg_replace('|(\[code[^\]]*?\].*?\[/code\])|is', '[$1]', $replaced_code);
	$replaced_code = $matches[1].$replaced_code.$matches[3];
	return $replaced_code;
}

// Add quick tag button into HTML/Comment Editor
add_action('init', 'wp_sh_load_quicktag');

function wp_sh_load_quicktag() {
	global $wp_sh_setting_opt;
	if ($wp_sh_setting_opt['quicktag'] == 1 || $wp_sh_setting_opt['highlight_comment'] == 1) {
		include_once('sh-pre-quicktag.php');
	
	}
}

// Highlighting in the comments
if ($wp_sh_setting_opt['highlight_comment'] == 1) {
	include_once('comment-highlight.php');
	// Allow addtional tags and attributes in the comments
	add_filter('comments_open', 'wp_sh_allow_tags_and_attribs');
	add_filter('pre_comment_approved', 'wp_sh_allow_tags_and_attribs');
	// Apply shortcode parser
	if ($wp_sh_setting_opt['comment_hl_bt_shorcode'] == 1 || $wp_sh_setting_opt['comment_hl_bt_tag'] == "shortcode") {
		add_filter('comment_text', 'wp_sh_do_shortcode', 0);
	}
}

// Load WP SyntaxHighlighter Widget
if ($wp_sh_setting_opt['highlight_widgets'] == 1) {
	include_once('wp-syntaxhighlighter-widget.php');

	add_action('admin_print_scripts-widgets.php', 'wp_sh_load_script_for_widget');
	// Load script for widget section
	function wp_sh_load_script_for_widget() {
		global $wp_sh_plugin_url;
		wp_enqueue_script('rc_textarea_hl_js', $wp_sh_plugin_url.'js/rc-textarea-hl.js', false, '1.1');
	}

}

// Highlighting in the bbPress
if ($wp_sh_setting_opt['highlight_bbpress'] == 1) {
	include_once('bbpress-highlight.php');
	// Allow addtional tags and attributes in the bbPress
	if ($wp_sh_setting_opt['bbpress_hl_bt_enable'] == 1 && $wp_sh_setting_opt['bbpress_hl_bt_tag'] == "pre") {
		add_action('bbp_init', 'wp_sh_allow_tags_in_bbpress');

		function wp_sh_allow_tags_in_bbpress() {
			global $wp_sh_setting_opt;
			if (($wp_sh_setting_opt['bbpress_hl_bt_guest'] == 1 && bbp_is_anonymous()) || !bbp_is_anonymous()) {
				$bbp_root = get_option('_bbp_root_slug');
				if (get_option('_bbp_include_root') == true) {
					$bbp_forum = "/".get_option('_bbp_root_slug')."/".get_option('_bbp_forum_slug')."/";
					$bbp_topic = "/".get_option('_bbp_root_slug')."/".get_option('_bbp_topic_slug')."/";
					$bbp_reply = "/".get_option('_bbp_root_slug')."/".get_option('_bbp_reply_slug')."/";
				} else {
					$bbp_forum = "/".get_option('_bbp_forum_slug')."/";
					$bbp_topic = "/".get_option('_bbp_topic_slug')."/";
					$bbp_reply = "/".get_option('_bbp_reply_slug')."/";
				}
				if (strpos($_SERVER['REQUEST_URI'], '?post_type=forum') ||
					strpos($_SERVER['REQUEST_URI'], '?forum=') ||
					strpos($_SERVER['REQUEST_URI'], '?topic=') ||
					strpos($_SERVER['REQUEST_URI'], '?reply=') ||
					strpos($_SERVER['REQUEST_URI'], $bbp_root) ||
					strpos($_SERVER['REQUEST_URI'], $bbp_forum) ||
					strpos($_SERVER['REQUEST_URI'], $bbp_topic) ||
					strpos($_SERVER['REQUEST_URI'], $bbp_reply)
				) {
					add_filter('bbp_get_allowed_tags', 'wp_sh_allow_tags_and_attribs');
					bbp_get_allowed_tags();

					if (!current_user_can('unfiltered_html')) {
						wp_sh_allow_posttags_and_attribs();
					}
				}
			}
		}
	}

	// Apply shortcode parser
	if ($wp_sh_setting_opt['bbpress_hl_bt_shorcode'] == 1 || $wp_sh_setting_opt['bbpress_hl_bt_tag'] == "shortcode") {
		add_action('bbp_init', 'wp_sh_bbpress_register_shortcode');

		function wp_sh_bbpress_register_shortcode() {
			global $wp_sh_setting_opt;
			add_filter('bbp_get_topic_content', 'wp_sh_do_shortcode', 0);
			add_filter('bbp_get_reply_content', 'wp_sh_do_shortcode', 0);
		}

	}
}

// Add allowed posttags to editor for users without unfiltered_html cap
add_action('init', 'wp_sh_add_allowposttags_editor');

function wp_sh_add_allowposttags_editor() {
	global $wp_sh_setting_opt;
	if (($wp_sh_setting_opt['editor_no_unfiltered_html'] == 1 || $wp_sh_setting_opt['highlight_bbpress'] == 1) && !current_user_can('unfiltered_html')) {
		if (strpos($_SERVER['REQUEST_URI'], 'post.php') ||
			strpos($_SERVER['REQUEST_URI'], 'post-new.php') ||
			strpos($_SERVER['REQUEST_URI'], 'page.php') ||
			strpos($_SERVER['REQUEST_URI'], 'page-new.php')
		) {
			wp_sh_allow_posttags_and_attribs();
		}
	}
}

// Allow addtional tags and attributes
function wp_sh_allow_tags_and_attribs($data) {
	global $allowedtags;
	$allowedtags['pre'] = array(
				'class'=>array());
	return $data;
}

function wp_sh_allow_posttags_and_attribs() {
	global $allowedposttags;
	define('CUSTOM_TAGS', true);
	$allowedposttags['pre'] = array(
				'class'=>array(),
				'style' => array(),
				'width' => array ());
}

// Register shortcode
if ($wp_sh_setting_opt['editor_shorcode'] == 1 || $wp_sh_setting_opt['comment_hl_bt_shorcode'] == 1 || $wp_sh_setting_opt['wiget_shorcode'] == 1 || $wp_sh_setting_opt['bbpress_hl_bt_shorcode'] == 1) {
	include_once('wp-sh-shortcode.php');
}

// Load additional style sheet
if ($wp_sh_setting_opt['addl_style_enable'] == 1) {
	add_action('wp_head', 'wp_sh_load_addl_style');

	function wp_sh_load_addl_style() {
		global $wp_sh_ver, $wp_sh_setting_opt;
		if ($wp_sh_setting_opt['addl_style_enable'] == 1 && ((is_home() && $wp_sh_setting_opt['highlight_home'] == 1) ||
			((is_single() || is_page()) && $wp_sh_setting_opt['highlight_posts'] == 1) ||
			(is_category() && $wp_sh_setting_opt['highlight_categories'] == 1) ||
			(is_archive() && $wp_sh_setting_opt['highlight_archives'] == 1) ||
			(is_search() && $wp_sh_setting_opt['highlight_search'] == 1) ||
			(comments_open() && $wp_sh_setting_opt['highlight_comment'] == 1) ||
			($wp_sh_setting_opt['highlight_widgets'] == 1 && is_active_widget(false, false, 'wpsyntaxhighlighterwidget', true)) ||
			((is_single() || is_archive()) && $wp_sh_setting_opt['highlight_bbpress'] == 1) ||
			(!is_home() && !is_single() && !is_page() && !is_category() && !is_archive() && !is_search() && !is_admin() && $wp_sh_setting_opt['highlight_others'] == 1) ||
			is_admin())
		) {
			echo "\n<!-- WP SyntaxHighlighter Ver.".$wp_sh_ver." CSS for code Begin -->\n";
			if (isset($_POST['WP_SH_Setting_submit']) && $_POST['wp_sh_hidden_value'] == "true") {
				$wp_sh_addl_style = wp_sh_valid_css(strip_tags(stripslashes($_POST['wp_sh_addl_style'])));
				if ($wp_sh_addl_style == "invalid") {
					$wp_sh_addl_style = "";
				}
				echo "<style type='text/css'>\n".$wp_sh_addl_style."\n</style>\n";
			} else {
				$wp_sh_addl_style = wp_sh_valid_css(strip_tags(get_option('wp_sh_addl_style')));
				if ($wp_sh_addl_style == "invalid") {
					$wp_sh_addl_style = "";
				}
				echo "<style type='text/css'>\n".$wp_sh_addl_style."\n</style>\n";
			}
			echo "<!-- WP SyntaxHighlighter Ver.".$wp_sh_ver." CSS for code End -->\n";
		}
	}

}

// Remove CDATA tag
add_filter('the_content', 'wp_sh_post_on_print', 1);

function wp_sh_post_on_print($content) {
	$content = preg_replace("|(<script[^>]*?>)<!\[CDATA\[(.*?)\]\]></script>|is", '$1$2</script>', $content);
	return $content;
}

// Load css for SyntaxHighlighter on the header
add_action('wp_print_styles', 'wp_sh_load_style');

function wp_sh_load_style() {
	global $wp_sh_plugin_url, $wp_sh_setting_opt;
	if ((is_home() && $wp_sh_setting_opt['highlight_home'] == 1) ||
		((is_single() || is_page()) && $wp_sh_setting_opt['highlight_posts'] == 1) ||
		(is_category() && $wp_sh_setting_opt['highlight_categories'] == 1) ||
		(is_archive() && $wp_sh_setting_opt['highlight_archives'] == 1) ||
		(is_search() && $wp_sh_setting_opt['highlight_search'] == 1) ||
		(comments_open() && $wp_sh_setting_opt['highlight_comment'] == 1) ||
		($wp_sh_setting_opt['highlight_widgets'] == 1 && is_active_widget(false, false, 'wpsyntaxhighlighterwidget', true)) ||
		((is_single() || is_archive()) && $wp_sh_setting_opt['highlight_bbpress'] == 1) ||
		(!is_home() && !is_single() && !is_page() && !is_category() && !is_archive() && !is_search() && !is_admin() && $wp_sh_setting_opt['highlight_others'] == 1) ||
		is_admin()
	) {
		$wp_sh_theme_list['Default'] = array('1', 'shCoreDefault.css', 'shThemeDefault.css');
		$wp_sh_theme_list['Django'] = array('2', 'shCoreDjango.css', 'shThemeDjango.css');
		$wp_sh_theme_list['Eclipse'] = array('3', 'shCoreEclipse.css', 'shThemeEclipse.css');
		$wp_sh_theme_list['Emacs'] = array('4', 'shCoreEmacs.css', 'shThemeEmacs.css');
		$wp_sh_theme_list['FadeToGrey'] = array('5', 'shCoreFadeToGrey.css', 'shThemeFadeToGrey.css');
		$wp_sh_theme_list['MDUltra'] = array('8', 'shCoreMDUltra.css', 'shThemeMDUltra.css');
		$wp_sh_theme_list['Midnight'] = array('6', 'shCoreMidnight.css', 'shThemeMidnight.css');
		$wp_sh_theme_list['RDark'] = array('7', 'shCoreRDark.css', 'shThemeRDark.css');
		$theme = $wp_sh_setting_opt['theme'];
		$wp_sh_lib_ver = $wp_sh_setting_opt['lib_version'];
		if ($wp_sh_lib_ver == "3.0") {
			$lib_dir = 'syntaxhighlighter3';
		} elseif ($wp_sh_lib_ver == "2.1") {
			$lib_dir = 'syntaxhighlighter2';
		}
		if ($wp_sh_setting_opt['css'] == 1) {
			wp_enqueue_style('pre-tag', $wp_sh_plugin_url.'css/pre.css', false, '1.0');
		}
		wp_enqueue_style('core'.$wp_sh_lib_ver, $wp_sh_plugin_url.$lib_dir.'/styles/shCore.css', false, $wp_sh_lib_ver);
		if ($theme == "Random") {
			if ($wp_sh_lib_ver == "3.0") {
				$theme_no = mt_rand(1, 8);
				foreach ($wp_sh_theme_list as $theme_name => $val) {
					if ($theme_no == $val[0]) {
						wp_enqueue_style('core-'.$theme_name.$wp_sh_lib_ver, $wp_sh_plugin_url.$lib_dir.'/styles/'.$val[1], false, $wp_sh_lib_ver);
						wp_enqueue_style('theme-'.$theme_name.$wp_sh_lib_ver, $wp_sh_plugin_url.$lib_dir.'/styles/'.$val[2], false, $wp_sh_lib_ver);
					}
				}
			}
			if ($wp_sh_lib_ver == "2.1") {
				$theme_no = mt_rand(1, 7);
				foreach ($wp_sh_theme_list as $theme_name => $val) {
					if ($theme_no == $val[0]) {
						wp_enqueue_style('theme-'.$theme_name.$wp_sh_lib_ver, $wp_sh_plugin_url.$lib_dir.'/styles/'.$val[2], false, $wp_sh_lib_ver);
					}
				}
			}
		} else {
			if ($wp_sh_lib_ver == "2.1" && $theme == "MDUltra") {
				wp_enqueue_style('core-'.$theme.$wp_sh_lib_ver, $wp_sh_plugin_url.$lib_dir.'/styles/shThemeDefault.css', false, $wp_sh_lib_ver);
			} else {
				if ($wp_sh_lib_ver == "3.0") {
					wp_enqueue_style('core-'.$theme.$wp_sh_lib_ver, $wp_sh_plugin_url.$lib_dir.'/styles/'.$wp_sh_theme_list[$theme][1], false, $wp_sh_lib_ver);
				}
				wp_enqueue_style('theme-'.$theme.$wp_sh_lib_ver, $wp_sh_plugin_url.$lib_dir.'/styles/'.$wp_sh_theme_list[$theme][2], false, $wp_sh_lib_ver);
			}
		}
		if ($wp_sh_lib_ver == "3.0") {
			do_action('wpsh_css_for_3'); // Action hook for developers
		} elseif ($wp_sh_lib_ver == "2.1") {
			do_action('wpsh_css_for_2'); // Action hook for developers
		}
	}
}

// Load scripts for SyntaxHighlighter on the footer
add_action('plugins_loaded', 'wp_sh_scripts_for_content');

// Run checking for content
function wp_sh_scripts_for_content() {
	add_action('wp_footer', 'wp_sh_check_valid_tag');
}

// When call this by widget method, write out scripts without checking
function wp_sh_load_scripts_by_shortcut() {
	add_action('wp_footer', 'wp_sh_load_scripts_on_footer');
}

// Determine whether valid tag is present
function wp_sh_check_valid_tag() {
	global $wp_sh_setting_opt, $post, $wp_query;
	$highlight = 0;
	// Search valid tag
	if ((is_home() && $wp_sh_setting_opt['highlight_home'] == 1) ||
		((is_single() || is_page()) && $wp_sh_setting_opt['highlight_posts'] == 1) ||
		(is_category() && $wp_sh_setting_opt['highlight_categories'] == 1) ||
		(is_archive() && $wp_sh_setting_opt['highlight_archives'] == 1) ||
		(is_search() && $wp_sh_setting_opt['highlight_search'] == 1) ||
		(!is_home() && !is_single() && !is_page() && !is_category() && !is_archive() && !is_search() && !is_admin() && $wp_sh_setting_opt['highlight_others'] == 1)) {
		foreach ((array)$wp_query->posts as $key => $post_properties) {
			if (preg_match("/(<pre[^>]*?brush:[^>]*?>)|(\[source[^\]]*?language=[^\]]*?\])|(\[code[^\]]*?language=[^\]]*?\])/i", $post_properties->post_content) || preg_match("/<script[^>]*?type=['\"]syntaxhighlighter['\"][^>]*?>/i", $post_properties->post_content)) {
				$highlight = 1;
				break;
			}
			if ($wp_sh_setting_opt['legacy'] == 1 && $highlight == 0) {
				if (preg_match("/<[(pre)(textarea)][^>]*?name=['\"]code['\"][^>]*?>/i", $post_properties->post_content)) {
					$highlight = 1;
					break;
				}
			}
		}
	}
	if (comments_open() && $wp_sh_setting_opt['highlight_comment'] == 1 && $highlight == 0) {
		$comments = get_comments(array('post_id' => $post->ID, 'status' => ''));
		foreach ($comments as $comment) {
			if (preg_match("/(<pre[^>]*?brush:[^>]*?>)|(\[source[^\]]*?language=[^\]]*?\])|(\[code[^\]]*?language=[^\]]*?\])/i", $comment->comment_content) || preg_match("/<script[^>]*?type=['\"]syntaxhighlighter['\"][^>]*?>/i", $comment->comment_content)) {
				$highlight = 1;
				break;
			}
			if ($wp_sh_setting_opt['legacy'] && $highlight == 0) {
				if (preg_match("/<[(pre)(textarea)][^>]*?name=['\"]code['\"][^>]*?>/i", $comment->comment_content)) {
					$highlight = 1;
					break;
				}
			}
		}
	}
	if ($wp_sh_setting_opt['highlight_archives'] != 1 && $wp_sh_setting_opt['highlight_bbpress'] == 1 && (function_exists('bbp_is_forum_archive') && bbp_is_forum_archive())) {
		$highlight = 1;
	}
	// Write out scripts
	if ($highlight == 1) {
		wp_sh_load_scripts_on_footer();
	} else {
		echo '';
	}
}

// Write out scripts for SyntaxHighlighter on footer
function wp_sh_load_scripts_on_footer() {
	global $wp_sh_ver, $wp_sh_plugin_url, $wp_sh_setting_opt;
	static $count = 0;
	$count++;
	if ($count == 1) {
		// Define values
		$wp_sh_lib_ver = $wp_sh_setting_opt['lib_version'];
		if ($wp_sh_lib_ver == "3.0") {
			$lib_dir = 'syntaxhighlighter3';
		} elseif ($wp_sh_lib_ver == "2.1") {
			$lib_dir = 'syntaxhighlighter2';
		}
		$auto_links = $wp_sh_setting_opt['auto_links'];
		$quick_code = $wp_sh_setting_opt['quick_code'];
		$title = str_replace("'", "&#039;", get_option('wp_sh_code_title'));
		$class_name = get_option('wp_sh_class_name');
		if (strpos($wp_sh_class_name, "\"") || strpos($wp_sh_class_name, "'")) {
			$class_name = "";
		}
		$collapse = $wp_sh_setting_opt['collapse'];
		$first_line = $wp_sh_setting_opt['first_line'];
		if (!preg_match("/^[0-9]+$/", $first_line)) {
			$wp_sh_setting_opt['first_line'] = "1";
		}
		$gutter = $wp_sh_setting_opt['gutter'];
		$padding_line = $wp_sh_setting_opt['padding_line'];
		$smart_tabs = $wp_sh_setting_opt['smart_tabs'];
		$tab_size = $wp_sh_setting_opt['tab_size'];
		if (!preg_match("/^[0-9]+$/", $tab_size)) {
			$wp_sh_setting_opt['tab_size'] = "4";
		}
		$toolbar = $wp_sh_setting_opt['toolbar'];
		$wrap = $wp_sh_setting_opt['wrap'];
		$legacy_enable = $wp_sh_setting_opt['legacy'];
		if ($legacy_enable == 1) {
			$legacy = "dp.SyntaxHighlighter.HighlightAll('code')";
		} else {
			$legacy = "";
		}
		$collapse_lable_text = str_replace("'", "&#039;", get_option('wp_sh_collapse_lable_text'));
		if ($wp_sh_lib_ver == "3.0") {
			if ($collapse_lable_text != "" && $title == "") {
				$collapse_lable = $collapse_lable_text;
			} else {
				$collapse_lable = __('+ expand source', 'wp_sh');
			}
		} elseif ($wp_sh_lib_ver == "2.1") {
			if ($collapse_lable_text != "") {
				$collapse_lable = $collapse_lable_text;
			} else {
				$collapse_lable = __('show source', 'wp_sh');
			}
		}
		// Write out
		echo "\n<!-- WP SyntaxHighlighter Ver.".$wp_sh_ver." Begin -->\n";
		echo "<script type=\"text/javascript\" src=\"".$wp_sh_plugin_url.$lib_dir."/scripts/shCore.js?ver=".$wp_sh_lib_ver."\"></script>\n";
		if ($legacy_enable == 1) {
			echo "<script type=\"text/javascript\" src=\"".$wp_sh_plugin_url.$lib_dir."/scripts/shLegacy.js?ver=".$wp_sh_lib_ver."\"></script>\n";
		}
		$wp_sh_brush_files = get_option('wp_sh_brush_files');
		if ($wp_sh_lib_ver == "3.0" && $legacy_enable == 0) {
			echo "<script type=\"text/javascript\" src=\"".$wp_sh_plugin_url.$lib_dir."/scripts/shAutoloader.js?ver=".$wp_sh_lib_ver."\"></script>\n";
			if ($wp_sh_brush_files[XML][3] == "true" || is_admin()) {
				echo "<script type=\"text/javascript\" src=\"".$wp_sh_plugin_url.$lib_dir."/scripts/shBrushXml.js?ver=".$wp_sh_lib_ver."\"></script>\n";
			}
		} elseif ($wp_sh_lib_ver == "3.0" && $legacy_enable == 1) {
			echo "<script type=\"text/javascript\" src=\"".$wp_sh_plugin_url.$lib_dir."/scripts/shAutoloader.js?ver=".$wp_sh_lib_ver."\"></script>\n";
			foreach ($wp_sh_brush_files as $lang => $val) {
				$brush_file = $val[0];
				$brush_ver = $val[2];
				$brush_enable = $val[3];
				if (($brush_ver == '3.0' || $brush_ver == '2.1' || $brush_ver == '1.5') && $brush_enable == 'true') {
					echo "<script type=\"text/javascript\" src=\"".$wp_sh_plugin_url.$lib_dir."/scripts/".$brush_file."?ver=".$wp_sh_lib_ver."\"></script>\n";
				} elseif (($brush_ver == '3.0' || $brush_ver == 'all') && $brush_enable == 'added') {
					echo "<script type=\"text/javascript\" src=\"".$brush_file."?ver=".$wp_sh_lib_ver."\"></script>\n";
				}
			}
			if (is_admin() && $wp_sh_brush_files[XML][3] == "false") {
				echo "<script type=\"text/javascript\" src=\"".$wp_sh_plugin_url.$lib_dir."/scripts/shBrushXml.js?ver=".$wp_sh_lib_ver."\"></script>\n";
			}
		} elseif ($wp_sh_lib_ver == "2.1") {
			$wp_sh_brush_files = get_option('wp_sh_brush_files');
			foreach ($wp_sh_brush_files as $lang => $val) {
				$brush_file = $val[0];
				$brush_ver = $val[2];
				$brush_enable = $val[3];
				if (($brush_ver == '2.1' || $brush_ver == '1.5') && $brush_enable == 'true') {
					echo "<script type=\"text/javascript\" src=\"".$wp_sh_plugin_url.$lib_dir."/scripts/".$brush_file."?ver=".$wp_sh_lib_ver."\"></script>\n";
				} elseif (($brush_ver == '2.1' || $brush_ver == 'all') && $brush_enable == 'added') {
					echo "<script type=\"text/javascript\" src=\"".$brush_file."?ver=".$wp_sh_lib_ver."\"></script>\n";
				}
			}
			if (is_admin() && $wp_sh_brush_files[PHP][3] == "false") {
				echo "<script type=\"text/javascript\" src=\"".$wp_sh_plugin_url.$lib_dir."/scripts/shBrushPhp.js?ver=".$wp_sh_lib_ver."\"></script>\n";
			}
			if (is_admin() && $wp_sh_brush_files[XML][3] == "false") {
				echo "<script type=\"text/javascript\" src=\"".$wp_sh_plugin_url.$lib_dir."/scripts/shBrushXml.js?ver=".$wp_sh_lib_ver."\"></script>\n";
			}
			do_action('wpsh_brush_for_2'); // Action hook for developers
		}
		if ($wp_sh_lib_ver == "3.0") {
			echo "<script type=\"text/javascript\">//<![CDATA[
	SyntaxHighlighter.autoloader(\n";
			$wp_sh_brush_files = get_option('wp_sh_brush_files');
			$count = 0;
			foreach ($wp_sh_brush_files as $lang => $val) {
				$brush_file = $val[0];
				$brush_alias = $val[1];
				$brush_ver = $val[2];
				$brush_enable = $val[3];
				if ($brush_enable == 'true' || (is_admin() && $lang == "PHP")) {
					$count = $count + 1;
					if ($count == 1) {
						echo "	\"".$brush_alias."	".$wp_sh_plugin_url.$lib_dir."/scripts/".$brush_file."?ver=".$wp_sh_lib_ver."\"\n";
					} else {
						echo "	,\"".$brush_alias."	".$wp_sh_plugin_url.$lib_dir."/scripts/".$brush_file."?ver=".$wp_sh_lib_ver."\"\n";
					}
				} elseif (($brush_ver == '3.0' || $brush_ver == 'all') && $brush_enable == 'added') {
					$count = $count + 1;
					if ($count == 1) {
						echo "	\"".$brush_alias."	".$brush_file."?ver=".$wp_sh_lib_ver."\"\n";
					} else {
						echo "	,\"".$brush_alias."	".$brush_file."?ver=".$wp_sh_lib_ver."\"\n";
					}
				}
			}
			do_action('wpsh_brush_for_3'); // Action hook for developers
			echo "	);
	SyntaxHighlighter.defaults['auto-links'] = ".$auto_links.";
	SyntaxHighlighter.defaults['quick-code'] = ".$quick_code.";
	SyntaxHighlighter.defaults['title'] = '".$title."';
	SyntaxHighlighter.defaults['class-name'] = '".$class_name."';
	SyntaxHighlighter.defaults['collapse'] = ".$collapse.";
	SyntaxHighlighter.defaults['first-line'] = ".$first_line.";
	SyntaxHighlighter.defaults['gutter'] = ".$gutter.";
	SyntaxHighlighter.defaults['pad-line-numbers'] = ".$padding_line.";
	SyntaxHighlighter.defaults['smart-tabs'] = ".$smart_tabs.";
	SyntaxHighlighter.defaults['tab-size'] = ".$tab_size.";
	SyntaxHighlighter.defaults['toolbar'] = ".$toolbar.";\n";
			if ($legacy_enable == 1) {
				echo " SyntaxHighlighter.config.stripBrs = true;\n";
			}
			echo "	SyntaxHighlighter.config.strings.expandSource = '".$collapse_lable."';
	SyntaxHighlighter.config.strings.help = '".__( '?', 'wp_sh' )."';
	SyntaxHighlighter.config.strings.alert = '".__( 'SyntaxHighlighter\n\n', 'wp_sh' )."';
	SyntaxHighlighter.config.strings.noBrush = \"".__( 'Can\'t find brush for: ', 'wp_sh' )."\";
	SyntaxHighlighter.config.strings.brushNotHtmlScript = \"".__( 'Brush wasn\'t configured for html-script option: ', 'wp_sh' )."\";
	SyntaxHighlighter.all();
	".$legacy."
//]]></script>
<!-- WP SyntaxHighlighter Ver.".$wp_sh_ver." End -->\n";
		} elseif ($wp_sh_lib_ver == "2.1") {
			echo "<script type=\"text/javascript\">//<![CDATA[
	SyntaxHighlighter.config.clipboardSwf = '".$wp_sh_plugin_url.$lib_dir."/scripts/clipboard.swf';
	SyntaxHighlighter.defaults['auto-links'] = ".$auto_links.";
	SyntaxHighlighter.defaults['class-name'] = '".$class_name."';
	SyntaxHighlighter.defaults['collapse'] = ".$collapse.";
	SyntaxHighlighter.defaults['first-line'] = ".$first_line.";
	SyntaxHighlighter.defaults['gutter'] = ".$gutter.";
	SyntaxHighlighter.defaults['pad-line-numbers'] = ".$padding_line.";
	SyntaxHighlighter.defaults['smart-tabs'] = ".$smart_tabs.";
	SyntaxHighlighter.defaults['tab-size'] = ".$tab_size.";
	SyntaxHighlighter.defaults['toolbar'] = ".$toolbar.";
	SyntaxHighlighter.defaults['wrap-lines'] = ".$wrap.";\n";
			if ($legacy_enable == 1) {
				echo " SyntaxHighlighter.config.stripBrs = true;\n";
			}
			echo "	SyntaxHighlighter.config.strings.expandSource = '".$collapse_lable."';
	SyntaxHighlighter.config.strings.viewSource = '".__( 'view source', 'wp_sh' )."';
	SyntaxHighlighter.config.strings.copyToClipboard = '".__( 'copy to clipboard', 'wp_sh' )."';
	SyntaxHighlighter.config.strings.copyToClipboardConfirmation = '".__( 'The code is in your clipboard now', 'wp_sh' )."';
	SyntaxHighlighter.config.strings.print = '".__( 'print', 'wp_sh' )."';
	SyntaxHighlighter.config.strings.help = '".__( '?', 'wp_sh' )."';
	SyntaxHighlighter.config.strings.alert = '".__( 'SyntaxHighlighter\n\n', 'wp_sh' )."';
	SyntaxHighlighter.config.strings.noBrush = \"".__( 'Can\'t find brush for: ', 'wp_sh' )."\";
	SyntaxHighlighter.config.strings.brushNotHtmlScript = \"".__( 'Brush wasn\'t configured for html-script option: ', 'wp_sh' )."\";
	SyntaxHighlighter.all();
	".$legacy."
//]]></script>
<!-- WP SyntaxHighlighter Ver.".$wp_sh_ver." End -->\n";
		}
	} else {
		echo '';
	}
}

// Validate css format
function wp_sh_valid_css($css) {
	if (preg_match("/(http|https):\/\/.*?\.js/is", $css) ||
	preg_match("/src=['\"][^(http)(https)][^'\"]*?['\"]/is", $css) ||
	preg_match("/((java|vb)script:|about:)/is", $css) ||
	preg_match("/@i[^;]*?;/is", $css) ||
	preg_match("/(expression|behavi(o|ou)r|-moz-binding|include-source)/is", $css) ||
	preg_match("/document\.cookie/i", $css) ||
	preg_match("/eval\([^\)]*?\)/i", $css) ||
	preg_match("/on.{4,}?=/is", $css) ||
	preg_match("/&\{[^\}]*?\}/i", $css) ||
	preg_match("/\xhh/i", $css) ||
	preg_match("/\\\\[^'\"\{\};:\(\)#A\*]/i", $css)) {
		return "invalid";
	} else {
		$css = preg_replace("|\*/([^ ].?)|", "*/  $1", $css);
		return $css;
	}
}

// Validate free style text data
function wp_sh_valid_text($text, $level) {
	global $allowedposttags, $allowedtags;
	if ($level == "0") {
		return $text;
	} elseif ($level == "1") {
		if (preg_match("/<meta[^>]*?>/is", $text) ||
		preg_match("/<title[^>]*?>/is", $text) ||
		preg_match("/<plaintext[^>]*?>/is", $text) ||
		preg_match("/<marquee[^>]*?>/is", $text) ||
		preg_match("/<isindex[^>]*?>/is", $text) ||
		preg_match("/<xmp[^>]*?>/is", $text) ||
		preg_match("/<listing[^>]*?>/is", $text)) {
			return "invalid";
		} else {
			return $text;
		}
	} elseif ($level == "2") {
		if (preg_match("/<script[^>]*?>/is", $text) ||
		preg_match("/<input[^>]*?>/is", $text) ||
		preg_match("/<textarea[^>]*?>/is", $text) ||
		preg_match("/<\/textarea>/is", $text) ||
		preg_match("/<object[^>]*?>/is", $text) ||
		preg_match("/<applet[^>]*?>/is", $text) ||
		preg_match("/<embed[^>]*?>/i", $text) ||
		preg_match("/<table[^>]*?>/is", $text) ||
		preg_match("/<form[^>]*?>/is", $text) ||
		preg_match("/<meta[^>]*?>/is", $text) ||
		preg_match("/<title[^>]*?>/is", $text) ||
		preg_match("/<frame[^>]*?>/is", $text) ||
		preg_match("/<plaintext[^>]*?>/is", $text) ||
		preg_match("/<marquee[^>]*?>/is", $text) ||
		preg_match("/<isindex[^>]*?>/is", $text) ||
		preg_match("/<xmp[^>]*?>/is", $text) ||
		preg_match("/<listing[^>]*?>/is", $text) ||
		preg_match("/<body[^>]*?>/is", $text) ||
		preg_match("/<style[^>]*?>/is", $text) ||
		preg_match("/<link[^>]*?>/is", $text) ||
		preg_match("/on.{4,}?=/is", $text) ||
		preg_match("/background[^=]*?=/is", $text) ||
		preg_match("/(http|https):\/\/.*?\.js/is", $text)) {
			return "invalid";
		} else {
			return $text;
		}
	} elseif ($level == "3") {
		$filtered_text = wp_kses($text, $allowedposttags);
		if ($text != $filtered_text) {
			return "invalid";
		} else {
			return $filtered_text;
		}
	} elseif ($level == "4") {
		$filtered_text = wp_kses($text, $allowedtags);
		if ($text != $filtered_text) {
			return "invalid";
		} else {
			return $filtered_text;
		}
	} elseif ($level == "5") {
		$text = esc_html($text);
		return $text;
	} else {
		return $text;
	}
}

// Load setting panel
if (is_admin()) {
	include_once('wp-syntaxhighlighter-admin.php');
}

?>