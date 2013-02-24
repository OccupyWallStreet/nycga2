<?php 
/*
 * This is the page users will see logged in. 
 * You can edit this, but for upgrade safety you should copy and modify this file into your template folder.
 * The location from within your template folder is plugins/login-with-ajax/ (create these directories if they don't exist)
*/
?>
<?php
	global $current_user;
	if( $is_widget ){
		echo $before_widget . $before_title . '<span id="LoginWithAjax_Title">' . __( 'Hi', 'login-with-ajax' ) . " " . $current_user->display_name  . '</span>' . $after_title;
	}else{
		//If you want the AJAX login widget to work without refreshing the screen upon login, this is needed for the widget title to update.
		echo '<span id="LoginWithAjax_Title_Substitute" style="display:none">' . __( 'Hi', 'login-with-ajax' ) . " " . $current_user->display_name  . '</span>';
	}
?>
<div id="LoginWithAjax">
	<?php 
		global $current_user;
		global $user_level;
		global $wpmu_version;
		get_currentuserinfo();
	?>
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td class="avatar" id="LoginWithAjax_Avatar">
				<?php echo get_avatar( $current_user->ID, $size = '50' );  ?>
			</td>
			<td id="LoginWithAjax_Info">
				<?php
					//Admin URL
					if ( $lwa_data['profile_link'] == '1' ) {
						if( function_exists('bp_loggedin_user_link') ){
							?>
							<a href="<?php bp_loggedin_user_link(); ?>"><?php echo strtolower(__('Profile')) ?></a><br/>
							<?php	
						}else{
							?>
							<a href="<?php bloginfo('wpurl') ?>/wp-admin/profile.php"><?php echo strtolower(__('Profile')) ?></a><br/>
							<?php	
						}
					}
					//Logout URL
					?>
					<a id="wp-logout" href="<?php echo wp_logout_url() ?>"><?php echo strtolower(__( 'Log Out' )) ?></a><br />
					<?php
					//Blog Admin
					if( !empty($wpmu_version) || $user_level > 8 ) {
						?>
						<a href="<?php bloginfo('wpurl') ?>/wp-admin/"><?php _e("blog admin", 'loginwithajax'); ?></a>
						<?php
					}
				?>
			</td>
		</tr>
	</table>
</div>
<?php
	if( $is_widget ){
		echo $after_widget;
	}
?>