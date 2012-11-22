<?php
$ninja_forms_subs_table_name = $wpdb->prefix . "ninja_forms_subs";
$ninja_forms_subs = $wpdb->get_results( 
$wpdb->prepare("SELECT * FROM $ninja_forms_subs_table_name WHERE form_id = %d AND sub_status = 'complete' ORDER BY id DESC", $form_id)
, ARRAY_A);
$ninja_forms_fields_columns = $wpdb->get_results( 
$wpdb->prepare("SELECT * FROM $ninja_forms_fields_table_name WHERE form_id = %d AND type <> 'hr' AND type <> 'heading' AND type <> 'desc'  AND type <> 'submit' AND type <> 'spam' AND type <> 'divider' AND type <> 'progressbar' AND type <> 'steps' AND type <> 'postcontent' ORDER BY field_order ASC LIMIT 3", $form_id)
, ARRAY_A);
$ninja_forms_fields = $wpdb->get_results( 
$wpdb->prepare("SELECT * FROM $ninja_forms_fields_table_name WHERE form_id = %d AND type <> 'hr' AND type <> 'heading' AND type <> 'desc'  AND type <> 'submit' AND type <> 'spam' AND type <> 'divider' AND type <> 'progressbar' AND type <> 'steps'  AND type <> 'postcontent' ORDER BY field_order ASC", $form_id)
, ARRAY_A);

$current_fields = array();
$x = 0;
foreach($ninja_forms_fields as $field){
	$current_fields[$x]['id'] = $field['id'];
	$current_fields[$x]['label'] = $field['label'];
	$x++;
}

$x = 0;
foreach($ninja_forms_fields_columns as $columns){
	foreach($current_fields as $field){
		if($field['id'] == $columns['id']){
			unset($current_fields[$x]);
			$x++;
			break;
		}
		$x++;
	}
}
$current_fields = array_values($current_fields);
?>
					<div id="nav-menu-header">
	<div id="submitpost" class="submitbox">
		<div class="major-publishing-actions">
			&nbsp;
		</div><!-- END .major-publishing-actions -->
	</div><!-- END #submitpost .submitbox -->
	
</div><!-- END #nav-menu-header -->
<div id="post-body">
	<div id="post-body-content">
<table class="widefat tablesorter" cellspacing="0" id="ninja_subs_table"> 
	<thead> 
		<tr> 
			<th class="mange-column" width="20%"><?php _e('Action', 'ninja-forms');?></th> 
			<th class="mange-column" width="20%"><?php _e('Date', 'ninja-forms');?></th> 
		<?php
if($ninja_forms_fields_columns){
	foreach($ninja_forms_fields_columns as $field){
	?>
		<th width="20%"><?php echo $field['label'];?></th>
		<?php
		}
}
?>
		</tr> 
	</thead> 
	<tbody class="plugins"> 
<?php
if($ninja_forms_subs){
	foreach($ninja_forms_subs as $sub){
		$form_values = unserialize($sub['form_values']);
		$date = date("m/d/Y g:ia", strtotime($sub['date_updated']));
		//$date = $sub['date'];
		$sub_id = $sub['id'];
		$found = '';
		//Grab the first key from the form values array. It will not always be 0.
		foreach($form_values as $key => $val){
			$x = $key;
			break;
		}
		//Figure out if we have any values for fields that have been deleted.
		$old_values = $form_values;
		if($form_values){
			//$x = 0;
			foreach($form_values as $value){
				if($ninja_forms_fields){
					foreach($ninja_forms_fields as $field){
						if(($value['id'] == $field['id']) OR $value['type'] == 'postcontent'){
							$found = 'yes';
							unset($old_values[$x]);
							break;
						}else{
							$found = 'no';
						}
					}
					$x++;
				}
			} // Foreach Old Value
		} //If Form Values exists
		$old_values = array_values($old_values);

		//End old value checking.
		//Begin output of our main "visible" section.
?>
<tr id="sub_<?php echo $sub['id'];?>_visible" class="sub_tr_<?php echo $sub['id'];?>">
		<td width="20%"><span class="delete"><a href="#" id="sub_<?php echo $sub['id'];?>" class="ninja_delete_sub"><?php _e('Delete', 'ninja-forms');?></a></span><?php if($current_fields || $old_values){ ?> | <a href="#" id="more_<?php echo $sub['id'];?>" class="ninja_sub_more"><?php _e('More', 'ninja-forms');?></a><?php } ?></td>
		<td  width="20%"><?php echo $date;?></td>
		<?php
		$field_value = array();
		$x = 0;
		foreach($ninja_forms_fields_columns as $fields){
			//$field_value[] = $fields['id'];
			foreach($form_values as $value){
				if($fields['id'] == $value['id']){
					$field_value[$x] = $value['value'];
					break; //we found our value, break the foreach statement.
				}else{
					$field_value[$x] = '----';
				}
			}
			$x++;
		}
		foreach($field_value as $value){
			if($value == ''){
				$value = '----';
			}
			if(is_array($value)){
				foreach($value as $value){
					$value .= ", $value";
				}
			}
			$value = str_replace("Array, ", "", $value);
		?>
			<td width="20%"><?php echo $value;?></td>
			<?php
		}
		?>
</tr>
<?php
	//End Output of our main "visible" section.
	//Begin output of our "more" section. This is for fields that are not the column headers.
	if($current_fields){
		
	?>
<tr id="sub_<?php echo $sub['id'];?>_more" class="expand-child sub_tr_<?php echo $sub['id'];?>" style="display:none;">
	<td colspan="2">
		<strong><?php _e('Other Fields', 'ninja-forms');?>:</strong><div class="other-fields" style="border-style:solid;border-width:1px;padding:5px">
	<?php
			foreach($current_fields as $field){
				foreach($form_values as $value){
					if($value['id'] == $field['id']){
						$user_value = $value['value'];
						$found = 'yes';
						break;
					}else{
						$found = 'no';
					}
				}
				if($found == 'no'){
					$user_value = '----';
				}
				if(is_array($user_value)){
					foreach($user_value as $value){
						$user_value .= ", $value";
					}
				}
				$user_value = str_replace("Array, ", "", $user_value);
			?>
				<p><div class="ninja-fields"><strong><?php echo $field['label'];?></strong> - <?php echo $user_value;?></div></p>
				
			<?php				
			}
		?>
		</div>
	</td>
	<td colspan="3">
	<?php
		if($old_values){
			?>
			<strong><?php _e('Old Values', 'ninja-forms');?>:</strong><div class="old-fields"  style="border-style:solid;border-width:1px;padding:5px">
			<?php
			foreach($old_values as $value){
				if(is_array($value['value'])){
					$z = 0;
					foreach($value['value'] as $v){
						if($z == 0){
						?>
						<div class="ninja-fields"><strong><?php echo $value['label'];?></strong> - <?php echo $v;?></div>
						<?php
						}else{
						?>
						<div class="ninja-fields"><strong><?php echo $value['label'];?></strong> - <?php echo $v;?></div>
						<?php
						}
						$z++;
					}
				}else{
			?>
			<div class="ninja-fields"><strong><?php echo $value['label'];?></strong> - <?php echo $value['value'];?></div>
			<?php
				}
			
			
			}
		}
		//End Output of "old values" section.
		?>
	</div>
	</td>
</tr>
	<?php
	} //End Current Field IF
	}// End Sub loop
		?>
		
		
<?php
	}else{ //If there are aren't any submissions.
	?>
	<tr>
		<td><?php _e('No Submissions Found', 'ninja-forms');?></td>
	</tr>
	<?php
	}

?>
	</tbody>
	<tfoot> 
		<tr> 
			<th class="mange-column"><?php _e('Action', 'ninja-forms');?></th> 
			<th class="mange-column"><?php _e('Date', 'ninja-forms');?></th> 
		<?php
	if($ninja_forms_fields_columns){
		foreach($ninja_forms_fields_columns as $field){
		?>
		<th><?php echo $field['label'];?></th>
		<?php
		}
	}
?>
		</tr> 
	</tfoot> 
</table>
<div id="ninja_pager" class="pager"></div>
</div><!-- /#post-body-content -->
</div><!-- /#post-body -->
<div id="nav-menu-footer">
	<div class="major-publishing-actions">
		&nbsp;		
	</div><!-- END #nav-menu-header -->
</div>