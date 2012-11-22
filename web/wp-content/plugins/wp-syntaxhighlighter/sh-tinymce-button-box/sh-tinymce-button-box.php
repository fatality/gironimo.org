<?php
/*
SH TinyMCE Button CodeBox
by Redcocker
Last modified: 2011/12/3
License: GPL v2
http://www.near-mint.com/blog/
*/

function shtb_box_addbuttons() {
	global $wp_sh_setting_opt;
	// Don't bother doing this stuff if the current user lacks permissions
	if (((!$wp_sh_setting_opt['editor_no_unfiltered_html'] == 1 && !$wp_sh_setting_opt['highlight_bbpress'] == 1) && !current_user_can('unfiltered_html')) || (!current_user_can('edit_posts') && !current_user_can('edit_pages') && !current_user_can('edit_topics') && !current_user_can('edit_replies')))
		return;
	// Add only in Rich Editor mode
	if (get_user_option('rich_editing') == 'true') {
	// add the button for wp25 in a new way
		add_filter("mce_external_plugins", 'add_shtb_box_tinymce_plugin');
		$button_row = $wp_sh_setting_opt['button_row'];
		if ($button_row== "2" || $button_row== "3" || $button_row== "4") {
			add_filter('mce_buttons_'.$button_row, 'register_shtb_box_button');
		} else {
			add_filter('mce_buttons', 'register_shtb_box_button');
		}
		if (version_compare(get_bloginfo('version'), "3.2", ">=")) {
			add_filter('wp_fullscreen_buttons', 'shtb_box_fullscreen');
		}
	}
}

// used to insert button in wordpress 2.5x editor
function register_shtb_box_button($buttons) {
	array_push($buttons, "shtb_box");
	return $buttons;
}

// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function add_shtb_box_tinymce_plugin($plugin_array) {
	global $wp_sh_plugin_url, $wp_sh_setting_opt;
	if ($wp_sh_setting_opt['button_window_size'] == "105") {
		$plugin_array['shtb_box'] = $wp_sh_plugin_url.'sh-tinymce-button-box/editor_plugin_105.js';
	} elseif ($wp_sh_setting_opt['button_window_size'] == "110") {
		$plugin_array['shtb_box'] = $wp_sh_plugin_url.'sh-tinymce-button-box/editor_plugin_110.js';
	} else {
		$plugin_array['shtb_box'] = $wp_sh_plugin_url.'sh-tinymce-button-box/editor_plugin.js';
	}
	return $plugin_array;
}

function shtb_box_change_tinymce_version($version) {
	return ++$version;
}

// For fullscreen mode
function shtb_box_fullscreen($buttons) {
	$buttons['shtb_box'] = array(
	'title' => __('WP SyntaxHighlighter SHTB CodeBox', 'wp_sh'),
	'onclick' => "tinyMCE.execCommand('shtb_box_cmd');",
	'both' => false);
	return $buttons;
}

// Modify the version when tinyMCE plugins are changed.
add_filter('tiny_mce_version', 'shtb_box_change_tinymce_version');
// init process for button control
add_action('init', 'shtb_box_addbuttons');

?>