<?php
/**
* Bowe codes settings!
*
*/

function bowe_codes_options(){
	$defaultcss = file_get_contents(BOWE_CODES_PLUGIN_URL.'/css/default.css');
	if($_POST['bc_settings']){
		update_option( 'bc_default_css', $_POST['bc_default_css'] );
		if(is_multisite()) update_option( 'bc_enable_network', $_POST['bc_enable_network'] );
	}
	
	$bc_option = get_option('bc_default_css');
	if($bc_option =="") $bc_option = "no";
	if(is_multisite()){
		$bc_no_child_blog = get_option('bc_enable_network');
		if($bc_no_child_blog =="") $bc_no_child_blog = "no";
	}
	?>
	<div class="wrap">
		<h2><?php _e('Bowe codes options','bowe-codes');?></h2>
		<div style="width:400px">
		<form action="" method="post">
			<table class="form-bowe-code">
				<tr>
					<td><label for="bc_default_css"><?php _e('Disable default css','bowe-codes');?></label></td>
					<td><input type="radio" name="bc_default_css" value="yes" <?php if($bc_option=="yes") echo 'checked';?>><?php _e('Yes','bowe-codes');?>&nbsp;
					<input type="radio" name="bc_default_css" value="no" <?php if($bc_option=="no") echo 'checked';?>><?php _e('No','bowe-codes');?></td>
				</tr>
				<?php if(is_multisite()):?>
				<tr>
					<td><label for="bc_enable_network"><?php _e('Hide button for child blogs','bowe-codes');?></label></td>
					<td><input type="radio" name="bc_enable_network" value="yes" <?php if($bc_no_child_blog=="yes") echo 'checked';?>><?php _e('Yes','bowe-codes');?>&nbsp;
					<input type="radio" name="bc_enable_network" value="no" <?php if($bc_no_child_blog=="no") echo 'checked';?>><?php _e('No','bowe-codes');?></td>
				</tr>
				<?php endif;?>
				<tr>
					<td class="action-btn" colspan="2"><input type="submit" value="<?php _e('Save','bowe-codes');?>" class="button-primary" name="bc_settings"/></td>
				</tr>
			</table>
		</form>
		</div>
		<div class="css-file-view">
			<h3><?php _e('Content of default.css (for info)','bowe-codes');?></h3>
			<textarea id="css-file-src"><?php echo $defaultcss;?></textarea>
		</div>
	</div>
	<?php
}

?>