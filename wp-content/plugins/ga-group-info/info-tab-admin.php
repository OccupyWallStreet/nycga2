<?php
// Fetch info metadata - Does the metadata exist already?
$infometa = groups_get_groupmeta( $bp->groups->current_group->id, $this->slug );

// If not, create it.
if (!$infometa) {
    // Get default data
    include( dirname( __FILE__ ) . '/info-tab-default-data.php' );
    $infometa = gait_default_fields();

    // get old contact fields
    $old_fields = json_decode( groups_get_groupmeta( $bp->groups->current_group->id, 'contact_fields' ) );
    $old_data = array();
    if (!empty($old_fields)) { // if the fields have been setup, get the field data
	foreach ($old_fields as $field){
	    $old_data[$field->slug] = groups_get_groupmeta($bp->groups->current_group->id, $field->slug);
	}
    } else {
	$old_data = array( // make the indices exist in case they didn't before...
	    'e_mail' => '',
	    'phone' => '',
	    'twitter' => '',
	    'mailing_list' => ''
	);
    }
    
    // import the old data into the new data structure
    $infometa['email']['value'] = $old_data['e_mail'];
    $infometa['phone']['value'] = $old_data['phone'];
    $infometa['twitter']['value'] = $old_data['twitter'];
    $infometa['listserve']['value'] = $old_data['mailing_list'];
    
    // save it so we've got something to work from later.
    groups_update_groupmeta( $bp->groups->current_group->id, $this->slug , $infometa );

}

$editor_options = array(
    'media_buttons'	=>	true,
    'textarea_name'	=>	'' // we'll set this later, in the foreach loop.
)
?>
<h2>Edit: <?php echo esc_attr( $this->name ); ?></h2>
		
		<?php
		    foreach( $infometa as $slug => $data ){
			$value = $data['value'];
			$active = $data['active'];
			$type = $data['type'];
			$name = $data['name'];
			?>
			<label for="gait-<?php echo $slug; ?>"><?php echo $name; ?></label>
			<?php
			switch($type){
			    case 'single-line':
				echo "<input id='gait-{$slug}' type='text' value='{$value}' name='gait-{$slug}' />";
				break;
			    
			    case 'wysiwyg':
				$editor_options['textarea_name'] = 'gait-' . $slug;
				wp_editor( $value, 'gait-' . $slug, $editor_options );
				break;
			}
		    }
		?>
		<p><input type="submit" name="save" value="Save Changes" /></p>
		
		<?php
		wp_nonce_field( 'gait_edit_save_' . $this->slug );

