<?php
require_once('dcwp_plugin_admin.php');
require_once ('class.pagination.php');

class dc_jqslicksocial_stats {
	
	/*Default values*/
	var $total_twitter = 0;
		
	/** constructor */
    function dc_jqslicksocial_stats() {
		
		settings_fields('dcssb_options_group'); $options = get_option('dcssb_options');
		
		global $wpdb;
		$dcssb = '';
		
		$show = get_option('dcssb_stats_show') ? get_option('dcssb_stats_show'): 10;
		$type = get_option('dcssb_stats_type') ? get_option('dcssb_stats_type'): 'home';
		$cat = get_option('dcssb_stats_category') ? get_option('dcssb_stats_category'): 0;
		$display = get_option('dcssb_stats_display') ? get_option('dcssb_stats_display'): 'count';
		$order = get_option('dcssb_stats_order') ? get_option('dcssb_stats_order'): 'date-desc';

		switch($type)
		{
        case 'home':
            $items = 1;
            break;
		case 'post':
			if($cat != 0){
				$items = get_category($cat)->count;
			} else {
				$items = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post'");
			}
            break;
		case 'page':
            $items = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'page'");
			$items = $items + 1;
            break;
		case 'category':
            $items = wp_count_terms('category');
            break;
		default:
            $items = 1;
            break;
		}
		
		if($items > 0) {
			
			$p = new dcssb_pagination;
			$p->items($items);
			$p->limit($show); // Limit entries per page
			$p->target("admin.php?page=slick-social-share-buttons-stats");
			$p->currentPage($_GET[$p->paging]); // Gets and validates the current page
			$p->calculate(); // Calculates what to show
			$p->parameterName('paging');
			$p->adjacents(3); //No. of page away from the current page
	 
			if(!isset($_GET['paging'])) {
				$p->page = 1;
			} else {
				$p->page = $_GET['paging'];
			}
	 
			//Query for limit paging
			$limit = "LIMIT " . ($p->page - 1) * $p->limit  . ", " . $p->limit;
			
			$stStart = ($p->page - 1) * $p->limit;
			$stEnd = $stStart + $p->limit;
			$stEnd = $stEnd > $items ? $items : $stEnd;
			$status = $stStart.' to '.$stEnd.' of '.$items;
		}
		
		$dcssb = '<div class="dcssb-stats-nav top">';
		$dcssb .= $this->slick_stats_type_nav();
		$dcssb .= $type == 'post' ? $this->slick_stats_category_nav() : '';
		$dcssb .= $type == 'post' ||  $type == 'page' ? $this->slick_stats_order_nav() : '';
		$dcssb .= $this->slick_stats_display_nav();
		$dcssb .= $this->slick_stats_qty_nav();
		
		$dcssb .= '<div class="dcssb-stats-status">'.$status.'</div>';
		$dcssb .= '<div class="clear"></div></div>';
		
		$order = $this->get_dcssb_default('dcssb_order');
		$functions = explode(',', $order);
		
		$title = 'Posts';
		if($type == 'home'){
			$title = 'Home Page';
		} else if($type == 'page'){
			$title = 'Pages';
		} else if($type == 'category'){
			$title = 'Category Pages';
		}
		
		$dcssb .= '<table width="100%" cellpadding="0" cellspacing="0" border="0" id="dcssb-stats">
              <thead>
                <tr>
				<th class="first">'.$title.'</th>';
		
		foreach($functions as $function) {
		
			if($function != ''){
				$inc = 'inc'.ucwords($function);
				$inc = $this->get_dcssb_default($inc);
				if($inc == 'true'){
					$dcssb .= '<th><img src="'.dc_jqslicksocial::get_plugin_directory().'/css/images/'.$function.'.png" alt="'.$function.'" /></th>';
				}
			}
		}
		$dcssb .= '<th class="total">Total</th>';
		$dcssb .= '</tr>
		</thead>
		<tbody>';
		
		if($items > 0) {
		
			switch($type)
			{
			case 'home':
				$dcssb .= $this->get_dcssb_default('show_home') == true ? $this->slick_stats_home() : '';
				break;
			case 'post':
				$dcssb .= $this->get_dcssb_default('show_post') == true ? $this->slick_stats_posts($show,$stStart,$cat) : '';
				break;
			case 'page':
				 $dcssb .= $this->get_dcssb_default('show_home') == true ? $this->slick_stats_home() : '';
				$dcssb .= $this->get_dcssb_default('show_page') == true ? $this->slick_stats_pages($show,$stStart) : '';
				break;
			case 'category':
				$dcssb .= $this->get_dcssb_default('show_category') == true ? $this->slick_stats_categories() : '';
				break;
			}
			
		} else {
		
			$i = 2;
			foreach($functions as $function) {
				$inc = 'inc'.ucwords($function);
				$inc = $this->get_dcssb_default($inc);
				if($inc == 'true'){
					$i++;
				}
			}
			$dcssb .= '<tr class="stats-row"><td colspan="'.$i.'" class="stats-empty">There are no results for this option</td></tr>';
		}
		
		$dcssb .= '</tbody>';
		$dcssb .= '<tfoot><tr class="total">';
		$dcssb .= '<td>Total:</td>';
		
		$sum = 0;
		
		foreach($functions as $function) {
		
			if($function != ''){
				$inc = 'inc'.ucwords($function);
				$inc = $this->get_dcssb_default($inc);
				if($inc == 'true'){
					$total = 'total_'.$function;
					$class = $this->$total == 0 ? ' zero': '';
					$dcssb .= '<td class="stats-btn sum'.$class.'">'.$this->$total.'</td>';
					$sum = $this->$total + $sum;
				}
			}
		}
		$dcssb .= '<td class="stats-btn sum total">'.$sum.'</td></tr></tfoot></table>';
		
		if($items > 0) {

			$dcssb .= '<div class="tablenav float-right dcssb-pagination">
		<div class="tablenav-pages">';
			$dcssb .= $p->getOutput();
			$dcssb .= '</div></div>';
			
		}
		
		echo $dcssb;
		
		echo $display == 'button' ? $this->slick_button_js(): '';

	}

	/** Creates the buttons */
	function slick_button_js(){
	
		$order = $this->get_dcssb_default('dcssb_order');
		$functions = explode(',', $order);
		
		foreach($functions as $function) {
		
			if($function != ''){
				$f_name = 'dcssb_stats_'.$function.'_js';
				$dcssb .= $this->$f_name();
			}
		}

		return $dcssb;
	}
	
	/* Creates qty drop down */
	function slick_stats_type_nav(){

		$type = get_option('dcssb_stats_type') ? get_option('dcssb_stats_type') : 'home';
		
		$dcssb = '<form method="post" id="form-dcssb-stats-type" action="'.get_option('siteurl').'/wp-admin/admin-ajax.php">';
		$dcssb .= '<label>Show: </label><select id="dcssb-select-show" class="dcssb-select-stats" name="option_value">';
		
		$dcssb .= '<option value="">Select</option>';
		if($this->get_dcssb_default('show_home') == true){
			$val = 'home';
			$select = $type == $val ? ' selected="selected"': '';
			$dcssb .= '<option value="'.$val.'"'.$select.'>Home Page</option>';
		}
		if($this->get_dcssb_default('show_post') == true){
			$val = 'post';
			$select = $type == $val ? ' selected="selected"': '';
			$dcssb .= '<option value="'.$val.'"'.$select.'>Posts</option>';
		}
		if($this->get_dcssb_default('show_page') == true){
			$val = 'page';
			$select = $type == $val ? ' selected="selected"': '';
			$dcssb .= '<option value="'.$val.'"'.$select.'>Pages</option>';
		}
		if($this->get_dcssb_default('show_category') == true){
			$val = 'category';
			$select = $type == $val ? ' selected="selected"': '';
			$dcssb .= '<option value="'.$val.'"'.$select.'>Categories</option>';
		}
		$dcssb .= '</select>';
		$dcssb .= '<input name="option_name" type="hidden" value="dcssb_stats_type" />';
		$dcssb .= '<input name="action" type="hidden" value="dcssb_update" />';
		$dcssb .= '<img src="'.dc_jqslicksocial::get_plugin_directory().'/css/images/loading.gif" alt="loading" class="dcssb-loading" />';
		$dcssb .= '<div class="dcwp-response"></div></form>';
		
		return $dcssb;
	}
	
	/* Creates qty drop down */
	function slick_stats_qty_nav(){

		$show = get_option('dcssb_stats_show') ? get_option('dcssb_stats_show') : 10;
		
		$dcssb = '<form method="post" id="form-dcssb-stats-show" action="'.get_option('siteurl').'/wp-admin/admin-ajax.php">';
		$dcssb .= '<label>per page: </label>
			  <select id="dcssb-select-show" class="dcssb-select-stats" name="option_value">';
		
		$options = '10,20,30,50,100';
		$options = explode(',', $options);
		
		foreach($options as $option) {
		
			if($option != ''){
				$select = $show == $option ? ' selected="selected"': '';
				$dcssb .= '<option value="'.$option.'"'.$select.'>'.$option.'</option>';
			}
		}
		
		$dcssb .= '</select>';
		$dcssb .= '<input name="option_name" type="hidden" value="dcssb_stats_show" />';
		$dcssb .= '<input name="action" type="hidden" value="dcssb_update" />';
		$dcssb .= '<img src="'.dc_jqslicksocial::get_plugin_directory().'/css/images/loading.gif" alt="loading" class="dcssb-loading" />';
		$dcssb .= '<div class="dcwp-response"></div></form>';
		
		return $dcssb;
	}
	
	/* Creates category drop down */
	function slick_stats_category_nav(){

		global $post;
		$args = array('orderby' => 'name', 'order' => 'ASC');
		$categories = get_categories($args);
		$exclude_category = $this->get_dcssb_default('exclude_category');
		$cat = get_option('dcssb_stats_category') ? get_option('dcssb_stats_category') : '';

		$dcssb = '<form method="post" id="form-dcssb-stats-category" action="'.get_option('siteurl').'/wp-admin/admin-ajax.php">';
		$dcssb .= '<label>Filter: </label><select id="dcssb-select-category" class="dcssb-select-stats" name="option_value">';
		$dcssb .= '<option value="0"'.$select.'>Show All</option>';
		
		foreach($categories as $category) {
		
			if (!strlen(strstr($exclude_category,','.$category->term_id.','))>0) {
			
				$id = $category->term_id;
				$select = $cat == $id ? ' selected="selected"': '';
				$dcssb .= '<option value="'.$id.'"'.$select.'>'.$category->name.'</option>';
			}
		}
		
		$dcssb .= '</select><input name="option_name" type="hidden" value="dcssb_stats_category" />';
		$dcssb .= '<input name="action" type="hidden" value="dcssb_update" />';
		$dcssb .= '<img src="'.dc_jqslicksocial::get_plugin_directory().'/css/images/loading.gif" alt="loading" class="dcssb-loading" />';
		$dcssb .= '<div class="dcwp-response"></div></form>';
		
		return $dcssb;
	}

	/* Creates display drop down */
	function slick_stats_display_nav(){

		$display = get_option('dcssb_stats_display') ? get_option('dcssb_stats_display') : 'count';
		
		$dcssb = '<form method="post" id="form-dcssb-stats-display" action="'.get_option('siteurl').'/wp-admin/admin-ajax.php">';
		$dcssb .= '<label>Display: </label><select id="dcssb-select-display" class="dcssb-select-stats" name="option_value">';
		
		$val = 'count';
		$select = $display == 'count' ? ' selected="selected"': '';
		$dcssb .= '<option value="'.$val.'"'.$select.'>Show Count</option>';
		
		$val = 'heatmap';
		$select = $display == 'heatmap' ? ' selected="selected"': '';
		$dcssb .= '<option value="'.$val.'"'.$select.'>Count + Heatmap</option>';
		
		$val = 'buttons';
		$select = $display == 'buttons' ? ' selected="selected"': '';
		$dcssb .= '<option value="'.$val.'"'.$select.'>Show Buttons</option>';
		
		$dcssb .= '</select>';
		$dcssb .= '<input name="option_name" type="hidden" value="dcssb_stats_display" />';
		$dcssb .= '<input name="action" type="hidden" value="dcssb_update" />';
		$dcssb .= '<img src="'.dc_jqslicksocial::get_plugin_directory().'/css/images/loading.gif" alt="loading" class="dcssb-loading" />';
		$dcssb .= '<div class="dcwp-response"></div></form>';
		
		return $dcssb;
	}
	
	/* Creates display drop down */
	function slick_stats_order_nav(){

		$order = get_option('dcssb_stats_order') ? get_option('dcssb_stats_order') : 'date-desc';
		
		$dcssb = '<form method="post" id="form-dcssb-stats-order" action="'.get_option('siteurl').'/wp-admin/admin-ajax.php">';
		$dcssb .= '<label>Order By: </label><select id="dcssb-select-order" class="dcssb-select-stats" name="option_value">';
		
		$val = 'date-desc';
		$select = $order == 'date-desc' ? ' selected="selected"': '';
		$dcssb .= '<option value="'.$val.'"'.$select.'>Date DESC</option>';
		
		$val = 'date-asc';
		$select = $order == 'date-asc' ? ' selected="selected"': '';
		$dcssb .= '<option value="'.$val.'"'.$select.'>Date ASC</option>';
		
		$val = 'title-desc';
		$select = $order == 'title-desc' ? ' selected="selected"': '';
		$dcssb .= '<option value="'.$val.'"'.$select.'>Title DESC</option>';
		
		$val = 'title-asc';
		$select = $order == 'title-asc' ? ' selected="selected"': '';
		$dcssb .= '<option value="'.$val.'"'.$select.'>Title ASC</option>';
		
		$dcssb .= '</select>';
		$dcssb .= '<input name="option_name" type="hidden" value="dcssb_stats_order" />';
		$dcssb .= '<input name="action" type="hidden" value="dcssb_update" />';
		$dcssb .= '<img src="'.dc_jqslicksocial::get_plugin_directory().'/css/images/loading.gif" alt="loading" class="dcssb-loading" />';
		$dcssb .= '<div class="dcwp-response"></div></form>';
		
		return $dcssb;
	}
	
	/** Creates the buttons */
	function slick_stats_row($link,$title){
	
		$order = $this->get_dcssb_default('dcssb_order');
		$functions = explode(',', $order);
		$counts = $this->slick_stats_count($link);
		$display = get_option('dcssb_stats_display') ? get_option('dcssb_stats_display'): 'count';
		$sum = 0;
		
		$tr = $display == 'heatmap' ? ' heatmap' : '';
		$dcssb = '<tr class="stats-row'.$tr.'">';
		$dcssb .= '<td class="stats-title"><a href="'.$link.'">'.$title.'</a></td>';
		
		foreach($functions as $function) {
		
			if($function != ''){
				$inc = 'inc'.ucwords($function);
				$inc = $this->get_dcssb_default($inc);
				if($inc == 'true'){
					$f_name = 'dcssb_stats_'.$function;
					$total = 'total_'.$function;
					$this->$total = $this->$total + $counts[$function];
					$class = $counts[$function] == 0 ? ' zero': '';
					$dcssb .= '<td class="stats-btn single'.$class.'" rel="'.$counts[$function].'">';
					$dcssb .= $display == 'buttons' ? $this->$f_name($link) : '<span class="dcssb-data">'.$counts[$function].'</span>';
					$dcssb .= '</td>';
					$sum = $sum + $counts[$function];
				}
			}
		}
		$class = $sum == 0 ? ' zero': '';
		$dcssb .= '<td class="stats-btn total'.$class.'" rel="'.$sum.'">'.$sum.'</td>';
		$dcssb .= '</tr>';

		return $dcssb;
	}
	
	/** Get button totals */
	function slick_stats_count($link){
	
		$url = $link;
		@$json = file_get_contents("http://api.sharedcount.com/?url=" . rawurlencode($link));
		$counts = json_decode($json, true);
		$count = Array();
		
		$count['twitter'] = $counts["Twitter"];
		$count['facebook'] = $counts["Facebook"]["like_count"];
		$count['plusone'] = $counts["GooglePlusOne"];
		$count['linkedin'] = $counts["LinkedIn"];
		$count['stumble'] = $counts["StumbleUpon"];
		$count['digg'] = $counts["Diggs"];
		$count['delicious'] = $counts["Delicious"];
		$count['reddit'] = $counts["Reddit"];
		$count['buffer'] = '';

		$url_json = esc_url_raw('http://api.pinterest.com/v1/urls/count.json?callback=&url='.$link, array('http', 'https'));
		$response = wp_remote_get($url_json);
		
		$code = wp_remote_retrieve_response_code($response);
		$pinit = 0;
		if ($code == 200){
				$data = $response['body'];
				$data = str_replace(')', '', str_replace('(', '', $data));
				$data = json_decode($data);
				$pinit = $data->{'count'} != '' ? $data->{'count'} : 0 ;
		}
		$count['pinit'] = $pinit;

		return $count;
	}
	
	/** Get pinit button count */
	function dcssb_pinit_count($link){
	
		$url_json = esc_url_raw('http://api.pinterest.com/v1/urls/count.json?callback=&url='.$link, array('http', 'https'));
		$response = wp_remote_get($url_json);
		
		$code = wp_remote_retrieve_response_code($response);
		$pinit = 0;
		if ($code == 200){
		
				$data = $response['body'];
				$data = str_replace(')', '', str_replace('(', '', $data));
				$data = json_decode($data);
				$pinit = $data->{'count'} != '' ? $data->{'count'} : 0 ;
			
		}
		
		return $pinit;
	}
	
	/* Creates buttons for home page */
	function slick_stats_home(){

		global $post;
		$link = get_bloginfo('url');
		$title = get_bloginfo('name');
		$dcssb .= $this->slick_stats_row($link, $title);

		return $dcssb;
	}
	
	/* Creates buttons for posts */
	function slick_stats_posts($show, $offset, $cat){

		global $post;
		$display = get_option('dcssb_stats_order') ? get_option('dcssb_stats_order') : 'date-desc';
		$order = 'DESC';
		$orderby = 'post_date';
		
		switch($display)
		{
			case 'date-asc':
			$order = 'ASC';
			$orderby = 'post_date';
			break;
			case 'title-desc':
			$order = 'DESC';
			$orderby = 'title';
			break;
			case 'title-asc':
			$order = 'ASC';
			$orderby = 'title';
			break;
		}
		
		if($cat == 0){
			$args = array(
			'numberposts'     => $show,
			'offset'          => $offset,
			'orderby'         => $orderby,
			'order'           => $order,
			'post_type'       => 'post',
			'post_status'     => 'publish' );
		} else {
			$args = array(
			'numberposts'     => $show,
			'offset'          => $offset,
			'category' 		  => $cat,
			'orderby'         => $orderby,
			'order'           => $order,
			'post_type'       => 'post',
			'post_status'     => 'publish' );
		}
		$items = get_posts( $args );
		
		foreach( $items as $post ) :	setup_postdata($post);
		
			$id = $post->ID;
			$link = get_permalink($post->ID);
			$title = $post->post_title;
			$dcssb .= $this->slick_stats_row($link, $title);
		
		endforeach;
		
		return $dcssb;
	}
	
	/* Creates buttons for pages */
	function slick_stats_pages($show, $offset){

		global $post;
		$display = get_option('dcssb_stats_order') ? get_option('dcssb_stats_order') : 'date-desc';
		$order = 'DESC';
		$orderby = 'post_date';
		
		switch($display)
		{
			case 'date-asc':
			$order = 'ASC';
			$orderby = 'post_date';
			break;
			case 'title-desc':
			$order = 'DESC';
			$orderby = 'title';
			break;
			case 'title-asc':
			$order = 'ASC';
			$orderby = 'title';
			break;
		}
		
		$args = array(
    'numberposts'     => $show,
    'offset'          => $offset,
    'orderby'         => $orderby,
    'order'           => $order,
    'post_type'       => 'page',
    'post_status'     => 'publish' );
		$items = get_posts( $args );
		
		foreach( $items as $post ) :	setup_postdata($post);
		
			$id = $post->ID;
			$link = get_permalink($post->ID);
			$title = $post->post_title;
			$dcssb .= $this->slick_stats_row($link, $title);
		
		endforeach;
		
		return $dcssb;
	}
	
	/* Creates buttons for category pages */
	function slick_stats_categories(){

		global $post;
		$args = array('orderby' => 'name', 'order' => 'ASC');
		$categories = get_categories($args);
		$exclude_category = $this->get_dcssb_default('exclude_category');
		
		foreach($categories as $category) {
						
			if (!strlen(strstr($exclude_category,','.$category->term_id.','))>0) {
			
				$link = get_category_link($category->term_id);
				$dcssb .= $this->slick_stats_row($link, $category->name);
				
			}
		}

		return $dcssb;
	}

	/* Facebook */
	function dcssb_stats_facebook($link){
		
		$elink = urlencode($link);
		$size = 'button_count';
		$appId = $this->get_dcssb_default('app_facebook');
		
		$button = '<iframe src="http://www.facebook.com/plugins/like.php?app_id='.$appId.'&amp;href='.$elink.'&amp;send=false&amp;layout='.$size.'&amp;width=80&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=22" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height:22px;" allowTransparency="true"></iframe>';
		
		return $button;
	}
	
	/* Google +1 */
	function dcssb_stats_plusone($link){

		$size = 'medium';
		$button .= '<g:plusone size="'.$size.'" href="'.$link.'" count="true"></g:plusone>';

		return $button;
	}
	
	/* Twitter */
	function dcssb_stats_twitter($link){
	
		$title = '';
		$twitterId = $this->get_dcssb_default('user_twitter');
		$size = 'horizontal';

		$button .= '<a href="http://twitter.com/share" data-url="'.$link.'" data-counturl="'.$link.'" data-text="'.$title.'" class="twitter-share-button" data-count="'.$size.'" data-via="'.$twitterId.'"></a>';
		
		return $button;
	}

	/* LinkedIn */
	function dcssb_stats_linkedin($link){
	
		$size = 'right';
		$button .= '<script type="in/share" data-url="'.$link.'" data-counter="'.$size.'"></script>';
			
		return $button;
	}
	
	/* Stumbleupon */
	function dcssb_stats_stumble($link){
	
		$elink = urlencode($link);
		$size = '2';
		$button = '<iframe src="http://www.stumbleupon.com/badge/embed/'.$size.'/?url='.$elink.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height: 22px;" allowTransparency="true"></iframe>';

		return $button;
	}
	
	/* Digg */
	function dcssb_stats_digg($link){
	
		$elink = urlencode($link);
		$title = '';
		@$description = '';
		$size = 'DiggCompact';
		$button = '<a href="http://digg.com/submit?url='.$elink.'&amp;title='.$title.'" class="DiggThisButton '.$size.'"></a>';
		$button .= '<span style="display: none;">'.$description.'</span>';
		
		return $button;
	}
	
	/* Delicious */
	function dcssb_stats_delicious($link){
	
		$size = 'wide';
		$title = '';
		$button .= '<a class="delicious-button" href="http://delicious.com/save">
 <!-- {
 url:"'.$link.'"
 ,title:"'.$title.'"
 ,button:"'.$size.'"
 } -->
 Delicious
</a>';
			
		return $button;
	}
	
	/* Reddit */
	function dcssb_stats_reddit($link){
	
		$size = 'wide';
		$title = '';
		$button .= '<script type="text/javascript">
							  reddit_url = "'.$link.'";
							  reddit_title = "'.$title.'";
							  reddit_newwindow="1"
							  </script>
							  <script type="text/javascript" src="http://www.reddit.com/static/button/button1.js"></script>';
		return $button;
	}
	
	/* Pin It */
	function dcssb_stats_pinit($link){
	
		$elink = urlencode($link);
		$title = '';
		$pageId = '';
		@$description = '';
		$size = 'horizontal';
		
		if(function_exists('get_post_thumbnail_id')){
			$imageId = get_post_thumbnail_id($pageId);
			$image_url = wp_get_attachment_image_src($imageId,'large');
			$image_url = $image_url[0];
        } else {
            $image_url = '';
        }
	
		$image_default = ($image_url == '' ? dc_jqslicksocial_stats::get_dcssb_default('image_pinit') : $image_url);
		
		$button = '<a href="http://pinterest.com/pin/create/button/?url='.$elink.'&amp;media='.urlencode($image_default).'&amp;description='.$description.'" class="pin-it-button" count-layout="'.$size.'">Pin It</a>';

		return $button;
	}
	
	/* Buffer */
	function dcssb_stats_buffer($link){
	
		$title = '';
		$twitterId = $this->get_dcssb_default('user_twitter');
		$size = 'horizontal';
		$button .= '<a href="http://bufferapp.com/add" data-url="'.$link.'" data-text="'.$title.'" class="buffer-add-button" data-count="'.$size.'" data-via="'.$twitterId.'">Buffer</a>';
		
		return $button;
	}
	
	/* Facebook js */
	function dcssb_stats_facebook_js(){

		$button = '';
		return $button;
	}
	
	/* Google +1 js */
	function dcssb_stats_plusone_js(){
	
			$button = '<script type="text/javascript">
				(function() {
					var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
					po.src = "https://apis.google.com/js/plusone.js";
					var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
				})();
				</script>
				';

		return $button;
	}
	
	/* Twitter */
	function dcssb_stats_twitter_js(){
	
		$button = '<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
			';
		return $button;
	}
	
	/* LinkedIn js */
	function dcssb_stats_linkedin_js(){
	
		$button = '<script type="text/javascript" src="http://platform.linkedin.com/in.js"></script>
			';
		return $button;
	}
	
	
	/* Stumbleupon js */
	function dcssb_stats_stumble_js(){
	
		$button = '';
		return $button;
	}
	
	/* Digg js */
	function dcssb_stats_digg_js(){
	
		$button = '<script type="text/javascript">
(function() {
var s = document.createElement("SCRIPT"), s1 = document.getElementsByTagName("SCRIPT")[0];
s.type = "text/javascript";
s.async = true;
s.src = "http://widgets.digg.com/buttons.js";
s1.parentNode.insertBefore(s, s1);
})();
</script>
';
		return $button;
	}
	
	/* Delicious js */
	function dcssb_stats_delicious_js(){
	
		$button = '';
		return $button;
	}
	
	/* Reddit js */
	function dcssb_stats_reddit_js(){
	
		$button = '';
		return $button;
	}
	
	/* Pin It js */
	function dcssb_stats_pinit_js(){
	
		$button = '<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>';
		return $button;
	}
	
	/* Buffer js */
	function dcssb_stats_buffer_js(){
	
		$button = '<script type="text/javascript" src="http://static.bufferapp.com/js/button.js"></script>';
		return $button;
	}
	
	function http_build_query($query_data, $numeric_prefix='', $arg_separator='&'){
       $arr = array();
       foreach ( $query_data as $key => $val )
         $arr[] = urlencode($numeric_prefix.$key) . '=' . urlencode($val);
       return implode($arr, $arg_separator);
    }

	function get_dcssb_default($option){

		$options = get_option('dcssb_options');
		$default = $options[$option];
		return $default;
	}
} // class dc_jqslicksocial_stats