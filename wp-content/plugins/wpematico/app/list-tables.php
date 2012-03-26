<?PHP
//backwarts copatibility lower than wp 3.1
if (!class_exists('WPematico_List_Table')) {
	//if (is_file(trailingslashit(ABSPATH).'wp-admin/includes/class-wp-list-table.php'))
	//	include_once( trailingslashit(ABSPATH).'wp-admin/includes/class-wp-list-table.php' );

	//if (!class_exists('WPematico_List_Table')) // after WP 3.1
	//	if (is_file(trailingslashit(ABSPATH).'wp-admin/includes/list-table.php'))
	//		include_once( trailingslashit(ABSPATH).'wp-admin/includes/list-table.php' );
	
	//if (!class_exists('WPematico_List_Table')) // help!!
		include_once('compatibility/list-table.php');
}

class WPeMatico_Campaigns_Table extends WPematico_List_Table {
	function WPeMatico_Campaigns_Table() {
		global $current_screen;
		parent::WPematico_List_Table( array(
			'screen' => $current_screen,
			'plural' => 'jobs',
			'singular' => 'job'
		) );
	}
	
	function check_permissions() {
		if ( !current_user_can( 10 ) )
			wp_die( __( 'No rights' ) );
	}	
	
	function prepare_items() {
		global $mode;
		$this->items=get_option('wpematico_jobs');
		$mode = empty( $_REQUEST['mode'] ) ? 'list' : $_REQUEST['mode'];
	}
	
	function pagination( $which ) {
		global $mode;

		parent::pagination( $which );

		if ( 'top' == $which )
			$this->view_switcher( $mode );
	}
	
	function no_items() {
		_e( 'No Campaigns.','wpematico');
	}

	function get_bulk_actions() {
		$actions = array();
		$actions['delete'] = __( 'Delete' );
		$actions['reset'] = __( 'Reset' );

		return $actions;
	}
	
	function get_columns() {
		$posts_columns = array();
		$posts_columns['cb'] = '<input type="checkbox" />';
		$posts_columns['id'] = __('ID','wpematico');
		$posts_columns['jobname'] = __('Campaign Name','wpematico');
		$posts_columns['type'] = __('Post type','wpematico');
		$posts_columns['count'] = __('Posts','wpematico');
		$posts_columns['next'] = __('Next Run','wpematico');
		$posts_columns['last'] = __('Last Run','wpematico');
		return $posts_columns;
	}

	function get_sortable_columns() {
		return array();
	}	

	function get_hidden_columns() {
		return (array) get_user_option( 'wpematico_jobs_columnshidden' );
	}
	
	function display_rows() {
		$style = '';
		foreach ( $this->items as $jobid => $jobvalue ) {
			$jobvalue=wpematico_check_job_vars($jobvalue,$jobid);//Set and check job settings
			$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
			echo "\n\t", $this->single_row( $jobid, $jobvalue, $style );
		}
	}
	
	function single_row( $jobid, $jobvalue, $style = '' ) {
		global $mode;
		list( $columns, $hidden ) = $this->get_column_headers();
		$r = "<tr id='jodid-$jobid'$style>";
		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class=\"$column_name column-$column_name\"";

			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			$attributes = "$class$style";
			
			switch( $column_name ) {
				case 'cb':
					$r .=  '<th scope="row" class="check-column"><input type="checkbox" name="jobs[]" value="'. esc_attr($jobid) .'" /></th>';
					break;
				case 'id':
					$r .=  "<td $attributes>".$jobid."</td>"; 
					break;
				case 'jobname':
					$r .=  "<td $attributes><strong><a href=\"".wp_nonce_url('admin.php?page=WPeMatico&subpage=edit&jobid='.$jobid, 'edit-job')."\" title=\"".__('Edit:','wpematico').$jobvalue['name']."\">".esc_html($jobvalue['name'])."</a></strong>";
					$actions = array();
					if (empty($jobvalue['starttime'])) {
						$actions['edit'] = "<a href=\"" . wp_nonce_url('admin.php?page=WPeMatico&subpage=edit&jobid='.$jobid, 'edit-job') . "\">" . __('Edit') . "</a>";
						$actions['toggle'] = "<a href=\"" . wp_nonce_url('admin.php?page=WPeMatico&action=toggle&jobid='.$jobid, 'toggle-job_'.$jobid) . "\">" . (($jobvalue['activated'])? __('Deactivate','wpematico') :  __('Activate','wpematico')) . "</a>";
						$actions['copy'] = "<a href=\"" . wp_nonce_url('admin.php?page=WPeMatico&action=copy&jobid='.$jobid, 'copy-job_'.$jobid) . "\">" . __('Copy','wpematico') . "</a>";
						$actions['delete'] = "<a class=\"submitdelete\" href=\"" . wp_nonce_url('admin.php?page=WPeMatico&action=delete&jobs[]='.$jobid, 'bulk-jobs') . "\" onclick=\"if ( confirm('" . esc_js(__("You are about to delete this Campaign. \n  'Cancel' to stop, 'OK' to delete.","wpematico")) . "') ) { return true;}return false;\">" . __('Delete') . "</a>";
						$actions['reset'] = "<a class=\"submitdelete\" href=\"" . wp_nonce_url('admin.php?page=WPeMatico&action=reset&jobid='.$jobid, 'reset-job_'.$jobid) . "\" onclick=\"if ( confirm('" . esc_js(__("You are about to reset the posts count of this Campaign. \n  'Cancel' to stop, 'OK' to delete.","wpematico")) . "') ) { return true;}return false;\">" . __('Reset') . "</a>";
						$actions['runnow'] = "<a href=\"" . wp_nonce_url('admin.php?page=WPeMatico&subpage=runnow&jobid='.$jobid, 'runnow-job_'.$jobid) . "\">" . __('Run Now','wpematico') . "</a>";
					} else {
						$actions['clear'] = "<a class=\"submitdelete\" href=\"" . wp_nonce_url('admin.php?page=WPeMatico&action=clear&jobid='.$jobid, 'clear-job_'.$jobid) . "\">" . __('Clear','wpematico') . "</a>";
					}
					$action_count = count($actions);
					$i = 0;
					$r .=  '<br /><div class="row-actions">';
					foreach ( $actions as $action => $linkaction ) {
						++$i;
						( $i == $action_count ) ? $sep = '' : $sep = ' | ';
						$r .=  "<span class='$action'>$linkaction$sep</span>";
					}
					$r .=  '</div>';
					$r .=  '</td>';
					break;	
				case 'type':
					$r .=  "<td $attributes>";
					$r .=  $jobvalue['campaign_posttype'] ; // 
					$r .=  "</td>";
					break;
				case 'count':
					$r .=  "<td $attributes>";
					$r .=  $jobvalue['postscount'] ; // 
					$r .=  "</td>";
					break;
				case 'next':
					$r .= "<td $attributes>";
					if ($jobvalue['starttime']>0 and !empty($jobvalue['logfile'])) {
						$runtime=current_time('timestamp')-$jobvalue['starttime'];
						$r .=  __('Running since:','wpematico').' '.$runtime.' '.__('sec.','wpematico');
					} elseif ($jobvalue['activated']) {
						$r .=  date(get_option('date_format'),$jobvalue['cronnextrun']).'-'. date(get_option('time_format'),$jobvalue['cronnextrun']);
					} else {
						$r .= __('Inactive','wpematico');
					}
					if ( 'excerpt' == $mode ) {
						$r .= '<br />'.__('<a href="http://wikipedia.org/wiki/Cron" target="_blank">Cron</a>:','wpematico').' '.$jobvalue['cron'];
					}
					$r .=  "</td>";
					break;
				case 'last':
					$r .=  "<td $attributes>";
					if ($jobvalue['lastrun']) {
						$r .=  date_i18n(get_option('date_format'),$jobvalue['lastrun']).'-'. date_i18n(get_option('time_format'),$jobvalue['lastrun']); 
						if (isset($jobvalue['lastruntime']))
							$r .=  '<br />'.__('Runtime:','wpematico').' '.$jobvalue['lastruntime'].' '.__('sec.','wpematico');
					} else {
						$r .= __('None','wpematico');
					}
					$r .=  "</td>";
					break;
			}
		}
		$r .= '</tr>';
		return $r;
	}
}

?>