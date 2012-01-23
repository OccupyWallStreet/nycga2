<?php
/**
 * WangGuard Queue Table class.
 *
 */


require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );


class WangGuard_Queue_Table extends WP_List_Table {

	function WangGuard_Queue_Table() {
		
		global $wp_version;
		$cur_wp_version = preg_replace('/-.*$/', '', $wp_version);
		$callConstructor = version_compare($cur_wp_version , '3.2.1' , ">=");
		
	
		if (!$callConstructor) {
			parent::WP_List_Table( array(
				'singular' => 'report',
				'plural'   => 'reports'
			) );
		}
		else {
			parent::__construct( array(
				'singular' => 'report',
				'plural'   => 'reports'
			) );
		}
	}

	function prepare_items() {
		global $usersearch;

		$usersearch = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';

		$users_per_page = $this->get_items_per_page( "users_per_page" );

		$paged = $this->get_pagenum();

		$args = array(
			'number' => $users_per_page,
			'offset' => ( $paged-1 ) * $users_per_page,
			'search' => $usersearch,
			'fields' => 'all_with_meta'
		);

		$args['search'] = '*' . $args['search'] . '*';

		if ( isset( $_REQUEST['orderby'] ) )
			$args['orderby'] = $_REQUEST['orderby'];

		if ( isset( $_REQUEST['order'] ) )
			$args['order'] = $_REQUEST['order'];

		// Query the user IDs for this page
		$wp_user_search = new WangGuard_Queue_Query( $args );

		$this->items = $wp_user_search->get_results();

		$this->set_pagination_args( array(
			'total_items' => $wp_user_search->get_total(),
			'per_page' => $users_per_page,
		) );
	}

	function no_items() {
		_e( 'No reported users or blogs were found.' , 'wangguard' );
	}

	function get_views() {
		global $wpdb;
		$url = 'admin.php?page=wangguard_queue';
		
		$table_name = $wpdb->base_prefix . "wangguardreportqueue";
		$Count = $wpdb->get_col( $wpdb->prepare("select count(*) as q from $table_name where ID IS NOT NULL"));
		$total_users = $Count[0];

		$Count = $wpdb->get_col( $wpdb->prepare("select count(*) as q from $table_name where blog_id IS NOT NULL"));
		$total_blogs = $Count[0];

		$class = empty($_REQUEST['type']) ? ' class="current"' : '';
		
		$total = array();
		$total['all'] = "<a href='$url'$class>" . sprintf( __( 'All Reports <span class="count">(%s)</span>' , $total_users, 'wangguard' ), number_format_i18n( $total_users + $total_blogs ) ) . '</a>';
		
		$class = ($_REQUEST['type'] == "u") ? ' class="current"' : '';
		$total['reporteducount'] = "<a href='" . add_query_arg( 'type', "u", $url ) . "'$class>".sprintf( __( 'Reported users <span class="count">(%s)</span>' , 'wangguard'), number_format_i18n( $total_users ) )."</a>";
		
		$class = ($_REQUEST['type'] == "b") ? ' class="current"' : '';
		$total['reportedbcount'] = "<a href='" . add_query_arg( 'type', "b", $url ) . "'$class>".sprintf( __( 'Reported blogs <span class="count">(%s)</span>' , 'wangguard'), number_format_i18n( $total_blogs ) )."</a>";

		return $total;
	}

	function get_bulk_actions() {
		$actions = array();

		$actions['unreport'] = __( 'Remove from Queue', 'wangguard' );
		$actions['reportassplog'] = __( 'Report as Splogger', 'wangguard' );
		
		return $actions;
	}

	function extra_tablenav( $which ) {
		return;
	}

	function current_action() {
		if ( isset($_REQUEST['changeit']) && !empty($_REQUEST['new_role']) )
			return 'promote';

		return parent::current_action();
	}

	function get_columns() {
		$c = array(
			'cb'       => '<input type="checkbox" />',
			'username' => __( 'Username' ),
			'wgtype' => __( 'Type' , "wangguard" ),
			'email'    => __( 'E-mail' ),
			'wgreported_by'    => __( 'Reported by' , 'wangguard' ),
			'wgreported_on'    => __( 'Reported on' , 'wangguard' )
		);

		return $c;
	}

	function get_sortable_columns() {
		$c = array(
			'username' => 'login',
			'wgtype' => 'wgtype',
			'wgreported_by' => 'wgreported_by',
			'wgreported_on' => 'wgreported_on'
		);

		return $c;
	}

	function display_rows() {
		// Query the post counts for this page
		$style = '';
		foreach ( $this->items as $userid => $row_data ) {
			$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
			echo "\n\t", $this->single_row( $row_data, $style );
		}
	}

	/**
	 * Generate HTML for a single row on the users.php admin panel.
	 */
	function single_row( $row_data, $style = '') {
		global $wpdb;

	
		$url = admin_url('admin.php?page=wangguard_queue&order='.$_REQUEST['order'].'&orderby='.$_REQUEST['orderby']);

		$row_data->reported_by = sanitize_user_object( $row_data->reported_by, 'display' );
		$authors_div = "";
		
		if ( is_a( $row_data, 'WP_User' ) ) {
			//USER
			$row_data = sanitize_user_object( $row_data, 'display' );
			$email = $row_data->user_email;
			$checkbox = '';
			$editobj_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( stripslashes( $_SERVER['REQUEST_URI'] ) ), "user-edit.php?user_id=" . $row_data->ID ) );
			
			// Set up the hover actions for this user
			$actions = array();

			$report = "<strong><a target=\"_blank\" href=\"$editobj_link\">{$row_data->user_login}</a></strong><br />";

			$actions['unreport'] = "<a href='javascript:void(0)' rel='".$row_data->ID."' class='wangguard-queue-remove-user'>" . __( 'Remove from Queue', 'wangguard' ) . "</a>";

			$report .= $this->row_actions( $actions );

			// Set up the checkbox ( because the user is editable, otherwise its empty )
			$checkbox = "<input type='checkbox' name='users[]' id='user_{$row_data->ID}' value='{$row_data->ID}' />";

			$avatar = get_avatar( $row_data->ID, 32 );
			
			$userid = $row_data->ID;
			$statushtml = wangguard_user_custom_columns("" , "wangguardstatus" , $userid);
			
			$rowID = "user-".$userid;
		}
		else {
			//BLOG
			$checkbox = '';
			$editobj_link = network_admin_url("site-info.php?id=$row_data->ID");
			
			$email = $row_data->blog_email;

			$authors_links = array();
			
			$blog_prefix = $wpdb->get_blog_prefix( $row_data->ID );

			$authors     = $wpdb->get_results( "SELECT u.user_login, um.user_id, um.meta_value AS caps FROM $wpdb->users u, $wpdb->usermeta um WHERE u.ID = um.user_id AND meta_key = '{$blog_prefix}capabilities'" );
			foreach( (array)$authors as $author ) {
				
				$caps = maybe_unserialize( $author->caps );

				if ( isset( $caps['subscriber'] ) || isset( $caps['contributor'] ) ) continue;
				
				$editauthor_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( stripslashes( $_SERVER['REQUEST_URI'] ) ), "user-edit.php?user_id=" . $author->user_id ) );
				$authors_links[] = "<a target=\"_blank\" href=\"$editauthor_link\">{$author->user_login}</a>";
			}
			if (count($authors_links)) {
				$authors_div = "<div class=\"row-actions\">" . __("Authors: " , "wangguard") . implode(" | ", $authors_links) . "</div>";
			}
			
			$actions = array();
			$report = "<strong><a target=\"_blank\" href=\"$editobj_link\">$row_data->user_login</a></strong><br />";
			$actions['unreport'] = "<a href='javascript:void(0)' rel='".$row_data->ID."' class='wangguard-queue-remove-blog'>" . __( 'Remove from Queue', 'wangguard' ) . "</a>";
			$report .= $this->row_actions( $actions );

			// Set up the checkbox ( because the user is editable, otherwise its empty )
			$checkbox = "<input type='checkbox' name='blogs[]' id='blog_{$row_data->ID}' value='{$row_data->ID}' />";

			$avatar = '';
			$statushtml .= "<div class=\"row-actions\">";
			$statushtml .= '<a href="javascript:void(0)" rel="'.$row_data->ID.'" class="wangguard-splogger-blog">'.esc_html(__('Report authors as Sploggers', 'wangguard')).'</a>';
			$statushtml .= '<br/><a href="'.$row_data->site_url.'" target="_blank">'.esc_html(__('Open blog', 'wangguard')).'</a>';
			$statushtml .= "</div>";

			$rowID = "blog-".$row_data->ID;
		}


		$r = "<tr id='$rowID'$style>";

		list( $columns, $hidden ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class=\"$column_name column-$column_name\"";

			$style = '';

			$attributes = "$class$style";

			switch ( $column_name ) {
				case 'cb':
					$r .= "<th scope='row' class='check-column'>$checkbox</th>";
					break;
				case 'username':
					$r .= "<td $attributes>$avatar $report</td>";
					break;
				case 'wgtype':
					$r .= "<td $attributes>$row_data->wgtype $authors_div</td>";
					break;
				case 'name':
					$r .= "<td $attributes>$row_data->first_name $row_data->last_name</td>";
					break;
				case 'email':
					$r .= "<td $attributes><a href='mailto:$email' title='" . esc_attr( sprintf( __( 'E-mail: %s' ), $email ) ) . "'>$email</a></td>";
					break;
				case 'wgreported_by':
					$edit_reported_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( stripslashes( $_SERVER['REQUEST_URI'] ) ), "user-edit.php?user_id=" . $row_data->reported_by->ID ) );
					$r .= "<td $attributes><a href='$edit_reported_link'>{$row_data->reported_by->user_login}</a></td>";
					break;
				case 'wgreported_on':
					$r .= "<td $attributes>".date(get_option('date_format'), strtotime($row_data->reported_on)) . " " . date(get_option('time_format'), strtotime($row_data->reported_on))."</td>";
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















class WangGuard_Queue_Query {

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
	
	var $query_fields_b;
	var $query_from_b;
	var $query_where_b;
	
	var $query_orderby;
	var $query_limit;

	/**
	 * PHP4 constructor
	 */
	function WangGuard_Queue_Query( $query = null ) {
		$this->__construct( $query );
	}

	/**
	 * PHP5 constructor
	 * @return WangGuard_Queue_Query
	 */
	function __construct( $query = null ) {
		if ( !empty( $query ) ) {
			$this->query_vars = wp_parse_args( $query, array(
				'orderby' => 'login',
				'order' => 'ASC',
				'offset' => '', 'number' => '',
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

		$tableQueue = $wpdb->base_prefix . "wangguardreportqueue";
		
		$this->query_fields_u = "$wpdb->users.ID , $wpdb->users.user_login , 1 as wgtype , $tableQueue.reported_on as wgreported_on , $tableQueue.reported_by_ID as wgreported_by_ID";
		$this->query_from_u = "FROM $wpdb->users JOIN $tableQueue ON $wpdb->users.ID = $tableQueue.ID";
		
		if ($_REQUEST['type']=="b")
			$this->query_where_u = "WHERE 1=2";
		else
			$this->query_where_u = "WHERE 1=1";
		
		$this->query_fields_b = "$wpdb->blogs.blog_id , CONCAT($wpdb->blogs.domain , $wpdb->blogs.path) as path , 0 as wgtype , $tableQueue.reported_on as wgreported_on , $tableQueue.reported_by_ID as wgreported_by_ID";
		$this->query_from_b = "FROM $wpdb->blogs JOIN $tableQueue ON $wpdb->blogs.blog_id = $tableQueue.blog_id";
		if ($_REQUEST['type']=="u")
			$this->query_where_b = "WHERE 1=2";
		else
			$this->query_where_b = "WHERE 1=1";

		// sorting
		switch ($qv['orderby']) {
			case "login":
				$orderby = '1';
				break;
			case "wgreported_by":
				$orderby = '5';
				break;
			case "wgreported_on":
				$orderby = '4';
				break;
			default:
				$orderby = '2';
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

		$this->results = $wpdb->get_results("
				SELECT $this->query_fields_u $this->query_from_u $this->query_where_u " .
				(!empty ($wpdb->blogs) ?
				"
				UNION ALL
				SELECT $this->query_fields_b $this->query_from_b $this->query_where_b " : "") .
				" $this->query_orderby $this->query_limit");


		
		if ( $this->query_vars['count_total'] ) {
			$this->total_users = $wpdb->get_var("SELECT COUNT(*) $this->query_from_u $this->query_where_u");
			if (!empty ($wpdb->blogs))
				$this->total_blogs = $wpdb->get_var("SELECT COUNT(*) $this->query_from_b $this->query_where_b");
			else
				$this->total_blogs = 0;
			
			$this->total_users += $this->total_blogs;
		}

		if ( !$this->results )
			return;

	
		$r = array();
		foreach ( $this->results as $userrow ) {
			$userid = $userrow->ID;
			if ($userrow->wgtype == 1) {
				$r[ "u-".$userid ] = new WP_User( $userid );
				$r[ "u-".$userid ]->wgtypeID = 1;
				$r[ "u-".$userid ]->wgtype = __("User" , "wangguard");
				$r[ "u-".$userid ]->reported_on = $userrow->wgreported_on;
				$r[ "u-".$userid ]->reported_by = new WP_User( $userrow->wgreported_by_ID);
			}
			else {
				switch_to_blog($userrow->ID);
				$email = get_option("admin_email");
				$url = get_option("siteurl");
				restore_current_blog();

				$r[ "b-".$userid ] = new _wangguard_dummy();
				$r[ "b-".$userid ]->ID = $userrow->ID;
				$r[ "b-".$userid ]->user_login = $userrow->user_login;
				$r[ "b-".$userid ]->blog_email = $email;
				$r[ "b-".$userid ]->wgtypeID = 0;
				$r[ "b-".$userid ]->wgtype = __("Blog" , "wangguard");
				$r[ "b-".$userid ]->reported_on = $userrow->wgreported_on;
				$r[ "b-".$userid ]->reported_by = new WP_User( $userrow->wgreported_by_ID);
				$r[ "b-".$userid ]->site_url = $url;
			}
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

class _wangguard_dummy {
	var $dummy = 1;
}
?>