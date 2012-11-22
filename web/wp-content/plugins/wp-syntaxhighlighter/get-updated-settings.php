<?php
/*
Get updated settings
by Redcocker
Last modified: 2011/12/14
License: GPL v2
http://www.near-mint.com/blog/
*/

if ($_POST['highlight_home'] == 1) {
	$wp_sh_setting_opt['highlight_home'] = 1;
} else {
	$wp_sh_setting_opt['highlight_home'] = 0;
}
if ($_POST['highlight_posts'] == 1) {
	$wp_sh_setting_opt['highlight_posts'] = 1;
} else {
	$wp_sh_setting_opt['highlight_posts'] = 0;
}
if ($_POST['highlight_categories'] == 1) {
	$wp_sh_setting_opt['highlight_categories'] = 1;
} else {
	$wp_sh_setting_opt['highlight_categories'] = 0;
}
if ($_POST['highlight_archives'] == 1) {
	$wp_sh_setting_opt['highlight_archives'] = 1;
} else {
	$wp_sh_setting_opt['highlight_archives'] = 0;
}
if ($_POST['highlight_search'] == 1) {
	$wp_sh_setting_opt['highlight_search'] = 1;
} else {
	$wp_sh_setting_opt['highlight_search'] = 0;
}
if ($_POST['highlight_comment'] == 1 || $_POST['comment_hl_bt_enable'] == 1) {
	$wp_sh_setting_opt['highlight_comment'] = 1;
} else {
	$wp_sh_setting_opt['highlight_comment'] = 0;
}
if ($_POST['highlight_others'] == 1) {
	$wp_sh_setting_opt['highlight_others'] = 1;
} else {
	$wp_sh_setting_opt['highlight_others'] = 0;
}
if ($_POST['highlight_widgets'] == 1) {
	$wp_sh_setting_opt['highlight_widgets'] = 1;
} else {
	$wp_sh_setting_opt['highlight_widgets'] = 0;
}
if ($_POST['highlight_bbpress'] == 1 || $_POST['bbpress_hl_bt_enable'] == 1) {
	$wp_sh_setting_opt['highlight_bbpress'] = 1;
} else {
	$wp_sh_setting_opt['highlight_bbpress'] = 0;
}
$wp_sh_setting_opt['lib_version'] = $_POST['lib_version'];
if ($_POST['lib_version'] == "2.1" && $_POST['theme'] =="MDUltra") {
	$wp_sh_setting_opt['theme'] = "Default";
} else {
	$wp_sh_setting_opt['theme'] = $_POST['theme'];
}
if ($_POST['auto_links'] == "true") {
	$wp_sh_setting_opt['auto_links'] = "true";
} else {
	$wp_sh_setting_opt['auto_links'] = "false";
}
if ($_POST['quick_code'] == "true") {
	$wp_sh_setting_opt['quick_code'] = "true";
} else {
	$wp_sh_setting_opt['quick_code'] = "false";
}
if ($_POST['addl_style_enable'] == 1) {
	$wp_sh_setting_opt['addl_style_enable'] = 1;
} else {
	$wp_sh_setting_opt['addl_style_enable'] = 0;
}
if ($_POST['collapse'] == "true") {
	$wp_sh_setting_opt['collapse'] = "true";
} else {
	$wp_sh_setting_opt['collapse'] = "false";
}
if ($_POST['gutter'] == "true") {
	$wp_sh_setting_opt['gutter'] = "true";
} else {
	$wp_sh_setting_opt['gutter'] = "false";
}
$wp_sh_setting_opt['first_line'] = $_POST['first_line'];
$wp_sh_setting_opt['padding_line'] = $_POST['padding_line'];
if ($_POST['smart_tabs'] == "true") {
	$wp_sh_setting_opt['smart_tabs'] = "true";
} else {
	$wp_sh_setting_opt['smart_tabs'] = "false";
}
$wp_sh_setting_opt['tab_size'] = $_POST['tab_size'];
if ($_POST['toolbar'] == "true" || $_POST['collapse'] == "true") {
	$wp_sh_setting_opt['toolbar'] = "true";
} else {
	$wp_sh_setting_opt['toolbar'] = "false";
}
if ($_POST['wrap'] == "true") {
	$wp_sh_setting_opt['wrap'] = "true";
} else {
	$wp_sh_setting_opt['wrap'] = "false";
}
if ($_POST['legacy'] == 1) {
	$wp_sh_setting_opt['legacy'] = 1;
} else {
	$wp_sh_setting_opt['legacy'] = 0;
}
if ($_POST['css'] == 1) {
	$wp_sh_setting_opt['css'] = 1;
} else {
	$wp_sh_setting_opt['css'] = 0;
}
if ($_POST['select_insert'] == 1) {
	$wp_sh_setting_opt['select_insert'] = 1;
} else {
	$wp_sh_setting_opt['select_insert'] = 0;
}
if ($_POST['codebox'] == 1) {
	$wp_sh_setting_opt['codebox'] = 1;
} else {
	$wp_sh_setting_opt['codebox'] = 0;
}
$wp_sh_setting_opt['button_window_size'] = $_POST['button_window_size'];
$wp_sh_setting_opt['button_row'] = $_POST['button_row'];
if ($_POST['quicktag'] == 1) {
	$wp_sh_setting_opt['quicktag'] = 1;
} else {
	$wp_sh_setting_opt['quicktag'] = 0;
}
if ($_POST['quicktag_jquery'] == 1) {
	$wp_sh_setting_opt['quicktag_jquery'] = 1;
} else {
	$wp_sh_setting_opt['quicktag_jquery'] = 0;
}
if ($_POST['editor_shorcode'] == 1 || $wp_sh_setting_opt['bbpress_hl_bt_shorcode'] == 1) {
	$wp_sh_setting_opt['editor_shorcode'] = 1;
} else {
	$wp_sh_setting_opt['editor_shorcode'] = 0;
}
if ($_POST['editor_no_unfiltered_html'] == 1) {
	$wp_sh_setting_opt['editor_no_unfiltered_html'] = 1;
} else {
	$wp_sh_setting_opt['editor_no_unfiltered_html'] = 0;
}
if ($_POST['comment_hl_bt_enable'] == 1) {
	$wp_sh_setting_opt['comment_hl_bt_enable'] = 1;
} else {
	$wp_sh_setting_opt['comment_hl_bt_enable'] = 0;
}
$wp_sh_setting_opt['comment_hl_bt_type'] = $_POST['comment_hl_bt_type'];
if ($_POST['comment_hl_bt_shorcode'] == 1 || $_POST['comment_hl_bt_type'] == "shortcode") {
	$wp_sh_setting_opt['comment_hl_bt_shorcode'] = 1;
} else {
	$wp_sh_setting_opt['comment_hl_bt_shorcode'] = 0;
}
if ($_POST['comment_hl_description_before_enable'] == 1) {
	$wp_sh_setting_opt['comment_hl_description_before_enable'] = 1;
} else {
	$wp_sh_setting_opt['comment_hl_description_before_enable'] = 0;
}
if ($_POST['comment_hl_stylesheet_enable'] == 1) {
	$wp_sh_setting_opt['comment_hl_stylesheet_enable'] = 1;
} else {
	$wp_sh_setting_opt['comment_hl_stylesheet_enable'] = 0;
}
if ($_POST['comment_quicktag'] == 1) {
	$wp_sh_setting_opt['comment_quicktag'] = 1;
} else {
	$wp_sh_setting_opt['comment_quicktag'] = 0;
}
if ($_POST['comment_jquery'] == 1) {
	$wp_sh_setting_opt['comment_jquery'] = 1;
} else {
	$wp_sh_setting_opt['comment_jquery'] = 0;
}
$wp_sh_setting_opt['wiget_tag'] = $_POST['wiget_tag'];
if ($_POST['wiget_shorcode'] == 1 || $_POST['wiget_tag'] == "shortcode") {
	$wp_sh_setting_opt['wiget_shorcode'] = 1;
} else {
	$wp_sh_setting_opt['wiget_shorcode'] = 0;
}
if ($_POST['bbpress_hl_bt_enable'] == 1) {
	$wp_sh_setting_opt['bbpress_hl_bt_enable'] = 1;
} else {
	$wp_sh_setting_opt['bbpress_hl_bt_enable'] = 0;
}
$wp_sh_setting_opt['bbpress_hl_bt_type'] = $_POST['bbpress_hl_bt_type'];
if ($_POST['bbpress_hl_bt_shorcode'] == 1 || $_POST['bbpress_hl_bt_type'] == "shortcode") {
	$wp_sh_setting_opt['bbpress_hl_bt_shorcode'] = 1;
} else {
	$wp_sh_setting_opt['bbpress_hl_bt_shorcode'] = 0;
}
if ($_POST['bbpress_hl_bt_guest'] == 1) {
	$wp_sh_setting_opt['bbpress_hl_bt_guest'] = 1;
} else {
	$wp_sh_setting_opt['bbpress_hl_bt_guest'] = 0;
}
if ($_POST['bbpress_hl_description_before_enable'] == 1) {
	$wp_sh_setting_opt['bbpress_hl_description_before_enable'] = 1;
} else {
	$wp_sh_setting_opt['bbpress_hl_description_before_enable'] = 0;
}
if ($_POST['bbpress_hl_stylesheet_enable'] == 1) {
	$wp_sh_setting_opt['bbpress_hl_stylesheet_enable'] = 1;
} else {
	$wp_sh_setting_opt['bbpress_hl_stylesheet_enable'] = 0;
}
?>