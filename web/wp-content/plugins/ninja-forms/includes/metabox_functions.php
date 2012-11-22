<?php
/* Define the custom box */

// WP 3.0+
add_action('add_meta_boxes', 'ninja_forms_add_custom_box');

// backwards compatible
//add_action('admin_init', 'myplugin_add_custom_box', 1);

/* Do something with the data entered */
add_action('save_post', 'ninja_forms_save_postdata');

/* Adds a box to the main column on the Post and Page edit screens */
function ninja_forms_add_custom_box() {
	add_meta_box( 
		'ninja_forms_selector',
		__( 'Append A Ninja Form', 'ninja_forms_textdomain' , 'ninja-forms'),
		'ninja_forms_inner_custom_box',
		'post',
		'side',
		'low'
	);
	add_meta_box(
		'ninja_forms_selector',
		__( 'Append A Ninja Form', 'ninja_forms_textdomain' , 'ninja-forms'), 
		'ninja_forms_inner_custom_box',
		'page',
		'side',
		'low'
	);
}

/* Prints the box content */
function ninja_forms_inner_custom_box() {
	global $wpdb;
	$post_id = esc_html($_REQUEST['post']);
	$ninja_forms_table_name = $wpdb->prefix . "ninja_forms";
	$ninja_forms_row = $wpdb->get_row( 
	$wpdb->prepare( "SELECT * FROM $ninja_forms_table_name WHERE append_page = %d", $form_id)
	, ARRAY_A);
	// Use nonce for verification
	wp_nonce_field( plugin_basename(__FILE__), 'ninja_forms_nonce' );

	// The actual fields for data entry
	/*
	echo '<label for="myplugin_new_field">';
	   _e('Select A Form', 'ninja_forms' );
	echo '</label> ';
	*/
	echo '<select id="ninja_form_select" name="ninja_form_select">';
	echo '<option value="0">--- ';
	_e('None', 'ninja-forms');
	echo '---</option>';
	$ninja_all_forms = $wpdb->get_results(
	$wpdb->prepare( "SELECT * FROM $ninja_forms_table_name")
	, ARRAY_A);
	foreach($ninja_all_forms as $form){
		$append_page = unserialize($form['append_page']);
		echo "<option value='".$form['id']."'";
		if($append_page){
			foreach($append_page as $val){
				if($val == $post_id){
					echo ' selected ';
				}
			}
		}
		echo ">";
		echo $form['title'];
		echo "</option>";
	}
	echo '</select>';
}

/* When the post is saved, saves our custom data */
function ninja_forms_save_postdata( $post_id ) {
	global $wpdb;
	if(isset($_POST['ninja_forms_nonce'])){
		// verify if this is an auto save routine. 
		// If it is our form has not been submitted, so we dont want to do anything
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		  return $post_id;

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times

		if ( !wp_verify_nonce( $_POST['ninja_forms_nonce'], plugin_basename(__FILE__) ) )
		  return $post_id;


		// Check permissions
		if ( 'page' == $_POST['post_type'] ) 
		{
		if ( !current_user_can( 'edit_page', $post_id ) )
			return $post_id;
		}
		else
		{
		if ( !current_user_can( 'edit_post', $post_id ) )
			return $post_id;
		}

		// OK, we're authenticated: we need to find and save the data
		$post_id = $_POST['post_ID'];
		$form_id = $_POST['ninja_form_select'];
		//$post_id = $post_id + 0; //Turn our post_ID string into a number.
		$form_id = $form_id + 0;//Turn our Form_id string into a number.
		$ninja_forms_table_name = $wpdb->prefix . "ninja_forms";	

		$ninja_all_forms = $wpdb->get_results( 
		$wpdb->prepare("SELECT * FROM $ninja_forms_table_name")
		, ARRAY_A);
		foreach($ninja_all_forms as $row){
			$row_id = $row['id'];
			$append_page = unserialize($row['append_page']);
			$new_pages = array();
			if($append_page){
				//print_r($append_page);
				foreach($append_page as $val){
					if($val != $post_id){
						$new_pages[] = $val;
					}
				}
				$val = serialize($new_pages);

			}
			$key = 'append_page';
			$update_array[$key] = $val;
			//print_r($update_array);
			$wpdb->update( $ninja_forms_table_name, $update_array, array( 'id' => $row_id ));
		}
		
		if($form_id != 0){
			$ninja_forms_row = $wpdb->get_row( 
			$wpdb->prepare("SELECT * FROM $ninja_forms_table_name WHERE id = %d", $form_id)
			, ARRAY_A);
			$append_page = unserialize($ninja_forms_row['append_page']);
			$x = '';
			if($append_page){
				foreach($append_page as $val){
					if($val == $post_id){
						$x = 'exists';
					}
				}
			}
			if($x != 'exists'){
				$append_page[] = $post_id;
			}
			$val = serialize($append_page);
			$key = 'append_page';
			$update_array[$key] = $val;
			//print_r($update_array);
			$wpdb->update( $ninja_forms_table_name, $update_array, array( 'id' => $form_id ));
				
		
		}

		return $form_id;
	}
}