<?php
if(isset($_REQUEST['submitted'])){
	$submitted = $_REQUEST['submitted'];
}else{
	$submitted = '';
}
if(isset($_REQUEST['tab'])){
	$tab = $_REQUEST['tab'];
}else{
	$tab = '';
}


if($submitted == 'yes'){
	if($tab == 'fields'){
		if(check_admin_referer('ninja_save_form_fields','ninja_form_fields')){
			//Get the tablename
			$ninja_forms_table_name = $wpdb->prefix . "ninja_forms";
			$ninja_forms_subs_table_name = $wpdb->prefix . "ninja_forms_subs";
			$ninja_forms_fields_table_name = $wpdb->prefix . "ninja_forms_fields";
			$form_id = $_REQUEST['ninja_form_id'];
			$multi = $_REQUEST['multi'];
			$post = $_REQUEST['ninja_post'];
			$multi_options = serialize($_REQUEST['multi_options']);
			$post_options = serialize($_REQUEST['post_options']);
			$wpdb->update( $ninja_forms_table_name, array('multi' => $multi, 'multi_options' =>$multi_options, 'post' => $post, 'post_options' => $post_options), array('id' => $form_id));
			
			$order = $_REQUEST['ninja_form_fields_order'];
			//echo $order;
			$order = explode(",", $order);
			$x = 0;
			foreach($order as $field){
				$field_id = str_replace('field_', '', $field);
				//echo $field_id;
				$ninja_forms_fields_row = $wpdb->get_row( 
				$wpdb->prepare("SELECT type FROM $ninja_forms_fields_table_name WHERE id = %d" , $field_id)
				, ARRAY_A);
				$field_type = $ninja_forms_fields_row['type'];
				//echo $field_type;
				if(isset($_REQUEST[$field])){
					$_REQUEST[$field]['label'] = htmlspecialchars($_REQUEST[$field]['label']);
					$field_array = $_REQUEST[$field];
					//print_r($_REQUEST[$field]);
				}else{
					$field_array = '';
				}
				//print_r($field_array);

				if($field_array){
					foreach($field_array as $key => $val){
						if($key == 'extra'){
							$extra[$key] = $val;
						}else{
							$update_array[$key] = $val;
						}
					}
					if(isset($extra)){
						$extra = serialize($extra);
						$update_array['extra'] = $extra;
						//print_r($extra);
					}
					$update_array['field_order'] = $x;
					$update_array['type'] = $field_type;
				}else{
					$update_array['field_order'] = $x;
				}
				$wpdb->update( $ninja_forms_fields_table_name, $update_array, array( 'id' => $field_id ));		
				
				//print_r($update_array);
				$update_array = "";
				$extra = "";
				$x++;
				
			}
		}
	}elseif($tab == 'settings'){
		$email_fields = '';
		if(check_admin_referer('ninja_save_form_settings','ninja_form_settings')){	
			$append_page = '';
			$form_id = $_REQUEST['ninja_form_id'];
			$form = "form_$form_id";
			$form_array = $_REQUEST[$form];
			$ninja_forms_row = $wpdb->get_row( 
			$wpdb->prepare( "SELECT append_page FROM $ninja_forms_table_name WHERE id = %d", $form_id)
			, ARRAY_A);
			if($form_id != 'new'){
				$current_pages = unserialize($ninja_forms_row['append_page']);
				if($current_pages){	
					foreach($current_pages as $val){
						$this_post = get_post($val, 'ARRAY_A');
						if($this_post['post_type'] == 'post'){
							$append_page[] = $val;
						}
					}
				}
			}
			foreach($form_array as $key => $val){
				if($key == 'append_page'){
					foreach($val as $key => $val){
						if($val == 'checked'){
							$append_page[] = $key;
						}
					}
				
					if(count($append_page)){
						$val = serialize($append_page);
					}
					$key = 'append_page';
				}
				if($key == 'email_fields'){
					foreach($val as $key => $val){
						if($val == 'checked'){
							$email_fields[] = $key;
						}
					}
				
					if(count($email_fields)){
						$val = serialize($email_fields);
					}
					$key = 'email_fields';
				}
				if($key == 'save_status_options'){
					$val = serialize($val);
				}
				if($val == 'unchecked'){
					$val = '';
				}
				
				$update_array[$key] = $val;
			}
			$ninja_all_forms = $wpdb->get_results( 
			$wpdb->prepare("SELECT id,append_page FROM $ninja_forms_table_name WHERE id <> %d", $form_id)
			, ARRAY_A);
			if($ninja_all_forms){
				foreach($ninja_all_forms as $row){
					$row_id = $row['id'];
					$row_pages = unserialize($row['append_page']);
					if($append_page){
						foreach($append_page as $val){
							if($row_pages){
								$search = array_search($val, $row_pages);
								if($search !== false){
									unset($row_pages[$search]);
								}
							}
						}
					}
					if($row_pages){
						$row_pages = array_values($row_pages);
						$new_value = serialize($row_pages);
					}else{
						$new_value = '';
					}
					$new_array['append_page'] = $new_value;
					$wpdb->update( $ninja_forms_table_name, $new_array, array( 'id' => $row_id ));
				}
			}
			
			if($form_id == 'new'){
				//print_r($update_array);
				$wpdb->insert($ninja_forms_table_name, $update_array);
				$ninja_forms_results = $wpdb->get_results( 
				$wpdb->prepare( "SELECT id FROM $ninja_forms_table_name ORDER BY id DESC")
				, ARRAY_A);
				$form_id = $ninja_forms_results[0]['id'];
				$link = add_query_arg(array('tab' => $tab, 'ninja_form_id' => $form_id));
				?>
				<script language="javascript">
					window.location = "<?php echo $link;?>";
				</script>
				<?php
			}else{
				$wpdb->update( $ninja_forms_table_name, $update_array, array( 'id' => $form_id ));
			}
			//print_r($update_array);
		}
	}
}