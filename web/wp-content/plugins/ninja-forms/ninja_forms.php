<?php
/*
Plugin Name: Ninja Forms Lite
Plugin URI: http://ninjaforms.com
Description: Ninja Forms is a webform builder with unparalleled ease of use and features.
Version: 1.3.4
Author: The WP Ninjas
Author URI: http://wpninjas.net
*/

/*
Copyright 2011 WP Ninjas/Kevin Stover.
/*

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Ninja Forms also uses the following jQuery plugins. Their licenses can be found in their respective files.

	jQuery TipTip Tooltip v1.3
	code.drewwilson.com/entry/tiptip-jquery-plugin
	www.drewwilson.com	
	Copyright 2010 Drew Wilson
	
	jQuery MaskedInput v.1.3
	http://digitalbush.co
	Copyright (c) 2007-2011 Josh Bush
	
	jQuery Tablesorter Plugin v.2.0.5
	http://tablesorter.com
	Copyright (c) Christian Bach 2012
	
	jQuery AutoNumeric Plugin v.1.7.4-B
	http://www.decorplanit.com/plugin/
	By: Bob Knothe And okolov Yura aka funny_falcon
	
*/
global $version_compare, $wpdb, $wp_version;
define("NINJA_FORMS_DIR", WP_PLUGIN_DIR."/ninja-forms");
define("NINJA_FORMS_URL", WP_PLUGIN_URL."/ninja-forms");
define("NINJA_FORMS_VERSION", "1.3.4");
define("NINJA_FORMS_TYPE", "Lite");

if(session_id() == '') {
	session_start();
}
$_SESSION['NINJA_FORMS_DIR'] = NINJA_FORMS_DIR;
$_SESSION['NINJA_FORMS_URL'] = NINJA_FORMS_URL;

function ninja_forms_load_lang() {
	$plugin_dir = basename(dirname(__FILE__));
	$lang_dir = $plugin_dir.'/lang/';
	load_plugin_textdomain( 'ninja-forms', false, $lang_dir );
}
add_action('init', 'ninja_forms_load_lang');

$plugin_settings = get_option("ninja_forms_settings");

$version_compare = version_compare( $wp_version, '3.2-Beta1' , '>=');
$ninja_forms_table_name = $wpdb->prefix . "ninja_forms";
$ninja_forms_fields_table_name = $wpdb->prefix . "ninja_forms_fields";
$ninja_forms_subs_table_name = $wpdb->prefix. "ninja_forms_subs";

if(isset($plugin_settings['version'])){
	$current_version = $plugin_settings['version'];
}else{
	$current_version = '';
}
if(version_compare($current_version, '1.3.1' , '<')){
	ninja_forms_initial_setup();
}

if(NINJA_FORMS_TYPE == 'Pro'){
	add_filter( 'http_request_args', 'ninja_forms_ignore_repo', 5, 2 );
}

function ninja_forms_ignore_repo( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) )
		return $r; // Not a plugin update request. Bail immediately.
	$plugins = unserialize( $r['body']['plugins'] );
	unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
	unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
	$r['body']['plugins'] = serialize( $plugins );
	return $r;
}

/*
if($plugin_settings['product_key'] != ''){
	require_once(WP_PLUGIN_DIR.NINJA_FORMS_DIR."/includes/plugin-update-checker.php");
	$ninja_forms_update = new PluginUpdateChecker(
		'http://ninjaforms.com/version/current_version.json.php', 
		__FILE__, 
		'ninja-forms-lite'
	);
	$ninja_forms_update->addQueryArgFilter('ninja_add_keys');
	function ninja_add_keys($query){
		$plugin_settings = get_option("ninja_forms_settings");
		$email = $plugin_settings['product_email'];
		$key = $plugin_settings['product_key'];
		$query['email'] = $email;
		$query['key'] = $key;
		return $query;
	}
}
*/



add_action('admin_menu', 'ninja_add_forms_menu');
function ninja_add_forms_menu(){
	$plugins_url = plugins_url();
	$page = add_menu_page("Ninja Forms ".NINJA_FORMS_TYPE , "Ninja Forms ".NINJA_FORMS_TYPE, "administrator", "ninja-custom-forms", "ninja_edit_forms", NINJA_FORMS_URL."/images/ninja-head-ico-small.png", 55);
	$edit = add_submenu_page("ninja-custom-forms", "Edit Forms", "Edit Forms", "administrator", "ninja-custom-forms", "ninja_edit_forms");
	$settings = add_submenu_page("ninja-custom-forms", "Ninja Form Settings", "Plugin Settings", "administrator", "ninja-forms-settings", "ninja_forms_settings");
	//$import = add_submenu_page("ninja-custom-forms", "Import/Export", "Import / Export", "administrator", "ninja-forms-impexp", "ninja_forms_impexp");
	add_action('admin_print_styles-' . $page, 'ninja_form_admin_css');
	add_action('admin_print_styles-' . $page, 'ninja_form_admin_js');	
	add_action('admin_print_styles-' . $settings, 'ninja_form_admin_js');	
	add_action('admin_print_styles-' . $settings, 'ninja_form_admin_css');	
}

function ninja_forms_settings(){
	require_once(NINJA_FORMS_DIR."/includes/plugin_settings.php");
}

function ninja_edit_forms(){
	global $wpdb, $version_compare;
	
	$ninja_forms_table_name = $wpdb->prefix . "ninja_forms";
	$ninja_forms_fields_table_name = $wpdb->prefix . "ninja_forms_fields";
	$ninja_forms_subs_table_name = $wpdb->prefix. "ninja_forms_subs";

	require_once(NINJA_FORMS_DIR."/includes/save.php");	
	require_once(NINJA_FORMS_DIR."/includes/setup_vars.php");


	?>

	<div class="wrap">
		<?php if($current_tab == 'fields'){ ?><form id="ninja_form_fields" name="" action="" method="post"><?php } ?>
		<div id="icon-ninja-custom-forms" class="icon32"><img src="<?php echo NINJA_FORMS_URL;?>/images/wpnj-ninja-head.png"></div>
		<h2 id="ninja_test"><?php _e('Ninja Forms', 'ninja-forms'); ?> <?php echo NINJA_FORMS_TYPE;?></h2><?php if($current_tab == 'list'){ ?><h2><input class="button-secondary ninja_new_form" id="" name="" type="button" value="<?php _e('Add New', 'ninja-forms'); ?>" /></h2><?php }elseif($form_id == 'new'){  _e('- Add A New Form', 'ninja-forms'); }else{ echo "<h3> - $form_title";}?>
		<h3>
			
		</h3>
		<?php if($current_tab != 'list'){ // Are we attempting to list all of the forms?
				$link = esc_url(remove_query_arg(array('tab', 'ninja_form_id')));
		?>
		<div id="nav-menus-frame">
			<?php require_once(NINJA_FORMS_DIR."/includes/sidebar.php"); ?>
		</div><!-- /#menu-settings-column -->
		
		<div id="menu-management-liquid">
			<div id="menu-management">
				<div class="nav-tabs-wrapper">
				<div class="nav-tabs">
					<?php

					foreach($tab_list as $key => $val){
						if($current_tab == $key){
						?>
						<span class="nav-tab nav-tab-active"><?php echo $val;?></span>
						<?php
						}else{
							$link = esc_url(add_query_arg(array('tab' => $key)));
						?>
						<a href="<?php echo $link;?>" class="nav-tab"><?php echo $val;?></a>
						<?php
						}
					}
					?>
				</div>
				</div>
				<div class="menu-edit">
					<?php
					switch($current_tab){
						case 'fields':
							require_once(NINJA_FORMS_DIR."/includes/body_fields.php");
						break;
						case 'settings':
							require_once(NINJA_FORMS_DIR."/includes/body_settings.php");
						break;
						case 'preview':
							require_once(NINJA_FORMS_DIR."/includes/body_preview.php");
						break;
						case 'subs':
							require_once(NINJA_FORMS_DIR."/includes/body_subs.php");				
						break;
					}
					?>
				</div><!-- /.menu-edit -->
			</div><!-- /#menu-management -->
		</div><!-- /#menu-management-liquid -->
	</div><!-- /#nav-menus-frame -->
		<?php }else{ // List all forms
			require_once(NINJA_FORMS_DIR."/includes/body_list.php");
			} ?>
		
		</div><!-- /.wrap-->
<?php
} //End ninja_edit_forms function

if(is_admin()){
	require_once(ABSPATH . 'wp-admin/includes/post.php');
}

if(version_compare( $wp_version, '3.3-beta3-19254' , '<')){
	require_once(NINJA_FORMS_DIR . '/includes/wp-editor-return.php');
}
require_once(ABSPATH . '/wp-admin/includes/template.php');
require_once(NINJA_FORMS_DIR."/includes/scripts_functions.php");
require_once(NINJA_FORMS_DIR."/includes/field_editor_functions.php");
require_once(NINJA_FORMS_DIR."/includes/display_functions.php");
require_once(NINJA_FORMS_DIR."/includes/metabox_functions.php");
require_once(NINJA_FORMS_DIR."/includes/ajax_functions.php");
require_once(NINJA_FORMS_DIR."/includes/processing_functions.php");
require_once(NINJA_FORMS_DIR."/includes/output_xls.php");

function ninja_forms_initial_setup(){

	global $wpdb;
	//Get the tablenames
	$ninja_forms_table_name = $wpdb->prefix . "ninja_forms";
	$ninja_forms_subs_table_name = $wpdb->prefix . "ninja_forms_subs";
	$ninja_forms_fields_table_name = $wpdb->prefix . "ninja_forms_fields";
	
	$plugin_settings = get_option("ninja_forms_settings");
	
	//Set our initial options
	$version = NINJA_FORMS_VERSION;
	$new_db_version = "1.2";
	
	if($plugin_settings['admin_help']){
		$email = $plugin_settings['product_email'];
		$key = $plugin_settings['product_key'];
		$admin_help =  $plugin_settings['admin_help'];
		$default_style =  $plugin_settings['default_style'];
		$help_color = $plugin_settings['help_color'];
		$help_size =  $plugin_settings['help_size'];
		$settings_sidebar_order = $plugin_settings['settings_sidebar_order'];
		$fields_sidebar_order =  $plugin_settings['fields_sidebar_order'];
		$subs_sidebar_order =  $plugin_settings['subs_sidebar_order'];
		$upload_dir = $plugin_settings['upload_dir'];
		$upload_size = $plugin_settings['upload_size'];
		$old_db_version = $plugin_settings['db_version'];
	}else{
		$email = '';
		$key = '';
		$admin_help = 'checked';
		$default_style = 'checked';
		$help_color = 'all-black';
		$help_size = '150';
		$settings_sidebar_order = array('subs-settings', 'append-page-settings', 'append-post-settings');
		$subs_sidebar_order = array('export-subs', 'manage-subs');
		$upload_dir = dirname(__FILE__);
		$upload_dir = str_replace("includes", "", $upload_dir);
		$upload_dir .= "/uploads/";
		$upload_size = "1";
		$old_db_version = $new_db_version;
	}
	$fields_sidebar_order = array('custom-fields', 'defined-fields', 'layout-elements', 'multi-part', 'post-elements');
	$settings_sidebar_order = array('subs-settings', 'append-page-settings', 'append-post-settings');
	$subs_sidebar_order = array('export-subs', 'manage-subs');
	$current_version = $plugin_settings['version'];

	$upload_dir = dirname(__FILE__);
	$upload_dir = str_replace("includes", "", $upload_dir);
	$upload_dir .= "/uploads/";
	$upload_size = "1";	
	
	$options_array = array("product_email" => $email, "product_key" => $key, "subs_sidebar_order" => $subs_sidebar_order, "fields_sidebar_order" => $fields_sidebar_order, "settings_sidebar_order" =>$settings_sidebar_order, "version" => $version, "db_version" => $new_db_version, "admin_help" => $admin_help, "default_style" => $default_style, "help_color" => $help_color, "help_size" => $help_size, "upload_dir" => $upload_dir, "upload_size" => $upload_size);
	update_option("ninja_forms_settings", $options_array);
	
	/* As of version 1.2.4, the default upload directory has changed and the tables were renamed from a wpnj_ prefix to a ninja_ prefix.
		To fix this, we'll reset the upload directory and
		Check for these old names and alter the tables if necessary.
	*/
	
	$old_forms_table_name = $wpdb->prefix . "wpnj_forms";
	$old_forms_subs_table_name = $wpdb->prefix . "wpnj_forms_subs";
	$old_forms_fields_table_name = $wpdb->prefix . "wpnj_forms_fields";
	if($wpdb->get_var("SHOW TABLES LIKE '".$old_forms_table_name."'") == $old_forms_table_name) {
		$sql = "RENAME TABLE  $old_forms_table_name TO $ninja_forms_table_name";
		$wpdb->query($sql);
	}		
	if($wpdb->get_var("SHOW TABLES LIKE '".$old_forms_subs_table_name."'") == $old_forms_subs_table_name) {
		$sql = "RENAME TABLE  $old_forms_subs_table_name TO $ninja_forms_subs_table_name";
		$wpdb->query($sql);
	}		
	if($wpdb->get_var("SHOW TABLES LIKE '".$old_forms_fields_table_name."'") == $old_forms_fields_table_name) {
		$sql = "RENAME TABLE  $old_forms_fields_table_name TO $ninja_forms_fields_table_name";
		$wpdb->query($sql);
	}
	
	
	
	//Check to see if the table already exists. If it does, don't do anything.
	if($wpdb->get_var("SHOW TABLES LIKE '".$ninja_forms_table_name."'") != $ninja_forms_table_name) {
		
		$sql = "CREATE TABLE " . $ninja_forms_table_name . " (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		title longtext NOT NULL,
		show_title text NOT NULL,
		`desc` longtext NOT NULL,
		show_desc text NOT NULL,
		mailto longtext NOT NULL,
		subject longtext NOT NULL,
		new smallint(1) DEFAULT '1' NOT NULL,
		success_msg longtext NOT NULL,
		save_subs longtext NOT NULL,
		send_email longtext NOT NULL,
		ajax longtext NOT NULL,
		landing_page longtext NOT NULL,
		append_page longtext NOT NULL,
		email_from longtext NOT NULL,
		email_msg longtext NOT NULL,
		email_fields longtext NOT NULL,
		email_type mediumtext NOT NULL,
		multi mediumtext NOT NULL,
		multi_options longtext NOT NULL,
		post mediumtext NOT NULL,
		post_options longtext NOT NULL,
		save_status varchar(10) NOT NULL,
		save_status_options longtext NOT NULL,
		UNIQUE KEY id (id)
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		$title = "First Test Form";
		$show_title = '';
		$desc = '';
		$show_desc = '';
		$mailto = "me@me.net";
		$subject = "";
		$new = 0;
		$success_msg = '';
		$save_subs = 'checked';
		$send_email = 'checked';
		$ajax = 'checked';
		$landing_page = '';
		$append_page = '';
		$email_from = '';
		$email_msg = '';
		$email_fields = '';
		$multi = 'unchecked';
		$multi_options = '';
		$post = 'unchecked';
		$post_options = '';
		$save_status = 'unchecked';
		$save_status_options = '';

		$first_form = $wpdb->insert( $ninja_forms_table_name, array( 'title' => $title, 'show_title' => $show_title, 'desc' => $desc, 'show_desc' => $show_desc, 'mailto' => $mailto, 'subject' => $subject, 'new' => $new, 'success_msg' => $success_msg, 'save_subs' => $save_subs, 'send_email' => $send_email, 'ajax' => $ajax, 'landing_page' => $landing_page, 'append_page' => $append_page, 'email_from' => $email_from, 'email_msg' => $email_msg, 'email_fields' => $email_fields, 'multi' => $multi, 'multi_options' => $multi_options, 'post' => $post, 'post_options' => $post_options, 'save_status' => $save_status, 'save_status_options' => $save_status_options) );
	}
	

	if($wpdb->get_var("SHOW COLUMNS FROM $ninja_forms_table_name LIKE 'email_type'") != 'email_type') {
		$sql = "ALTER TABLE  $ninja_forms_table_name ADD email_type mediumtext NOT NULL";
		$wpdb->query($sql);
	}

	
	if(version_compare( $old_db_version, '1.2' , '<')){
		if($wpdb->get_var("SHOW COLUMNS FROM $ninja_forms_table_name LIKE 'save_status'") != 'save_status') {
			$sql = "ALTER TABLE  $ninja_forms_table_name ADD save_status VARCHAR(10) NOT NULL";
			$wpdb->query($sql);
		}	
		if($wpdb->get_var("SHOW COLUMNS FROM $ninja_forms_table_name LIKE 'save_status_options'") != 'save_status_options') {
			$sql = "ALTER TABLE  $ninja_forms_table_name ADD save_status_options LONGTEXT NOT NULL";
			$wpdb->query($sql);
		}
	}	
	
	if($wpdb->get_var("SHOW TABLES LIKE '".$ninja_forms_fields_table_name."'") != $ninja_forms_fields_table_name) {
	
		$sql = "CREATE TABLE " . $ninja_forms_fields_table_name . " (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		label longtext NOT NULL,
		type longtext NOT NULL,
		form_id bigint(11) DEFAULT '0' NOT NULL,
		value longtext NOT NULL,
		field_order bigint(11) DEFAULT '0' NOT NULL,
		req smallint(1) DEFAULT '0' NOT NULL,
		extra longtext NOT NULL,
		class longtext NOT NULL,
		help longtext NOT NULL,
		UNIQUE KEY id (id)
		);";

	
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		$form_id = 1;
		$type = 'textbox';
		$value = "none";
		$req = 0;	
		$label = "New Label";
		$field_order = 9999;
		$class = "Comma,Separated,List";
		$help ="Sample Help Text";
		$extra = array('extra' =>
		array('show_help' => 'unchecked', 'mask' => 'none'));
		$extra = serialize($extra);
		
		$first_row = $wpdb->insert( $ninja_forms_fields_table_name, array( 'id' => NULL, 'label' => $label, 'type' => $type, 'form_id' => $form_id, 'value' => $value, 'field_order' => $field_order, 'req' => $req , 'extra' => $extra,  'class' => $class, 'help' => $help) );
	}

	if($wpdb->get_var("SHOW TABLES LIKE '".$ninja_forms_subs_table_name."'") != $ninja_forms_subs_table_name) {
		$sql = "CREATE TABLE " . $ninja_forms_subs_table_name . " (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		form_id mediumint(9) NOT NULL,
		user_id mediumint(9) NOT NULL,
		form_values longtext NOT NULL,
		date_updated TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		sub_status VARCHAR(15) NOT NULL,
		email longtext NOT NULL,
		password longtext NOT NULL,
		UNIQUE KEY id (id)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}


	if(version_compare( $old_db_version, '1.2' , '<')){
		if($wpdb->get_var("SHOW COLUMNS FROM $ninja_forms_subs_table_name LIKE 'date'") == 'date') {
			$sql = "ALTER TABLE  $ninja_forms_subs_table_name CHANGE  `date`  `date_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
			$wpdb->query($sql);
		}	
			
		if($wpdb->get_var("SHOW COLUMNS FROM $ninja_forms_subs_table_name LIKE 'sub_status'") != 'sub_status') {
			$sql = "ALTER TABLE  $ninja_forms_subs_table_name ADD sub_status VARCHAR( 255 ) NOT NULL";
			$wpdb->query($sql);
		}	
		if($wpdb->get_var("SHOW COLUMNS FROM $ninja_forms_subs_table_name LIKE 'email'") != 'email') {
			$sql = "ALTER TABLE  $ninja_forms_subs_table_name ADD email longtext NOT NULL";
			$wpdb->query($sql);
		}	
		if($wpdb->get_var("SHOW COLUMNS FROM $ninja_forms_subs_table_name LIKE 'password'") != 'password') {
			$sql = "ALTER TABLE  $ninja_forms_subs_table_name ADD password longtext NOT NULL";
			$wpdb->query($sql);
		}	
	}

	wp_schedule_event(time(), 'daily', 'ninja_delete_incomplete');
}

register_activation_hook( __FILE__, 'ninja_forms_initial_setup' );