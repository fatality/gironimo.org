<?php
// Setting data migration for ver.1.5 or older
$wp_sh_setting_opt = get_option('wp_sh_setting_opt');
$wp_sh_setting_opt['lib_version'] = get_option('wp_sh_version');
$wp_sh_setting_opt['theme'] = get_option('wp_sh_theme');
if (get_option('wp_sh_auto_links') == 1) {
	$wp_sh_setting_opt['auto_links'] = "true";
} else {
	$wp_sh_setting_opt['auto_links'] = "false";
}
if (get_option('wp_sh_collapse') == 1) {
	$wp_sh_setting_opt['collapse'] = "true";
} else {
	$wp_sh_setting_opt['collapse'] = "false";
}
if (get_option('wp_sh_gutter') == 1) {
	$wp_sh_setting_opt['gutter'] = "true";
} else {
	$wp_sh_setting_opt['gutter'] = "false";
}
$wp_sh_setting_opt['first_line'] = get_option('wp_sh_first_line');
if (get_option('wp_sh_smart_tabs') == 1) {
	$wp_sh_setting_opt['smart_tabs'] = "true";
} else {
	$wp_sh_setting_opt['smart_tabs'] = "false";
}
$wp_sh_setting_opt['tab_size'] = get_option('wp_sh_tab_size');
if (get_option('wp_sh_toolbar') == 1) {
	$wp_sh_setting_opt['toolbar'] = "true";
} else {
	$wp_sh_setting_opt['toolbar'] = "false";
}
if (get_option('wp_sh_wrap') == 1) {
	$wp_sh_setting_opt['wrap'] = "true";
} else {
	$wp_sh_setting_opt['wrap'] = "false";
}
if (get_option('wp_sh_legacy') == 1) {
	$wp_sh_setting_opt['legacy'] = 1;
} else {
	$wp_sh_setting_opt['legacy'] = 0;
}
if (get_option('wp_sh_css') == 1) {
	$wp_sh_setting_opt['css'] = 1;
} else {
	$wp_sh_setting_opt['css'] = 0;
}
if (version_compare($current_checkver_stamp, "1.3", ">=")) {
	// Since ver. 1.2
	if (get_option('wp_sh_insert') == 1) {
		$wp_sh_setting_opt['select_insert'] = 1;
	} else {
		$wp_sh_setting_opt['select_insert'] = 0;
	}
	// Since ver. 1.2
	if (get_option('wp_sh_codebox') == 1) {
		$wp_sh_setting_opt['codebox'] = 1;
	} else {
		$wp_sh_setting_opt['codebox'] = 0;
	}
	// Since ver. 1.3.5
	if (get_option('wp_sh_addl_style_enable') == 1) {
		$wp_sh_setting_opt['addl_style_enable'] = 1;
	} else {
		$wp_sh_setting_opt['addl_style_enable'] = 0;
	}
	// Since ver. 1.3.5
	$button_row = get_option('wp_sh_button_row');
	if ($button_row) {
		$wp_sh_setting_opt['button_row'] = $button_row;
	}
	// since ver. 1.3.7
	$button_window_size = get_option('wp_sh_button_window_size');
	if ($button_window_size) {
		$wp_sh_setting_opt['button_window_size'] = $button_window_size;
	}
}
if (version_compare($current_checkver_stamp, "1.3.9", ">=")) {
	if (get_option('wp_sh_highlight_home') == 1) {
		$wp_sh_setting_opt['highlight_home'] = 1;
	} else {
		$wp_sh_setting_opt['highlight_home'] = 0;
	}
	if (get_option('wp_sh_highlight_posts') == 1) {
		$wp_sh_setting_opt['highlight_posts'] = 1;
	} else {
		$wp_sh_setting_opt['highlight_posts'] = 0;
	}
	if (get_option('wp_sh_highlight_categories') == 1) {
		$wp_sh_setting_opt['highlight_categories'] = 1;
	} else {
		$wp_sh_setting_opt['highlight_categories'] = 0;
	}
	if (get_option('wp_sh_highlight_archives') == 1) {
		$wp_sh_setting_opt['highlight_archives'] = 1;
	} else {
		$wp_sh_setting_opt['highlight_archives'] = 0;
	}
	if (get_option('wp_sh_highlight_search') == 1) {
		$wp_sh_setting_opt['highlight_search'] = 1;
	} else {
		$wp_sh_setting_opt['highlight_search'] = 0;
	}
	$wp_sh_setting_opt['padding_line'] = get_option('wp_sh_padding_line');
	// Since ver. 1.4
	if (get_option('wp_sh_highlight_comment') == 1) {
		$wp_sh_setting_opt['highlight_comment'] = 1;
	} else {
		$wp_sh_setting_opt['highlight_comment'] = 0;
	}
}
if (version_compare($current_checkver_stamp, "1.4.3", ">=")) {
	if (get_option('wp_sh_highlight_others') == 1) {
		$wp_sh_setting_opt['highlight_others'] = 1;
	} else {
		$wp_sh_setting_opt['highlight_others'] = 0;
	}
}
if (version_compare($current_checkver_stamp, "1.5", "=")) {
	if (get_option('wp_sh_highlight_widgets') == 1) {
		$wp_sh_setting_opt['highlight_widgets'] = 1;
	} else {
		$wp_sh_setting_opt['highlight_widgets'] = 0;
	}
	if (get_option('wp_sh_comment_hl_bt_enable') == 1) {
		$wp_sh_setting_opt['comment_hl_bt_enable'] = 1;
	} else {
		$wp_sh_setting_opt['comment_hl_bt_enable'] = 0;
	}
	if (get_option('wp_sh_comment_hl_description_before_enable') == 1) {
		$wp_sh_setting_opt['comment_hl_description_before_enable'] = 1;
	} else {
		$wp_sh_setting_opt['comment_hl_description_before_enable'] = 0;
	}
	if (get_option('wp_sh_comment_hl_stylesheet_enable') == 1) {
		$wp_sh_setting_opt['comment_hl_stylesheet_enable'] = 1;
	} else {
		$wp_sh_setting_opt['comment_hl_stylesheet_enable'] = 0;
	}
}
update_option('wp_sh_setting_opt', $wp_sh_setting_opt);
?>