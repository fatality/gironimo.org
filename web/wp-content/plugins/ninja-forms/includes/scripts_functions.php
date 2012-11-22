<?php
//Load up our WP Ninja Custom Form JS files.
function ninja_form_admin_css(){
	$plugin_settings = get_option('ninja_forms_settings');
	$tooltip_color = $plugin_settings['help_color'];
	$wp_version = get_bloginfo('version');
	if(isset($_REQUEST['tab'])){
		$tab = esc_html($_REQUEST['tab']);
	}else{
		$tab = '';
	}
	if($tab == 'preview'){
		$plugin_settings = get_option("ninja_forms_settings");
		$default_style = $plugin_settings['default_style'];
		if($default_style == 'checked'){
			wp_enqueue_style( 'ninja_forms_display_css', NINJA_FORMS_URL .'/css/ninja_forms_display.css');
			wp_enqueue_style( 'jquery-smoothness-css', NINJA_FORMS_URL .'/css/smoothness/jquery-smoothness.css');
		}
		wp_enqueue_style( 'ninja-forms-tipTIp', NINJA_FORMS_URL .'/css/tooltips/'.$tooltip_color.'.css');
	}else{
		wp_enqueue_style( 'ninja-forms-tipTIp', NINJA_FORMS_URL .'/css/tipTip.css');
	}
	if($tab == 'subs'){
		wp_enqueue_style( 'jquery-tablesorter-css', NINJA_FORMS_URL .'/css/tablesorter-style.css');
	}
	wp_enqueue_style( 'jquery-smoothness-css', NINJA_FORMS_URL .'/css/smoothness/jquery-smoothness.css');
	wp_enqueue_style( 'ninja_forms_admin_css', NINJA_FORMS_URL .'/css/ninja_forms_admin.css');

	if(version_compare( $wp_version, '3.3-beta3-19254' , '<')){
		wp_enqueue_style( 'ninja_forms_tinymce_css', NINJA_FORMS_URL .'/css/editor-buttons.css');
		wp_admin_css( 'nav-menu' );
	}else{
		add_filter('admin_body_class', 'ninja_forms_add_class');
	}
}

function ninja_forms_add_class($classes) {
	// add 'class-name' to the $classes array
	$classes .= 'nav-menus-php';
	// return the $classes array
	return $classes;
}
function ninja_form_admin_js(){
	global $version_compare;
	$wp_version = get_bloginfo('version');
	
	$plugin_settings = get_option("ninja_forms_settings");
	
	if(version_compare( $wp_version, '3.3-beta3-19254' , '<')){
		wp_enqueue_script( 'jquery-ui-datepicker', 
		NINJA_FORMS_URL .'/js/min/jquery.ui.datepicker.min.js',
		array('jquery', 'jquery-ui-core'));	
	}
	
	wp_enqueue_script( 'jquery-tablesorter', 
	NINJA_FORMS_URL .'/js/min/jquery.tablesorter.mod.min.js',
	array('jquery', 'jquery-ui-core'));	
	
	wp_enqueue_script( 'jquery-tablesorter-pager', 
	NINJA_FORMS_URL .'/js/min/jquery.tablesorter.pager.min.js',
	array('jquery', 'jquery-ui-core'));			
	
	wp_enqueue_script( 'jquery-tipTip', 
	NINJA_FORMS_URL .'/js/min/jquery.tipTip.min.js',
	array('jquery', 'jquery-ui-core'));		
	
	if($version_compare){
		//wp_enqueue_script('ninja_forms_admin_js',
		//NINJA_FORMS_URL .'/js/dev/ninja_forms_admin.js',
		//array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-dialog', 'jquery-ui-datepicker'), '', true);		
		
		wp_enqueue_script('ninja_forms_admin_js',
		NINJA_FORMS_URL .'/js/min/ninja_forms_admin.min.js',
		array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-dialog', 'jquery-ui-datepicker'), '', true);
	}else{
		wp_enqueue_script('ninja_forms_admin_js',
		NINJA_FORMS_URL .'/js/min/ninja_forms_admin_3.1.min.js',
		array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-dialog', 'jquery-ui-datepicker'), '', true);
	}
	wp_localize_script( 'ninja_forms_admin_js', 'settings', array( 'plugin_url' => NINJA_FORMS_URL, 'help_size' => $plugin_settings['help_size'], 'help_color' => $plugin_settings['help_color'], 'admin_help' => $plugin_settings['admin_help']) );
}

add_action('init', 'ninja_form_display_js');
function ninja_form_display_js(){
	global $version_compare;
	$wp_version = get_bloginfo('version');
	if(isset($_REQUEST['tab'])){
		$tab = esc_html($_REQUEST['tab']);
	}else{
		$tab = '';
	}
	if(!is_admin() OR $tab== 'preview' ){
		
		wp_enqueue_script('jquery-ui-progressbar',
		NINJA_FORMS_URL .'/js/min/jquery.ui.progressbar.min.js',
		array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-form'));				
		
		wp_enqueue_script('jquery-maskedinput',
		NINJA_FORMS_URL .'/js/min/jquery.maskedinput.min.js',
		array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-form'));				
		
		wp_enqueue_script('jquery-autonumeric',
		NINJA_FORMS_URL .'/js/min/autoNumeric.min.js',
		array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-form'));		
		
		wp_enqueue_script( 'jquery-tipTip', 
		NINJA_FORMS_URL .'/js/min/jquery.tipTip.min.js',
		array('jquery', 'jquery-ui-core'));		
		
		if(version_compare( $wp_version, '3.3-beta3-19254' , '<')){
			wp_enqueue_script('jquery-ui-datepicker',
			NINJA_FORMS_URL .'/js/min/jquery.ui.datepicker.min.js',
			array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-form'));		
			
			wp_enqueue_script('jquery-ui-widget',
			NINJA_FORMS_URL .'/js/min/jquery.ui.widget.min.js',
			array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-form'));
		}
		
		if($version_compare){
			wp_enqueue_script('ninja_forms_display-js',
			NINJA_FORMS_URL .'/js/min/ninja_forms_display.min.js',
			array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-form', 'jquery-ui-datepicker'));	
			
			//wp_enqueue_script('ninja_forms_display-js',
			//NINJA_FORMS_URL .'/js/dev/ninja_forms_display.js',
			//array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-form'));	
		}else{
			wp_enqueue_script('ninja_forms_display-js',
			NINJA_FORMS_URL .'/js/min/ninja_forms_display_3.1.min.js',
			array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-form', 'jquery-ui-datepicker'));			
			
			//wp_enqueue_script('ninja_forms_display-js',
			//NINJA_FORMS_URL .'/js/dev/ninja_forms_display_3.1.js',
			//array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-form'));
			
			
		}
		$ninja_forms_general_error = __('There were errors with your submission. Please correct these errors before continuing.', 'ninja-forms');
		$ninja_forms_required = __('Please ensure that all required fields are filled out properly.', 'ninja-forms');
		$ninja_forms_spam_error = __('Please answer the anti-spam question correctly.', 'ninja-forms');
		$ninja_forms_file_size_error = __('One or more files are larger than the allowed filesize.', 'ninja-forms');
		$ninja_forms_file_type_error = __('Please ensure all files are of a valid type.', 'ninja-forms');
		$ninja_forms_exists_error = __('A form is already saved with that email address. Please login to make changes to that form', 'ninja-forms');
		$ninja_forms_saved_error = __('A saved copy of this form has not been found.', 'ninja-forms');
		$ninja_forms_login_error = __('Invalid Email/Password Combination. Please try again', 'ninja-forms');
		$ninja_forms_password_match_error = __('Your passwords do not match', 'ninja-forms');
		$ninja_forms_password_blank_error = __("Password fields can't be blank.", 'ninja-forms');
		$ninja_forms_uploaded_file = __("Password fields can't be blank.", 'ninja-forms');
		$ninja_forms_email_not_found = __('Email address not found.', 'ninja-forms');
		$ninja_forms_password_reset = __('Your password has been emailed.', 'ninja-forms');
		$ninja_forms_wait = __('Please Wait...', 'ninja-forms');
		
		wp_localize_script( 'ninja_forms_display-js', 'ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'ninja_forms_required' => $ninja_forms_required,
		'ninja_forms_general_error' => $ninja_forms_general_error, 'ninja_forms_spam_error' => $ninja_forms_spam_error, 'ninja_forms_file_type_error' => $ninja_forms_file_type_error,
		'ninja_forms_file_size_error' => $ninja_forms_file_size_error, 'ninja_forms_exists_error' => $ninja_forms_exists_error, 'ninja_forms_saved_error' => $ninja_forms_saved_error,
		'ninja_forms_login_error' => $ninja_forms_login_error, 'ninja_forms_password_match_error' => $ninja_forms_password_match_error, 'ninja_forms_password_blank_error' => $ninja_forms_password_blank_error,
		'ninja_forms_uploaded_file' => $ninja_forms_uploaded_file, 'ninja_forms_email_not_found' => $ninja_forms_email_not_found, 'ninja_forms_password_reset' => $ninja_forms_password_reset,
		'ninja_forms_wait' => $ninja_forms_wait) );
		wp_localize_script( 'ninja_forms_js-js', 'plugin', array( 'url' => WP_PLUGIN_URL ));

	}
}


add_action('init', 'ninja_form_display_css');
function ninja_form_display_css(){
	$plugin_settings = get_option('ninja_forms_settings');
	$tooltip_color = $plugin_settings['help_color'];
	$wp_version = get_bloginfo('version');
	if(!is_admin()){
		$plugin_settings = get_option("ninja_forms_settings");
		$default_style = $plugin_settings['default_style'];
		if($default_style == 'checked'){
			wp_enqueue_style( 'jquery-smoothness', NINJA_FORMS_URL .'/css/smoothness/jquery-smoothness.css');
			wp_enqueue_style( 'ninja_forms_display_css', NINJA_FORMS_URL .'/css/ninja_forms_display.css');
		}
	
		wp_enqueue_style( 'ninja-forms-tipTip', NINJA_FORMS_URL .'/css/tooltips/'.$tooltip_color.'.css');
		if(version_compare( $wp_version, '3.3-beta3-19254' , '<')){
			wp_enqueue_style( 'ninja_forms_tinymce_css', NINJA_FORMS_URL .'/css/editor-buttons.css');
		}
	}
}