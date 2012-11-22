<?php
function ninja_form_pre_process(){
	global $wpdb, $ninja_form, $ninja_post, $user_id;
	$server_os = strtoupper(PHP_OS);
	 if(substr($server_os,0,3) == "WIN")    {
        $server_os = 'win';
    }else{
        $server_os = 'other';
    }
	$plugin_settings = get_option("ninja_forms_settings");
	if(isset($_REQUEST['ninja_form_id'])){
		$form_id = $_REQUEST['ninja_form_id'];
	}else{
		$form_id = '';
	}
	if(isset($_REQUEST['ninja_user_id'])){
		$user_id = $_REQUEST['ninja_user_id'];			
	}else{
		$user_id = '';
	}
	$nonce = $_REQUEST['ninja_forms_nonce'];
	if(wp_verify_nonce($nonce, 'ninja_forms_submit')){
		if($user_id){
			$user_info = get_userdata($user_id);
			$user_name = $user_info->user_nicename;
		}else{
			$user_name = '';
		}
		$ninja_forms_table_name = $wpdb->prefix . "ninja_forms";	
		$ninja_forms_fields_table_name = $wpdb->prefix . "ninja_forms_fields";
		$ninja_forms_row = $wpdb->get_row( 
		$wpdb->prepare("SELECT * FROM $ninja_forms_table_name WHERE id = %d", $form_id)
		, ARRAY_A);
		$ninja_spam_field = $wpdb->get_row( 
		$wpdb->prepare("SELECT * FROM $ninja_forms_fields_table_name WHERE form_id = %d AND type = 'spam'", $form_id)
		, ARRAY_A);
		$ninja_all_forms = $wpdb->get_results( 
		$wpdb->prepare( "SELECT * FROM $ninja_forms_table_name")
		, ARRAY_A);

		$ninja_form = array();
		$ninja_post = array();
		
		$ninja_form['id'] = $form_id;
		$ninja_form['title'] = stripslashes($ninja_forms_row['title']);
		$ninja_form['desc'] = stripslashes($ninja_forms_row['desc']);
		$ninja_form['success_msg'] = stripslashes($ninja_forms_row['success_msg']);
		$ninja_form['send_email'] = $ninja_forms_row['send_email'];
		$ninja_form['mailto'] = $ninja_forms_row['mailto'];
		$ninja_form['subject'] = stripslashes($ninja_forms_row['subject']);
		$ninja_form['from'] = $ninja_forms_row['email_from'];
		$ninja_form['landing_page'] = $ninja_forms_row['landing_page'];
		$ninja_form['ajax_submit'] = $ninja_forms_row['ajax'];
		$ninja_form['save_subs'] = $ninja_forms_row['save_subs'];
		$ninja_form['email_msg'] = stripslashes($ninja_forms_row['email_msg']);
		$ninja_form['email_fields'] = unserialize($ninja_forms_row['email_fields']);
		$ninja_form['email_type'] = $ninja_forms_row['email_type'];
		$ninja_form['ninja_post'] = $ninja_forms_row['post'];
		$ninja_form['post_options'] = unserialize($ninja_forms_row['post_options']);
		$ninja_form['save_status'] = $ninja_forms_row['save_status'];
		$ninja_form['save_status_options'] = unserialize($ninja_forms_row['save_status_options']);
		if(isset($_POST['ninja_create_cat'])){
			$ninja_form['create_cat'] = $_POST['ninja_create_cat'];	
		}else{
			$ninja_form['create_cat'] = '';
		}
		$ninja_form['save_button'] = $_POST['ninja_save_status'];
		$ninja_form['save_email'] = $_POST['ninja_form_save_email'];
		$ninja_form['save_password'] = $_POST['ninja_form_save_password'];
		if(isset($_POST['ninja_form_form_continue'])){
			$ninja_form['form_continue'] = $_POST['ninja_form_form_continue'];
		}else{
			$ninja_form['form_continue'] = '';
		}
		if(isset($plugin_settings['upload_dir'])){
			$upload_dir = stripslashes($plugin_settings['upload_dir']);
		}else{
			$upload_dir = '';
		}
		$form_title = ereg_replace("[^A-Za-z0-9]", "", $ninja_form['title']);
		$upload_dir = str_replace("%formtitle%", $form_title, $upload_dir);
		$upload_dir = str_replace("%date%", date('Y-m-d'), $upload_dir);
		$upload_dir = str_replace("%month%", date('m'), $upload_dir);
		$upload_dir = str_replace("%day%", date('d'), $upload_dir);
		$upload_dir = str_replace("%year%", date('Y'), $upload_dir);
		if($user_name){
			$user_name = ereg_replace("[^A-Za-z0-9]", "", $user_name);
			$user_name = strtolower($user_name);
			$upload_dir = str_replace("%username%", $user_name, $upload_dir);
		}
		//$upload_dir = strtolower($upload_dir);
		$ninja_forms_file_fields = $wpdb->get_results( 
				$wpdb->prepare( "SELECT * FROM $ninja_forms_fields_table_name WHERE type = 'file' AND form_id = %d", $form_id)
				, ARRAY_A); //Grab all of our file upload fields from the DB
		foreach($ninja_forms_file_fields as $file){
			$id = $file['id'];
			if($_FILES['ninja_field_'.$id]['name']){ // Check the $_FILES superglobal for our file upload fields.
				if ($server_os == 'win') { 
					$dir_array = explode('\\', $upload_dir);
					$upload_dir = '';
				} else { 
					$dir_array = explode('/', $upload_dir);
					$upload_dir = "/";
				}
			
				foreach($dir_array as $directory){
					//echo $directory;
					if($directory != ''){
						if ($server_os == 'win') { 
							$upload_dir .= $directory."\\";
						}else{
							$upload_dir .= $directory."/";
						}
						//echo $upload_dir . '<br />';
						if(!is_dir($upload_dir) AND !file_exists($upload_dir)){
							mkdir($upload_dir);
						}
					}
				}

				$this_id = str_replace("ninja_field_", "", $id);
				$ninja_forms_fields_row = $wpdb->get_row( 
				$wpdb->prepare( "SELECT * FROM $ninja_forms_fields_table_name WHERE id = %d", $this_id)
				, ARRAY_A);
				$name_format = unserialize($ninja_forms_fields_row['extra']);
				$name_format = $name_format['extra']['upload_rename'];
				if($name_format){
					$orig_name = ereg_replace("[^A-Za-z0-9\.]", "", $_FILES['ninja_field_'.$id]['name']);
					$orig_name = strtolower($orig_name);
					$orig_name = explode(".", $orig_name);
					$ext = $orig_name[count($orig_name)-1];
					
					$main_name = '';
					$y = 0;
					while($y < (count($orig_name)-1)){
						$main_name .= $orig_name[$y];
						$y++;
					}
					$file_name = str_replace("%filename%", $main_name, $name_format);
					$file_name = str_replace("%formtitle%", $form_title, $file_name);
					$file_name = str_replace("%date%", date('Y-m-d'), $file_name);
					$file_name = str_replace("%month%", date('m'), $file_name);
					$file_name = str_replace("%day%", date('d'), $file_name);
					$file_name = str_replace("%year%", date('Y'), $file_name);
					if($user_name){
						$user_name = ereg_replace("[^A-Za-z0-9]", "", $user_name);
						$file_name = str_replace("%username%", $user_name, $file_name);
					}
					
					$file_name .= ".$ext";
				}else{
					$file_name = ereg_replace("[^A-Za-z0-9\.]", "", $_FILES['ninja_field_'.$id]['name']);
				}
				$_POST['ninja_field_'.$id] = $file_name;
				if($server_os == 'win'){
					$upload_file = "/".$upload_dir . basename($file_name);				
				}else{
					$upload_file = $upload_dir . basename($file_name);				
				}

				if (move_uploaded_file($_FILES['ninja_field_'.$id]['tmp_name'], $upload_file)) {

				}else {
					echo "<p class='ninja_error'>An error occured in uploading your file. Please try again</p>";
				}
			}
			
		}
		
		$x = 1;
		foreach($_POST as $key => $val){
			$key = str_replace("ninja_field_", "", $key);
			if(!is_array($val)){
				$val = stripslashes($val);
			}
			$ninja_forms_fields_row = $wpdb->get_row( 
			$wpdb->prepare( "SELECT * FROM $ninja_forms_fields_table_name WHERE id = %d", $key)
			, ARRAY_A);
			if($ninja_forms_fields_row){
				$id = $ninja_forms_fields_row['id'];
				$label = stripslashes($ninja_forms_fields_row['label']);
				$type = $ninja_forms_fields_row['type'];
				$extra = unserialize($ninja_forms_fields_row['extra']);
				if($type == 'posttitle'){
					$ninja_form['post_title'] = $val;
				}elseif($type == 'postcontent'){
					$ninja_form['post_content'] = $val;
				}elseif($type == 'postexcerpt'){
					$ninja_form['post_excerpt'] = $val;
				}elseif($type == 'postcat'){
					$ninja_form['post_cat'] = $val;
				}elseif($type == 'posttags'){
					$ninja_form['post_tags'] = $val;
				}
				$ninja_post[$x]['id'] = $id;
				$ninja_post[$x]['label'] = $label;
				$ninja_post[$x]['value'] = $val;
				$ninja_post[$x]['type'] = $type;
				$ninja_post[$x]['extra'] = $extra;
				
				$x++;
			}
		} //end foreach statement
		if(!isset($ninja_form['post_title'])){
			$ninja_form['post_title'] = 'Post Added by Ninja Forms';
		}		
		if(!isset($ninja_form['post_content'])){
			$ninja_form['post_content'] = 'Post Added by Ninja Forms';
		}		
		if(!isset($ninja_form['post_tags'])){
			$ninja_form['post_tags'] = '';
		}		
		if(!isset($ninja_form['post_excerpt'])){
			$ninja_form['post_excerpt'] = '';
		}
		//print_r($ninja_post);
		do_action('ninja_form_pre_process', $ninja_form, $ninja_post);
		foreach($ninja_all_forms as $form){
			$form_id = $form['id'];
			do_action("ninja_form_pre_process_$form_id", $ninja_form, $ninja_post);
		}
	}
}

add_action( 'wp_ajax_nopriv_ninja_form_process', 'ninja_form_process' );
add_action( 'wp_ajax_ninja_form_process', 'ninja_form_process'); 
function ninja_form_process(){
	if(session_id() == '') {
		session_start();
	}
	global $ninja_form, $ninja_post, $wpdb, $user_id;
	$ninja_forms_table_name = $wpdb->prefix . "ninja_forms";
	$ninja_forms_fields_table_name = $wpdb->prefix . "ninja_forms_fields";
	$ninja_forms_subs_table_name = $wpdb->prefix . "ninja_forms_subs";
	$nonce = $_REQUEST['ninja_forms_nonce'];
	if(wp_verify_nonce($nonce, 'ninja_forms_submit')){
		if(isset($_REQUEST['ninja_field_spam'])){
			$spam = $_REQUEST['ninja_field_spam'];
		}else{
			$spam = '';
		}
		$error = 'none';
		foreach($_POST as $key => $val){
			$key = str_replace("ninja_field_", "", $key);
			if(!is_array($val)){
				$val = stripslashes($val);
			}
			$ninja_forms_fields_row = $wpdb->get_row( 
			$wpdb->prepare( "SELECT * FROM $ninja_forms_fields_table_name WHERE id = %d", $key)
			, ARRAY_A); //Check the DB to make sure we have a registered file
			if($ninja_forms_fields_row){
				$id = $ninja_forms_fields_row['id'];
				$type = $ninja_forms_fields_row['type'];
				$extra = unserialize($ninja_forms_fields_row['extra']);
				if(isset($extra['extra']['upload_types'])){
					$upload_types = $extra['extra']['upload_types'];
				}else{
					$upload_types = '';
				}
				if($type == 'file'){ //If the DB type is set to file upload, look in the $_FILES superglobal for the proper field name.
					if($_FILES['ninja_field_'.$id]['name']){
						$file_name = ereg_replace("[^A-Za-z0-9\.]", "", $_FILES['ninja_field_'.$id]['name']);
						$file_name = strtolower($file_name);
						$ext = explode(".", $file_name);
						$ext = $ext[count($ext)-1];
						if($upload_types && !strpos($upload_types, $ext)){
							$error .= $id."_file-type-error|ninja_forms|";
						}
						if($_FILES['ninja_field_'.$id]['error'] == 2){
							$error .= $id."_file-size-error|ninja_forms|";
						}
					}
				}
			}
		} //end foreach statement
		
		if(isset($_REQUEST['ninja_form_id'])){
			$form_id = $_REQUEST['ninja_form_id'];
		}else{
			$form_id = '';
		}
		if(isset($_REQUEST['ninja_user_id'])){
			$user_id = $_REQUEST['ninja_user_id'];			
		}else{
			$user_id = '';
		}

		$ninja_forms_table_name = $wpdb->prefix . "ninja_forms";	
		$ninja_spam_field = $wpdb->get_row( 
			$wpdb->prepare("SELECT * FROM $ninja_forms_fields_table_name WHERE form_id = %d AND type = 'spam'", $form_id)
		, ARRAY_A);

		if($ninja_spam_field){
			if($spam == $ninja_spam_field['value']){
				
			}else{
				$error .= "0_spam-error|ninja_forms|";
			}
		}else{
			
		}
		if($_POST['ninja_save_status'] == 'yes'){
			$error = 'none';
		}
		if($error == 'none'){
			ninja_form_pre_process();
			do_action('ninja_form_process', $ninja_form, $ninja_post);
			if($ninja_post){
				foreach($ninja_post as $post){
					$id = $post['id'];
					$label = $post['label'];
					$val = $post['value'];
					$extra = $post['extra'];
					$_SESSION['ninja_field_'.$label] = $val;
				}
			}
			if($ninja_form['ajax_submit'] == 'checked'){
				if($_POST['ninja_save_status'] != 'yes'){
					$success_msg = $ninja_form['success_msg'];	
					if($ninja_post){
						//print_r($ninja_post);
						foreach($ninja_post as $post){
							$id = $post['id'];
							$label = $post['label'];
							$val = $post['value'];
							$extra = $post['extra'];
							if(!is_array($val)){
								$success_msg = str_replace("[$label]", $val, $success_msg);
							}
						}
					}
					echo $success_msg;
				}
			}else{
				echo $ninja_form['landing_page']."?ninja_form_id=$form_id&success=yes";
			}
		}else{ //we didn't have a valid spam answer, so echo out "fail".
			echo $error;
		} //end spam check
		
		die();
	}
}

//This action sends emails to both the user and each email address listed for the form.
add_action('ninja_form_process', 'ninja_mail_form', 11, 2);
//This action saves form submissions to the appropriate database.
add_action('ninja_form_process', 'ninja_save_form',10,2);
//This action saves post data if the user has the appropriate box checked.
add_action('ninja_form_process', 'ninja_form_post',12,2);
//Test per form action.
//add_action('ninja_form_pre_process_18', 'ninja_test',10,2);
//add_action('ninja_form_pre_process', 'ninja_test');

function ninja_mail_form($ninja_form, $ninja_post){
	global $wpdb, $ninja_form, $ninja_post, $user_id;
	if($ninja_form['save_button'] != 'yes'){
		$form_id = $ninja_form['id'];
		$form_title = $ninja_form['title'];
		$form_desc = $ninja_form['desc'];
		$success_msg = $ninja_form['success_msg'];
		$send_email = $ninja_form['send_email'];
		if(isset($ninja_form['mailto']) AND $ninja_form['mailto'] != ''){
			$form_mailto = $ninja_form['mailto'];
		}else{
			$form_mailto = '';
		}
		$form_subject = $ninja_form['subject'];
		$email_from = $ninja_form['from'];
		$landing_page = $ninja_form['landing_page'];
		$ajax_submit = $ninja_form['ajax_submit'];
		$email_msg = $ninja_form['email_msg'];
		$email_fields = $ninja_form['email_fields'];
		$email_type = $ninja_form['email_type'];
		$msg = '';
		$user_msg = '';
		$headers = '';
		if($form_subject == ''){
			$form_subject = $form_title;
		}
		if($email_type == 'text'){
			$email_msg = str_replace("<p>", "\r\n", $email_msg);
			$email_msg = str_replace("</p>", "", $email_msg);
			$email_msg = str_replace("<br>", "\r\n", $email_msg);
			$email_msg = str_replace("<br />", "\r\n", $email_msg);
			$email_msg = strip_tags($email_msg);
		}
		
		//Do we have any admin email addresses and/or are we sending an email to the user?
		if($form_mailto != ''){
			$form_mailto = explode(",", $form_mailto);
		}
		if($email_type == 'html'){
			$msg = "<table>";
		}
		$user_msg = $msg;
		$user_email = '';
		foreach($ninja_post as $post){
			$id = $post['id'];
			$label = $post['label'];
			$type = $post['type'];
			$val = $post['value'];
			$extra = $post['extra'];
			if(isset($extra['extra']['email'])){
				$email = $extra['extra']['email'];
			}else{
				$email = '';
			}
			if(isset($extra['extra']['send_email'])){
				$field_send_email = $extra['extra']['send_email'];
			}else{
				$field_send_email = '';
			}
			
			if($email_msg){
				$email_msg = str_replace("[$label]", $val, $email_msg);
			}
			
			if($email == 'checked' && $field_send_email == 'checked'){
				$user_email[] = $post['value'];
			}
			if(is_array($val)){
				$x = 0;
				foreach($val as $v){
					if($x == 0){
						if($email_type == 'html'){
							$msg .= "<tr><td>".$label.":</td><td>".$v."</td></tr>";
						}else{
							$msg .= $label." - ".$v."\r\n";
						}
					}else{
						if($email_type == 'html'){
							$msg .= "<tr><td>&nbsp;</td><td>".$v."</td></tr>";
						}else{
							$msg .= "--- ".$v."\r\n";
						}
					}
					$x++;
				}
			}else{
				if($email_type == 'html'){
					$msg .= "<tr><td>".$label.":</td><td>".$val."</td></tr>";
				}else{
					$msg .= $label." - ".$val."\r\n";
				}
			}
			if($type != 'hidden'){
				if($email_fields){
					foreach($email_fields as $email_field_id){
						if($id == $email_field_id){
							if(is_array($val)){
								$x = 0;
								foreach($val as $v){
									if($x == 0){
										if($email_type == 'html'){
											$user_msg .= "<tr><td>".$label.":</td><td>".$v."</td></tr>";
										}else{
											$user_msg .= $label." - ".$v."\r\n";
										}
									}else{
										if($email_type == 'html'){	
											$user_msg .= "<tr><td>&nbsp;</td><td>".$v."</td></tr>";
										}else{
											$user_msg .= "--- ".$v."\r\n";										
										}
									}
									$x++;
								}
							}else{
								if($email_type == 'html'){
									$user_msg .= "<tr><td>".$label.":</td><td>".$val."</td></tr>";
								}else{
									$user_msg .= $label." - ".$val."\r\n";									
								}
							}
							break;
						}
					}
				}
			}
		} //end $post foreach.
		if($email_type == 'html'){
			$msg .= "</table>";
			$user_msg .= "</table>";
		}else{
			$msg .= "\r\n";
			$user_msg .= "\r\n";
		}
		$headers .= "MIME-Version: 1.0\r\n";
		if($email_type == 'html'){
			$headers .= "Content-Type: text/html; charset=utf-8\r\n";
		}else{
			$headers .= "Content-type: text/plain; charset=utf-8\r\n";
		}
		$headers .= 'From: '.$email_from . "\r\n";
		if($form_mailto != ''){ 
			foreach($form_mailto as $recipient){
				if (mail($recipient, $form_subject, $msg, $headers)){
					$mailed = 1;
				}else{
					$mailed = 0;
				}
			}
		}
		if($email_msg != ''){
			if($email_type == 'html'){
				$user_msg = "<p>$email_msg</p><h3>Submitted Fields:</h3>$user_msg";
			}else{
				$user_msg = $email_msg." \r\n\r\nSubmitted Fields: \r\n".$user_msg;			
			}
		}
		if($send_email == 'checked'){
			if($user_email){
				foreach($user_email as $user_email){
					if (mail($user_email, $form_subject, $user_msg, $headers)){
						$mailed = 1;
					}else{
						$mailed = 0;
					}
				}
			}
		}
	}
} //Close function

function ninja_save_form($ninja_form, $ninja_post){
	global $wpdb, $ninja_form, $ninja_post, $user_id;
	if(session_id() == '') {
		session_start();
	}
	$ninja_forms_subs_table_name = $wpdb->prefix . "ninja_forms_subs";		
	$save_status = 'incomplete';
	$save_email = $ninja_form['save_email'];
	$save_password = $ninja_form['save_password'];
	$save_password = md5($save_password);
	$form_id = $ninja_form['id'];
	if($user_id){
		$user_info = get_userdata($user_id);
		$save_email = $user_info->user_email;
		$login_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $ninja_forms_subs_table_name WHERE user_id = %d AND form_id = %d AND sub_status = 'incomplete'", $user_id, $form_id), ARRAY_A);
		//echo $wpdb->prepare("SELECT * FROM $ninja_forms_subs_table_name WHERE user_id = %d AND form_id = %d AND sub_status = 'incomplete'", $user_id, $form_id);
	}else{
		$login_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $ninja_forms_subs_table_name WHERE email = %s AND form_id = %d AND sub_status = 'incomplete'", $save_email, $form_id), ARRAY_A);
	}
	
	if($ninja_form['save_button'] == 'yes'){
		if($login_row AND !isset($_SESSION['ninja_forms_continue'])){
			echo '0_email-exists';
			$insert = 'no';
			$update = 'no';
		}elseif($login_row AND $_SESSION['ninja_forms_continue']){
			$update = 'yes';
			$insert = 'no';
			echo stripslashes($ninja_form['save_status_options']['msg']);
		}elseif(!$login_row){
			$insert = 'yes';
			$update = 'no';
			echo stripslashes($ninja_form['save_status_options']['msg']);
		}
	}else{
		if($login_row AND isset($_SESSION['ninja_forms_continue'])){
			$update = 'yes';
			$insert = 'no';
		}else{
			$update = 'no';
			$insert = 'yes';
		}
		$save_status = 'complete';
	}
	$values = serialize($ninja_post);
	$form_id = $ninja_form['id'];
	
	if(($ninja_form['save_subs'] == 'checked' OR $ninja_form['save_status'] == 'checked') AND $insert == 'yes'){
		$wpdb->insert( $ninja_forms_subs_table_name, array( 'user_id' => $user_id, 'form_id' => $form_id, 'form_values' => $values, 'sub_status' => $save_status, 'email' => $save_email, 'password' => $save_password));
	}
	
	if(($ninja_form['save_subs'] == 'checked' OR $ninja_form['save_status'] == 'checked') AND $update == 'yes'){
		$wpdb->update($ninja_forms_subs_table_name, array( 'user_id' => $user_id, 'form_id' => $form_id, 'form_values' => $values, 'sub_status' => $save_status, 'email' => $save_email, 'password' => $save_password), array("id" => $_SESSION['ninja_forms_continue']));
	}
	
}

function ninja_form_post($ninja_form, $ninja_post){
	global $wpdb, $ninja_form, $ninja_post, $user_id;
	if($ninja_form['save_button'] != 'yes'){
		$form_id = $ninja_form['id'];
		$form_title = $ninja_form['title'];
		$form_desc = $ninja_form['desc'];
		$success_msg = $ninja_form['success_msg'];
		$send_email = $ninja_form['send_email'];
		$form_mailto = $ninja_form['mailto'];
		$form_subject = $ninja_form['subject'];
		$email_from = $ninja_form['from'];
		$landing_page = $ninja_form['landing_page'];
		$ajax_submit = $ninja_form['ajax_submit'];
		$email_msg = $ninja_form['email_msg'];
		$email_fields = $ninja_form['email_fields'];
		$ninja_create_post = $ninja_form['ninja_post'];
		$post_options = $ninja_form['post_options'];
		if(isset($ninja_form['post_cat'])){
			$ninja_form['post_cat'] = str_replace(", ", ",", $ninja_form['post_cat']);
		}
		$cats = '';
		if(($post_options['login'] == 'checked' && $user_id) OR $post_options['login'] == 'unchecked'){
			if(!isset($ninja_form['post_cat'])){
				$ninja_form['post_cat'] = array(0);
			}
			if($post_options['user'] != 0){
				if($post_options['user']){
					$author_id = $post_options['user'];
				}else{
					$author_id = 1;
				}
			}else{
				$author_id = $user_id;
			}
			if($post_options['post_type']){
				$post_type = $post_options['post_type'];
			}else{
				$post_type = 'post';
			}
			if($ninja_form['create_cat']){
				$create_cat = explode(",", $ninja_form['create_cat']);
				$x = 0;
				foreach($create_cat as $cat){
					$term = get_term_by('name', $cat, 'category');
					if(!$term){
						$cats[$x] = wp_create_category($cat);
						$x++;
					}else{
						$cats[$x] = $cat;
					}
				}
			}
			if($cats != ''){
				$cats = array_merge($cats, $ninja_form['post_cat']);
			}else{
				$cats = $ninja_form['post_cat'];
			}
			if($ninja_create_post == 'checked'){
				$new_post = array(
				 'post_title' => $ninja_form['post_title'],
				 'post_content' => $ninja_form['post_content'],
				 'post_status' => $post_options['post_status'],
				 'post_author' => $author_id,
				 'post_category' => $cats,
				 'tags_input' => $ninja_form['post_tags'],
				 'post_excerpt' => $ninja_form['post_excerpt'],
				 'post_type' => $post_type
				  ); 

				// Insert the post into the database
				$new_post_id = wp_insert_post($new_post);
				if($ninja_post){
					foreach($ninja_post as $item){
						//print_r($item);
						//echo "<br>";
						$extra = $item['extra'];
						$meta_key = $extra['extra']['meta_key'];
						if($meta_key == '_new'){
							$meta_key = str_replace(' ', '_', $item['label']);
							$meta_key = strtolower($meta_key);
						}
						add_post_meta($new_post_id, $meta_key, $item['value']);
					}
				}
			}
		}
	}
}

function ninja_get_subs_by_user($user_id, $form_id = 'none'){
	global $wpdb;
	$ninja_forms_subs_table_name = $wpdb->prefix . "ninja_forms_subs";
	if($form_id == 'none'){
		$ninja_user_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $ninja_forms_subs_table_name WHERE user_id = %d", $user_id), ARRAY_A);
		$y = 0;
		foreach($ninja_user_results as $user){
			$form_values = unserialize($user['form_values']);
			$x = 0;
			$user_values[$y]['sub_id'] = $user['id'];
			$user_values[$y]['date'] = $user['date'];
			$user_values[$y]['form_id'] = $user['form_id'];
			foreach($form_values as $value){
				$user_values[$y][$x]['label'] = $value['label'];
				$user_values[$y][$x]['value'] = $value['value'];
				$x++;
			}
			$y++;
		}
	
	}else{
		$ninja_user_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $ninja_forms_subs_table_name WHERE user_id = %d AND form_id = %d", $user_id, $form_id), ARRAY_A);
		$y = 0;
		foreach($ninja_user_results as $user){
			$form_values = unserialize($user['form_values']);
			$form_id = $user['form_id'];
			$x = 0;
			$user_values[$form_id][$y]['sub_id'] = $user['id'];
			$user_values[$form_id][$y]['date'] = $user['date'];
			foreach($form_values as $value){
				$user_values[$form_id][$y][$x]['label'] = $value['label'];
				$user_values[$form_id][$y][$x]['value'] = $value['value'];
				$x++;
			}
			$y++;
		}
	
	
	}

	return $user_values;

	//return $tmp;
}

function ninja_get_subs_by_form($form_id){
	global $wpdb;
	$ninja_forms_subs_table_name = $wpdb->prefix . "ninja_forms_subs";
	$ninja_user_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $ninja_forms_subs_table_name WHERE form_id = %d", $form_id), ARRAY_A);
	$y = 0;
	foreach($ninja_user_results as $user){
		$form_values = unserialize($user['form_values']);
		$user_id = $user['user_id'];
		$user_values[$user_id][$y]['date'] = $user['date'];
		$x = 0;
		foreach($form_values as $value){
			$user_values[$y][$x]['label'] = $value['label'];
			$user_values[$y][$x]['value'] = $value['value'];
			$x++;
		}
		$y++;
	}
	return $user_values;
}

function ninja_update_sub($sub_id, $new_data){
	global $wpdb;
	$new_data = serialize($new_data);
	$ninja_forms_subs_table_name = $wpdb->prefix . "ninja_forms_subs";
	$wpdb->update( $ninja_forms_subs_table_name, array('form_values' => $new_data), array('id' => $sub_id));
}

function ninja_delete_incomplete(){
	global $wpdb;
	$ninja_forms_table_name = $wpdb->prefix . "ninja_forms";
	$ninja_forms_subs_table_name = $wpdb->prefix . "ninja_forms_subs";
	$all_forms = $wpdb->get_results("SELECT * FROM $ninja_forms_table_name", ARRAY_A);
	foreach($all_forms as $form){
		$save_status_options = unserialize($form['save_status_options']);
		$days = $save_status_options['delete'];
		if($days != ''){
			$old_date = strtotime("- $days days");
			$old_date = date("Y-m-d H:i:s", $old_date);
			$form_id = $form['id'];
			$old_subs = $wpdb->get_results("SELECT * FROM $ninja_forms_subs_table_name WHERE form_id = $form_id AND sub_status = 'incomplete' AND date_updated <= '$old_date'", ARRAY_A);
			foreach($old_subs as $sub){
				$sub_id = $sub['id'];
				$wpdb->query("DELETE FROM $ninja_forms_subs_table_name WHERE id = $sub_id");
			}
		}
	}
}