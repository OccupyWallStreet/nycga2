<?php
// Fetch info metadata - Does the metadata exist already?
$infometa = groups_get_groupmeta( $bp->groups->current_group->id, $this->slug );

// If not, create it. TO DO: pull in old info from the places that this info used to live
if (!$infometa) {
    $infometa = array(
	'name' => array (
	    'email' =>		'Email Address',
	    'phone' =>		'Group Phone',
	    'twitter' =>	'Twitter',
	    'facebook' =>	'Facebook',
	    'website' =>	'External Website',
	    'listserve' =>	'List Serve',
	    'description' =>	'Detailed Description / Charter',
	    'contact' =>	'Contact Phone'
	),
	'data' => array (
	    'email' =>		'',
	    'phone' =>		'',
	    'twitter' =>	'',
	    'facebook' =>	'',
	    'website' =>	'',
	    'listserve' =>	'',
	    'description' =>	'',
	    'contact' =>	''
	),
	'active' => array (
	    'email' =>		true,
	    'phone' =>		true,
	    'twitter' =>	true,
	    'facebook' =>	true,
	    'website' =>	true,
	    'listserve' =>	true,
	    'description' =>	true,
	    'contact' =>	true
	),
	'type' => array (
	    'email' =>		'single-line',
	    'phone' =>		'single-line',
	    'twitter' =>	'single-line',
	    'facebook' =>	'single-line',
	    'website' =>	'single-line',
	    'listserve' =>	'single-line',
	    'description' =>	'multi-line',
	    'contact' =>	'single-line'
	)
    );
    groups_update_groupmeta( $bp->groups->current_group->id, $this->slug , $infometa );
}
?>
<h2>Edit: <?php echo esc_attr( $this->name ); ?></h2>
		
		<?php
		    foreach( $infometa['name'] as $slug => $name ){
			$value = $infometa['data'][$slug];
			$active = $infometa['active'][$slug];
			$type = $infometa['type'][$slug];
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

