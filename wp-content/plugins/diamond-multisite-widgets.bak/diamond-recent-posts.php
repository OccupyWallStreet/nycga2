<?php
class DiamondRP {

	function DiamondRP() {
		add_action('widgets_init', array($this, 'init_diamondRP'));
	}
		 
		function init_diamondRP() {	
		
		add_shortcode('diamond-post', array($this, 'diamond_post_handler'));
		
		if ( !function_exists('register_sidebar_widget') ||
		!function_exists('register_widget_control') )
		return;
		 
		register_sidebar_widget(array(__('Diamond Recent Posts', 'diamond'),'widgets'),array($this, 'widget_endView'));
		register_widget_control(array(__('Diamond Recent Posts', 'diamond'), 'widgets'), array($this, 'widget_controlView'));		
		 
	}

	function diamond_post_handler(  $atts, $content = null  ) {
				
		 extract( shortcode_atts( array(
		'exclude' => '',
		'count' => '',
		'format'	 => '',
		'avatar_size' => '',
		'default_avatar' => '',
		'date_format' => '',
		'before_item' =>'',
		'after_item' => '',
		'before_content' => '',
		'more_text' => '',
		'after_content' => '',
		'whitelist' => '',
		'post_limit' => 0
		), $atts ) );
			
		return $this->render_output(split(',',$exclude), $count, html_entity_decode($format), $avatar_size, $default_avatar, $date_format, html_entity_decode($before_item), html_entity_decode($after_item), html_entity_decode($before_content), html_entity_decode($after_content), $more_text, split(',', $whitelist), $post_limit);
	}
	

	function widget_endView($args)
	{		
		$wgt_title=get_option('wgt_title');
		$wgt_count=get_option('wgt_count');		
		$wgt_miss= split(';', get_option('wgt_miss'));		
		$wgt_format = get_option('wgt_format');		
		$wgt_avsize = get_option('wgt_avsize');		
		$wgt_mtext = get_option('wgt_mtext');		
		$wgt_defav = get_option('wgt_defav');		
		$wgt_dt = get_option('wgt_dt');				
		$wgt_white = split(';', get_option('wgt_white'));
		$wgt_post_limit = get_option('wgt_post_limit');				
		
	
	//print_r($args);
		
		extract($args);
		
		$output = '';
		
		$output .= $before_widget.$before_title.$wgt_title. $after_title;
		
	
		$output .= $this->render_output($wgt_miss, $wgt_count, $wgt_format, $wgt_avsize, $wgt_defav, $wgt_dt, '<li>', '</li>', '<ul>', '</ul>', $wgt_mtext, $wgt_white, $wgt_post_limit) ;
		
		$output .=  $after_widget;
		
		echo $output;
	}
	
	
	function render_output($wgt_miss, $wgt_count, $wgt_format, $wgt_avsize, $wgt_defav, $wgt_dt, $before_item, $after_item, $before_cont, $after_cont, $wgt_mtext, $wgt_white, $post_limit = 0)	 {		
	
		global $DiamondCache;
		
		$cachekey = 'diamond_post_'.diamond_arr_to_str($wgt_miss).'-'.$wgt_count.'-'.$wgt_format . diamond_arr_to_str($wgt_white) .
		'-'.$wgt_avsize.'-'.$wgt_defav.'-'.$wgt_dt.'-'.$before_item.'-'.$after_item.'-'.$before_cont.'-'.
		$after_cont.'-'.$wgt_mtext.'-'.$post_limit;
		$output = $DiamondCache->get($cachekey, 'recent-posts');
					
		if ($output != false)
			return $output;					
	
		global $switched;		
		global $wpdb;
		$table_prefix = $wpdb->base_prefix;
		
		if (!isset($wgt_dt) || trim($wgt_dt) =='') 
			$wgt_dt = 'M. d. Y.';
		
		if (!isset($wgt_avsize) || $wgt_avsize == '')
			$wgt_avsize = 96;
			
		if (!isset($before_item) || $before_item == '')
			$before_item = '<li>';	
			
		if (!isset($after_item) || $after_item == '')
			$after_item = '</li>';			
			
		if (!isset($before_cont) || $before_cont == '')
			$before_cont = '<ul>';	
			
		if (!isset($after_cont) || $after_cont == '')
			$after_cont = '</ul>';			
			
		if (!isset($wgt_miss) || $wgt_miss == '')
			$wgt_miss = array ();

		$white = 0;
		if (isset($wgt_white) && $wgt_white != '' && count($wgt_white) > 0 && $wgt_white[0] && $wgt_white[0]!='')
			$white = 1;		
		
		
		$limitstr = '';
		if ((int)$post_limit>0) $limitstr = ' LIMIT '.(int)$post_limit;
		
		$sqlstr = '';
		$blog_list = get_blog_list( 0, 'all' );
		if (($white == 0 && !in_array(1, $wgt_miss)) || ($white == 1 && in_array(1, $wgt_white))) {
			$sqlstr = "(SELECT 1 as blog_id, id, post_date_gmt from ".$table_prefix ."posts where post_status = 'publish' and post_type = 'post' and post_title <> '".__('Hello world!')."' ".$limitstr.")";
		}
		$uni = '';
		
		foreach ($blog_list AS $blog) {
			if (($white == 0 && !in_array($blog['blog_id'], $wgt_miss) && $blog['blog_id'] != 1) ||
			($white == 1 && $blog['blog_id'] != 1 && in_array($blog['blog_id'], $wgt_white))) {
				if ($sqlstr != '')
					$uni = ' union ';;	
				$sqlstr .= $uni . " (SELECT ".$blog['blog_id']." as blog_id, id, post_date_gmt from ".$table_prefix .$blog['blog_id']."_posts  where post_status = 'publish' and post_type = 'post' and post_title <> '".__('Hello world!')."' ".$limitstr.")";				
			}
		}
		
		$limit = '';
		if ((int)$wgt_count > 0)
			$limit = ' LIMIT 0, '. (int)$wgt_count;
		$sqlstr .= " ORDER BY post_date_gmt desc " . $limit;		
		
		
		//echo $sqlstr; 
		$post_list = $wpdb->get_results($sqlstr, ARRAY_A);
		//echo $wpdb->print_error(); 
		
		$output = '';
		$output .=  $before_cont;
		foreach ($post_list AS $post) {
			$output .=  $before_item;
			
			$wgt_format = get_format_txt($wgt_format);
			$txt = ($wgt_format == '') ? '<strong>{title}</strong> - {date}' : $wgt_format;
			
			
			$p = get_blog_post($post["blog_id"], $post["id"]);			
			
			$av = get_avatar(get_userdata($p->post_author)->user_email, $wgt_avsize, $defav);
			
			$ex = $p->post_excerpt;
			if (!isset($ex) || trim($ex) == '')
				$ex = mb_substr(strip_tags($p->post_content), 0, 65) . '...';
			
			$txt = str_replace('{title}', '<a href="' .get_blog_permalink($post["blog_id"], $post["id"]).'">'.$p->post_title.'</a>' , $txt);
			$txt = str_replace('{more}', '<a href="' .get_blog_permalink($post["blog_id"], $post["id"]).'" class="button nice radius">'.$wgt_mtext.'</a>' , $txt);
			$txt = str_replace('{title_txt}', $p->post_title , $txt);
			$txt = str_replace('{date}', date_i18n($wgt_dt, strtotime($p->post_date)), $txt);
			$txt = str_replace('{excerpt}', $ex , $txt);
			$txt = str_replace('{author}', get_userdata($p->post_author)->nickname, $txt);
			$txt = str_replace('{avatar}', $av , $txt);
			$txt = str_replace('{blog}', get_blog_option($post["blog_id"], 'blogname') , $txt);		
							$burl = get_blog_option($post["blog_id"], 'home');
			
			$txt = str_replace('{blog_link}', '<a href="'.$burl.'/">'.get_blog_option($post["blog_id"], 'blogname').'</a>' , $txt);		
			$txt = str_replace('{blog_url}', $burl , $txt);		
			
			$output .=  $txt;
			$output .=  $after_item;
		}
		$output .=  $after_cont;
		
		$output .=  $wpdb->print_error();		
		
		$DiamondCache->add($cachekey, 'recent-posts', $output);		
		
		return $output; 
		
	}
	
	 
	function widget_controlView($is_admin = false)
	{
		global $DiamondCache;	
	
		// Title
		if ($_POST['wgt_post_hidden']) {
			$option=$_POST['wgt_p_title'];
			update_option('wgt_title',$option);		
		}
		$wgt_title=get_option('wgt_title');
		
		echo '<input type="hidden" name="wgt_post_hidden" value="success" />';
		
		echo '<label for="wgt_p_title">' . __('Widget Title', 'diamond') . ':<br /><input id="wgt__ptitle" name="wgt_p_title" type="text" value="'.$wgt_title.'" /></label>';
		
		
		if ($_POST['wgt_post_hidden']) {
			$DiamondCache->addSettings('recent-posts', 'expire', $_POST['diamond_p_cache']);			
		}
		$dccache=$DiamondCache->getSettings('recent-posts', 'expire');		
		if ($dccache=='')
			$dccache = 120;	
		echo '<br />';
		echo '<label for="diamond_p_cache">' . __('Cache Expire Time (sec)', 'diamond') . ':<br /><input id="diamond_p_cache" name="diamond_p_cache" type="text" value="'.$dccache.'" /></label>';
		
		// Count
		if ($_POST['wgt_post_hidden'])	 {
			$option=$_POST['wgt_count'];
			update_option('wgt_count',$option);
		}
		$wgt_count=get_option('wgt_count');
		echo '<br /><label for="wgt_number">' .__('Posts count', 'diamond') . ':<br /><input id="wgt_count" name="wgt_count" type="text" value="'.$wgt_count.'" /></label>';		
		
		// miss blogs
		if ($_POST['wgt_post_hidden']) {		
			$option=$_POST['wgt_miss'];
			$tmp = '';
			$sep = '';
			if (isset($option) && $option != '')
			foreach ($option AS $op) {			
				$tmp .= $sep .$op;
				$sep = ';';
			}
			update_option('wgt_miss',$tmp);		
		}
		
		$wgt_miss=get_option('wgt_miss');
		$miss = split(';',$wgt_miss);
		echo '<br /><label for="wgt_miss">' . __('Exclude blogs: (The first 50 blogs)','diamond');
		$blog_list = get_blog_list( 0, 50 ); 
		echo '<br />';
		foreach ($blog_list AS $blog) {
			echo '<input id="wgt_miss_'.$blog['blog_id'].'" name="wgt_miss[]" type="checkbox" value="'.$blog['blog_id'].'" ';
			if (in_array($blog['blog_id'], $miss)) echo ' checked="checked" ';
			echo ' />';
			echo get_blog_option( $blog['blog_id'], 'blogname' );
			echo '<br />';
		}
		echo '</label>';		
		
		//Whitelist
		if ($_POST['wgt_post_hidden']) {		
			$option=$_POST['wgt_white'];
			$tmp = '';
			$sep = '';
			if (isset($option) && $option != '')
			foreach ($option AS $op) {			
				$tmp .= $sep .$op;
				$sep = ';';
			}
			update_option('wgt_white',$tmp);		
		}
		
		$wgt_white=get_option('wgt_white');
		$miss = split(';',$wgt_white);
		echo '<br /><label for="wgt_white">' . __('White List: (The first 50 blogs)','diamond');
		$blog_list = get_blog_list( 0, 50 ); 
		echo '<br />';
		foreach ($blog_list AS $blog) {
			echo '<input id="wgt_white_'.$blog['blog_id'].'" name="wgt_white[]" type="checkbox" value="'.$blog['blog_id'].'" ';
			if (in_array($blog['blog_id'], $miss)) echo ' checked="checked" ';
			echo ' />';
			echo get_blog_option( $blog['blog_id'], 'blogname' );
			echo '<br />';
		}
		echo '</label>';	
		
		
		// Format
		if ($_POST['wgt_post_hidden']) {
			$option=$_POST['wgt_format'];
			if (!isset($option) || $option == '')
				$option = '<strong>{title}</strong> - {date}';
			update_option('wgt_format', get_format_code($option));
		}
		$wgt_format=htmlentities(str_replace('\"', '"', get_format_txt(get_option('wgt_format'))));
		echo '<label for="wgt_number">' . __('Format string', 'diamond') .':<br /><input id="wgt_format" name="wgt_format" type="text" value="'.$wgt_format.'" /></label><br />';		
		echo '{title} - '. __('The post\'s title', 'diamond').'<br />';
		echo '{title_txt} - '. __('The post\'s title', 'diamond').' '.__('(without link)', 'diamond').'<br />';
		echo '{excerpt} - '. __('The post\'s excerpt', 'diamond').'<br />';		
		echo '{date} - ' . __('The post\'s date', 'diamond') .'<br />';
		echo '{author} - ' . __('The post\'s author', 'diamond') .'<br />';
		echo '{avatar} - ' . __('Author\'s avatar', 'diamond') .'<br />';
		echo '{blog} - '. __('The post\'s blog name', 'diamond') .'<br />';
		echo '{blog_link} - '. __('The post\'s blog link', 'diamond') .'<br />';
		echo '{blog_url} - '. __('The post\'s blog url', 'diamond') .'<br />';
		echo '{more} - '. __('The "Read More" link', 'diamond') .'<br />';
		echo '<br />';
		
		if ($_POST['wgt_post_hidden'])	 {
			$option=$_POST['wgt_avsize'];			
			update_option('wgt_avsize',$option);		
		}
		$wgt_avsize=get_option('wgt_avsize');	
		
		echo '<label for="wgt_avsize">' . __('Avatar Size (px)', 'diamond') .
		':<br /><input id="wgt_avsize" name="wgt_avsize" type="text" value="'.
		$wgt_avsize.'" /></label>';
		echo '<br />';
		
		if ($_POST['wgt_post_hidden'])	 {
			$option=$_POST['wgt_defav'];			
			update_option('wgt_defav',$option);		
		}
		$wgt_defav=get_option('wgt_defav');	
		
		echo '<label for="wgt_defav">' . __('Default Avatar URL', 'diamond') .
		':<br /><input id="wgt_defav" name="wgt_defav" type="text" value="'.
		$wgt_defav.'" /></label>';
		echo '<br />';		
		
		if ($_POST['wgt_post_hidden'])	 {
			$option=$_POST['wgt_mtext'];
			if (!isset($option) || $option == '')
				$option = __('Read More', 'diamond');
			update_option('wgt_mtext',$option);		
		}
		$wgt_mtext=get_option('wgt_mtext');	
		
		echo '<label for="wgt_mtext">' . __('"Read More" link text', 'diamond') . 
		':<br /><input id="wgt_mtext" name="wgt_mtext" type="text" value="'.
		$wgt_mtext.'" /></label>';
		echo '<br />';	
		
		if ($_POST['wgt_post_hidden'])	 {
			$option=$_POST['wgt_dt'];			
			update_option('wgt_dt',$option);		
		}
		$wgt_dt=get_option('wgt_dt');	
		if (!isset($wgt_dt) || trim($wgt_dt) =='') {
			$wgt_dt = 'M. d. Y.';
			update_option('wgt_dt',$wgt_dt);				
		}
		
		echo '<label for="wgt_dt">' . __('DateTime format (<a href="http://php.net/manual/en/function.date.php" target="_blank">manual</a>)', 'diamond') . 
		':<br /><input id="wgt_dt" name="wgt_dt" type="text" value="'.
		$wgt_dt.'" /></label>';
		echo '<br />';	
		
		
		if (!$is_admin) {
			echo '<br />';
			_e('if you like this widget then', 'diamond');
			echo ': <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal%40amegrant%2ehu&lc=HU&item_name=Diamond%20Multisite%20WordPress%20Widget&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted" target="_blank">';
			_e('Buy me a beer!', 'diamond');
			echo '</a><br />';
		}
		
		
		if ($_POST['wgt_post_hidden'])	 {
			$option=$_POST['wgt_post_limit'];			
			update_option('wgt_post_limit',$option);		
		}
		$wgt_post_limit=get_option('wgt_post_limit');	
		if (!isset($wgt_post_limit) || trim($wgt_post_limit) =='') {
			$wgt_post_limit = 0;
			update_option('wgt_post_limit',$wgt_post_limit);				
		}
		
		echo '<label for="wgt_post_limit">' . __('Maximum posts per blog (0 for unlimited)', 'diamond') . 
		':<br /><input id="wgt_post_limit" name="wgt_post_limit" type="text" value="'.
		$wgt_post_limit.'" /></label>';
		echo '<br />';	
		
		
		
	}
	
	}
	$newWidget = new DiamondRP ();
 ?>