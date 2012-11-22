<?php
global $wpdb;
$ninja_forms_subs_table_name = $wpdb->prefix . "wpnj_forms_subs";
if(!empty($_POST) && check_admin_referer('ninja_save_plugin_settings','ninja_plugin_settings')){
	$current_settings = get_option("ninja_forms_settings");
	
	foreach($_POST as $key => $val){
		if($key != 'submitted' && $key != 'submit'){
			$current_settings[$key] = $val;
		}
	}
	update_option("ninja_forms_settings", $current_settings);
}
$plugin_settings = get_option("ninja_forms_settings");

$color_array = scandir(NINJA_FORMS_DIR."/css/tooltips");
unset($color_array[0]);
unset($color_array[1]);
	
$upload_dir = dirname(__FILE__);
$upload_dir = str_replace("includes", "", $upload_dir);
$upload_dir .= "uploads/";

$max_upload = (int)(ini_get('upload_max_filesize'));
$max_post = (int)(ini_get('post_max_size'));
$memory_limit = (int)(ini_get('memory_limit'));
$upload_mb = min($max_upload, $max_post, $memory_limit);
?>

<div class="wrap">
<div id="icon-ninja-custom-forms" class="icon32"><img src="<?php echo NINJA_FORMS_URL;?>/images/wpnj-ninja-head.png"></div>
<h2><?php printf(__('Ninja Forms %s - Plugin Settings', 'ninja-forms'), NINJA_FORMS_TYPE);?></h2>
<div class="wrap-left">
<h3><?php _e('Version', 'ninja-forms');?> <?php echo NINJA_FORMS_VERSION;?></h3>
<form id="" name="" action="" method="post">
<?php wp_nonce_field('ninja_save_plugin_settings','ninja_plugin_settings'); ?>
<input type="hidden" name="submitted" value="yes">
<input type="hidden" name="default_style" value="unchecked"><input type="checkbox" name="default_style" id="default_style" value="checked" <?php echo $plugin_settings['default_style'];?>><label for="default_style"> <?php _e('Use Ninja Forms Default Stylesheet', 'ninja-forms');?></label><br />
<input type="hidden" name="admin_help" value="unchecked"><input type="checkbox" name="admin_help" id="admin_help" value="checked" <?php echo $plugin_settings['admin_help'];?>><label for="admin_help"> <?php _e('Show Admin Help/Tips', 'ninja-forms');?></label><br />
<h4><?php _e('Form Help/Tips hover color scheme', 'ninja-forms');?>:</h4>
	<select name="help_color" id="help_color" class="ninja_forms_settings">
		<?php foreach($color_array as $color){ 
			$color_value = str_replace(".css", "", $color);
			$color_label = ucfirst($color_value);
		?>
			
		<option value="<?php echo $color_value;?>" <?php if($plugin_settings['help_color'] == $color_value){ echo 'selected';} ?>><?php echo $color_label;?></option>
		<?php } ?>
	</select><br />
<label for="help_size"><h4><?php _e('Help/Tip Box Size', 'ninja-forms');?>:</h4> <input type="text" name="help_size" id="help_size" value="<?php echo $plugin_settings['help_size'];?>" class="ninja_forms_settings"></label>

<?php /* Lite version does not include     */ 
	if(NINJA_FORMS_TYPE == 'Pro'){
		require_once(NINJA_FORMS_DIR."/includes/pro/plugin-settings-upload.php");
	}
/*               */ ?>
<input class="button-primary ninja_save_data" type="submit" value="<?php _e('Save Changes', 'ninja-forms');?>">
</form>	
</div>
<?php
	if(NINJA_FORMS_TYPE == 'Lite'){
	?>
<div class="wrap-right">
	<img src="<?php echo NINJA_FORMS_URL;?>/images/wpnj-logo-wt.png" width="263px" height="45px" />
	<h2>Upgrade to Ninja Forms Pro for many more great features including...</h2>
	<ul>
		<li><a href="http://wpninjas.net/?p=827">Save User's Progress</a></li>
		<li><a href="http://wpninjas.net/?p=825">Multi-Part Froms</a></li>
		<li><a href="http://wpninjas.net/?p=542">Front-End Post Submission</a></li>
		<li><a href="http://wpninjas.net/?p=510">1yr Premium Support</a></li>
	</ul>
	<a class="button-primary" href="http://wpninjas.net/?p=562">Upgrade Now!</a>
</div>
	<?php
	}
	?>
</div>