<?php
	global $wpdb, $bp, $EM_Location, $EM_Notices;
	$url = $bp->events->link . 'my-locations/'; //url to this page
	$limit = ( !empty($_REQUEST['limit']) ) ? $_REQUEST['limit'] : 20;//Default limit
	$page = ( !empty($_REQUEST['pno']) ) ? $_REQUEST['pno']:1;
	$offset = ( $page > 1 ) ? ($page-1)*$limit : 0;
	if( !empty($bp->action_variables[0]) && $bp->action_variables[0] == 'others' && current_user_can('read_others_locations') ){
		$locations = EM_Locations::get();
		$locations_mine_count = EM_Locations::count( array('owner'=>get_current_user_id()) );
		$locations_all_count = count($locations);
	}else{
		$locations = EM_Locations::get( array('owner'=>get_current_user_id()) );
		$locations_mine_count = count($locations);
		$locations_all_count = current_user_can('read_others_locations') ? EM_Locations::count():0;
	}
	$locations_count = count($locations);
?>
		<div class='wrap'>
			<?php echo $EM_Notices; ?>
			  
		 	 <form id='locations-filter' method='post' action=''>
				<input type='hidden' name='page' value='locations'/>
				<input type='hidden' name='limit' value='<?php echo $limit ?>' />	
				<input type='hidden' name='p' value='<?php echo $page ?>' />
 	 			<a href="<?php echo $url?>edit/" class="button add-new-h2"><?php _e('Add New','dbem'); ?></a> 
				<div class="subsubsub">
					<a href='<?php echo $url; ?>' <?php echo ( empty($bp->action_variables[0]) || $bp->action_variables[0] != 'all' ) ? 'class="current"':''; ?>><?php echo sprintf( __( 'My %s', 'dbem' ), __('Locations','dbem')); ?> <span class="count">(<?php echo $locations_mine_count; ?>)</span></a>
					<?php if( current_user_can('read_others_locations') ): ?>
					&nbsp;|&nbsp;
					<a href='<?php echo $url ?>others/' <?php echo ( !empty($bp->action_variables[0]) && $bp->action_variables[0] == 'all' ) ? 'class="current"':''; ?>><?php echo sprintf( __( 'All %s', 'dbem' ), __('Locations','dbem')); ?><span class="count">(<?php echo $locations_all_count; ?>)</span></a>
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
									<td><input type='checkbox' class ='row-selector' value='<?php echo $EM_Location->id ?>' name='locations[]'/></td>
									<td><a href='<?php echo $url; ?>edit/?location_id=<?php echo $EM_Location->id ?>'><?php echo $EM_Location->name ?></a></td>
									<td><?php echo $EM_Location->address. ', '.$EM_Location->town. ', '.$EM_Location->postcode ?></td>
									<td><?php echo $EM_Location->state ?></td>  
									<td><?php echo $EM_Location->get_country() ?></td>                             
								</tr>
							<?php endif; ?>
							<?php $i++; ?> 
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php else: ?>
				<p><?php _e('No venues have been inserted yet!', 'dbem') ?></p>
				<?php endif; ?>
			</form>
		</div>
  	<?php 