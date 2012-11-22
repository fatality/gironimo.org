<?php
/*
SH pre button
by Redcocker
Last modified: 2011/12/13
License: GPL v2
http://www.near-mint.com/blog/
*/

// Don't bother doing this stuff if the current user lacks permissions
if (((!$wp_sh_setting_opt['editor_no_unfiltered_html'] == 1 && !$wp_sh_setting_opt['highlight_bbpress'] == 1) && !current_user_can('unfiltered_html')) || (!current_user_can('edit_posts') && !current_user_can('edit_pages') && !current_user_can('edit_topics') && !current_user_can('edit_replies')))
	return;

// Load CSS for jQuery UI
if ($wp_sh_setting_opt['quicktag'] == 1 && $wp_sh_setting_opt['quicktag_jquery'] == 1) {
	add_action('admin_head-post.php', 'wp_sh_load_jqueryui_css');
	add_action('admin_head-post-new.php', 'wp_sh_load_jqueryui_css');
	add_action('admin_head-page.php', 'wp_sh_load_jqueryui_css');
	add_action('admin_head-page-new.php', 'wp_sh_load_jqueryui_css');
}

if ($wp_sh_setting_opt['highlight_comment'] == 1 && $wp_sh_setting_opt['comment_quicktag'] == 1 && $wp_sh_setting_opt['comment_jquery'] == 1) {
	add_action('admin_head-comment.php', 'wp_sh_load_jqueryui_css');
}

function wp_sh_load_jqueryui_css() {
	echo '<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/redmond/jquery-ui.css" rel="stylesheet" type="text/css"/>';
}

// Add quicktag to HTML editor
if ($wp_sh_setting_opt['quicktag'] == 1 && (strpos($_SERVER['REQUEST_URI'], 'post.php') ||
	strpos($_SERVER['REQUEST_URI'], 'post-new.php') ||
	strpos($_SERVER['REQUEST_URI'], 'page.php') ||
	strpos($_SERVER['REQUEST_URI'], 'page-new.php'))) {
	add_action('admin_print_footer_scripts', 'wp_sh_add_quicktag' );
}

if ($wp_sh_setting_opt['highlight_comment'] == 1 && $wp_sh_setting_opt['comment_quicktag'] == 1 && strpos($_SERVER['REQUEST_URI'], 'comment.php')) {
	add_action('admin_print_footer_scripts', 'wp_sh_add_quicktag' );
}

function wp_sh_add_quicktag() {
	global $wp_sh_setting_opt;
	if ($wp_sh_setting_opt['quicktag_jquery'] == 1) {
		$dialog_class = "shpre-dialog";
	} else {
		$dialog_class = "wp-dialog";
	}
	echo "\n<script type=\"text/javascript\">
function SHpreGetAreaRange(obj) {
	var pos = new Object();
	
	if (isIE) {
		obj.focus();
		var range = document.selection.createRange();
		var clone = range.duplicate();
		
		clone.moveToElementText(obj);
		clone.setEndPoint( 'EndToEnd', range );

		pos.start = clone.text.length - range.text.length;
		pos.end   = clone.text.length - range.text.length + range.text.length;
  	}

	else if(window.getSelection()) {
		pos.start = obj.selectionStart;
		pos.end   = obj.selectionEnd;
	}

	return pos;
}
var isIE = (navigator.appName.toLowerCase().indexOf('internet explorer')+1?1:0);
function SHpreSurroundHTML() {
	var target = document.getElementById('content');
	var pos = SHpreGetAreaRange(target);
	var val = target.value;
	var range = val.slice(pos.start, pos.end);
	range = range.replace(/&/g,\"&amp;\");
	range = range.replace(/</g,\"&lt;\").replace(/>/g,\"&gt;\");\n";
	if (strpos($_SERVER['REQUEST_URI'], 'comment.php')) {
		echo "	range = range.replace(/\"/g,\"&quot;\").replace(/'/g,\"&#039;\");\n";
		}
    		echo "	var beforeNode = val.slice(0, pos.start);
    	var afterNode  = val.slice(pos.end);
	var insertNode;
//	var tag = prompt('Enter language alias name.'+\"\\n\"+'e.g.: c, javascript, php, xhtml etc.', 'php');

	jQuery(\"#shpre_input_lang\").dialog(
	{
		modal: true,
		height: 'auto',
		width: 250,
		title: '".__("SH pre button", "wp_sh")."',
		resizable: true,
		dialogClass: '".$dialog_class."',
		zIndex: 300000,
		buttons: {
			\"".__("OK", "wp_sh")."\": function() {
				var tag = jQuery(\"#shpre_lang\").val();
				var checked = jQuery(\"#shpre_gutter\").is(':checked');
				var gut;
				if (checked == true) {
					gut = 'true';
				} else {
					gut = 'false';
				}
				var fsl = '".$wp_sh_setting_opt['first_line']."';
				if (range || pos.start != pos.end) {
					insertNode = '<pre class=\"brush: ' + tag + '; gutter: ' + gut + '; first-line: ' + fsl + '; highlight: []; html-script: false\">' + range + '</pre>';
					target.value = beforeNode + insertNode + afterNode;
				} else if (pos.start == pos.end) {
		insertNode = '<pre class=\"brush: ' + tag + '; gutter: ' + gut + '; first-line: ' + fsl + '; highlight: []; html-script: false\">' + range + '</pre>';
					target.value = beforeNode + insertNode + afterNode;
				}
				jQuery(this).dialog('close');
			},
			\"".__("CANCEL", "wp_sh")."\": function() {
				jQuery(this).dialog('close');
			},
		}
	});
}\n\n";
	if (version_compare(get_bloginfo('version'), "3.2.1", "<=")) {
		echo "function SHpreRegisterQtButton() {
	jQuery('#ed_toolbar').each( function() {
		var button = document.createElement('input');
		button.type = 'button';
		button.value = '".__("SH pre", "wp_sh")."';
		button.onclick = SHpreSurroundHTML;
		button.className = 'ed_button';
		button.title = '".__("For WP SyntaxHighlighter", "wp_sh")."';
		button.id = 'ed_prequicktag';

		jQuery(this).append(button);
	});
}
 
SHpreRegisterQtButton();\n";
	} else {
		echo "QTags.addButton( 'ed_prequicktag', '".__("SH pre", "wp_sh")."', SHpreSurroundHTML);\n";
		}
	echo "</script>\n";
	// Add dropdown menu
	echo "<div id=\"shpre_input_lang\" style=\"display:none; font-size:12px;\">
<div style=\"margin-top:15px; margin-left:10px;\">".__("Language", "wp_sh")." <select id=\"shpre_lang\" name=\"shpre_lang\">";
	if ($wp_sh_setting_opt['lib_version'] == '3.0') {
		$language_array = get_option('wp_sh_language3');
	} elseif ($wp_sh_setting_opt['lib_version'] == '2.1') {
		$language_array = get_option('wp_sh_language2');
	}
	if (is_array($language_array)) {
		asort($language_array);
		echo "\n";
		foreach ($language_array as $key => $val) {
			if ($val[1] == 'true' || $val[1] =='added') {
				echo '	<option value="'.$key.'">'.$val[0]."</option>\n";
			}
		}
	}
	echo "</select></div>\n";
	echo "<div style=\"margin-top:10px; margin-left:10px;\">".__("Show Line Number", "wp_sh")." <input type=\"checkbox\" id=\"shpre_gutter\" name=\"shpre_gutter\" value=\"true\" ";
	if ($wp_sh_setting_opt['gutter'] == "true") {
		echo 'checked="checked" ';
	}
	echo " /></div>
</div>\n";
}

// Load jQuery and jQuery UI
if ($wp_sh_setting_opt['quicktag'] == 1 && $wp_sh_setting_opt['quicktag_jquery'] == 1) {
	add_action('admin_footer-post.php', 'wp_sh_load_jqueryui');
	add_action('admin_footer-post-new.php', 'wp_sh_load_jqueryui');
	add_action('admin_footer-page.php', 'wp_sh_load_jqueryui');
	add_action('admin_footer-page-new.php', 'wp_sh_load_jqueryui');
}

if ($wp_sh_setting_opt['highlight_comment'] == 1 && $wp_sh_setting_opt['comment_quicktag'] == 1 && $wp_sh_setting_opt['comment_jquery'] == 1) {
	add_action('admin_footer-comment.php', 'wp_sh_load_jqueryui');
}

function wp_sh_load_jqueryui() {
	echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>';
}

?>