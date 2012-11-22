<?php
/*
Plugin Name: Lang Pack for WP SyntaxHighlighter
Plugin URI: http://www.near-mint.com/blog/software
Description: This plugin will add new languages to "WP SyntaxHighlighter" plugin. For WP SyntaxHighlighter 1.4.3 or higher.
Version: 1.3
Author: redcocker
Author URI: http://www.near-mint.com/blog/
Text Domain: langpack_wpsh
Domain Path: /languages
*/
/*
Last modified: 2011/11/19
License: GPL v2(Except bundled brush and css files.)
*/
/*
If you are going to distribute this plugin to the public, you should change following function name to unique one.
Defined functions: add_nl_new_brush, add_nl_new_lang_to_buttons, add_nl_new_lang, add_nl_update_lang, add_nl_admin_notice, add_nl_css_v3 ,add_nl_css_v2, add_nl_deactive
*/

load_plugin_textdomain('langpack_wpsh', false, dirname(plugin_basename(__FILE__)).'/languages');
$add_nl_plugin_url = plugin_dir_url( __FILE__ );

// Customizable part1 begin.
$plugin_name = 'Lang_Pack_for_WP_SyntaxHighlighter'; // Plugin name without spaces.
$version = '1.3'; // Plugin version. When you modify this file, you must increase the version.
// Customizable part1 end.

function add_nl_new_brush() {
	global $add_nl_plugin_url, $wp_sh_brush_files;

	// Customizable part2 begin.
	// Add new brush files.
	// Format:
	//$wp_sh_brush_files['Language'] = array($add_nl_plugin_url.'File path', 'Space-separated aliases', 'Possible version', 'added');
	// Language: It must be a unique name.
	// Possible version: Possible SyntaxHighlighter version. Possible value is 3.0, 2.1 or all.

	$wp_sh_brush_files['Biferno'] = array($add_nl_plugin_url.'biferno/shBrushBiferno.js', 'biferno', '3.0', 'added');
	$wp_sh_brush_files['Clojure'] = array($add_nl_plugin_url.'clojure/shBrushClojure.js', 'clojure Clojure clj', 'all', 'added');
	$wp_sh_brush_files['DOS_batch_file-v2'] = array($add_nl_plugin_url.'dos-batch/shBrushDosBatch-V2.js', 'dosbatch batch', '2.1', 'added');
	$wp_sh_brush_files['DOS_batch_file-v3'] = array($add_nl_plugin_url.'dos-batch/shBrushDosBatch-V3.js', 'dosbatch batch', '3.0', 'added');
	$wp_sh_brush_files['F#'] = array($add_nl_plugin_url.'fsharp/shBrushFSharp.js', 'f# f-sharp fsharp', 'all', 'added');
	$wp_sh_brush_files['LISP'] = array($add_nl_plugin_url.'lisp/shBrushLisp.js', 'lisp', 'all', 'added');
	$wp_sh_brush_files['Lua'] = array($add_nl_plugin_url.'lua/shBrushLua.js', 'lua', '3.0', 'added'); // Only for SyntaxHighlighter 3.0.
	$wp_sh_brush_files['MEL'] = array($add_nl_plugin_url.'mel/shBrushMel.js', 'MEL mel', 'all', 'added');
	$wp_sh_brush_files['Objective-C'] = array($add_nl_plugin_url.'objective-c/shBrushObjC.js', 'obj-c objc', 'all', 'added');
	$wp_sh_brush_files['PowerCLI'] = array($add_nl_plugin_url.'powercli/shBrushPowerCLI.js', 'powercli pcli', 'all', 'added');
	$wp_sh_brush_files['Processing'] = array($add_nl_plugin_url.'processing/shBrushProcessing.js', 'Processing processing', 'all', 'added');
	$wp_sh_brush_files['R'] = array($add_nl_plugin_url.'r/shBrushR.js', 'r s splus', 'all', 'added');
	$wp_sh_brush_files['Tcl'] = array($add_nl_plugin_url.'tcl/shBrushTcl.js', 'tcl', 'all', 'added');
	$wp_sh_brush_files['Verilog'] = array($add_nl_plugin_url.'verilog/shBrushVerilog.js', 'verilog v', 'all', 'added');
	$wp_sh_brush_files['Vim'] = array($add_nl_plugin_url.'vim/shBrushVimscript.js', 'vim vimscript', 'all', 'added');
	$wp_sh_brush_files['YAML'] = array($add_nl_plugin_url.'yaml/shBrushYaml.js', 'yaml yml', 'all', 'added');

	// Customizable part2 end.

}

function add_nl_new_lang_to_buttons() {
	global $wp_sh_language3, $wp_sh_language2;
	// Customizable part3 begin.
	// Add new languages to language list at the click of buttons. For ver. 3.0.
	// Format:
	//$wp_sh_language3['Alias'] = array('Language name', 'added');
	//Language name: It must be a unique name. It will be appeared in language list at the click of buttons.

	$wp_sh_language3['biferno'] = array('Biferno', 'added');
	$wp_sh_language3['clojure'] = array('Clojure', 'added');
	$wp_sh_language3['dosbatch'] = array('DOS batch file', 'added');
	$wp_sh_language3['f#'] = array('F#', 'added');
	$wp_sh_language3['lisp'] = array('LISP', 'added');
	$wp_sh_language3['lua'] = array('Lua', 'added');
	$wp_sh_language3['mel'] = array('MEL', 'added');
	$wp_sh_language3['objc'] = array('Objective-C', 'added');
	$wp_sh_language3['powercli'] = array('PowerCLI', 'added');
	$wp_sh_language3['processing'] = array('Processing', 'added');
	$wp_sh_language3['r'] = array('R', 'added');
	$wp_sh_language3['s'] = array('S', 'added');
	$wp_sh_language3['splus'] = array('S-PLUS', 'added');
	$wp_sh_language3['tcl'] = array('Tcl', 'added');
	$wp_sh_language3['verilog'] = array('Verilog', 'added');
	$wp_sh_language3['vim'] = array('Vim Script', 'added');
	$wp_sh_language3['yaml'] = array('YAML', 'added');

	// Add new languages to language list at the click of buttons. For ver. 2.1.
	// Format:
	//$wp_sh_language2['Alias'] = array('Language name', 'added');
	//Language name: It must be a unique name. It will be appeared in language list at the click of buttons.

	$wp_sh_language2['clojure'] = array('Clojure', 'added');
	$wp_sh_language2['dosbatch'] = array('DOS batch file', 'added');
	$wp_sh_language2['f#'] = array('F#', 'added');
	$wp_sh_language2['lisp'] = array('LISP', 'added');
	$wp_sh_language2['mel'] = array('MEL', 'added');
	$wp_sh_language2['objc'] = array('Objective-C', 'added');
	$wp_sh_language2['powercli'] = array('PowerCLI', 'added');
	$wp_sh_language2['processing'] = array('Processing', 'added');
	$wp_sh_language2['r'] = array('R', 'added');
	$wp_sh_language2['s'] = array('S', 'added');
	$wp_sh_language2['splus'] = array('S-PLUS', 'added');
	$wp_sh_language2['tcl'] = array('Tcl', 'added');
	$wp_sh_language2['verilog'] = array('Verilog', 'added');
	$wp_sh_language2['vim'] = array('Vim Script', 'added');
	$wp_sh_language2['yaml'] = array('YAML', 'added');

	// Customizable part3 end.

}

// Customizable part4 begin.
// If any css for new languages files, add them.
// Format:
//$add_nl_css['Language'] = array('File Path', 'Possible Sversion');
// Language: Same value as "Add new brush files" part.
// Possible version: Possible SyntaxHighlighter version. Possible value is 3.0, 2.1 or all.

$add_nl_css['Processing'] = array('processing/shBrushProcessing.css', 'all');

// Customizable part4 end.


// No customizable part below.
// No need to edit below, but you can change only function name to unique one.

// Add new languages.
function add_nl_new_lang() {
	global $add_nl_plugin_url, $plugin_name, $version, $wp_sh_brush_files, $wp_sh_language3, $wp_sh_language2;
	$wp_sh_brush_files = get_option('wp_sh_brush_files');
	$wp_sh_language2 = get_option('wp_sh_language2');
	$wp_sh_language3 = get_option('wp_sh_language3');
	$wp_sh_child_plugin[$plugin_name] = $version;

	add_nl_new_brush();

	add_nl_new_lang_to_buttons();

	update_option('wp_sh_child_plugin', $wp_sh_child_plugin);
	update_option('wp_sh_brush_files', $wp_sh_brush_files);
	update_option('wp_sh_language3', $wp_sh_language3);
	update_option('wp_sh_language2', $wp_sh_language2);
}

// Check plugin version and update languages.
function add_nl_update_lang() {
	global $plugin_name, $version;
	if (function_exists('wp_sh_register_menu_item')) {
		$wp_sh_child_plugin = get_option('wp_sh_child_plugin');
		$wp_sh_checkver_stamp = get_option('wp_sh_checkver_stamp');
		if (!$wp_sh_checkver_stamp || version_compare($wp_sh_checkver_stamp, '1.4.3', "<")) {
			add_action('admin_notices', 'add_nl_admin_notice');
		} elseif (version_compare($wp_sh_checkver_stamp, '1.4.3', ">=")) {
			if (is_array($wp_sh_child_plugin)) {
				if ($wp_sh_child_plugin[$plugin_name] != null) {
					$plugin_version = $wp_sh_child_plugin[$plugin_name];
					if (version_compare($version, $plugin_version, "!=")) {
						add_nl_deactive();
						add_nl_new_lang();
					}
				} else {
					add_nl_new_lang();
				}
			} else {
				add_nl_new_lang();
			}
		}
	}
}

add_action('admin_menu', 'add_nl_update_lang', 9999);

// Notice for admin.
function add_nl_admin_notice(){
	echo '<div id="message" class="updated"><p>'.__('Lang Pack for WP SyntaxHighlighter requires WP SyntaxHighlighter ver. 1.4.3 or higher', 'langpack_wpsh').'</p></div>';
}

// Add css files for SyntaxHighlighter 3.0.
add_action('wpsh_css_for_3', 'add_nl_css_v3');

function add_nl_css_v3() {
	global $add_nl_plugin_url, $add_nl_css;
	if (is_array($add_nl_css)) {
		$wp_sh_brush_files = get_option('wp_sh_brush_files');
		foreach($add_nl_css as $id => $val){
			$css_file = $val[0];
			$css_ver = $val[1];
			if ($wp_sh_brush_files[$id][3] == 'added') {
				if ($css_ver == '3.0' || $css_ver == 'all') {
					wp_enqueue_style($id, $add_nl_plugin_url.$css_file, false, $css_ver);
				}
			}
		}
	}
}

// Add css files for SyntaxHighlighter 2.1.
add_action('wpsh_css_for_2', 'add_nl_css_v2');

function add_nl_css_v2() {
	global $add_nl_plugin_url, $add_nl_css;
	if (is_array($add_nl_css)) {
		$wp_sh_brush_files = get_option('wp_sh_brush_files');
		foreach($add_nl_css as $id => $val){
			$css_file = $val[0];
			$css_ver = $val[1];
			if ($wp_sh_brush_files[$id][3] == 'added') {
				if ($css_ver == '2.1' || $css_ver == 'all') {
					wp_enqueue_style($id, $add_nl_plugin_url.$css_file, false, $css_ver);
				}
			}
		}
	}
}

// When deactivated this plugin, added language will be cleared.
function add_nl_deactive() {
	global $plugin_name, $wp_sh_brush_files, $wp_sh_language3, $wp_sh_language2;
	$wp_sh_child_plugin_unset = get_option('wp_sh_child_plugin');
	$wp_sh_brush_files_unset = get_option('wp_sh_brush_files');
	$wp_sh_language3_unset = get_option('wp_sh_language3');
	$wp_sh_language2_unset = get_option('wp_sh_language2');
	unset($wp_sh_child_plugin_unset[$plugin_name]);

	add_nl_new_brush();

	foreach($wp_sh_brush_files as $lang => $val){
		unset($wp_sh_brush_files_unset[$lang]);
	}

	add_nl_new_lang_to_buttons();

	foreach($wp_sh_language3 as $lang => $val){
		unset($wp_sh_language3_unset[$lang]);
	}

	foreach($wp_sh_language2 as $lang => $val){
		unset($wp_sh_language2_unset[$lang]);
	}

	update_option('wp_sh_child_plugin', $wp_sh_child_plugin_unset);
	update_option('wp_sh_brush_files', $wp_sh_brush_files_unset);
	update_option('wp_sh_language3', $wp_sh_language3_unset);
	update_option('wp_sh_language2', $wp_sh_language2_unset);
}

register_deactivation_hook(__FILE__, 'add_nl_deactive'); 

?>