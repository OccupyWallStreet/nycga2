<?php


/**
 * @author Max Bond
 * 
 * Table paging support. WP style
 * 
 */
class q2w3_post_order_table_paging {
	
	protected static $cur_page;
	
	const VAR_NAME = 'cp'; // Current Page GET var
		

	
	/**
	 * @return number Current page number
	 */
	public static function cur_page() {
		
		if (!self::$cur_page) {
		
			if (isset($_GET[self::VAR_NAME]) && intval($_GET[self::VAR_NAME])) self::$cur_page = $_GET[self::VAR_NAME]; else self::$cur_page = 1;
		
		}
		
		return self::$cur_page;
		
	}
	
	/**
	 * @param integer $total_rows Table total rows
	 * @param integer $rows_per_page Table rows per page value
	 * @return string Buttons html
	 */
	public static function controls($total_rows, $rows_per_page = 20) {
		
		$total_pages = ceil($total_rows / $rows_per_page); // Count total pages

		if ($total_pages <= 1) return false; // No buttons if there is only one page
		
		$cur_page = self::cur_page();
		
		if ($cur_page > 1) $prev_page = $cur_page - 1; else $prev_page = false; // Previos page number

		if ($cur_page < $total_pages) $next_page = $cur_page + 1; else $next_page = false; // Next page number
						
		$res = '<div class="tablenav-pages">';
		
		$page_begin = $cur_page*$rows_per_page - $rows_per_page + 1; // Row number of fist row on a page
		
		$page_end = $cur_page*$rows_per_page; // Row number of last row on a page
		
		if ($page_end > $total_rows) $page_end = $total_rows; // Corrects $page_end value for last page
		
		$res .= '<span class="displaying-num">'. $page_begin .'&#8211;'. $page_end .' '. __('from', q2w3_post_order::ID) .' '. $total_rows .'</span>';
		
		if ($prev_page) { // left arrow button
			
			$res .= '<a class="prev page-numbers" href="?'. self::change_qstring(self::VAR_NAME, $prev_page) .'">&laquo;</a> ';
		
		}
		
		$printed = false;
		
		$printed2 = false;
				
		for ($page = 1; $page <= $total_pages; $page++) { // page cycle
					
			if ($page == 1) { // first page
				
				$res .= self::page_button($page, $cur_page, self::VAR_NAME);
				
			} elseif ($cur_page - $page >= 3 && !$printed) { // hide unnecessary pages with dots
				
				$res .= '<span class="page-numbers dots">...</span> ';
				
				$printed = true;
				
			}
			
			if (abs($cur_page - $page) <= 2 && $page != $total_pages && $page != 1) { // display two nearest pages
			
				$res .= self::page_button($page, $cur_page, self::VAR_NAME);
							
			}
			
			if ($page == $total_pages) { // last page
				
				$res .= self::page_button($page, $cur_page, self::VAR_NAME);
				
			} elseif ($cur_page - $page <= -3 && !$printed2) { // hide unnecessary pages with dots
					
				$res .= '<span class="page-numbers dots">...</span> ';
				
				$printed2 = true;
				
			}	
					
		}
		
		if ($next_page) { // right arrow button
			
			$res .= '<a class="next page-numbers" href="?'. self::change_qstring(self::VAR_NAME, $next_page) .'">&raquo;</a> ';

		}
		
		$res .= '</div>';
				
		return $res;
				
	}
	
	/**
	 * @param integer $page Current page number in a cycle
	 * @param integer $cur_page Current page selected by user
	 * @param string $var_name $_GET variable name
	 * @return string Button html
	 */
	protected static function page_button($page, $cur_page, $var_name) {
			
		if ($page == $cur_page) {
				
			$res = '<span class="page-numbers current">'. $page .'</span> ';
				
		} else {
				
			$res = '<a class="page-numbers" href="?'. self::change_qstring($var_name, $page) .'">'. $page .'</a> ';
						
		}
			
		return $res;
			
	}
	
	/**
	 * Changes query string
	 * 
	 * @param string $get_var $_GET variable name
	 * @param mixed $value Variable value to be set
	 * @param string $qstring Query string to be changed if not specified takes $_SERVER['QUERY_STRING']
	 * @param string $delete_after_var $_GET variable name. If specified all data afte this variable will be deleted 
	 * @return string
	 */
	protected static function change_qstring($get_var, $value=false, $qstring=false, $delete_after_var=false) {
	
		if (!$qstring) {
		
			$qstring = $_SERVER['QUERY_STRING'];
		
		} 
	
		if(!$qstring) {
			
			if ($value) {
			
				return $get_var.'='.$value;
				
			} else {
				
				return NULL;
				
			}
			
		}
			
		parse_str(str_replace('&amp;', '&', $qstring), $vars);

		if (!array_key_exists($get_var, $vars)) {
			
			if ($value) $val = "&amp;$get_var=$value"; else $val = false;
			
			return $qstring.$val;
			
		} else {
			
			$vars[$get_var] = $value;
			
		}
		
		foreach ($vars as $key=>$value) {
			
			if ($delete_after_var) {
				
				$value = false;
				
			}
			
			if (!$value) unset($vars[$key]);
			
			if ($delete_after_var && $key == $get_var) {
				
				$delete_after = true;
				
			}
			
		}
		
		return http_build_query($vars,'','&amp;');
		
	}
		
}

/**
 * @author Max Bond
 * 
 * Table search by post title filter
 *
 */
class q2w3_post_order_table_search {
	
	const VAR_NAME = 's';

	
	public static function controls() {
		
		$res = '<form method="get" action="'. $_SERVER['PHP_SELF'] . '" >'.PHP_EOL;
		
		$res .= '<div class="search-box" style="float: left">'.PHP_EOL;

		$res .= '<input type="hidden" name="page" value="'.$_GET['page'].'" />';
		
		if (isset($_GET['p_type'])) $res .= '<input type="hidden" name="p_type" value="'.$_GET['p_type'].'" />';

		if (isset($_GET['tax_name'])) $res .= '<input type="hidden" name="tax_name" value="'.$_GET['tax_name'].'" />';
		
		if (isset($_GET['term_id'])) $res .= '<input type="hidden" name="term_id" value="'.$_GET['term_id'].'" />';
		
		if (isset($_GET[self::VAR_NAME])) $search_string = $_GET[self::VAR_NAME]; else $search_string = '';
		
		$res .= '<input type="text" id="post-search-input" name="'. self::VAR_NAME .'" value="'. $search_string .'" />'.PHP_EOL;
			
		$res .= '<input type="submit" name="" id="search-submit" class="button" value="'. __('Search') .'">'.PHP_EOL;
		
		$res .= '</div>'.PHP_EOL;
		
		$res .= '</form>'.PHP_EOL;
			
		return $res;
		
	}
	
}

?>
