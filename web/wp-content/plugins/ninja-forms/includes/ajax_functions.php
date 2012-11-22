<?php

// Begin Admin Ajax functions
add_action('wp_ajax_ninja_delete_field', 'ninja_delete_field');
function ninja_delete_field(){
	global $wpdb;
	$field_id = $_POST['id'];
	$ninja_forms_fields_table_name = $wpdb->prefix . "ninja_forms_fields";
	$wpdb->query($wpdb->prepare("DELETE FROM $ninja_forms_fields_table_name WHERE id = %d", $field_id));
	die();
}
add_action('wp_ajax_ninja_new_field', 'ninja_new_field');
function ninja_new_field(){
	global $wpdb;
	$ninja_forms_fields_table_name = $wpdb->prefix . "ninja_forms_fields";

	$field_id = $_POST['id'];
	$type = $_POST['type'];
	$form_id = $_POST['form_id'];

	$value = "none";
	$req = 0;	
	$label = "New Label";
	$field_order = 9999;
	$class = "Comma,Separated,List";
	$help ="Sample Help Text";
	$extra = array('extra' =>
		array('show_help' => 'unchecked', 'mask' => 'none'));
	$extra = serialize($extra);

	if($type == 'spam'){
		$label = "4 + 7 =";
		$req = 1;
		$value = "11";
	}elseif($type == 'submit'){
		$label = 'Submit';
	}elseif($type == 'steps'){
		$label = 'Step';
	}elseif($type == 'posttitle'){
		$label = 'Post Title';
		$req = 1;
	}elseif($type == 'postcontent'){
		$label = 'Post Content';
		$req = 1;
	}elseif($type == 'postexcerpt'){
		$label = 'Post Excerpt';
	}elseif($type == 'postcat'){
		$label = 'Post Category';
	}elseif($type == 'posttags'){
		$label = 'Post Tags';
	}
	
	$new_row = $wpdb->insert( $ninja_forms_fields_table_name, array( 'label' => $label, 'type' => $type, 'form_id' => $form_id, 'value' => $value, 'field_order' => $field_order, 'req' => $req, 'extra' => $extra,  'class' => $class, 'help' => $help ) );
	$ninja_forms_fields_row = $wpdb->get_row( 
	$wpdb->prepare("SELECT id FROM $ninja_forms_fields_table_name ORDER BY id DESC")
	, ARRAY_A);
	$id = $ninja_forms_fields_row['id'];
	echo "$id<----new----ninja----field---->";
	ninja_form_field_editor($id, true);

	die();
}
add_action('wp_ajax_ninja_delete_form', 'ninja_delete_form');
function ninja_delete_form(){
	global $wpdb;
	$form_id = $_POST['id'];
	$ninja_forms_table_name = $wpdb->prefix . "ninja_forms";
	$ninja_forms_fields_table_name = $wpdb->prefix . "ninja_forms_fields";
	
	$wpdb->query($wpdb->prepare( "DELETE FROM $ninja_forms_table_name WHERE id = %d", $form_id));
	$wpdb->query($wpdb->prepare( "DELETE FROM $ninja_forms_fields_table_name WHERE form_id = %d", $form_id));
	die();
}
add_action('wp_ajax_ninja_delete_all_subs', 'ninja_delete_all_subs');
function ninja_delete_all_subs(){
	global $wpdb;
	$form_id = $_POST['id'];
	$ninja_forms_subs_table_name = $wpdb->prefix . "ninja_forms_subs";
	$wpdb->query($wpdb->prepare( "DELETE FROM $ninja_forms_subs_table_name WHERE form_id = %d", $form_id));
}

add_action('wp_ajax_ninja_delete_sub', 'ninja_delete_sub');
function ninja_delete_sub(){
	global $wpdb;
	$id = $_POST['id'];
	$ninja_forms_subs_table_name = $wpdb->prefix . "ninja_forms_subs";
	$wpdb->query($wpdb->prepare( "DELETE FROM $ninja_forms_subs_table_name WHERE id = %d", $id));
	die();
}
add_action('wp_ajax_ninja_check_subs', 'ninja_check_subs');
function ninja_check_subs(){
	global $wpdb;
	$begin_date = esc_html($_REQUEST['begin_date']);
	$end_date = esc_html($_REQUEST['end_date']);
	$form_id = esc_html($_REQUEST['form_id']);
	if($begin_date != ''){
		$begin_date = date("Y-m-d G:i:s", strtotime($begin_date));
	}
	if($end_date != ''){
		$end_date .= " 23:59:59";
		$end_date = date("Y-m-d G:i:s", strtotime($end_date));
	}

	$ninja_forms_subs_table_name = $wpdb->prefix . "ninja_forms_subs";

	if($begin_date != '' && $end_date != ''){
		$ninja_forms_subs_rows = $wpdb->get_results( 
		$wpdb->prepare( "SELECT * FROM $ninja_forms_subs_table_name WHERE form_id = %d AND (date_updated BETWEEN %s AND %s) ORDER BY id DESC", $form_id, $begin_date, $end_date)
		, ARRAY_A);
	}elseif($begin_date != '' && $end_date == ''){
		$ninja_forms_subs_rows = $wpdb->get_results( 
		$wpdb->prepare( "SELECT * FROM $ninja_forms_subs_table_name WHERE form_id = %d AND date_updated >= %s ORDER BY id DESC", $form_id, $begin_date)
		, ARRAY_A);
	}elseif($begin_date == '' && $end_date != ''){
		$ninja_forms_subs_rows = $wpdb->get_results( 
		$wpdb->prepare( "SELECT * FROM $ninja_forms_subs_table_name WHERE form_id =%d AND date_updated <= %s ORDER BY id DESC", $form_id, $end_date)
		, ARRAY_A);
	}elseif($begin_date == '' && $end_date == ''){
		$ninja_forms_subs_rows = $wpdb->get_results( 
		$wpdb->prepare( "SELECT * FROM $ninja_forms_subs_table_name WHERE form_id = %d ORDER BY id DESC", $form_id)
		, ARRAY_A);
	}
	if($ninja_forms_subs_rows){
		echo 'found';
	}else{	
		echo 'none';
	}
	die();
}
add_action('wp_ajax_ninja_purge_fields', 'ninja_purge_fields');
function ninja_purge_fields(){
	global $wpdb;
	$form_id = $_POST['id'];
	$ninja_forms_table_name = $wpdb->prefix . "ninja_forms";
	$ninja_forms_fields_table_name = $wpdb->prefix . "ninja_forms_fields";
	$ninja_forms_subs_table_name = $wpdb->prefix . "ninja_forms_subs";
	
	$ninja_forms_fields_rows = $wpdb->get_results( 
	$wpdb->prepare( "SELECT * FROM $ninja_forms_fields_table_name WHERE form_id = %d ORDER BY id DESC", $form_id)
	, ARRAY_A);
	$ninja_forms_subs_rows = $wpdb->get_results( 
	$wpdb->prepare( "SELECT * FROM $ninja_forms_subs_table_name WHERE form_id = %d ORDER BY id DESC", $form_id)
	, ARRAY_A);

	if($ninja_forms_subs_rows){
		foreach($ninja_forms_subs_rows as $sub){	
			$x = 0;
			$sub_id = $sub['id'];
			$form_values = unserialize($sub['form_values']);
			$new_form_values = unserialize($sub['form_values']);
			foreach($form_values as $value){
				$found = 'no';
				foreach($ninja_forms_fields_rows as $field){
					if($value['id'] == $field['id']){
						$found = 'yes';
					}
				}
				if($found == 'no'){
					unset($new_form_values[$x]);
				}
				$x++;
			}
			
			$new_form_values = array_values($new_form_values);
			$form_values = serialize($new_form_values);
			$wpdb->update( $ninja_forms_subs_table_name, array('form_values' => $form_values), array( 'id' => $sub_id ));
		
		}
	}
	//print_r($new_form_values);
	die();
}
add_action('wp_ajax_ninja_save_settings_order', 'ninja_save_settings_order');
function ninja_save_settings_order(){
	$page = esc_html($_REQUEST['page']);
	$order = $_REQUEST['order'];
	//print_r($order);
	$plugin_settings = get_option('ninja_forms_settings');	
	//print_r($plugin_settings);	

	if($page == 'form-settings-list'){
		$plugin_settings['settings_sidebar_order'] = $order;
	}elseif($page == 'field-settings-list'){
		$plugin_settings['fields_sidebar_order'] = $order;	
	}elseif($page == 'subs-settings-list'){
		$plugin_settings['subs_sidebar_order'] = $order;
	}
	//print_r($plugin_settings);
	update_option("ninja_forms_settings", $plugin_settings);

	die();	
}

add_action('wp_ajax_ninja_save_settings_state', 'ninja_save_settings_state');
function ninja_save_settings_state(){
	
	$item = esc_html($_REQUEST['item']);
	$state = esc_html($_REQUEST['state']);
	
	$plugin_settings = get_option('ninja_forms_settings');
	$plugin_settings[$item] = $state;
	update_option("ninja_forms_settings", $plugin_settings);
	die();	
}

add_action( 'wp_ajax_nopriv_ninja_form_login', 'ninja_form_login' );
add_action( 'wp_ajax_ninja_form_login', 'ninja_form_login'); 
function ninja_form_login(){
	global $wpdb, $current_user;
	get_currentuserinfo();
	$current_userid = $current_user->ID;
	if(session_id() == '') {
		session_start();
	}
	$ninja_forms_subs = $wpdb->prefix."ninja_forms_subs";
	$ninja_forms_fields = $wpdb->prefix."ninja_forms_fields";
	$email = esc_html($_REQUEST['email']);
	$password = esc_html($_REQUEST['password']);
	$password = md5($password);
	if(isset($_REQUEST['form_id'])){
		$form_id = esc_html($_REQUEST['form_id']);
	}else{
		$form_id = '';
	}
	if(isset($_REQUEST['user_id'])){
		$user_id = esc_html($_REQUEST['user_id']);
	}else{
		$user_id = '';
	}
	if($user_id){
		if($user_id == $current_userid){
			$login_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $ninja_forms_subs WHERE user_id = %d AND form_id = %d AND sub_status = 'incomplete'", $user_id, $form_id), ARRAY_A);
		}else{
			echo 'fail';
		}
	}else{
		$login_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $ninja_forms_subs WHERE email = %s AND password = %s AND form_id = %d AND sub_status = 'incomplete'", $email, $password, $form_id), ARRAY_A);
	}
	$data = unserialize($login_row['form_values']);
	if($login_row){
		$_SESSION['ninja_forms_continue'] = $login_row['id'];
		echo $_SESSION['ninja_forms_continue'];
		echo '-ninja-';
		echo '{"fields": [';
		foreach($data as $field){
			$field_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $ninja_forms_fields WHERE id = %d", $field['id']), ARRAY_A);
			$type = $field_row['type'];
			$extra = unserialize($field_row['extra']);
			if(isset($extra['extra']['list_type'])){
				$list_type = $extra['extra']['list_type'];
			}else{
				$list_type = '';
			}
			if(is_array($field['value'])){
				$value = '';
				foreach($field['value'] as $item){
					if(!$value){
						$value = $item;
					}else{
						$value .= "|ninja|$item";
					}
				}
			}else{
				$value = $field['value'];
			}
			if($field_row){
				$value = str_replace('"', "&quot;", $value);
			echo '{
				"id":"'.$field['id'].'",
				"value":"'.$value.'",
				"type":"'.$type.'",				
				"list_type":"'.$list_type.'",			
				},'; 
			}
		}
		echo ']}';
	}else{
		echo "fail";
	}
	die();
}

add_action( 'wp_ajax_nopriv_ninja_email_pass', 'ninja_email_pass' );
add_action( 'wp_ajax_ninja_email_pass', 'ninja_email_pass');
function ninja_email_pass(){
	global $wpdb;
	$email = esc_html($_REQUEST['email']);
	$form_id = esc_html($_REQUEST['form_id']);
	$ninja_forms = $wpdb->prefix."ninja_forms";
	$ninja_forms_subs = $wpdb->prefix."ninja_forms_subs";
	$form_row = $wpdb->get_row($wpdb->prepare("SELECT email_from, title FROM $ninja_forms WHERE id = %d", $form_id), ARRAY_A);
	$user_row = $wpdb->get_row($wpdb->prepare("SELECT id, password FROM $ninja_forms_subs WHERE form_id = %d AND email = %s AND sub_status = 'incomplete'", $form_id, $email), ARRAY_A);
	$email_from = $form_row['email_from'];
	$form_title = $form_row['title'];
	$sub_id = $user_row['id'];
	if(!$email_from){
		$url = get_bloginfo('url');
		// get host name from URL
		preg_match("/^(http:\/\/)?([^\/]+)/i",$url, $matches);
		$host = $matches[2];
		
		// get last two segments of host name
		preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
		$email_from = "forgotten_password@".$matches[0];
	}

	if($user_row){
		$length = 8;
		$password = "";
		$possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
		$maxlength = strlen($possible);
  		if ($length > $maxlength) {
		  $length = $maxlength;
		}
	    $i = 0; 
        while ($i < $length) { 
			$char = substr($possible, mt_rand(0, $maxlength-1), 1);
            if (!strstr($password, $char)) { 
				// no, so it's OK to add it onto the end of whatever we've already got...
				$password .= $char;
				// ... and increase the counter by one
				$i++;
			}
		}

		$save_password = md5($password);
		$wpdb->update($ninja_forms_subs, array('password' => $save_password), array('id' => $sub_id));
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$email_from . "\r\n";
		$msg = "<p>Your password for the form $form_title has been reset. It is: $password</p>
		<p>Please save this email as your password cannot be changed.</p>
		<p>This is an automated email, please do not respond to this address.</p>";
		if (mail($email, "Your forgotten password", $msg, $headers)){
			
		}else{
			
		}
		
	}else{
		echo 'fail';
	}
	die();
}