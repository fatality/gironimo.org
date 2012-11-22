<?php
/*
For dashboard
by Redcocker
Last modified: 2012/2/29
License: GPL v2
http://www.near-mint.com/blog/
*/

// Register setting panel and hooks
add_action('admin_menu', 'wp_sh_register_menu_item');

function wp_sh_register_menu_item() {
	global $wp_sh_setting_opt;
	// Register setting panel
	$wp_sh_page_hook = add_options_page('WP SyntaxHighlighter Options', 'WP SyntaxHighlighter', 'manage_options', 'wp-syntaxhighlighter-options', 'wp_sh_options_panel');
	// CSS and scripts for setting panel
	if ($wp_sh_page_hook != null) {
		$wp_sh_page_hook = '-'.$wp_sh_page_hook;
	}
	add_action('admin_print_scripts'.$wp_sh_page_hook, 'wp_sh_load_jscript_for_admin');
	add_action('admin_print_styles'.$wp_sh_page_hook, 'wp_sh_load_style');
	if (isset($_POST['WP_SH_Setting_submit']) && $_POST['wp_sh_hidden_value'] == "true") {
		if ($_POST['addl_style_enable'] == 1) {
			add_action('admin_head'.$wp_sh_page_hook, 'wp_sh_load_addl_style');
		}
	} else {
		if ($wp_sh_setting_opt['addl_style_enable'] == 1) {
			add_action('admin_head'.$wp_sh_page_hook, 'wp_sh_load_addl_style');
		}
	}
	// Show messages for admin
	$updated = get_option('wp_sh_updated');
	if (($updated == "true" || $updated == "migration") && !(isset($_POST['WP_SH_Setting_submit']) && $_POST['wp_sh_hidden_value'] == "true") && !(isset($_POST['WP_SH_Reset']) && $_POST['wp_sh_reset'] == "true")) {
		add_action('admin_notices', 'wp_sh_admin_updated_notice');
	}
	if ($updated == "migration" && !(isset($_POST['WP_SH_Setting_submit']) && $_POST['wp_sh_hidden_value'] == "true") && !(isset($_POST['WP_SH_Reset']) && $_POST['wp_sh_reset'] == "true")) {
		add_action('admin_notices', 'wp_sh_admin_migration_notice');
	}
}

// Script for the setting panel
function wp_sh_load_jscript_for_admin() {
	global $wp_sh_plugin_url;
	wp_enqueue_script('rc_admin_js', $wp_sh_plugin_url.'js/rc-admin-js.js', false, '1.1');
}

// Message for admin when DB table updated
function wp_sh_admin_updated_notice() {
	echo '<div id="message" class="updated"><p>'.__('WP SyntaxHighlighter has successfully created new DB table.<br />If you upgraded to this version, some new setting options may be added.<br />Go to the <a href="options-general.php?page=wp-syntaxhighlighter-options">setting panel</a> and configure WP SyntaxHighlighter now. Once you save your settings, this message will be cleared.', 'wp_sh').'</p></div>';
}

// Message for admin when data migration done
function wp_sh_admin_migration_notice() {
	echo '<div id="message" class="updated"><p>'.__('All setting data has migrated successfully.<br />If you upgraded from ver. 1.3 or older to this version, a part of setting parameters may be reset to the default values.<br />Go to the <a href="options-general.php?page=wp-syntaxhighlighter-options">setting panel</a> and check your setting parameters. Once you save your settings, this message will be cleared.', 'wp_sh').'</p></div>';
}

// Resotre updated settings in DB
function wp_sh_update_setting() {
	global $wp_sh_allowed_str, $wp_sh_setting_opt;
	$wp_sh_brushes = get_option('wp_sh_brush_files');
	// Get updated settings
	$wp_sh_code_title = stripslashes($_POST['wp_sh_code_title']);
	$wp_sh_class_name = stripslashes($_POST['wp_sh_class_name']);
	$wp_sh_addl_style = stripslashes($_POST['wp_sh_addl_style']);
	$wp_sh_collapse_lable_text = stripslashes($_POST['wp_sh_collapse_lable_text']);
	if (version_compare(get_bloginfo('version'), "3.0", ">=")) {
		$wp_sh_comment_hl_description_before = stripslashes($_POST['wp_sh_comment_hl_description_before']);
		$wp_sh_comment_hl_stylesheet = stripslashes($_POST['wp_sh_comment_hl_stylesheet']);
	} else {
		$wp_sh_comment_hl_description_before = wp_sh_default_setting_value('comment_desc');
		$wp_sh_comment_hl_stylesheet = wp_sh_default_setting_value('comment_style');
	}
	$wp_sh_bbpress_hl_description_before = stripslashes($_POST['wp_sh_bbpress_hl_description_before']);
	$wp_sh_bbpress_hl_stylesheet = stripslashes($_POST['wp_sh_bbpress_hl_stylesheet']);
	// Transforming before validation
	$wp_sh_addl_style = strip_tags($wp_sh_addl_style);
	$wp_sh_comment_hl_description_before = str_replace(array("\r\n", "\r", "\n"), "<br />", $wp_sh_comment_hl_description_before);
	$wp_sh_comment_hl_stylesheet = strip_tags($wp_sh_comment_hl_stylesheet);
	$wp_sh_bbpress_hl_description_before = str_replace(array("\r\n", "\r", "\n"), "<br />", $wp_sh_bbpress_hl_description_before);
	$wp_sh_bbpress_hl_stylesheet = strip_tags($wp_sh_bbpress_hl_stylesheet);
	// Validate values
	if (!preg_match("/^[0-9]+$/", $wp_sh_setting_opt['first_line'])) {
		wp_die(__("Invalid value. Settings could not be saved.<br />Your \"Line number(Gutter)\" must be entered in numbers.", 'wp_sh'));
	}
	if (!preg_match("/^[0-9]+$/", $wp_sh_setting_opt['tab_size'])) {
		wp_die(__("Invalid value. Settings could not be saved.<br />Your \"Tab size\" must be entered in numbers.", 'wp_sh'));
	}	
	if (strpos($wp_sh_class_name, "\"") || strpos($wp_sh_class_name, "'")) {
		wp_die(__("Invalid value. Settings could not be saved.<br />Your \"Class attribute\" contains \" or ' that are not allowed to use.", 'wp_sh'));
	}
	if (wp_sh_valid_css($wp_sh_addl_style) == "invalid") {
		wp_die(__('Invalid value. Settings could not be saved.<br />Your "Stylesheet" contains some character strings that are not allowed to use.', 'wp_sh'));
	} else {
		$wp_sh_addl_style = wp_sh_valid_css($wp_sh_addl_style);
	}
	if (wp_sh_valid_text($wp_sh_comment_hl_description_before, $wp_sh_allowed_str) == "invalid") {
		wp_die(__('Invalid value. Settings could not be saved.<br />Your "Description" for "Comment Highlighter Button" contains some character strings that are not allowed to use.', 'wp_sh'));
	} else {
		$wp_sh_comment_hl_description_before = wp_sh_valid_text($wp_sh_comment_hl_description_before, $wp_sh_allowed_str);
	}
	if (wp_sh_valid_css($wp_sh_comment_hl_stylesheet) == "invalid") {
		wp_die(__('Invalid value. Settings could not be saved.<br />Your "Stylesheet" for "Comment Highlighter Button" contains some character strings that are not allowed to use.', 'wp_sh'));
	} else {
		$wp_sh_comment_hl_stylesheet = wp_sh_valid_css($wp_sh_comment_hl_stylesheet);
	}
	if (wp_sh_valid_text($wp_sh_bbpress_hl_description_before, $wp_sh_allowed_str) == "invalid") {
		wp_die(__('Invalid value. Settings could not be saved.<br />Your "Description" for "bbpress Highlighter Button" contains some character strings that are not allowed to use.', 'wp_sh'));
	} else {
		$wp_sh_bbpress_hl_description_before = wp_sh_valid_text($wp_sh_bbpress_hl_description_before, $wp_sh_allowed_str);
	}
	if (wp_sh_valid_css($wp_sh_bbpress_hl_stylesheet) == "invalid") {
		wp_die(__('Invalid value. Settings could not be saved.<br />Your "Stylesheet" for "bbPress Highlighter Button" contains some character strings that are not allowed to use.', 'wp_sh'));
	} else {
		$wp_sh_bbpress_hl_stylesheet = wp_sh_valid_css($wp_sh_bbpress_hl_stylesheet);
	}
	// For backward compatibility
	$wp_sh_version = $_POST['lib_version'];
	if ($_POST['gutter'] == "true") {
		$wp_sh_gutter = 1;
	} else {
		$wp_sh_gutter = 0;
	}
	$wp_sh_first_line = $_POST['first_line'];
	// Update languages
	foreach ($wp_sh_brushes as $lang => $val) {
		$brush_file = $val[0];
		$brush_alias = $val[1];
		$brush_ver = $val[2];
		$wp_sh_brush_files[$lang] = array($brush_file, $brush_alias, $brush_ver, $_POST[$lang]);
	}
	// Restore in DB
	update_option('wp_sh_version', $wp_sh_version); // For backward compatibility
	update_option('wp_sh_gutter', $wp_sh_gutter); // For backward compatibility
	update_option('wp_sh_first_line', $wp_sh_first_line); // For backward compatibility
	update_option('wp_sh_setting_opt', $wp_sh_setting_opt);
	update_option('wp_sh_code_title', $wp_sh_code_title);
	update_option('wp_sh_class_name', $wp_sh_class_name);
	update_option('wp_sh_addl_style', $wp_sh_addl_style);
	update_option('wp_sh_collapse_lable_text', $wp_sh_collapse_lable_text);
	update_option('wp_sh_comment_hl_description_before', $wp_sh_comment_hl_description_before);
	update_option('wp_sh_comment_hl_stylesheet', $wp_sh_comment_hl_stylesheet);
	update_option('wp_sh_bbpress_hl_description_before', $wp_sh_bbpress_hl_description_before);
	update_option('wp_sh_bbpress_hl_stylesheet', $wp_sh_bbpress_hl_stylesheet);
	update_option('wp_sh_brush_files', $wp_sh_brush_files);
	update_option('wp_sh_updated', 'false');
	// Rebuild language list
	wp_sh_rebuild_lang_list();
	// Message for admin
	echo "<div id='setting-error-settings_updated' class='updated fade'><p><strong>".__("Settings saved.","wp_sh")."</strong></p></div>";
}

// Rebuild language list
function wp_sh_rebuild_lang_list() {
	$wp_sh_language3 = get_option('wp_sh_language3');
	$wp_sh_language2 = get_option('wp_sh_language2');
	$wp_sh_brush_files = get_option('wp_sh_brush_files');
	foreach ($wp_sh_language3 as $alias3 => $val3) {
		$lang_label = $val3[0];
		$lang_enable = $val3[1];
		$pattern1 = '/^'.$alias3.'$/i';
		$pattern2 = '/^'.$alias3.'\s/i';
		$pattern3 = '/\s'.$alias3.'\s/i';
		$pattern4 = '/\s'.$alias3.'$/i';
		if ($lang_enable == 'added') {
			$wp_sh_language3[$alias3] = array($lang_label, $lang_enable);
		}
		foreach ($wp_sh_brush_files as $lang3 => $value3) {
			$brush_alias = $value3[1];
			$brush_enable = $value3[3];
			if (preg_match($pattern1, $brush_alias) || preg_match($pattern2, $brush_alias) || preg_match($pattern3, $brush_alias) || preg_match($pattern4, $brush_alias)) {
				$wp_sh_language3[$alias3] = array($lang_label, $brush_enable);
			}
		}
	}
	foreach ($wp_sh_language2 as $alias2 => $val2) {
		$lang_label = $val2[0];
		$lang_enable = $val2[1];
		$pattern1 = '/^'.$alias2.'$/i';
		$pattern2 = '/^'.$alias2.'\s/i';
		$pattern3 = '/\s'.$alias2.'\s/i';
		$pattern4 = '/\s'.$alias2.'$/i';
		if ($lang_enable == 'added') {
			$wp_sh_language2[$alias2] = array($lang_label, $lang_enable);
		}
		foreach ($wp_sh_brush_files as $lang2 => $value2) {
			$brush_alias = $value2[1];
			$brush_enable = $value2[3];
			if (preg_match($pattern1, $brush_alias) || preg_match($pattern2, $brush_alias) || preg_match($pattern3, $brush_alias) || preg_match($pattern4, $brush_alias)) {
				$wp_sh_language2[$alias2] = array($lang_label, $brush_enable);
			}
		}
	}
	update_option('wp_sh_language3', $wp_sh_language3);
	update_option('wp_sh_language2', $wp_sh_language2);
}

// Reset all settings to default
function wp_sh_reset_setting() {
	global $wp_sh_db_ver;
	// Remove all settings from DB
	include_once('uninstall.php');
	// Register default settings
	wp_sh_language_array();
	wp_sh_setting_array();
	add_option('wp_sh_checkver_stamp', $wp_sh_db_ver);
	// Message for admin
	echo "<div id='setting-error-settings_updated' class='updated fade'><p><strong>".__("All settings were reset. Please <a href=\"options-general.php?page=wp-syntaxhighlighter-options\">reload the page</a>.","wp_sh")."</strong></p></div>";
}

// Setting panel
function wp_sh_options_panel() {
	global $wp_sh_plugin_url, $wp_sh_allowed_str, $wp_sh_setting_opt;
	if(!function_exists('current_user_can') || !current_user_can('manage_options')){
			die(__('Cheatin&#8217; uh?'));
	}

	// Show info on footer
	add_action('in_admin_footer', 'wp_sh_add_admin_footer');

	// Update settings
	if (isset($_POST['WP_SH_Setting_submit']) && $_POST['wp_sh_hidden_value'] == "true" && check_admin_referer("wp-sh-update_options", "_wpnonce_update_options")) {
		wp_sh_update_setting();
	}
	// Reset all settings
	if (isset($_POST['WP_SH_Reset']) && $_POST['wp_sh_reset'] == "true" && check_admin_referer("wp-sh-reset_options", "_wpnonce_reset_options")) {
		wp_sh_reset_setting();
	}

	// Load script for admin footer
	add_action('admin_footer', 'wp_sh_load_scripts_on_footer');

	?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2>WP SyntaxHighlighter</h2>
	<h3><?php _e("1. Basic Settings", 'wp_sh') ?></h3>
	<form method="post" action="">
	<input type="hidden" name="wp_sh_hidden_value" value="true" />
	<?php wp_nonce_field("wp-sh-update_options", "_wpnonce_update_options"); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Higlight your code in', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="highlight_home" value="1" <?php if ($wp_sh_setting_opt['highlight_home'] == '1') {echo 'checked=\"checked\"';} ?>/><?php _e('Home', 'wp_sh') ?> <input type="checkbox" name="highlight_posts" value="1" <?php if ($wp_sh_setting_opt['highlight_posts'] == '1') {echo 'checked=\"checked\"';} ?>/><?php _e('Posts & Pages', 'wp_sh') ?> <input type="checkbox" name="highlight_categories" value="1" <?php if ($wp_sh_setting_opt['highlight_categories'] == '1') {echo 'checked=\"checked\"';} ?>/><?php _e('Categories', 'wp_sh') ?> <input type="checkbox" name="highlight_archives" value="1" <?php if ($wp_sh_setting_opt['highlight_archives'] == '1') {echo 'checked=\"checked\"';} ?>/><?php _e('Archives', 'wp_sh') ?> <input type="checkbox" name="highlight_search" value="1" <?php if ($wp_sh_setting_opt['highlight_search'] == '1') {echo 'checked=\"checked\"';} ?>/><?php _e('Search result', 'wp_sh') ?> <input type="checkbox" name="highlight_comment" value="1" <?php if ($wp_sh_setting_opt['highlight_comment'] == '1') {echo 'checked=\"checked\"';} ?>/><?php _e('Comments', 'wp_sh') ?> <input type="checkbox" name="highlight_others" value="1" <?php if ($wp_sh_setting_opt['highlight_others'] == '1') {echo 'checked=\"checked\"';} ?>/><?php _e('Others', 'wp_sh') ?>
					<p><small><?php _e("Highlighting your code in the 'Categories', 'Archives' or 'Search result' depends on your using theme.<br />Sometimes you may only have to enable 'Home' and 'Posts & Pages'.<br />Note: To enable 'Comments' means you allow visitors to post their source code as comments.", "wp_sh") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Widget', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="highlight_widgets" value="1" <?php if ($wp_sh_setting_opt['highlight_widgets'] == '1') {echo 'checked=\"checked\"';} ?>/><?php _e('Use WP SyntaxHighlighter Widget', 'wp_sh') ?>
					<p><small><?php _e("'WP SyntaxHighlighter Widget' is the widget to show highlighted code.<br />Go to 'Widgets' section under 'Appearance' menu to add the 'WP SyntaxHighlighter Widget' in your sidebar.", "wp_sh") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('bbPress', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="highlight_bbpress" value="1" <?php if ($wp_sh_setting_opt['highlight_bbpress'] == '1') {echo 'checked=\"checked\"';} ?>/><?php _e('Highlight codes in bbPress', 'wp_sh') ?>
					<p><small><?php _e("This plugin can highlight topics/replies with codes in bbPress.<br />Remenber to configure '<a href=\"#bbpress_highlight\">5. bbPress Highlighter Button Settings</a>'.<br />Before this option is enabled, you must install and activate '<a href=\"http://wordpress.org/extend/plugins/bbpress/\">bbPress</a>' plugin.", "wp_sh") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Library version', 'wp_sh') ?></th> 
				<td>
					<select name="lib_version">
						<option value="3.0" <?php if ($wp_sh_setting_opt['lib_version'] == "3.0") {echo 'selected="selected"';} ?>>3.0.83</option>
						<option value="2.1" <?php if ($wp_sh_setting_opt['lib_version'] == "2.1") {echo 'selected="selected"';} ?>>2.1.382</option>
					</select>
					<p><small><?php _e("Choose 'Syntax Highlighter' JavaScript library version.<br />In ver. 2.1, 'autoloader' will be disabled and <a href=\"#loaded_languages\">pre-selected language definition files</a> will be loaded at once.", "wp_sh") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Theme', 'wp_sh') ?></th> 
				<td>
					<select name="theme">
						<option value="Default" <?php if ($wp_sh_setting_opt['theme'] == "Default") {echo 'selected="selected"';} ?>>Default</option>
						<option value="Django" <?php if ($wp_sh_setting_opt['theme'] == "Django") {echo 'selected="selected"';} ?>>Django</option>
						<option value="Eclipse" <?php if ($wp_sh_setting_opt['theme'] == "Eclipse") {echo 'selected="selected"';} ?>>Eclipse</option>
						<option value="Emacs" <?php if ($wp_sh_setting_opt['theme'] == "Emacs") {echo 'selected="selected"';} ?>>Emacs</option>
						<option value="FadeToGrey" <?php if ($wp_sh_setting_opt['theme'] == "FadeToGrey") {echo 'selected="selected"';} ?>>FadeToGrey</option>
						<option value="MDUltra" <?php if ($wp_sh_setting_opt['theme'] == "MDUltra") {echo 'selected="selected"';} ?>>MDUltra</option>
						<option value="Midnight" <?php if ($wp_sh_setting_opt['theme'] == "Midnight") {echo 'selected="selected"';} ?>>Midnight</option>
						<option value="RDark" <?php if ($wp_sh_setting_opt['theme'] == "RDark") {echo 'selected="selected"';} ?>>RDark</option>
						<option value="None" <?php if ($wp_sh_setting_opt['theme'] == "None") {echo 'selected="selected"';} ?>><?php _e('None', 'wp_sh') ?></option>
						<option value="Random" <?php if ($wp_sh_setting_opt['theme'] == "Random") {echo 'selected="selected"';} ?>><?php _e('Random', 'wp_sh') ?></option>
					</select>
					<p><small><?php _e("Themes for the highlighted elements. 'MDUltra' is not available for ver. 2.1.<br />If you choose 'MDUltra' in ver. 2.1, Theme will be replaced with 'Defaut' theme.<br />When 'Random' is selected, this plugin will apply different theme on each page.", "wp_sh") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Auto links', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="auto_links" value="true" <?php if($wp_sh_setting_opt['auto_links'] == "true"){echo 'checked="checked" ';} ?>/>
					<p><small><?php _e('URL in the highlighted element will be linked by default.', 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Quick code copy', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="quick_code" value="true" <?php if($wp_sh_setting_opt['quick_code'] == "true"){echo 'checked="checked" ';} ?>/>
					<p><small><?php _e("Only for ver. 3.0. 'Quick code copy' makes easy to copy the code on the codeblock to clipboard.<br />If visitors double click anywhere on the codeblock, the entire code view will be replaced with a pre-selected view.", "wp_sh") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Title', 'wp_sh') ?></th>
				<td>
					<input type="text" name="wp_sh_code_title" size="80" value="<?php echo esc_html(get_option('wp_sh_code_title')); ?>" />
					<p><small><?php _e('Only for ver. 3.0. This option will allow you to place Title above each code blocks or in each collapsed code blocks.', 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Class attribute', 'wp_sh') ?></th>
				<td>
					<input type="text" name="wp_sh_class_name" value="<?php echo esc_attr(get_option('wp_sh_class_name')); ?>" />
					<p><small><?php _e('Enter space-separated class attribute values for code block. You can use them with addtional stylesheet.<br />TIP: To enter "notranslate" will stop translating your code with "Google Translate".', 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Stylesheet', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="addl_style_enable" value="1" <?php if($wp_sh_setting_opt['addl_style_enable'] == 1){echo 'checked="checked" ';} ?>/> <?php _e('Use additional CSS', 'wp_sh') ?><br />
					<textarea name="wp_sh_addl_style"  rows="8" style="width:500px"><?php echo esc_html(wp_sh_valid_css(strip_tags(get_option('wp_sh_addl_style')))); ?></textarea>
					<p><small><?php _e("Define additional CSS for code block. The defined CSS will be inserted into <code>&lt;head&gt;</code> section.<br />You can also use the class attribute defined by 'Class attribute' option.", 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Collapse', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="collapse" value="true" <?php if($wp_sh_setting_opt['collapse'] == "true"){echo 'checked="checked" ';} ?>/>
					<p><small><?php _e("The code box will be collapsed by default. If you want to use this option, 'Toolbar' option must be enabled.<br />After this option is enabled, 'Toolbar' option will be enabled automatically.", "wp_sh") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Label for collapsed code block', 'wp_sh') ?></th>
				<td>
					<input type="text" name="wp_sh_collapse_lable_text" size="80" value="<?php echo esc_html(get_option('wp_sh_collapse_lable_text')); ?>" />
					<p><small><?php _e("Enter text label for collapsed code block. If you use this option in ver. 3.0, 'Title' must be empty.", "wp_sh") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Line number(Gutter)', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="gutter" value="true" <?php if($wp_sh_setting_opt['gutter'] == "true"){echo 'checked="checked" ';} ?>/>
					<p><small><?php _e('Line number(Gutter) will be shown by default.', 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Starting line number', 'wp_sh') ?></th>
				<td>
					<input type="text" name="first_line" size="2" value="<?php echo $wp_sh_setting_opt['first_line']; ?>" />
					<p><small><?php _e('Enter starting line number for the line numbering(Gutter).', 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Line Number Padding', 'wp_sh') ?></th> 
				<td>
					<select name="padding_line">
						<option value="false" <?php if ($wp_sh_setting_opt['padding_line'] == "false") {echo 'selected="selected"';} ?>><?php _e('None', 'wp_sh') ?></option>
						<option value="true" <?php if ($wp_sh_setting_opt['padding_line'] == "true") {echo 'selected="selected"';} ?>><?php _e('Auto', 'wp_sh') ?></option>
						<option value="1" <?php if ($wp_sh_setting_opt['padding_line'] == "1") {echo 'selected="selected"';} ?>>1</option>
						<option value="2" <?php if ($wp_sh_setting_opt['padding_line'] == "2") {echo 'selected="selected"';} ?>>2</option>
						<option value="3" <?php if ($wp_sh_setting_opt['padding_line'] == "3") {echo 'selected="selected"';} ?>>3</option>
						<option value="4" <?php if ($wp_sh_setting_opt['padding_line'] == "4") {echo 'selected="selected"';} ?>>4</option>
						<option value="5" <?php if ($wp_sh_setting_opt['padding_line'] == "5") {echo 'selected="selected"';} ?>>5</option>
					</select>
					<p><small><?php _e("This option will allow you to pad line numbers with leading zeros.", "wp_sh") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Smart tabs', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="smart_tabs" value="true" <?php if($wp_sh_setting_opt['smart_tabs'] == "true"){echo 'checked="checked" ';} ?>/>
					<p><small><?php _e("Indent by 'tab' will be optimized by default.", "wp_sh") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Tab size', 'wp_sh') ?></th>
				<td>
					<input type="text" name="tab_size" size="2" value="<?php echo $wp_sh_setting_opt['tab_size']; ?>" />
					<p><small><?php _e("Tab size for 'Smart tabs' feature.", "wp_sh") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Toolbar', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="toolbar" value="true" <?php if($wp_sh_setting_opt['toolbar'] == "true"){echo 'checked="checked" ';} ?>/>
					<p><small><?php _e('Toolbar will be shown by default.', 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Wrap lines', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="wrap" value="true" <?php if($wp_sh_setting_opt['wrap'] == "true"){echo 'checked="checked" ';} ?>/>
					<p><small><?php _e('Only for ver. 2.1. Long lines will be wrapped automatically by default.', 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Legacy mode', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="legacy" value="1" <?php if($wp_sh_setting_opt['legacy'] == 1){echo 'checked="checked" ';} ?>/>
					<p><small><?php _e("Support <a href=\"http://code.google.com/p/syntaxhighlighter/wiki/Usage\">old style <code>&lt;pre&gt;</code> tag</a> in SyntaxHighlighter ver. 1.5(Not mean WP SyntaxHighlighter ver. 1.5).<br />After this option is enabled, <a href=\"#loaded_languages\">pre-selected brush files</a> will be loaded at once.", "wp_sh") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('CSS for <code>&lt;pre&gt;</code> tag', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="css" value="1" <?php if($wp_sh_setting_opt['css'] == 1){echo 'checked="checked" ';} ?>/>
					<p><small><?php _e("If it is some time before your code highlighted, Unbeautiful code block will appear for a while.<br />This CSS will define its visual layout. Your code block may look some more good.", "wp_sh") ?></small></p>
				</td>
			</tr>
		</table>
	<h3><a href="javascript:showhide('id1');" name="visula_editor_settings"><?php _e("2. Visual Editor Settings", 'wp_sh') ?></a></h3>
	<div id="id1" style="display:none;">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Add TinyMCE Button', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="select_insert" value="1" <?php if($wp_sh_setting_opt['select_insert'] == 1){echo 'checked="checked" ';} ?>/> Select &amp; Insert <input type="checkbox" name="codebox" value="1" <?php if($wp_sh_setting_opt['codebox'] == 1){echo 'checked="checked" ';} ?>/> Codebox
					<p><small><?php _e("Enable/Disable TinyMCE Buttons. They can help you to type <code>&lt;pre&gt;</code> tag in 'Visual Editor'.<br />'Select &amp; Insert' will help you to wrap your code on the post or page in <code>&lt;pre&gt;</code> tag or to update values of previously-markuped code.<br />'CodeBox' will allow you to paste your code into the post or page and wrap in <code>&lt;pre&gt;</code> tag automatically, keeping indent by tabs.<br/ >In 'Visual Editor', 'Select &amp; Insert' will appear as 'pre' icon and 'CodeBox' will appear as 'CODE' icon.", "wp_sh") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Window size', 'wp_sh') ?></th> 
				<td>
					<select name="button_window_size">
						<option value="100" <?php if ($wp_sh_setting_opt['button_window_size'] == "100") {echo 'selected="selected"';} ?>>100%</option>
						<option value="105" <?php if ($wp_sh_setting_opt['button_window_size'] == "105") {echo 'selected="selected"';} ?>>105%</option>
						<option value="110" <?php if ($wp_sh_setting_opt['button_window_size'] == "110") {echo 'selected="selected"';} ?>>110%</option>
					</select>
					<p><small><?php _e("Choose size of pop-up window at the click of buttons.", "wp_sh") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Place the buttons in', 'wp_sh') ?></th> 
				<td>
					<select name="button_row">
						<option value="1" <?php if ($wp_sh_setting_opt['button_row'] == "1") {echo 'selected="selected"';} ?>><?php _e("1st row", "wp_sh") ?></option>
						<option value="2" <?php if ($wp_sh_setting_opt['button_row'] == "2") {echo 'selected="selected"';} ?>><?php _e("2nd row", "wp_sh") ?></option>
						<option value="3" <?php if ($wp_sh_setting_opt['button_row'] == "3") {echo 'selected="selected"';} ?>><?php _e("3rd row", "wp_sh") ?></option>
						<option value="4" <?php if ($wp_sh_setting_opt['button_row'] == "4") {echo 'selected="selected"';} ?>><?php _e("4th row", "wp_sh") ?></option>
					</select> <?php _e("of TinyMCE toolbar.", "wp_sh") ?>
					<p><small><?php _e("Choose TinyMCE toolbar row which buttons will be placed in.", "wp_sh") ?></small></p>
				</td>
			</tr>
		</table>
	</div>
	<h3><a href="javascript:showhide('id2');" name="html_editor_settings"><?php _e("3. HTML Editor Settings", 'wp_sh') ?></a></h3>
	<div id="id2" style="display:none;">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Add Quicktag Button', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="quicktag" value="1" <?php if($wp_sh_setting_opt['quicktag'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("Enable/Disable Quicktag, 'SH pre' button. It can help you to type <code>&lt;pre&gt;</code> tag in 'HTML Editor'.<br />'SH pre' button makes easy to escape your code to HTML entities and wrap it in <code>&lt;pre&gt;</code> tag.", 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Load jQuery and jQuery UI', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="quicktag_jquery" value="1" <?php if($wp_sh_setting_opt['quicktag_jquery'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("When 'SH pre' or other buttons on the editor don't pop up a dialog box, try to enable/disable this option.<br />If you use WordPress 3.0.6 or older, you should enable this option usually.<br />After this option is enabled, jQuery and jQuery UI will be loaded on the editor.", 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Support shortcode', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="editor_shorcode" value="1" <?php if($wp_sh_setting_opt['editor_shorcode'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("This plugin will allow to use <a href=\"http://en.support.wordpress.com/code/posting-source-code/\">shortcode</a> in HTML editor for highlighting codes.<br />If 'Support shortcode' in '7. bbPress Settings' is enabled, this option will also be enabled automatically.", 'wp_sh') ?></small></p>
				</td>
			</tr>
		</table>
	</div>
	<h3><a href="javascript:showhide('id3');" name="common_editor_settings"><?php _e("4. Common Settings for Editor", 'wp_sh') ?></a></h3>
	<div id="id3" style="display:none;">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Allow users to post codes', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="editor_no_unfiltered_html" value="1" <?php if($wp_sh_setting_opt['editor_no_unfiltered_html'] == 1){echo 'checked="checked" ';} ?>/> <?php _e('Allow users without unfiltered_htm capability to post a post with codes', 'wp_sh') ?><br />
					<p><small><?php _e("After you enable this option, users without <a href=\"http://codex.wordpress.org/Roles_and_Capabilities\">unfiltered_html</a> capability can edit a post with codes in the editor.<br />Not all users can edit a post with codes, Allowed users are limited to users that can edit a post originally. e.g. Author and Contributor.<br />If you enable 'Highlight codes in bbPress' option, this option will be ignored and enabled virtually.<br />This plugin will also allow users that can edit a topic/reply in the editor to edit a topic/reply with codes automatically.<br />So, 'Forum Moderator' and 'Forum Participant' also can edit a topic/reply with codes in the editor within their capabilities.", 'wp_sh') ?></small></p>
				</td>
			</tr>
		</table>
	</div>
	<h3><a href="javascript:showhide('id4');" name="comment_form_settings"><?php _e("5. Comment Form Settings", 'wp_sh') ?></a></h3>
	<div id="id4" style="display:none;">
		<table class="form-table">
		<?php if (version_compare(get_bloginfo('version'), "3.0", ">=")) { ?>
			<tr valign="top">
				<th scope="row"><?php _e('Add Comment HL Button', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="comment_hl_bt_enable" value="1" <?php if($wp_sh_setting_opt['comment_hl_bt_enable'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("'Comment Highlighter buttons' helps visitors to post a comment with their sourcecodes and highlight them.<br />Visitors can wrap their sourcecode in <code>&lt;pre&gt;</code> tag or shortcode easily.<br />Before you use 'Comment Highlighter buttons', you must enable 'comments' in 'Higlight your code in' option.<br />After this option is enabled, 'comments' will be enabled automatically.", 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Tag inserted by buttons', 'wp_sh') ?></th>
				<td>
					<input type="radio" id="" name="comment_hl_bt_tag" value="pre" <?php if($wp_sh_setting_opt['comment_hl_bt_tag'] == "pre"){echo 'checked="checked"';}?> /><?php _e('&lt;pre&gt; tag', 'wp_sh') ?> <input type="radio" id="" name="comment_hl_bt_tag" value="shortcode" <?php if($wp_sh_setting_opt['comment_hl_bt_tag'] == "shortcode"){echo 'checked="checked"';}?> /><?php _e('Shortcode', 'wp_sh') ?><br />
					<p><small><?php _e("If <code>&lt;pre&gt;</code> tag is chosen, visitors can wrap their sourcecode in <code>&lt;pre&gt;</code> tag easily using buttons.<br />This plugin also allows to use <a href=\"http://en.support.wordpress.com/code/posting-source-code/\">shortcode</a> instead of <code>&lt;pre&gt;</code> tag.", 'wp_sh') ?></small></p>
				</td>
			</tr>
		<?php } ?>
			<tr valign="top">
				<th scope="row"><?php _e('Support shortcode', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="comment_hl_bt_shorcode" value="1" <?php if($wp_sh_setting_opt['comment_hl_bt_shorcode'] == 1){echo 'checked="checked" ';} ?>/><br />
		<?php if (version_compare(get_bloginfo('version'), "3.0", ">=")) { ?>
					<p><small><?php _e("This plugin will allow to use <a href=\"http://en.support.wordpress.com/code/posting-source-code/\">shortcode</a> for highlighting codes.<br />After 'Shortcode' is chosen in 'Tag inserted by buttons' option, this option will be enabled automatically.", 'wp_sh') ?></small></p>
		<?php } else { ?>
					<p><small><?php _e("This plugin will allow to use <a href=\"http://en.support.wordpress.com/code/posting-source-code/\">shortcode</a> for highlighting codes.<br />Before you enable this option, you must enable 'comments' in 'Higlight your code in' option.", 'wp_sh') ?></small></p>
		<?php } ?>
				</td>
			</tr>
		<?php if (version_compare(get_bloginfo('version'), "3.0", ">=")) { ?>
			<tr valign="top">
				<th scope="row"><?php _e('Description', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="comment_hl_description_before_enable" value="1" <?php if($wp_sh_setting_opt['comment_hl_description_before_enable'] == 1){echo 'checked="checked" ';} ?>/> <?php _e('Add description', 'wp_sh') ?><br />
					<textarea name="wp_sh_comment_hl_description_before" rows="8" style="width:500px"><?php echo esc_html(wp_sh_valid_text(get_option('wp_sh_comment_hl_description_before'), $wp_sh_allowed_str)); ?></textarea>
					<p><small><?php _e("Enter description that is shown before the comment form.", 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Stylesheet', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="comment_hl_stylesheet_enable" value="1" <?php if($wp_sh_setting_opt['comment_hl_stylesheet_enable'] == 1){echo 'checked="checked" ';} ?>/> <?php _e('Use additional CSS', 'wp_sh') ?><br />
					<textarea name="wp_sh_comment_hl_stylesheet" rows="8" style="width:500px"><?php echo esc_html(wp_sh_valid_css(strip_tags(get_option('wp_sh_comment_hl_stylesheet')))); ?></textarea>
					<p><small><?php _e("If it is needed, define additional stylesheet for the comment form.", 'wp_sh') ?></small></p>
				</td>
			</tr>
		<?php } ?>
		</table>
	</div>
	<h3><a href="javascript:showhide('id5');" name="comment_editor_settings"><?php _e("6. Comment Editor Settings", 'wp_sh') ?></a></h3>
	<div id="id5" style="display:none;">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Add Quicktag Button', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="comment_quicktag" value="1" <?php if($wp_sh_setting_opt['comment_quicktag'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("Enable/Disable Quicktag, 'SH pre' button. It can help you to type <code>&lt;pre&gt;</code> tag in 'Comment Editor'.<br />'SH pre' button makes easy to escape your code to HTML entities and wrap it in <code>&lt;pre&gt;</code> tag.<br />Note: Only when 'comments' in 'Higlight your code in' option is enabled, It will be shown on 'Comment Editor' in the admin panel.", 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Load jQuery and jQuery UI', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="comment_jquery" value="1" <?php if($wp_sh_setting_opt['comment_jquery'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("When 'SH pre' or other buttons on 'Comment Editor' don't pop up a dialog box, try to enable/disable this option.<br />If you use WordPress 3.2.1 or older, you should enable this option usually.<br />After this option is enabled, jQuery and jQuery UI will be loaded on 'Comment Editor'.<br />Note: Only when 'comments' in 'Higlight your code in' option is enabled, jQuery and jQuery UI will be loaded.", 'wp_sh') ?></small></p>
				</td>
			</tr>
		</table>
	</div>
	<h3><a href="javascript:showhide('id6');" name="wpsh_wiget_settings"><?php _e("7. WP SyntaxHighlighter Widget Settings", 'wp_sh') ?></a></h3>
	<div id="id6" style="display:none;">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Tag inserted by buttons', 'wp_sh') ?></th>
				<td>
					<input type="radio" id="" name="wiget_tag" value="pre" <?php if($wp_sh_setting_opt['wiget_tag'] == "pre"){echo 'checked="checked"';}?> /><?php _e('&lt;pre&gt; tag', 'wp_sh') ?> <input type="radio" id="" name="wiget_tag" value="shortcode" <?php if($wp_sh_setting_opt['wiget_tag'] == "shortcode"){echo 'checked="checked"';}?> /><?php _e('Shortcode', 'wp_sh') ?><br />
					<p><small><?php _e("If <code>&lt;pre&gt;</code> tag is chosen, By clicking buttons on the widget form, your sourcecode is wrapped in <code>&lt;pre&gt;</code> tag.<br />This plugin also allows to use <a href=\"http://en.support.wordpress.com/code/posting-source-code/\">shortcode</a> instead of <code>&lt;pre&gt;</code> tag.", 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Support shortcode', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="wiget_shorcode" value="1" <?php if($wp_sh_setting_opt['wiget_shorcode'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("This plugin will allow to use <a href=\"http://en.support.wordpress.com/code/posting-source-code/\">shortcode</a> in WP SyntaxHighlighter Widget for highlighting codes.<br />After 'Shortcode' is chosen in 'Tag inserted by buttons' option, this option will be enabled automatically.", 'wp_sh') ?></small></p>
				</td>
			</tr>
		</table>
	</div>
	<h3><a href="javascript:showhide('id7');" name="bbpress_settngs"><?php _e("8. bbPress Settings", 'wp_sh') ?></a></h3>
	<div id="id7" style="display:none;">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Add bbPress HL Button', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="bbpress_hl_bt_enable" value="1" <?php if($wp_sh_setting_opt['bbpress_hl_bt_enable'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("'bbPress Highlighter buttons' helps visitors to post a topic or reply with their sourcecodes and highlight them in bbPress.<br />Visitors can wrap their sourcecode in <code>&lt;pre&gt;</code> tag or shortcode easily.<br />After this option is enabled, 'Highlight codes in bbPress' will be enabled automatically.", 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Tag inserted by buttons', 'wp_sh') ?></th>
				<td>
					<input type="radio" id="" name="bbpress_hl_bt_tag" value="pre" <?php if($wp_sh_setting_opt['bbpress_hl_bt_tag'] == "pre"){echo 'checked="checked"';}?> /><?php _e('&lt;pre&gt; tag', 'wp_sh') ?> <input type="radio" id="" name="bbpress_hl_bt_tag" value="shortcode" <?php if($wp_sh_setting_opt['bbpress_hl_bt_tag'] == "shortcode"){echo 'checked="checked"';}?> /><?php _e('Shortcode', 'wp_sh') ?><br />
					<p><small><?php _e("If <code>&lt;pre&gt;</code> tag is chosen, visitors can wrap their sourcecode in <code>&lt;pre&gt;</code> tag easily using buttons.<br />This plugin also allows to use <a href=\"http://en.support.wordpress.com/code/posting-source-code/\">shortcode</a> instead of <code>&lt;pre&gt;</code> tag.", 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Support shortcode', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="bbpress_hl_bt_shorcode" value="1" <?php if($wp_sh_setting_opt['bbpress_hl_bt_shorcode'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("This plugin will allow to use <a href=\"http://en.support.wordpress.com/code/posting-source-code/\">shortcode</a> for highlighting codes.<br />After 'Shortcode' is chosen in 'Tag inserted by buttons' option, this option will be enabled automatically.", 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Anonymous users', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="bbpress_hl_bt_guest" value="1" <?php if($wp_sh_setting_opt['bbpress_hl_bt_guest'] == 1){echo 'checked="checked" ';} ?>/> <?php _e('Allow guest users to post a topic/reply with codes', 'wp_sh') ?><br />
					<p><small><?php _e("Allow guest users without accounts to post a topic/reply with codes.<br />Before you enable this option, you must enable 'Allow Anonymous Posting' in the bbPress setting panel.", 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Description', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="bbpress_hl_description_before_enable" value="1" <?php if($wp_sh_setting_opt['bbpress_hl_description_before_enable'] == 1){echo 'checked="checked" ';} ?>/> <?php _e('Add description', 'wp_sh') ?><br />
					<textarea name="wp_sh_bbpress_hl_description_before" rows="8" style="width:500px"><?php echo esc_html(wp_sh_valid_text(get_option('wp_sh_bbpress_hl_description_before'), $wp_sh_allowed_str)); ?></textarea>
					<p><small><?php _e("Enter description that is shown before the post form.", 'wp_sh') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Stylesheet', 'wp_sh') ?></th>
				<td>
					<input type="checkbox" name="bbpress_hl_stylesheet_enable" value="1" <?php if($wp_sh_setting_opt['bbpress_hl_stylesheet_enable'] == 1){echo 'checked="checked" ';} ?>/> <?php _e('Use additional CSS', 'wp_sh') ?><br />
					<textarea name="wp_sh_bbpress_hl_stylesheet" rows="8" style="width:500px"><?php echo esc_html(wp_sh_valid_css(strip_tags(get_option('wp_sh_bbpress_hl_stylesheet')))); ?></textarea>
					<p><small><?php _e("If it is needed, define additional stylesheet for bbPress.", 'wp_sh') ?></small></p>
				</td>
			</tr>

		</table>
	</div>
	<h3><a href="javascript:showhide('id8');" name="loaded_languages"><?php _e("9. Default Languages Settings", 'wp_sh') ?></a></h3>
	<div id="id8" style="display:none;">
		<?php 
		$wp_sh_brush_files = get_option('wp_sh_brush_files');
		if (is_array($wp_sh_brush_files)) {
		ksort($wp_sh_brush_files);
		?>
		<table class="form-table">
			<tr valign="top"> 
				<th scope="row"></th>
				<td>
					<p><small><?php _e("Define pre-loaded or dynamic-autoloaded languages by default.<br />You should disable unused languages to prevent inserting unnecessary elements into your posts or pages.", "wp_sh") ?></small></p>
				</td>
			</tr>
			<?php
			foreach ($wp_sh_brush_files as $lang => $val) {
				$brush_enable = $val[3];
				if ($val[2] == "3.0") {
					$brush_ver = "3.0";
				} elseif (($brush_enable == "true" && $val[2] == "2.1") || ($brush_enable == "added" && $val[2] == "all")) {
					$brush_ver = "2.1, 3.0";
				} elseif (($brush_enable == "added" || $brush_enable == "removed") && $val[2] == "2.1") {
					$brush_ver = "2.1";
				} elseif (($brush_enable == "true" || $brush_enable == "false") && $val[2] == "2.1") {
					$brush_ver = "2.1, 3.0";
				} elseif ($val[2] == "1.5") {
					$brush_ver = "2.1, 3.0";
				}
				if ($brush_enable == 'true' || $brush_enable == 'false') {
			?>
			<tr valign="top">
				<th scope="row"><?php echo $lang.'(ver.'.$brush_ver.')'; ?></strong></th>
				<td>
				<label for="<?php echo $lang; ?>_Yes"><input type="radio" id="<?php echo $lang; ?>_Yes" name="<?php echo $lang; ?>" value="true" <?php if($brush_enable == 'true'){echo 'checked="checked"';}?> /><?php _e('Yes', 'wp_sh') ?></label>
				<label for="<?php echo $lang; ?>_No"><input type="radio" id="<?php echo $lang; ?>_No" name="<?php echo $lang; ?>" value="false" <?php if($brush_enable == 'false'){echo 'checked="checked"';}?> /><?php _e('No', 'wp_sh') ?></label>
				</td>
			</tr>
				<?php } elseif ($brush_enable == 'added' || $brush_enable == 'removed') { ?>
			<tr valign="top">
				<th scope="row"><?php echo $lang.'(ver.'.$brush_ver.')'; ?></strong></th>
				<td>
				<label for="<?php echo $lang; ?>_Added"><input type="radio" id="<?php echo $lang; ?>_Added" name="<?php echo $lang; ?>" value="added" <?php if($brush_enable == 'added'){echo 'checked="checked"';}?> /><?php _e('Yes', 'wp_sh') ?></label>
				<label for="<?php echo $lang; ?>_Removed"><input type="radio" id="<?php echo $lang; ?>_Removed" name="<?php echo $lang; ?>" value="removed" <?php if($brush_enable == 'removed'){echo 'checked="checked"';}?> /><?php _e('No', 'wp_sh') ?></label>
				</td>
			</tr>
				<?php }
			} ?>
		</table>
		<?php } ?>
	</div>
		<p class="submit">
		<input type="submit" name="WP_SH_Setting_submit" value="<?php _e('Save Changes', 'wp_sh') ?>" />
		</p>
	</form>
	<h3><?php _e("10. Restore all settings to default", 'wp_sh') ?></h3>
	<form method="post" action="" onsubmit="return confirmreset()">
		<?php wp_nonce_field("wp-sh-reset_options", "_wpnonce_reset_options"); ?>
		<input type="hidden" name="wp_sh_reset" value="true" />
		<p class="submit">
		<input type="submit" name="WP_SH_Reset" value="<?php _e('Reset All Settings', 'wp_sh') ?>" />
		</p>
	</form>
	<h3><?php _e('11. Your Current Theme', 'wp_sh') ?></h3>
	<div style="width:600px; margin-left:20px">
	<pre class="brush: php; html-script: true; highlight: 9">
	&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"&gt;
	&lt;html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"&gt;
	&lt;head&gt;
		&lt;meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /&gt;
		&lt;title&gt;Syntax Highlighting Example&lt;/title&gt;
	&lt;/head&gt;
	&lt;body style="width:500px"&gt;
		&lt;h1&gt;Syntax Highlighting Example&lt;/h1&gt;
		&lt;p&gt;&lt;?php echo 'Hello World!' ?&gt;&lt;/p&gt;
		&lt;p&gt;XHTML with PHP script.&lt;/p&gt;
		&lt;div class="tabs"&gt;
  			TAB	TAB		TAB	TAB
				TAB	TAB	TAB		TAB
			TAB		TAB		TAB	TAB
		&lt;p&gt;For 'Smart tabs'.&lt;/p&gt;
		&lt;/div&gt;

		&lt;p&gt;http://wordpress.org/&lt;/p&gt;
	&lt;/body&gt;
	&lt;/html&gt;</pre>
	</div>
	<h3><a href="javascript:showhide('id9');" name="system_info"><?php _e("12. Your System Info", 'wp_sh') ?></a></h3>
	<div id="id9" style="display:none; margin-left:20px;">
	<p>
	<?php _e('Server OS:', 'wp_sh') ?> <?php echo php_uname('s').' '.php_uname('r'); ?><br />
	<?php _e('PHP version:', 'wp_sh') ?> <?php echo phpversion(); ?><br />
	<?php _e('MySQL version:', 'wp_sh') ?> <?php echo mysql_get_server_info(); ?><br />
	<?php _e('WordPress version:', 'wp_sh') ?> <?php bloginfo("version"); ?><br />
	<?php _e('Site URL:', 'wp_sh') ?> <?php if(function_exists("home_url")) { echo home_url(); } else { echo get_option('home'); } ?><br />
	<?php _e('WordPress URL:', 'wp_sh') ?> <?php echo site_url(); ?><br />
	<?php _e('WordPress language:', 'wp_sh') ?> <?php bloginfo("language"); ?><br />
	<?php _e('WordPress character set:', 'wp_sh') ?> <?php bloginfo("charset"); ?><br />
	<?php _e('WordPress theme:', 'wp_sh') ?> <?php $wp_sh_theme = get_theme(get_current_theme()); echo $wp_sh_theme['Name'].' '.$wp_sh_theme['Version']; ?><br />
	<?php _e('WP SyntaxHighlighter version:', 'wp_sh') ?> <?php global $wp_sh_ver; echo $wp_sh_ver; ?><br />
	<?php _e('WP SyntaxHighlighter DB version:', 'wp_sh') ?> <?php echo get_option('wp_sh_checkver_stamp'); ?><br />
	<?php _e('WP SyntaxHighlighter URL:', 'wp_sh') ?> <?php echo $wp_sh_plugin_url; ?><br />
	<?php _e('Your browser:', 'wp_sh') ?> <?php echo $_SERVER['HTTP_USER_AGENT']; ?>
	</p>
	</div>
	<p>
	<?php _e("To report a bug ,submit requests and feedback, ", 'wp_sh') ?><?php _e('Use <a href="http://wordpress.org/tags/wp-syntaxhighlighter?forum_id=10">Forum</a> or <a href="http://www.near-mint.com/blog/contact">Mail From</a>', 'wp_sh') ?>
	</p>
	</div>
<?php }

?>