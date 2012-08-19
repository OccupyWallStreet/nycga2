<?php
/**
 * WangGuard Users Table class.
 *
 */


require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );


class WangGuard_Users_Table extends WP_List_Table {

	function WangGuard_Users_Table() {
		
		global $wp_version;
		$cur_wp_version = preg_replace('/-.*$/', '', $wp_version);
		$callConstructor = version_compare($cur_wp_version , '3.2.1' , ">=");
		
	
		if (!$callConstructor) {
			parent::WP_List_Table( array(
				'singular' => 'user',
				'plural'   => 'users'
			) );
		}
		else {
			parent::__construct( array(
				'singular' => 'user',
				'plural'   => 'users'
			) );
		}
	}

	function prepare_items() {
		global $usersearch;

		$usersearch = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';
		
		$usertype = isset( $_REQUEST['type'] ) ? $_REQUEST['type'] : '';

		$users_per_page = $this->get_items_per_page( "wangguard_page_wangguard_users_network_per_page" );

		$paged = $this->get_pagenum();

		$args = array(
			'number' => $users_per_page,
			'offset' => ( $paged-1 ) * $users_per_page,
			'search' => $usersearch,
			'type' => $usertype,
			'fields' => 'all_with_meta'
		);

		$args['search'] = $args['search'];

		if ( isset( $_REQUEST['orderby'] ) )
			$args['orderby'] = $_REQUEST['orderby'];

		if ( isset( $_REQUEST['order'] ) )
			$args['order'] = $_REQUEST['order'];

		// Query the user IDs for this page
		$wp_user_search = new WangGuard_Users_Query( $args );

		$this->items = $wp_user_search->get_results();

		$this->set_pagination_args( array(
			'total_items' => $wp_user_search->get_total(),
			'per_page' => $users_per_page,
		) );
	}

	function no_items() {
		_e( 'No users were found.' , 'wangguard' );
	}

	function get_views() {
		global $wpdb , $wangguard_g_splog_users_count;
		$url = 'admin.php?page=wangguard_users';
		
		$requestType = "";
		if (isset($_REQUEST['type']))
			$requestType = $_REQUEST['type'];
		
		$total = array();

		
		//Total users
		$Count = $wpdb->get_col( $wpdb->prepare("select count(*) as q from $wpdb->users where 1=1"));
		$total_users = $Count[0];
		
		$class = empty($requestType) ? ' class="current"' : '';
		$total['all'] = "<a href='$url'$class>" . sprintf( __( 'All Members <span class="count">(%s)</span>' , $total_users, 'wangguard' ), number_format_i18n( $total_users ) ) . '</a>';
		

		
		//Legitimate users
		$table_name = $wpdb->base_prefix . "wangguarduserstatus";
		$wgLegitimateSQL = " AND EXISTS (select user_status from $table_name where $table_name.ID = {$wpdb->users}.ID and $table_name.user_status IN ( 'checked', 'force-checked' ))";
		
		if (wangguard_is_multisite())
			$Count = $wpdb->get_col( $wpdb->prepare("select count(*) as q from $wpdb->users where $wpdb->users.user_status <> 1 AND $wpdb->users.spam = 0" . $wgLegitimateSQL));
		elseif (defined( 'BP_VERSION' ))
			$Count = $wpdb->get_col( $wpdb->prepare("select count(*) as q from $wpdb->users where $wpdb->users.user_status <> 1" . $wgLegitimateSQL));
		else 
			$Count = $wpdb->get_col( $wpdb->prepare("select count(*) as q from $wpdb->users where $wpdb->users.user_status <> 1" . $wgLegitimateSQL));
		
		$legitimate_users = $Count[0];
		
		$class = ($requestType == "l") ? ' class="current"' : '';
		$total['legitimate'] = "<a href='" . add_query_arg( 'type', "l", $url ) . "'$class>".sprintf( __( 'Verified Members <span class="count">(%s)</span>' , 'wangguard'), number_format_i18n( $legitimate_users ) )."</a>";
		

		
		//Spam users, only BP or MS
		if (wangguard_is_multisite() || defined( 'BP_VERSION' )) {
			if (wangguard_is_multisite())
				$Count = $wpdb->get_col( $wpdb->prepare("select count(*) as q from $wpdb->users where $wpdb->users.user_status = 1 OR $wpdb->users.spam = 1"));
			else
				$Count = $wpdb->get_col( $wpdb->prepare("select count(*) as q from $wpdb->users where $wpdb->users.user_status = 1"));
			$spam_users = $Count[0];
			
			$class = ($requestType == "spam") ? ' class="current"' : '';
			$total['spam'] = "<a href='" . add_query_arg( 'type', "spam", $url ) . "'$class>".sprintf( __( 'Spammers <span class="count">(%s)</span>' , 'wangguard'), number_format_i18n( $spam_users ) )."</a>";
		}
		
		
		//Sploggers users
		$table_name = $wpdb->base_prefix . "wangguarduserstatus";
		$Count = $wpdb->get_col( $wpdb->prepare("select count(*) as q from $wpdb->users where EXISTS (select user_status from $table_name where $table_name.ID = {$wpdb->users}.ID and $table_name.user_status IN ( 'reported', 'autorep' ))"));
		$splog_users = $wangguard_g_splog_users_count = $Count[0];
		
		$class = ($requestType == "spl") ? ' class="current"' : '';
		$total['sploggers'] = "<a href='" . add_query_arg( 'type', "spl", $url ) . "'$class>".sprintf( __( 'Sploggers <span class="count">(%s)</span>' , 'wangguard'), number_format_i18n( $splog_users ) )."</a>";
		
		return $total;
	}

	function get_bulk_actions() {
		$actions = array();

		$actions['reportassplog'] = __( 'Report as Splogger', 'wangguard' );
		if (wangguard_is_multisite() || defined( 'BP_VERSION' )) {
			$actions['spam'] = _x( 'Mark as Spam', 'user' );
			$actions['notspam'] = _x( 'Not Spam', 'user' );
		}
		$actions['delete'] = __( 'Delete Users', 'wangguard' );
		
		return $actions;
	}

	function extra_tablenav( $which ) {
		return;
	}

	function get_columns() {
		$c = array(
			'cb'		=> '<input type="checkbox" />',
			'username'	=> __( 'Username' ),
			'email'		=> __( 'E-mail' ),
			'user_registered'=> __( 'Signed up on' , 'wangguard' ),
			'from_ip'=>		__( 'User IP' , 'wangguard' ),
			'posts'		=> __( 'Posts' ),
			'blogs'		=> __( 'Blogs' ),
		);

		return $c;
	}

	function get_sortable_columns() {
		$c = array(
			'username' => 'login',
			'email' => 'email',
			'from_ip' => 'from_ip',
			'user_registered' => 'user_registered',
		);

		return $c;
	}

	function display_rows() {
		// Query the post counts for this page
		$style = '';
		
		$post_counts = count_many_users_posts( array_keys( $this->items ) );
		
		foreach ( $this->items as $userid => $row_data ) {
			$style = ( 'alternate' == $style ) ? '' : 'alternate';
			echo "\n\t", $this->single_row( $row_data, $style , $post_counts[$userid] );
		}
	}

	/**
	 * Generate HTML for a single row on the users.php admin panel.
	 */
	function single_row( $row_data, $style = '' , $numposts) {
		global $wpdb , $wp_roles;

	
		$url = admin_url('admin.php?page=wangguard_users&order='.(isset($_REQUEST['order']) ? $_REQUEST['order'] : '').'&orderby='.(isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : ''));

		
		//USER
		$row_data->filter = 'display';
		$email = $row_data->user_email;
		$checkbox = '';
		
		$actions = false;
		if (defined('BP_VERSION')) {
			$user_editobj_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( stripslashes( $_SERVER['REQUEST_URI'] ) ), "user-edit.php?user_id=" . $row_data->ID ) );
			$editobj_link = esc_url(  bp_core_get_user_domain($row_data->ID));
			
			// Set up the hover actions for this user
			$actions['edituser'] = "<a href='{$user_editobj_link}' target='_blank'>" . __( 'Edit user', 'wangguard' ) . "</a>";
			$actions['bpprofile'] = "<a href='{$editobj_link}' target='_blank'>" . __( 'BP Profile', 'wangguard' ) . "</a>";
			$report = "<strong><a target=\"_blank\" href=\"$editobj_link\">{$row_data->user_login}</a></strong><br />";
		}
		else {
			$editobj_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( stripslashes( $_SERVER['REQUEST_URI'] ) ), "user-edit.php?user_id=" . $row_data->ID ) );
			$report = "<strong><a target=\"_blank\" href=\"$editobj_link\">{$row_data->user_login}</a></strong><br />";
		}



		// Set up the checkbox ( because the user is editable, otherwise its empty )
		$checkbox = "<input type='checkbox' name='users[]' id='user_{$row_data->ID}' value='{$row_data->ID}' />";

		$avatar = get_avatar( $row_data->ID, 32 );
		
		$role = reset( $row_data->roles );
		if (!empty($role))
			$role = $wp_roles->role_names[$role];

		$userid = $row_data->ID;
		$statushtml = wangguard_user_custom_columns("" , "wangguardstatus" , $userid);

		$rowID = "user-".$userid;

		$trstyle = "class='$style ".(@$row_data->spam || @$row_data->user_status ? "site-spammed" : '')."'";

		
		$r = "<tr id='$rowID'$trstyle>";

		list( $columns, $hidden ) = $this->get_column_info();
		
		
		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class=\"$column_name column-{$column_name}\" $style";

			$attributes = $class;
			
			switch ( $column_name ) {
				case 'cb':
					$r .= "<th scope='row' class='check-column'>$checkbox</th>";
					break;
				case 'username':
					$r .= "<td $attributes>$avatar $report <span style='font-size:11px'>{$role}" . ($actions ? $this->row_actions( $actions ) : "") . "</span></td>";
					break;
				case 'name':
					$r .= "<td $attributes>$row_data->first_name $row_data->last_name</td>";
					break;
				case 'email':
					$r .= "<td $attributes><a href='mailto:$email' title='" . esc_attr( sprintf( __( 'E-mail: %s' ), $email ) ) . "'>$email</a></td>";
					break;
				case 'user_registered':
					$r .= "<td $attributes><span style='font-size:11px'>".date(get_option('date_format'), strtotime($row_data->user_registered)) . " " . date(get_option('time_format'), strtotime($row_data->user_registered))."</span></td>";
					break;
				case 'from_ip':
					$r .= "<td $attributes>";
					$r .= "<div class='wangguard-user-ip' data='{$row_data->user_ip}'><span class='wangguard-user-ip-bb'>";
					$r .= $row_data->user_ip;
					$r .= '</span>';
					
					if ($row_data->user_ip_is_proxy)
						$r .= " <span class='wangguard_proxy'>" . __( 'proxy' , 'wangguard') .  "</span>";

					$r .= "</div>";
					
					if (!empty($row_data->user_reported_proxy_ip)) {
						$r .= "<div class='wangguard-user-ip' data='{$row_data->user_reported_proxy_ip}'><span class='wangguard-user-ip-bb'>";
						$r .= $row_data->user_reported_proxy_ip;
						$r .= '</span>';
						$r .= " <span class='wangguard_proxy'>" . __( 'reported proxy' , 'wangguard') .  "</span>";
						$r .= "</div>";
					}
					
					$r .= "</td>";
					break;
				case 'posts':
					$attributes = 'class="posts column-posts num"' . $style;
					$r .= "<td $attributes>";
					if ( $numposts > 0 ) {
						$r .= "<a target='_blank' href='edit.php?author=$row_data->ID' title='" . esc_attr__( 'View posts by this author' ) . "' class='edit'>";
						$r .= $numposts;
						$r .= '</a>';
					} else {
						$r .= 0;
					}
					$r .= "</td>";
					break;
				case 'blogs':
					$r .= "<td $attributes>";
					if (function_exists("get_blogs_of_user")) {
						$blogs = @get_blogs_of_user( $row_data->ID, true );
						if (is_array($blogs))
							foreach ( (array) $blogs as $key => $details ) {
								$r .= '- <a href="'. $details->siteurl .'" title="'. htmlentities($details->siteurl, 0, 'UTF-8') .'" target="_new">'.$details->blogname.'</a><br/>';
							}
					}
					
					$r .= "</td>";
					break;
				case 'wgstatus':
					$r .= "<td $attributes>" . $statushtml . "</td>";
					break;
			}
		}
		$r .= '</tr>';

		return $r;
	}
}















class WangGuard_Users_Query {

	/**
	 * List of found user ids
	 */
	var $results;

	/**
	 * Total number of found users for the current query
	 */
	var $total_users = 0;

	// SQL clauses
	var $query_fields_u;
	var $query_from_u;
	var $query_where_u;
	
	var $query_orderby;
	var $query_limit;

	/**
	 * PHP4 constructor
	 */
	function WangGuard_Users_Query( $query = null ) {
		$this->__construct( $query );
	}

	/**
	 * PHP5 constructor
	 * @return WangGuard_Users_Query
	 */
	function __construct( $query = null ) {
		if ( !empty( $query ) ) {
			$this->query_vars = wp_parse_args( $query, array(
				'orderby' => 'login',
				'order' => 'ASC',
				'search' => '',
				'offset' => '', 
				'number' => '',
				'type' => '',
				'count_total' => true
			) );

			$this->prepare_query();
			$this->query();
		}
	}

	/**
	 * Prepare the query variables
	 */
	function prepare_query() {
		global $wpdb;

		$qv = &$this->query_vars;
		
		$tableUserStatus = $wpdb->base_prefix . "wangguarduserstatus";
		
		
		$this->query_fields_u = "$wpdb->users.ID , $wpdb->users.user_login , $tableUserStatus.user_status, $tableUserStatus.user_ip as status_user_ip, $tableUserStatus.user_proxy_ip as status_user_proxy_ip";
		$this->query_from_u = "FROM $wpdb->users LEFT JOIN $tableUserStatus ON $wpdb->users.ID = $tableUserStatus.ID";
		
		//search
		$this->query_where_u = '';
		if (!empty($qv['search'])) {
			
			if (empty($this->query_where_u))
				$this->query_where_u = " WHERE ";
			else
				$this->query_where_u .= " AND ";
			
			$this->query_where_u .= "($wpdb->users.user_login LIKE '%".like_escape($qv['search'])."%' OR $wpdb->users.user_nicename LIKE '%".like_escape($qv['search'])."%' OR $wpdb->users.user_email LIKE '%".like_escape($qv['search'])."%')";
		}
		
		switch ($qv['type']) {
			case 'l':
				//Legitimate users filter
				if (empty($this->query_where_u))
					$this->query_where_u = " WHERE ";
				else
					$this->query_where_u .= " AND ";

				//Legitimate users
				$wgLegitimateSQL = " $tableUserStatus.user_status IN ( 'checked', 'force-checked' )";

				if (wangguard_is_multisite())
					$wgLegitimateSQL = " $wpdb->users.user_status <> 1 AND $wpdb->users.spam = 0 AND " . $wgLegitimateSQL;
				elseif (defined( 'BP_VERSION' ))
					$wgLegitimateSQL = " $wpdb->users.user_status <> 1 AND " . $wgLegitimateSQL;
				else 
					$wgLegitimateSQL = " $wpdb->users.user_status <> 1 AND " . $wgLegitimateSQL;

				$this->query_where_u .= $wgLegitimateSQL;
				
				break;
				
				
			case 'spam':
				//Spam users filter
				
				if (!wangguard_is_multisite() && !defined('BP_VERSION'))
					break;
				
				if (empty($this->query_where_u))
					$this->query_where_u = " WHERE ";
				else
					$this->query_where_u .= " AND ";

				if (wangguard_is_multisite())
					$wgLegitimateSQL = " $wpdb->users.user_status = 1 OR $wpdb->users.spam = 1";
				else
					$wgLegitimateSQL = " $wpdb->users.user_status = 1";


				$this->query_where_u .= $wgLegitimateSQL;
				
				break;
				
				
			case 'spl':
				//Spoggers users filter
				if (empty($this->query_where_u))
					$this->query_where_u = " WHERE ";
				else
					$this->query_where_u .= " AND ";

				$wgLegitimateSQL = " $tableUserStatus.user_status IN ( 'reported', 'autorep' )";

				$this->query_where_u .= $wgLegitimateSQL;
				
				break;
		}


		// sorting
		switch ($qv['orderby']) {
			case "email":
				$orderby = "$wpdb->users.user_email";
				break;
			case "user_registered":
				$orderby = "$wpdb->users.user_registered";
				break;
			case "from_ip":
				$orderby = "case when $tableUserStatus.user_proxy_ip = '' then $tableUserStatus.user_ip else $tableUserStatus.user_proxy_ip end";
				break;
			case "login":
			default:
				$orderby = "$wpdb->users.user_login";
				break;
		}

		$qv['order'] = strtoupper( $qv['order'] );
		if ( 'ASC' == $qv['order'] )
			$order = 'ASC';
		else
			$order = 'DESC';
		$this->query_orderby = "ORDER BY $orderby $order";

		// limit
		if ( $qv['number'] ) {
			if ( $qv['offset'] )
				$this->query_limit = $wpdb->prepare("LIMIT %d, %d", $qv['offset'], $qv['number']);
			else
				$this->query_limit = $wpdb->prepare("LIMIT %d", $qv['number']);
		}

		//_parse_meta_query( $qv );
	}

	/**
	 * Execute the query, with the current variables
	 */
	function query() {
		global $wpdb;

		$this->results = $wpdb->get_results("SELECT {$this->query_fields_u} {$this->query_from_u} {$this->query_where_u} {$this->query_orderby} {$this->query_limit}");
		//echo("SELECT {$this->query_fields_u} {$this->query_from_u} {$this->query_where_u} {$this->query_orderby} {$this->query_limit}");

		
		if ( $this->query_vars['count_total'] ) {
			$this->total_users = $wpdb->get_var("SELECT COUNT(*) {$this->query_from_u} {$this->query_where_u}");
		}

		if ( !$this->results )
			return;

	
		$r = array();
		foreach ( $this->results as $userrow ) {
			$userid = $userrow->ID;
			$r[ $userid ] = new WP_User( $userid );

			if ($_SERVER['SERVER_ADDR'] == $userrow->status_user_ip) {
				//server is behind an nginx/other proxy, grab the proxy address
				$r[ $userid ]->user_ip = !empty($userrow->status_user_proxy_ip) ? $userrow->status_user_proxy_ip : $userrow->status_user_ip;
				$r[ $userid ]->user_reported_proxy_ip = '';
				$r[ $userid ]->user_ip_is_proxy = !empty($userrow->status_user_proxy_ip);
			}
			else {
				//disply stored client IP addr, report proxy addr detected, just for admin info
				$r[ $userid ]->user_ip = $userrow->status_user_ip;
				$r[ $userid ]->user_reported_proxy_ip = $userrow->status_user_proxy_ip;
				$r[ $userid ]->user_ip_is_proxy = false;
			}
			$r[ $userid ]->user_row = $userrow;
		}

		$this->results = $r;
	}

	
	function get_results() {
		return $this->results;
	}

	function get_total() {
		return $this->total_users;
	}
}
?>