<?php 

function gtags_admin() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] )  && check_admin_referer('gtags_admin') ) {
		$blog_list = get_blog_list( 0, 'all' );//ERICLEWIS
		foreach ($blog_list AS $blog) { //ERICLEWIS
		    switch_to_blog($blog['blog_id']);//ERICLEWIS
		    update_option( 'gtags_dir_cloud', $_POST['gtags_dir_cloud'] );
			update_option( 'gtags_show_in_dir_list', $_POST['gtags_show_in_dir_list'] );
			
			if ( !$_POST['gtags_single_header'] )
				$_POST['gtags_single_header'] = 'false';
			update_option( 'gtags_single_header', $_POST['gtags_single_header'] );
			
			// this is an array
			update_option( 'gtags_cloud_args', $_POST['gtags_cloud_args'] );
			
			update_option( 'gtags_exclude', $_POST['gtags_exclude'] );
			update_option( 'gtags_include', $_POST['gtags_include'] );
			if ($_POST['gtags_exclude'] && $_POST['gtags_include'])
				echo "<div class='error'><p>If you fill in both exclude and include, include will be ignored. Just letting ya know. </p></div>";
				
			update_option( 'gtags_popular_limit', $_POST['gtags_popular_limit'] );

			// consolidate group category form data into array
			$i = 0;
			foreach($_POST['gtags_category']['slug'] as $slug ) {
				if ($slug)
					$gtags_category_array[ trim( $slug ) ] = trim( $_POST['gtags_category']['desc'][$i] );
				$i++;
			}
			update_option( 'gtags_category', $gtags_category_array );
					
			$updated = true;
		    restore_current_blog();//ERICLEWIS
		}//ERICLEWIS
		
	}

?>	

<div class="wrap">
<h2>BuddyPress Group Tags</h2>

<?php if ( isset($updated) ) : ?><div id='message' class='updated fade'><p>Settings Updated</p></div><?php endif; ?>

<form action="<?php echo network_admin_url('admin.php?page=gtags_admin') ?>" name="gtags-settings-form" id="gtags-settings-form" method="post">

	<h3>Group Directory Settings</h3>
	<b>The Tag Cloud above the group directory should be:</b><br>
	<input type="radio" name="gtags_dir_cloud" value="never" <?php if ( get_option('gtags_dir_cloud')=='never' ) echo 'checked="checked"'; ?>> hidden<br>
	<input type="radio" name="gtags_dir_cloud" value="link" <?php if ( get_option('gtags_dir_cloud')=='link' ) echo 'checked="checked"'; ?>> a link which expands when clicked<br>
	<input type="radio" name="gtags_dir_cloud" value="show" <?php if ( get_option('gtags_dir_cloud')=='show' || !get_option('gtags_dir_cloud') ) echo 'checked="checked"'; ?>> always visible (default)<br>
	<br>
	<input type="checkbox" value="true" name="gtags_show_in_dir_list" <?php if ( get_option('gtags_show_in_dir_list')=='true' ) echo 'checked="checked"'; ?>> <b>Show tags for each group in the group directory list</b> (off by default)<br><br>
	
	<h3>Single Group Settings</h3>
	<input type="checkbox" value="true" name="gtags_single_header" <?php if ( get_option('gtags_single_header') == 'true' || !get_option('gtags_single_header') ) echo 'checked="checked"'; ?>> <b>Show group tags in the group header</b> below the description (on by default)<br><br>
	
	<h3>Tag Cloud Settings</h3>
	<p>If you want to learn about the values below read the WordPress <a href="http://codex.wordpress.org/Template_Tags/wp_tag_cloud" target="_blank">function reference for wp_tag_cloud</a><p>
	
	<?php $r = gtags_cloud_args(); extract( $r ); ?>
	<input type="text" name="gtags_cloud_args[smallest]" value="<?php echo $smallest; ?>" style="width:3em;">px - <b>smallest</b> tag size (default: 9)<br>
	<input type="text" name="gtags_cloud_args[largest]" value="<?php echo $largest; ?>" style="width:3em;">px - <b>largest</b> tag size (default: 20)<br>
	<input type="text" name="gtags_cloud_args[number]" value="<?php echo $number; ?>" style="width:5em;"> - <b>number</b> of tags to display (default: 36)<br>
	<input type="text" name="gtags_cloud_args[orderby]" value="<?php echo $orderby; ?>" style="width:5em;"> - <b>orderby</b> count or name - count puts them in order from most to least. name puts them alphabetical (default: count)<br>
	<input type="text" name="gtags_cloud_args[order]" value="<?php echo $order; ?>" style="width:5em;"> - <b>order</b> can be ASC, DESC or RAND - ascending, descending or random order (default: DESC)<br>
	<input type="text" name="gtags_cloud_args[separator]" value="<?php echo $separator; ?>" style="width:5em;"> - <b>separator</b> between tags. It is fine if this field is blank. (default: [space])<br>
	<br>
	
	<h3>Tag Cloud Include & Exclude</h3>
	<b>Group Tags to EXclude:</b> (comma separated)<br>
	<textarea name="gtags_exclude" style="width:95%;max-width:60em;height:4.5em;"><?php 
		echo stripslashes( get_option('gtags_exclude') ); 
	?></textarea><br><br>
	
	<b>OR Group Tags to INclude</b> (only show these group tags, comma separated)<br>
	<textarea name="gtags_include" style="width:95%;max-width:60em;height:4.5em;"><?php 
		echo stripslashes( get_option('gtags_include') ); 
	?></textarea><br><br>
	

	<h3>Group Admin Settings</h3>
	Show <input type="text" name="gtags_popular_limit" value="<?php if ( !get_option('gtags_popular_limit') ) echo 36; else echo get_option('gtags_popular_limit'); ?>" style="width:5em;">  popular tags for group admins in Group Settings. (default: 36)<br><br>
	

	<h3>Group Categories</h3>
	<p>Create pre-defined group categories. An additional drop down menu will be created where group admins can choose a category for a group. Only one can be chosen. The chosen one then gets added as a group tag (and saved in group meta). Leave blank to disable.</p>
	<p>Enter a short category name/tag/slug and category description for each category.</p>
	<?php $gtags_category = get_option( 'gtags_category' ); ?>
	<?php if ( empty( $gtags_category) ) $gtags_category = array(''=>'')?>
	<?php foreach ( $gtags_category as $cat_slug => $cat_name ) : ?>
		<div class="gtags_category">
			Category name/tag:<input type="text" name="gtags_category[slug][]" value="<?php echo $cat_slug; ?>"> &nbsp; description:<input type="text" name="gtags_category[desc][]" value="<?php echo $cat_name; ?>"> 
		</div>
	<?php endforeach; ?>
	<a href="#" id="add-another-catogory">Add another group category +</a>
	
<script type="text/javascript">jQuery(document).ready(function($) {
	$('#add-another-catogory').live('click',function(event) {
		event.preventDefault();
		var $this = $(this);
		var $last = $this.prev(); // $this.parents('.something').prev() also useful
		var $clone = $last.clone(true);
		var $inputs = $clone.find('input,textarea,select');
		$inputs.val('');
		$last.after($clone);
		$inputs.eq(0).focus();
		//if($clone.find('.remove-this-row').size() < 1) {
		//	$clone.find('td:last').append('<div><a href="#" class="remove-this-row">Remove</a></div>');
		//}
	});	
}); </script>	



	<p class="submit"><input type="submit" name="submit" value="Save Settings"/></p>
	
	<?php
	/* This is very important, don't leave it out. */
	wp_nonce_field( 'gtags_admin' );
	?>
</form>



<!-- Rating link and Paypal button -->
<hr>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
If you enjoy using this plugin <a href="http://wordpress.org/extend/plugins/buddypress-group-tags/" target="_blank">please rate it</a>.<br>
Please make a donation now to support ongoing development.<br>
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="PXD76LU2VQ5AS">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>



</div>
<?php
}


?>
