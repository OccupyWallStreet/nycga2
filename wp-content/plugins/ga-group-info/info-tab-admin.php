<?php
// Fetch info metadata - Does the metadata exist already?
//$infometa = groups_get_groupmeta( $bp->groups->current_group->id, $this->slug );
$infometa = false;
// If not, create it. TO DO: pull in old info from the places that this info used to live
if (!$infometa) {
    $infometa = array(
	'email' => array(
	    'name' =>	'Email Address',
	    'value' =>	'',
	    'active' =>	true,
	    'type' =>	'single-line'
	),
	'phone' => array(
	    'name' =>	'Group Phone',
	    'value' =>	'',
	    'active' =>	true,
	    'type' =>	'single-line'
	),
	'twitter' => array(
	    'name' =>	'Twitter',
	    'value' =>	'',
	    'active' =>	true,
	    'type' =>	'single-line'
	),
	'facebook' => array(
	    'name' =>	'Facebook',
	    'value' =>	'',
	    'active' =>	true,
	    'type' =>	'single-line'
	),
	'website' => array(
	    'name' =>	'External Website',
	    'value' =>	'',
	    'active' =>	true,
	    'type' =>	'single-line'
	),
	'listserve' => array(
	    'name' =>	'List Serve',
	    'value' =>	'',
	    'active' =>	true,
	    'type' =>	'single-line'
	),
	'description' => array(
	    'name' =>	'Detailed Description / Charter',
	    'value' =>	'',
	    'active' =>	true,
	    'type' =>	'multi-line'
	),
	'contact' => array(
	    'name' =>	'Contact Phone',
	    'value' =>	'',
	    'active' =>	true,
	    'type' =>	'single-line'
	)
    );
    groups_update_groupmeta( $bp->groups->current_group->id, $this->slug , $infometa );
}
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
				echo "<input id='gait-{$slug}' type='text' value='{$value}' name='gait-[{$slug}]' />";
				break;
			    
			    case 'multi-line':
				echo "<textarea id='gait-{$slug}' name='gait-[{$slug}]'>{$value}</textarea>";
				break;
			}
		    }
		?>
		<p><input type="submit" name="save" value="Save Changes" /></p>
		
		<?php
		wp_nonce_field( 'gait_edit_save_' . $this->slug );

