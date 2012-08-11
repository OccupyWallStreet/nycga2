<?php
function em_admin_categories_page() {      
	global $wpdb, $EM_Category, $EM_Notices;

	//Take actions
	if( (!empty($_REQUEST['action']) && ( ($_REQUEST['action'] == "edit" && !empty($_REQUEST['category_id'])) || $_REQUEST['action'] == "add")) ) { 
		em_categories_edit_layout();
	} elseif( !empty($_REQUEST['action']) && $_REQUEST['action'] == "category_save" ) {
		em_categories_edit_layout();
	} else { 
		// no action, just a locations list
		em_categories_table_layout();
  	}
  	return;
	//TODO move categories action logic to em-actions.php
	if( !empty($_REQUEST['action']) ){
		if( $_REQUEST['action'] == "save") {
			// save (add/update) category
			if( empty($EM_Category) || !is_object($EM_Category) ){
				$EM_Category = new EM_Category(); //blank category
				$success_message = __('The category has been added.', 'dbem');
			}else{
				$success_message = __('The category has been updated.', 'dbem');
			}
			
			if ( $EM_Category->get_post() && $EM_Category->save() ) {
				$EM_Notices->add_confirm($EM_Category->feedback_message);
				em_categories_table_layout();
			} else {
				$EM_Notices->add_error($EM_Category->errors);
				em_categories_edit_layout();
			}
		} elseif( $_REQUEST['action'] == "edit" ){
			em_categories_edit_layout();
		} elseif( $_REQUEST['action'] == "delete" ){
			//delelte category
			EM_Categories::delete($_REQUEST['categories']);
			//FIXME no result verification when deleting various categories
			$message = __('Categories Deleted', "dbem" );
			em_categories_table_layout($message);
		}
	}else{
		em_categories_table_layout();
	}
} 

function em_categories_table_layout($message = "") {
	global $EM_Notices;
	$categories = EM_Categories::get();
	$destination = get_bloginfo('url')."/wp-admin/admin.php"; 
	?>
	<div class='wrap'>
		<div id='icon-edit' class='icon32'>
			<br/>
		</div>
  		<h2>
  			<?php echo __('Categories', 'dbem') ?>
  			<a href="admin.php?page=events-manager-categories&action=add" class="button add-new-h2"><?php _e('Add New', 'dbem') ?></a>
  		</h2>
	 		
		<?php echo $EM_Notices; ?>

	 	 <form id='bookings-filter' method='post' action='<?php echo get_bloginfo('wpurl') ?>/wp-admin/admin.php?page=events-manager-categories'>
			<input type='hidden' name='action' value='category_delete'/>
			<input type='hidden' name='_wpnonce' value='<?php echo wp_create_nonce('category_delete'); ?>'/>
			<?php if (count($categories)>0) : ?>
				<table class='widefat'>
					<thead>
						<tr>
							<th class='manage-column column-cb check-column' scope='col'><input type='checkbox' class='select-all' value='1'/></th>
							<th><?php echo __('ID', 'dbem') ?></th>
							<th><?php echo __('Name', 'dbem') ?></th>
						</tr> 
					</thead>
					<tfoot>
						<tr>
							<th class='manage-column column-cb check-column' scope='col'><input type='checkbox' class='select-all' value='1'/></th>
							<th><?php echo __('ID', 'dbem') ?></th>
							<th><?php echo __('Name', 'dbem') ?></th>
						</tr>             
					</tfoot>
					<tbody>
						<?php foreach ($categories as $EM_Category) : ?>
						<tr>
							<td><input type='checkbox' class ='row-selector' value='<?php echo $EM_Category->id ?>' name='categories[]'/></td>
							<td><a href='<?php echo get_bloginfo('wpurl') ?>/wp-admin/admin.php?page=events-manager-categories&amp;action=edit&amp;category_id=<?php echo $EM_Category->id ?>'><?php echo htmlspecialchars($EM_Category->id, ENT_QUOTES); ?></a></td>
							<td><a href='<?php echo get_bloginfo('wpurl') ?>/wp-admin/admin.php?page=events-manager-categories&amp;action=edit&amp;category_id=<?php echo $EM_Category->id ?>'><?php echo htmlspecialchars($EM_Category->name, ENT_QUOTES); ?></a></td>
						</tr>
						<?php endforeach; ?>
					</tbody>

				</table>

				<div class='tablenav'>
					<div class='alignleft actions'>
				 	<input class='button-secondary action' type='submit' name='doaction2' value='Delete'/>
					<br class='clear'/> 
					</div>
					<br class='clear'/>
				</div>
			<?php else: ?>
				<p><?php echo __('No categories have been inserted yet!', 'dbem'); ?></p>
			<?php endif; ?>
		</form>
  	</div>
  	<?php
}


function em_categories_edit_layout($message = "") {
	global $EM_Category, $EM_Notices;
	if( !is_object($EM_Category) ){
		$EM_Category = new EM_Category();
	}
	//check that user can access this page
	if( is_object($EM_Category) && !$EM_Category->can_manage('edit_categories') ){
		?>
		<div class="wrap"><h2><?php _e('Unauthorized Access','dbem'); ?></h2><p><?php implode('<br/>',$EM_Category->get_errors()); ?></p></div>
		<?php
		return;
	}
	?>
	<div class='wrap'>
		<div id='icon-edit' class='icon32'>
			<br/>
		</div>
			
		<h2><?php echo __('Edit category', 'dbem') ?></h2>  
 		
		<?php echo $EM_Notices ?>

		<div id='ajax-response'></div>

		<div id="poststuff" class="metabox-holder">
			<div id="post-body">
				<div id="post-body-content">
					<form enctype='multipart/form-data' name='editcat' id='editcat' method='post' action='admin.php?page=events-manager-categories' class='validate'>
						<input type='hidden' name='action' value='category_save' />
						<input type='hidden' name='category_id' value='<?php echo $EM_Category->id ?>'/>
						<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('category_save'); ?>" />
						
						<?php do_action('em_admin_category_form_header'); ?>
						
							<div id="category_description" class="postbox">
								<h3><?php echo __('Category name', 'dbem') ?></h3>
								<div class="inside">					
									<input name='category_name' id='category-name' type='text' value='<?php echo htmlspecialchars($EM_Category->name,ENT_QUOTES); ?>' size='40'  />
									<br />
					           		<em><?php echo __('The name of the category', 'dbem') ?></em>
									<?php $slug_link = __('View Slug','dbem'); ?>
									<a href="#" id="category-slug-trigger"><?php echo $slug_link; ?></a>
									<script type="text/javascript">
										jQuery(document).ready(function($){
											$('#category-slug-trigger').click(function(){
												if( $(this).text() == '<?php echo $slug_link; ?>'){
													$('.category-slug').show(); 
													 $(this).text('<?php _e('Hide Slug','dbem'); ?>');
												}else{ 
													$('.category-slug').hide(); 
													 $(this).text('<?php echo $slug_link; ?>'); 
												}
											});
										});
									</script>
									<p class='category-slug' style="display:none">
										<?php echo sprintf(__('%s Slug','dbem'),__('Category','dbem')); ?>: <input type="text" name="category_slug" id="category-slug" value="<?php echo $EM_Category->slug; ?>" />
										<br />
										<?php _e ( 'The event slug. If the event slug already exists, a random number will be appended to the end.', 'dbem' )?>
									</p>					           		
								</div>
							</div>
											
							<div id="category_description" class="postbox">
								<h3>
									<?php _e ( 'Details', 'dbem' ); ?>
								</h3>
								<div class="inside">
									<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">
										<?php the_editor($EM_Category->description ); ?>
									</div>
									<br />
									<em><?php _e ( 'Details about the category', 'dbem' )?></em>
								</div>
							</div>
										
							<div id="category_description" class="stuffbox">
								<h3>
									<?php _e ( 'Category image', 'dbem' ); ?>
								</h3>
								<div class="inside" style="padding:10px;">
										<?php if ($EM_Category->get_image_url() != '') : ?> 
											<img src='<?php echo $EM_Category->image_url; ?>' alt='<?php echo $EM_Category->name ?>'/>
										<?php else : ?> 
											<em><?php _e('No image uploaded for this category yet', 'dbem') ?></em>
										<?php endif; ?>
										<br /><br />
										<label for='category_image'><?php _e('Upload/change picture', 'dbem') ?></label> <input id='locacategoryge' name='category_image' id='category_image' type='file' size='40' />
										<br />
										<label for='category_image_delete'><?php _e('Delete Image?', 'dbem') ?></label> <input id='category-image-delete' name='category_image_delete' id='category_image_delete' type='checkbox' value='1' />
								</div>
							</div>
						</div>
						<?php do_action('em_admin_category_form_footer'); ?>
						<p class='submit'><input type='submit' class='button-primary' name='submit' value='<?php echo __('Update category', 'dbem') ?>' /></p>
					</form>
				</div>
			</div>
		</div>	
		
	</div>
	<?php
}
?>