<?php
/*
by Redcocker
Last modified: 2011/12/3
License: GPL v2
http://www.near-mint.com/blog/
*/

//Load bootstrap file
require_once( dirname( dirname(__FILE__) ) .'/wp-syntaxhighlighter-bootstrap.php');

global $wpdb;

//Check for rights
if (!is_user_logged_in() || (!current_user_can('edit_posts') && !current_user_can('edit_pages') && !current_user_can('edit_topics') && !current_user_can('edit_replies')))
	wp_die(__("You are not allowed to access this file.", "wp_sh"));
$wp_sh_shtb_ins_url = plugin_dir_url( __FILE__ );
$wp_sh_setting_opt = get_option('wp_sh_setting_opt');
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>WP SyntaxHighlighter Select &amp; Insert</title>
<!-- 	<meta http-equiv="Content-Type" content="<?php// bloginfo('html_type'); ?>; charset=<?php //echo get_option('blog_charset'); ?>" /> -->
<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $wp_sh_shtb_ins_url; ?>tinymce.js?ver=0.2.9"></script>
<script language="javascript" type="text/javascript" src="<?php echo $wp_sh_shtb_ins_url; ?>re_write.js?ver=0.2.9"></script>
<base target="_self" />
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
	<form name="shtb_ins" action="#">
		<table border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td nowrap="nowrap"><label for="shtb_ins_language"><?php _e("Select Language", 'wp_sh'); ?></label>
				</td>
				<td>
					<select id="shtb_ins_language" name="shtb_ins_language" style="width: 200px">
					<?php 
						if ($wp_sh_setting_opt['lib_version'] == '3.0') {
							$shtb_ins_language = get_option('wp_sh_language3');
						} elseif ($wp_sh_setting_opt['lib_version'] == '2.1') {
							$shtb_ins_language = get_option('wp_sh_language2');
						}
						if (is_array($shtb_ins_language)) {
							asort($shtb_ins_language);
							echo "\n";
							foreach ($shtb_ins_language as $key => $val) {
								if ($val[1] == 'true' || $val[1] =='added') {
									echo '						<option value="'.$key.'">'.$val[0]."</option>\n";
								}
							}
							echo "\n";
						} 
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td nowrap="nowrap" valign="top">
					<label for="shtb_ins_linenumbers"><?php _e("Show Line Number", 'wp_sh'); ?></label>
				</td>
				<td>
					<label><input name="shtb_ins_linenumbers" id='shtb_ins_linenumbers' type="checkbox" <?php if ($wp_sh_setting_opt['gutter'] == "true") {echo 'checked="checked"';} ?>/></label>
				</td>
			</tr>
		</table>
		<div class="mceActionPanel">
			<div style="float: left">
				<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'wp_sh'); ?>" onclick="insertSHTBINScode();" />
			</div>
			<div style="float: right">
				<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'wp_sh'); ?>" onclick="tinyMCEPopup.close();" />
			</div>
		</div>
	</form>
</body>
</html>