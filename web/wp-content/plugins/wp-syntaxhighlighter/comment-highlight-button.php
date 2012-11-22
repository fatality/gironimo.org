<?php
/*
Comment Highlight Button
by Redcocker
Last modified: 2011/12/14
License: GPL v2
http://www.near-mint.com/blog/
*/

// Load style sheet
if ($wp_sh_setting_opt['comment_hl_stylesheet_enable'] == "1") {
	add_action('wp_head', 'comment_hl_load_style');

	function comment_hl_load_style() {
		global $wp_sh_setting_opt;
		if (comments_open() && $wp_sh_setting_opt['comment_hl_stylesheet_enable'] == "1") {
			$wp_sh_comment_hl_stylesheet = wp_sh_valid_css(stripslashes(get_option('wp_sh_comment_hl_stylesheet')));
			if ($wp_sh_comment_hl_stylesheet == "invalid") {
				$wp_sh_comment_hl_stylesheet = "";
			}
			echo "\n<!-- Comment Highlight Button CSS Begin -->\n";
			echo "<style type='text/css'>\n".$wp_sh_comment_hl_stylesheet."\n</style>\n";
			echo "<!-- Comment Highlight Button CSS End -->\n";
		}
	}

}

// Load script
add_action('wp_print_scripts', 'comment_hl_load_jscript');

function comment_hl_load_jscript() {
	global $wp_sh_plugin_url;
	if (comments_open()) {
		wp_enqueue_script('rc_textarea_hl_js', $wp_sh_plugin_url.'js/rc-textarea-hl.js', false, '1.3');
	}
}

// Add description and buttons to inset <pre> tag
add_action('comment_form_after_fields', 'comment_hl_add_buttons');
add_action('comment_form_logged_in_after', 'comment_hl_add_buttons');

function comment_hl_add_buttons() {
	global $wp_sh_allowed_str, $wp_sh_setting_opt;
	if (comments_open()) {
		echo "<div class=\"comment_highlight\">";
		if ($wp_sh_setting_opt['comment_hl_description_before_enable'] == 1) {
			$wp_sh_comment_hl_description_before = wp_sh_valid_text(get_option('wp_sh_comment_hl_description_before'), $wp_sh_allowed_str);

			if ($wp_sh_comment_hl_description_before == "invalid") {
				$wp_sh_comment_hl_description_before = wp_sh_default_setting_value('comment_desc');
			}
			echo "<p>".str_replace("<pre>", "&lt;pre&gt;", $wp_sh_comment_hl_description_before)."</p>";
		}
		if ($wp_sh_setting_opt['comment_hl_bt_tag'] == "shortcode") {
			$tag = "shorcode";
		} else {
			$tag = "pre";
		}
		echo "<div class=\"comment_highlight_button\">";
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
					echo "<a href=\"javascript:void(0);\" onclick=\"surroundHTML('".$key."','comment','".$gutter."','".$wp_sh_setting_opt['first_line']."','".$tag."','0');\">".$val[0]."</a> | ";
				}
			}
			unset($val);
		}
		echo "</div>";
		echo "</div>";
	}
}

?>