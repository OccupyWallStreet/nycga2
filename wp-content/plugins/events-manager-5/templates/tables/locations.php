<?php
	global $wpdb, $EM_Location, $EM_Notices;
	//add new button will only appear if called from em_location_admin template tag, or if the $show_add_new var is set	
	if(!empty($show_add_new) && current_user_can('edit_locations')) echo '<a class="em-button button add-new-h2" href="'.em_add_get_params($_SERVER['REQUEST_URI'],array('action'=>'edit','scope'=>null,'status'=>null,'location_id'=>null)).'">'.__('Add New','dbem').'</a>';
?>
<?php echo $EM_Notices; ?>			  
<form id='locations-filter' method='post' action=''>
	<input type='hidden' name='pno' value='<?php echo $page ?>' />
	<div class="subsubsub">
		<a href='<?php echo em_add_get_params($_SERVER['REQUEST_URI'], array('view'=>null, 'pno'=>null)); ?>' <?php echo ( empty($_REQUEST['view']) ) ? 'class="current"':''; ?>><?php echo sprintf( __( 'My %s', 'dbem' ), __('Locations','dbem')); ?> <span class="count">(<?php echo $locations_mine_count; ?>)</span></a>
		<?php if( current_user_can('read_others_locations') ): ?>
		&nbsp;|&nbsp;
		<a href='<?php echo em_add_get_params($_SERVER['REQUEST_URI'], array('view'=>'others', 'pno'=>null)); ?>' <?php echo ( !empty($_REQUEST['view']) && $_REQUEST['view'] == 'others' ) ? 'class="current"':''; ?>><?php echo sprintf( __( 'All %s', 'dbem' ), __('Locations','dbem')); ?><span class="count">(<?php echo $locations_all_count; ?>)</span></a>
		<?php endif; ?>
	</div>						
	<?php if ( $locations_count > 0 ) : ?>
	<div class='tablenav'>					
		<div class="alignleft actions">
			<select name="action">
				<option value="" selected="selected"><?php _e ( 'Bulk Actions' ); ?></option>
				<option value="location_delete"><?php _e ( 'Delete selected','dbem' ); ?></option>
			</select> 
			<input type="submit" value="<?php _e ( 'Apply' ); ?>" id="doaction2" class="button-secondary action" /> 
		</div>
			<?php
			if ( $locations_count >= $limit ) {
				$locations_nav = em_admin_paginate( $locations_count, $limit, $page );
				echo $locations_nav;
			}
			?>
	</div>
	<table class='widefat'>
		<thead>
			<tr>
				<th class='manage-column column-cb check-column' scope='col'><input type='checkbox' class='select-all' value='1'/></th>
				<th><?php _e('Name', 'dbem') ?></th>
				<th><?php _e('Address', 'dbem') ?></th>
				<th><?php _e('State', 'dbem') ?></th>  
				<th><?php _e('Country', 'dbem') ?></th>                
			</tr> 
		</thead>
		<tfoot>
			<tr>
				<th class='manage-column column-cb check-column' scope='col'><input type='checkbox' class='select-all' value='1'/></th>
				<th><?php _e('Name', 'dbem') ?></th>
				<th><?php _e('Address', 'dbem') ?></th>
				<th><?php _e('State', 'dbem') ?></th> 
				<th><?php _e('Country', 'dbem') ?></th>      
			</tr>             
		</tfoot>
		<tbody>
			<?php $i = 1; ?>
			<?php foreach ($locations as $EM_Location) : ?>	
				<?php if( $i >= $offset && $i <= $offset+$limit ): ?>
					<tr>
						<td><input type='checkbox' class ='row-selector' value='<?php echo $EM_Location->location_id ?>' name='locations[]'/></td>
						<td>
							<?php if( $EM_Location->can_manage('edit_events','edit_others_events') ): ?>
							<a href='<?php echo esc_url($EM_Location->get_edit_url()); ?>'><?php echo $EM_Location->location_name ?></a>
							<?php else: ?>
							<strong><?php echo $EM_Location->location_name ?></strong> - 
							<a href='<?php echo $EM_Location->output('#_LOCATIONURL'); ?>'><?php _e('View') ?></a>
							<?php endif; ?>
						</td>
						<td><?php echo implode(',', array($EM_Location->location_address,$EM_Location->location_town,$EM_Location->location_postcode)); ?></td>
						<td><?php echo $EM_Location->location_state ?></td>  
						<td><?php echo $EM_Location->get_country() ?></td>                             
					</tr>
				<?php endif; ?>
				<?php $i++; ?> 
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php else: ?>
	<br class="clear" />
	<p><?php _e('No locations have been inserted yet!', 'dbem') ?></p>
	<?php endif; ?>
	
	<?php if ( !empty($locations_nav) ) echo $locations_nav; ?>
</form>