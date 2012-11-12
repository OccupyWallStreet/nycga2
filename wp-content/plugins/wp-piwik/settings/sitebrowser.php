<?php
$bolCURL = function_exists('curl_init');
$bolFOpen = ini_get('allow_url_fopen');
if (!$bolFOpen && !$bolCURL) {
?><tr>	
	<td colspan="2">
		<strong><?php _e('Error: cURL is not enabled and fopen is not allowed to open URLs. WP-Piwik won\'t be able to connect to Piwik.'); ?></strong>
	</td>
</tr><?php } else { ?>
</table>
<?php
if (!class_exists('WP_List_Table'))
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

if (isset($_GET['wpmu_show_stats']) && ($_GET['wpmu_show_stats'] == (int) $_GET['wpmu_show_stats']))
	$this->addPiwikSite();

// See wpengineer.com/2426/wp_list_table-a-step-by-step-guide/
class SiteBrowser extends WP_List_Table {

	var $aryData = array();
	
	function get_columns(){
  		$columns = array(
			'id'    	=> __('ID','wp-piwik'),
			'name' 		=> __('Title','wp-piwik'),
			'siteurl'   => __('URL','wp-piwik'),
			'piwikid'	=> __('Site ID (Piwik)','wp-piwik')
		);
		return $columns;
	}
	
	function prepare_items($bolNetwork = false) {
  		$current_page = $this->get_pagenum();
		$per_page = 10;
		global $blog_id;
		global $wpdb;
		global $pagenow;
		if (is_plugin_active_for_network('wp-piwik/wp-piwik.php')) {
			$total_items = $wpdb->get_var( $wpdb->prepare('SELECT COUNT(*) FROM '.$wpdb->blogs));
			$aryBlogs = $wpdb->get_results($wpdb->prepare('SELECT blog_id FROM '.$wpdb->blogs.' ORDER BY blog_id LIMIT '.(($current_page-1)*$per_page).','.$per_page));
			foreach ($aryBlogs as $aryBlog) {
				$objBlog = get_blog_details($aryBlog->blog_id, true);
				$this->aryData[] = array(
					'name' => $objBlog->blogname,
					'id' => $objBlog->blog_id,
					'siteurl' => $objBlog->siteurl,
					'piwikid' => WP_Piwik::getSiteID($objBlog->blog_id)
				);
			}
		} else {
			$objBlog = get_bloginfo();
			$this->aryData[] = array(
				'name' => get_bloginfo('name'),
				'id' => '-',
				'siteurl' => get_bloginfo('url'),
				'piwikid' => WP_Piwik::getSiteID()
			);
			$total_items = 1;
		}
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();
		$this->_column_headers = array($columns, $hidden, $sortable);
  		$this->set_pagination_args(array(
    		'total_items' => $total_items,
    		'per_page'    => $per_page
  		));
  		if ($bolNetwork) $pagenow = 'settings.php';
		foreach ($this->aryData as $intKey => $aryDataset) {
			if (empty($aryDataset['piwikid']) || !is_int($aryDataset['piwikid']))
				$this->aryData[$intKey]['piwikid'] = '<a href="'.admin_url(($pagenow == 'settings.php'?'network/':'')).$pagenow.'?page=wp-piwik/wp-piwik.php&tab=sitebrowser'.($aryDataset['id'] != '-'?'&wpmu_show_stats='.$aryDataset['id']:'').'">Create Piwik site</a>';
			if ($bolNetwork)
				$this->aryData[$intKey]['name']	= '<a href="?page=wp-piwik_stats&wpmu_show_stats='.$aryDataset['id'].'">'.$aryDataset['name'].'</a>';	
		}
  		$this->items = $this->aryData;
  		return count($this->items);
	}

	function column_default( $item, $column_name ) {
  		switch( $column_name ) {
    		case 'id':
    		case 'name':
    		case 'siteurl':
			case 'piwikid':
      			return $item[$column_name];
    		default:
      			return print_r($item,true);
		}
	}
}
$objSiteBrowser = new SiteBrowser();
$intCnt = $objSiteBrowser->prepare_items($this->bolNetwork);
if ($intCnt > 0) $objSiteBrowser->display();
else echo '<p>No site configured yet.</p>'
?>
<table>
<?php } ?>