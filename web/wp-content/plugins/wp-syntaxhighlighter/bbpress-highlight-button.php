<?php
/*
BBpress Highlight Button
by Redcocker
Last modified: 2011/12/14
License: GPL v2
http://www.near-mint.com/blog/
*/

// Load style sheet
if ($wp_sh_setting_opt['bbpress_hl_stylesheet_enable'] == "1" && !($wp_sh_setting_opt['bbpress_hl_bt_guest'] == 0 && bbp_is_anonymous())) {
	add_action('bbp_head', 'bbpress_hl_load_style');
}
// Load script
if (!($wp_sh_setting_opt['bbpress_hl_bt_guest'] == 0 && bbp_is_anonymous())) {
	add_action('bbp_enqueue_scripts', 'bbpress_hl_load_jscript');
	// Add description and buttons to inset <pre> tag
	add_action('bbp_theme_before_topic_form_content' , 'bbpress_hl_add_buttons');
	add_action('bbp_theme_before_reply_form_content' , 'bbpress_hl_add_buttons');
}

function bbpress_hl_load_style() {
	if (bbp_is_single_forum() || bbp_is_single_topic() || bbp_is_topic_edit() || bbp_is_reply_edit()) {
		$wp_sh_bbpress_hl_stylesheet = wp_sh_valid_css(stripslashes(get_option('wp_sh_bbpress_hl_stylesheet')));
		if ($wp_sh_bbpress_hl_stylesheet == "invalid") {
			$wp_sh_bbpress_hl_stylesheet = "";
		}
		echo "\n<!-- bbpress Highlight Button CSS Begin -->\n";
		echo "<style type='text/css'>\n".$wp_sh_bbpress_hl_stylesheet."\n</style>\n";
		echo "<!-- bbpress Highlight Button CSS End -->\n";
	}
}

function bbpress_hl_load_jscript() {
	global $wp_sh_plugin_url, $wp_sh_setting_opt;
	if (bbp_is_single_forum() || bbp_is_single_topic() || bbp_is_topic_edit() || bbp_is_reply_edit()) {
		wp_enqueue_script('rc_textarea_hl_js', $wp_sh_plugin_url.'js/rc-textarea-hl.js', false, '1.3');
	}
}

function bbpress_hl_add_buttons() {
	global $wp_sh_allowed_str, $wp_sh_setting_opt;
	if (bbp_is_single_forum() || bbp_is_single_topic() || bbp_is_topic_edit() || bbp_is_reply_edit()) {
		if (bbp_is_single_forum()) {
			$textarea_id = "bbp_topic_content";
		} elseif (bbp_is_topic_edit()) {
			$textarea_id = "bbp_topic_content";
		} elseif (bbp_is_reply_edit()) {
			$textarea_id = "bbp_reply_content";
		} elseif (bbp_is_single_topic()) {
			$textarea_id = "bbp_reply_content";
		}
		echo "<div class=\"bbpress_highlight\">";
		if ($wp_sh_setting_opt['bbpress_hl_description_before_enable'] == 1) {
			$wp_sh_bbpress_hl_description_before = wp_sh_valid_text(get_option('wp_sh_bbpress_hl_description_before'), $wp_sh_allowed_str);

			if ($wp_sh_bbpress_hl_description_before == "invalid") {
				$wp_sh_bbpress_hl_description_before = wp_sh_default_setting_value('bbp_desc');
			}
			echo "<p>".str_replace("<pre>", "&lt;pre&gt;", $wp_sh_bbpress_hl_description_before)."</p>";
		}
		if ($wp_sh_setting_opt['bbpress_hl_bt_tag'] == "shortcode") {
			$tag = "shorcode";
		} else {
			$tag = "pre";
		}
		echo "<div class=\"bbpress_highlight_button\">";
		if ($wp_sh_setting_opt['lib_version'] == '3.0') {
			$languages = get_option('wp_sh_language3');
		} elseif ($wp_sh_setting_opt['lib_version'] == '2.1') {
			$languages = get_option('wp_sh_language2');
		}
		$gutter = $wp_sh_setting_opt['gutter'];
		if (is_array($languages)) {
			asort($languages);
			foreach ($languages as $key => $val) {
				if ($val[1] == 'true' || $val[1] =='added') {
					echo "<a href=\"javascript:void(0);\" onclick=\"surroundHTML('".$key."','".$textarea_id."','".$gutter."','".$wp_sh_setting_opt['first_line']."','".$tag."','0');\">".$val[0]."</a> | ";
				}
			}
			unset($val);
		}
		echo "</div>";
		echo "</div>";
	}
}

?>