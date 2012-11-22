<?php
//This code searches up directories until it finds wp-load.php. This is useful incase a user has changed the location of their wp-content/plugins directory.
/*
$dir = '../';
$found = 'no';
while($found == 'no'){
	if ($handle = opendir($dir)) {
		/* This is the correct way to loop over the directory. */
		/*
		while (false !== ($file = readdir($handle))) {
			if($file == 'wp-load.php'){
				$found = 'yes';
				break;
			}
		}
	
	}
	if($found == 'no'){
		$dir .= '../';
	}
}
closedir($handle);
require_once($dir.'wp-load.php');

*/
if(isset($_REQUEST['download_subs'])){
	add_action('admin_init', 'ninja_forms_download_subs');
}
function ninja_forms_download_subs(){
	global $wpdb;

	require_once (NINJA_FORMS_DIR."/includes/xls.class.php");
	if(current_user_can('administrator')){
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
		$ninja_forms_table_name = $wpdb->prefix . "ninja_forms";
		$ninja_forms_fields_table_name = $wpdb->prefix . "ninja_forms_fields";

		if($begin_date != '' && $end_date != ''){
			$ninja_forms_subs_rows = $wpdb->get_results( 
			$wpdb->prepare( "SELECT * FROM $ninja_forms_subs_table_name WHERE form_id = %d AND sub_status = 'complete' AND (date_updated BETWEEN %s AND %s) ORDER BY id DESC", $form_id, $begin_date, $end_date)
			, ARRAY_A);
		}elseif($begin_date != '' && $end_date == ''){
			$ninja_forms_subs_rows = $wpdb->get_results( 
			$wpdb->prepare( "SELECT * FROM $ninja_forms_subs_table_name WHERE form_id = %d AND sub_status = 'complete' AND date_updated >= %s ORDER BY id DESC", $form_id, $begin_date)
			, ARRAY_A);
		}elseif($begin_date == '' && $end_date != ''){
			$ninja_forms_subs_rows = $wpdb->get_results( 
			$wpdb->prepare( "SELECT * FROM $ninja_forms_subs_table_name WHERE form_id =%d AND sub_status = 'complete' AND date_updated <= %s ORDER BY id DESC", $form_id, $end_date)
			, ARRAY_A);
		}elseif($begin_date == '' && $end_date == ''){
			$ninja_forms_subs_rows = $wpdb->get_results( 
			$wpdb->prepare( "SELECT * FROM $ninja_forms_subs_table_name WHERE form_id = %d AND sub_status = 'complete' ORDER BY id DESC", $form_id)
			, ARRAY_A);
			//echo $wpdb->prepare( "SELECT * FROM $ninja_forms_subs_table_name WHERE form_id = %d AND sub_status = 'complete' ORDER BY id DESC", $form_id);
		}

		$ninja_forms_fields_rows = $wpdb->get_results( 
			$wpdb->prepare("SELECT * FROM $ninja_forms_fields_table_name WHERE form_id = %d ORDER BY field_order ASC", $form_id)
			, ARRAY_A);
		$ninja_forms_row = $wpdb->get_row( 
			$wpdb->prepare("SELECT * FROM $ninja_forms_table_name WHERE id = %d", $form_id)
			, ARRAY_A);

		$form_title = $ninja_forms_row['title'];
		$current_time = current_time('timestamp');
		$date = date('m_d_Y.g.i.a', $current_time);

		$label_array = array();

		$label_array[0][] = "Date/Time";
		if($ninja_forms_fields_rows){
			foreach($ninja_forms_fields_rows as $field){
				$type = $field['type'];
				$label = $field['label'];
				$id = $field['id'];
				if($type != 'hr' && $type != 'heading' && $type != 'desc' && $type != 'submit' && $type != 'spam' && $type != 'progressbar' && $type != 'steps' && $type != 'divider' && $type != 'postcontent'){
					$id_array[] = $id;
					$label_array[0][] = $label;
				}
			}
		}
		$x = 0;
		if($ninja_forms_subs_rows){
			if($_REQUEST['download'] == 'no'){
				echo 'found';
				die();
			}
			foreach($ninja_forms_subs_rows as $sub){
				$form_values = unserialize($sub['form_values']);
				$value_array[$x][] = date('m/d/Y g:ia', strtotime($sub['date_updated']));
				foreach($id_array as $id){
					$found = 'no';
					$this_value = '----';
					foreach($form_values as $value){
						if($value['id'] == $id){
							$found = 'yes';
							$this_value = $value['value'];
							if(is_array($this_value)){
								foreach($this_value as $value){
									$this_value = $this_value.", $value";
								}
								$this_value = str_replace("Array, ", "", $this_value);
							}
							break;
						}
					}
					$value_array[$x][] = $this_value;
				}
				$x++;
			}

			//Instantiate the class.
			$xls = new xls($form_title."-".$date); 

			//Just build an example array out of test data
			$array = array(
				$label_array,
				$value_array
			);
			//Triggers the download using the passed array
			$xls->download_from_array($array);

		}else{
			echo "none";
		}	
	}
}