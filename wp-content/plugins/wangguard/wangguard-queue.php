<?php
//WangGuard Queue


function wangguard_queue() {
	global $wpdb;

	if ( !current_user_can('level_10') )
		die(__('Cheatin&#8217; uh?', 'wangguard'));
	
	
	include 'wangguard-class-wp-queue.php';
	$wp_list_table = new WangGuard_Queue_Table();
	$pagenum = $wp_list_table->get_pagenum();
	
	$messages = array();


	switch ( $wp_list_table->current_action() ) {
		case 'unreport':
			if (!wp_verify_nonce($_REQUEST['_wpnonce'], "bulk-reports" )) die("bad nonce");
			
			//remove selected blogs from the queue
			$removedBlogs = 0;
			$blogs = (array)$_REQUEST['blogs'];
			foreach ($blogs as $blogid) {
				$blogid = (int)$blogid;
				$table_name = $wpdb->base_prefix . "wangguardreportqueue";
				$wpdb->query( $wpdb->prepare("delete from $table_name where blog_id = '%d'" , $blogid ) );
				$removedBlogs++;
			}
			
			//remove selected users from the queue
			$removedUsers = 0;
			$users = (array)$_REQUEST['users'];
			foreach ($users as $userid) {
				$userid = (int)$userid;
				$table_name = $wpdb->base_prefix . "wangguardreportqueue";
				$wpdb->query( $wpdb->prepare("delete from $table_name where ID = '%d'" , $userid ) );
				$removedUsers++;
			}

			if ($removedBlogs)
				$messages[] = '<div id="message" class="updated fade"><p><strong>'. sprintf(__("%d blog(s) were removed from the WangGuard Moderation Queue" , "wangguard") , $removedBlogs) .'</strong></p></div>';
			
			if ($removedUsers)
				$messages[] = '<div id="message" class="updated fade"><p><strong>'. sprintf(__("%d user(s) were removed from the WangGuard Moderation Queue" , "wangguard") , $removedUsers) .'</strong></p></div>';
			break;
		
		case 'reportassplog':
			if (!wp_verify_nonce($_REQUEST['_wpnonce'], "bulk-reports" )) die("bad nonce");
			
			//report selected blogs
			$reportedBlogs = 0;
			$reportedAuthors = 0;
			$authors_ids = array();
			$blogs = (array)$_REQUEST['blogs'];
			$table_name = $wpdb->base_prefix . "wangguardreportqueue";
			foreach ($blogs as $blogid) {
				$blogid = (int)$blogid;
				$reportedBlogs++;

				//remove blog from queue (users are removed on the delete_user hook)
				$wpdb->query( $wpdb->prepare("delete from $table_name where blog_id = '%d'" , $blogid ) );
				
				//get the authors of each blog
				$blog_prefix = $wpdb->get_blog_prefix( $blogid );
				$authors = $wpdb->get_results( "SELECT user_id, meta_value as caps FROM $wpdb->users u, $wpdb->usermeta um WHERE u.ID = um.user_id AND meta_key = '{$blog_prefix}capabilities'" );
				foreach( (array)$authors as $author ) {
				
					$caps = maybe_unserialize( $author->caps );
					if ( isset( $caps['subscriber'] ) || isset( $caps['contributor'] ) ) continue;
					
					$authors_ids[] = $author->user_id;
				}
			}
			$res = wangguard_report_users($authors_ids);
			$resArr = explode(",", $res);
			$reportedAuthors = count($resArr);
			

			
			//report selected users
			$reportedUsers = 0;
			$users = (array)$_REQUEST['users'];
			$res = wangguard_report_users($users);
			$resArr = explode(",", $res);
			$reportedUsers = count($resArr);
				
			if ($reportedBlogs)
				$messages[] = '<div id="message" class="updated fade"><p><strong>'. sprintf(__("%d blog(s) and %d author(s) was reported as Splogger(s) and deleted" , "wangguard") , $reportedBlogs , $reportedAuthors) .'</strong></p></div>';
			
			if ($reportedUsers)
				$messages[] = '<div id="message" class="updated fade"><p><strong>'. sprintf(__("%d user(s) were reported as Splogger(s) and deleted" , "wangguard") , $reportedUsers) .'</strong></p></div>';
			break;
	}
		

	if ( count($messages) ) {
		foreach ( $messages as $msg )
			echo $msg;
	} ?>

	
	
	<div class="wrap">
		<div class="icon32" id="icon-wangguard"><br></div>
		<h2><?php _e('WangGuard Moderation Queue', 'wangguard'); ?></h2>

		<form action="admin.php" method="get" id="wangguard-queue-form">
			<input type="hidden" name="page" value="wangguard_queue" />
			<?php
			$wp_list_table->prepare_items();
			$total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );
			if ( $pagenum > $total_pages && $total_pages > 0 ) {
				wp_redirect( add_query_arg( 'paged', $total_pages ) );
				exit;
			}
			$wp_list_table->views();

			$wp_list_table->display();
			?>	
			
		</form>
		<br class="clear" />
	</div>
	
	<?php
}
?>