<?php
//WangGuard Users


function wangguard_users() {
	global $wpdb , $wangguard_is_network_admin , $wangguard_nonce , $wangguard_g_splog_users_count;

	if ( !current_user_can('level_10') )
		die(__('Cheatin&#8217; uh?', 'wangguard'));
	
	
	include 'wangguard-class-wp-users.php';
	$wp_list_table = new WangGuard_Users_Table();
	$pagenum = $wp_list_table->get_pagenum();
	
	$messages = array();


	switch ( $wp_list_table->current_action() ) {

		case 'delete':
			if (!wp_verify_nonce($_REQUEST['_wpnonce'], "bulk-users" )) die("bad nonce");
			//report selected users
			$reportedUsers = 0;
			$users = (array)$_REQUEST['users'];

			if (wangguard_is_multisite () && function_exists("wpmu_delete_user"))
				$delFunc = 'wpmu_delete_user';
			else
				$delFunc = 'wp_delete_user';

			$deletedUsers = 0;
			foreach ($users as $spuserID) {
				$user_object = new WP_User($spuserID);
				if ( !wangguard_is_admin($user_object) ) {
					$delFunc($spuserID);
					$deletedUsers++;
				}
			}

			if ($deletedUsers) {
				$messages[] = '<div id="message" class="updated fade"><p><strong>'. sprintf(__("%d user(s) were deleted" , "wangguard") , $deletedUsers) .'</strong></p></div>';
			}
			break;

			
		case 'reportassplog':
			if (!wp_verify_nonce($_REQUEST['_wpnonce'], "bulk-users" )) die("bad nonce");
			//report selected users
			$reportedUsers = 0;
			$users = (array)$_REQUEST['users'];
			$res = wangguard_report_users($users);
			$resArr = explode(",", $res);
			$reportedUsers = (count($users)==0) ? 0 : count($resArr);
				
			if ($reportedUsers) {
				if (wangguard_get_option ("wangguard-delete-users-on-report")=='1')
					$messages[] = '<div id="message" class="updated fade"><p><strong>'. sprintf(__("%d user(s) were reported as Splogger(s) and deleted" , "wangguard") , $reportedUsers) .'</strong></p></div>';
				else
					$messages[] = '<div id="message" class="updated fade"><p><strong>'. sprintf(__("%d user(s) were reported as Splogger(s)" , "wangguard") , $reportedUsers) .'</strong></p></div>';
			}
			break;


		case 'spam':
			$spamUsers = 0;
			$users = (array)$_REQUEST['users'];
			
			foreach ($users as $spuserID) {
				$user = new WP_User( $spuserID );

				if ( in_array( $user->user_login, get_super_admins() ) )
					continue;

				if (function_exists('get_blogs_of_user') && function_exists('update_blog_status')) {
					$blogs = get_blogs_of_user( $spuserID, true );
					foreach ( (array) $blogs as $key => $details ) {
						
						
//						if ( $details->userblog_id != $current_site->blog_id ) // main blog not a spam !
//							update_blog_status( $details->userblog_id, 'spam', '1' );
						$isMainBlog = false;
						if (isset ($current_site)) {
							$isMainBlog = ($details->userblog_id != $current_site->blog_id); // main blog not a spam !
						}
						elseif (defined("BP_ROOT_BLOG")) {
							$isMainBlog = ( 1 == $details->userblog_id || BP_ROOT_BLOG == $details->userblog_id );
						}
						else
							$isMainBlog = ($details->userblog_id == 1);

						$userIsAuthor = false;
						if (!$isMainBlog) {
							//Only works on WP 3+
							$blog_prefix = $wpdb->get_blog_prefix( $details->userblog_id );
							$authorcaps = $wpdb->get_var( sprintf("SELECT meta_value as caps FROM $wpdb->users u, $wpdb->usermeta um WHERE u.ID = %d and u.ID = um.user_id AND meta_key = '{$blog_prefix}capabilities'" , $spuserID ));

							$caps = maybe_unserialize( $authorcaps );
							$userIsAuthor = ( isset( $caps['administrator'] ) );
						}

						//Update blog to spam if the user is the author and its not the main blog
						if ((!$isMainBlog) && $userIsAuthor) {
							@update_blog_status( $details->userblog_id, 'spam', '1' );

							//remove blog from queue
							$table_name = $wpdb->base_prefix . "wangguardreportqueue";
							$wpdb->query( $wpdb->prepare("delete from $table_name where blog_id = '%d'" , $details->userblog_id ) );
						}

						
						
					}
				}
				if (function_exists('update_user_status'))
					update_user_status( $spuserID, 'spam', '1' );
				
				$wpdb->update( $wpdb->users, array( 'user_status' => 1 ), array( 'ID' => $spuserID ) );
				
				$spamUsers++;
			}

			if ($spamUsers) {
				$messages[] = '<div id="message" class="updated fade"><p><strong>'. sprintf(__("%d user(s) were marked as Spam" , "wangguard") , $spamUsers) .'</strong></p></div>';
			}
			
		break;

		case 'notspam':
			$spamUsers = 0;
			$users = (array)$_REQUEST['users'];
			
			foreach ($users as $spuserID) {
				
				if (function_exists('get_blogs_of_user') && function_exists('update_blog_status')) {
					$blogs = get_blogs_of_user( $spuserID, true );
					foreach ( (array) $blogs as $key => $details )
						update_blog_status( $details->userblog_id, 'spam', '0' );
				}

				if (function_exists('update_user_status'))
					update_user_status( $spuserID, 'spam', '0' );
				
				$wpdb->update( $wpdb->users, array( 'user_status' => 0 ), array( 'ID' => $spuserID ) );
				
				
				$spamUsers++;
			}

			if ($spamUsers) {
				$messages[] = '<div id="message" class="updated fade"><p><strong>'. sprintf(__("%d user(s) were marked as Safe" , "wangguard") , $spamUsers) .'</strong></p></div>';
			}
		break;
	
	}
		

	
	
	if ( count($messages) ) {
		foreach ( $messages as $msg )
			echo $msg;
	} ?>

	
	
	<div class="wrap" id="wangguard-users-cont">
		<div class="wangguard-confico"><img src="<?php echo WP_PLUGIN_URL ?>/wangguard/img/users.png" alt="<?php echo htmlentities(__('WangGuard Users', 'wangguard')) ?>" /></div>
		<div class="icon32" id="icon-wangguard"><br></div>
		<h2><?php _e('WangGuard Users', 'wangguard'); ?></h2>

		<?php $wp_list_table->prepare_items();	?>
		
		<form action="" method="get">
			<input type="hidden" name="page" value="wangguard_users" />
			<?php $wp_list_table->search_box( __( 'Search Users' ), 'user' ); ?>
		</form>
		
	
		<form action="admin.php" method="get" id="wangguard-users-form">

			<input type="hidden" name="page" value="wangguard_users" />
			<?php
			$total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );
			if ( $pagenum > $total_pages && $total_pages > 0 ) {
				wp_redirect( add_query_arg( 'paged', $total_pages ) );
				exit;
			}
			$wp_list_table->views();
			?>
			
			<?php
			
			$requestType = "";
			if (isset($_REQUEST['type']))
				$requestType = $_REQUEST['type'];
			
			if ($requestType == 'spl') {?>
				<div id="wangguard-deleteallsplcont" class="subsubsub"><a class="button-primary" id="wangguard-deleteallspl" href="javascript:void(0)"><?php echo __('Delete All Sploggers' , 'wangguard')  ?></a></div>
				<script type="text/javascript">
					<?php 
					$urlFunc = "admin_url";
					if ($wangguard_is_network_admin && function_exists("network_admin_url"))
						$urlFunc = "network_admin_url";
					$deleteSPURL = $urlFunc( 'admin.php?page=wangguard_wizard&wangguard_delete_splogguers=1&wangguard_splogcnt='.$wangguard_g_splog_users_count.'&wangguard_step=3&_wpnonce=' . wp_create_nonce( $wangguard_nonce )   );
					?>
					
					jQuery("a#wangguard-deleteallspl").click(function() {
						if (confirm('<?php echo __('Do you confirm to delete ALL Sploggers?','wangguard')?>')) {
							document.location = '<?php echo $deleteSPURL?>';
						}
					});
				</script>
			<?php } ?>
				

			<?php
			$wp_list_table->display();
			?>	
			
		</form>
		<br class="clear" />
	</div>
	<?php
}
?>
