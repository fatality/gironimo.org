<?php
/*
bbPress Highlighting
by Redcocker
Last modified: 2012/2/29
License: GPL v2
http://www.near-mint.com/blog/
*/

// Load javascript to highlight codes
add_action('bbp_theme_before_topic_form_content', 'wp_sh_bbpress_load_scripts');
add_action('bbp_theme_before_reply_form_content', 'wp_sh_bbpress_load_scripts');

function wp_sh_bbpress_load_scripts() {
	wp_sh_load_scripts_by_shortcut();
}

// Processing character strings in the bbPress
add_action('bbp_init', 'wp_sh_bbpress_filters');

function wp_sh_bbpress_filters() {
	global $wp_sh_setting_opt;
	// Load bbPress Highlight Button
	if ($wp_sh_setting_opt['bbpress_hl_bt_enable'] == 1) {
		include_once('bbpress-highlight-button.php');
	}

	// Apply substitute filters when saved
	if ((!current_user_can('unfiltered_html') && !bbp_is_anonymous()) || ($wp_sh_setting_opt['bbpress_hl_bt_guest'] == 1 && bbp_is_anonymous())) {
		remove_filter('bbp_new_topic_pre_content', 'wp_filter_kses');
		add_filter('bbp_new_topic_pre_content', 'wp_sh_wp_filter_kses');
		remove_filter('bbp_new_reply_pre_content', 'wp_filter_kses');
		add_filter('bbp_new_reply_pre_content', 'wp_sh_wp_filter_kses');
		remove_filter('bbp_edit_topic_pre_content', 'wp_filter_kses');
		add_filter('bbp_edit_topic_pre_content', 'wp_sh_wp_filter_kses');
		remove_filter('bbp_edit_reply_pre_content', 'wp_filter_kses');
		add_filter('bbp_edit_reply_pre_content', 'wp_sh_wp_filter_kses');
	}

	if (!($wp_sh_setting_opt['bbpress_hl_bt_guest'] == 0 && bbp_is_anonymous())) {
		// Escape to HTML entities when saved
		add_filter('bbp_new_topic_pre_content', 'wp_sh_escape_code', 1);
		add_filter('bbp_new_reply_pre_content', 'wp_sh_escape_code', 1);
		add_filter('bbp_edit_topic_pre_content', 'wp_sh_escape_code', 1);
		add_filter('bbp_edit_reply_pre_content', 'wp_sh_escape_code', 1);

		// Replaced marker with escaped <pre> when saved
		add_filter('bbp_new_topic_pre_content', 'wp_sh_replace_marker', 2);
		add_filter('bbp_new_reply_pre_content', 'wp_sh_replace_marker', 2);
		add_filter('bbp_edit_topic_pre_content', 'wp_sh_replace_marker', 2);
		add_filter('bbp_edit_reply_pre_content', 'wp_sh_replace_marker', 2);
	}

	// Apply substitute make_clickable filters
	if (has_filter('bbp_get_topic_content', 'make_clickable')) {
		remove_filter('bbp_get_topic_content', 'make_clickable', 9);
		add_filter('bbp_get_topic_content', 'wp_sh_make_clickable', 9);
	}
	if (has_filter('bbp_get_reply_content', 'make_clickable')) {
		remove_filter('bbp_get_reply_content', 'make_clickable', 9);
		add_filter('bbp_get_reply_content', 'wp_sh_make_clickable', 9);
	}

	// Escape to HTML entities when shown
	add_filter('bbp_get_topic_content', 'wp_sh_escape_code', 1);
	add_filter('bbp_get_reply_content', 'wp_sh_escape_code', 1);

	// Add extra "[]" into shortcode when shown
	add_filter('bbp_get_topic_content', 'wp_sh_add_extra_bracket', -1);
	add_filter('bbp_get_reply_content', 'wp_sh_add_extra_bracket', -1);
}

?>