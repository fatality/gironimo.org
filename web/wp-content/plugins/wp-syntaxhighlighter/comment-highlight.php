<?php
/*
Commnet Highlighting
by Redcocker
Last modified: 2012/2/29
License: GPL v2
http://www.near-mint.com/blog/
*/

// Load Comment Highlight Button
if ($wp_sh_setting_opt['comment_hl_bt_enable'] == 1 && version_compare(get_bloginfo('version'), "3.0", ">=")) {
	include_once('comment-highlight-button.php');
}

// Processing character strings in the comments
// Substitute filters
add_action('init', 'wp_comment_filters');

function wp_comment_filters() {
	global $wp_sh_setting_opt;
	if (!current_user_can('unfiltered_html')) {
		// Remove wp_filter_kses filter
		remove_filter('pre_comment_content', 'wp_filter_kses');
		// Apply substitute wp_filter_kses filter to comments excluding codes
		add_filter('pre_comment_content', 'wp_sh_wp_filter_kses');
	}

	if (has_filter('comment_text', 'make_clickable')) {
		// Remove make_clickable filter
		remove_filter('comment_text', 'make_clickable', 9);
		// Apply substitute make_clickable filter to comments excluding codes
		add_filter('comment_text', 'wp_sh_make_clickable', 9);
	}

	// Apply filters to escape to HTML entities
	add_filter('pre_comment_content', 'wp_sh_escape_code', 1);
	add_filter('comment_text', 'wp_sh_escape_code');

	// Replaced marker with escaped <pre>
	add_filter('pre_comment_content', 'wp_sh_replace_marker', 2);

	// Add extra "[]" into shortcode when shown
	if ($wp_sh_setting_opt['comment_hl_bt_shorcode'] == 1) {
		add_filter('comment_text', 'wp_sh_add_extra_bracket', -1);
	}

}

?>