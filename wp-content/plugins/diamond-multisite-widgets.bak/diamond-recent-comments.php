<?php
class DiamondRC {

	function DiamondRC() {
	add_action('widgets_init', array($this, 'init_diamondRC'));
	}
		 
		function init_diamondRC() {
		if ( !function_exists('register_sidebar_widget') ||
		!function_exists('register_widget_control') )
		return;
		 
		register_sidebar_widget(array(__('Diamond Recent Comments', 'diamond'),'widgets'),array($this, 'widget_endView'));
		register_widget_control(array(__('Diamond Recent Comments', 'diamond'), 'widgets'), array($this, 'widget_controlView'));
		
		add_shortcode('diamond-comment', array($this, 'diamond_comment_handler'));
		 
	}
	
	

	function diamond_comment_handler(  $atts, $content = null  ) {
			
		 extract( shortcode_atts( array(
		'exclude' => '',
		'count' => '',
		'format'	 => '',
		'avatar_size' => '',
		'default_avatar' => '',
		'date_format' => '',
		'before_item' =>'',
		'$after_item' => '',
		'before_content' => '',
		'after_content' => '',
		'whitelist' => ''
		), $atts ) );
			

		return $this->render_output(split(',',$exclude), $count, $format, $avatar_size, $default_avatar, $date_format, $before_item, $after_item, $before_content, $after_content, split(',', $whitelist));
	}
	
	function widget_endView($args)
	{		
		
		$wgt_title=get_option('c_wgt_title');
		$wgt_count=get_option('c_wgt_count');		
		$wgt_miss= split(';', get_option('c_wgt_miss'));		
		$wgt_format= get_option('c_wgt_format');		
		$wgt_avsize = get_option('wgtc_avsize');		
		$wgt_mtext = get_option('wgtc_mtext');		
		$wgt_defav = get_option('wgtc_defav');		
		$wgt_dt = get_option('wgtc_dt');		
		$wgt_white= split(';', get_option('c_wgt_white'));		
		
		$output = '';
		
		extract($args);
		$output .= $before_widget.$before_title.$wgt_title.$after_title;	

		$output .= $this->render_output($wgt_miss, $wgt_count, $wgt_format, $wgt_avsize, $wgt_defav, $wgt_dt, '<li>', '</li>', '<ul>', '</ul>', $wgt_white);
		
		$output .= $after_widget;
		
		echo $output;
	}
	
	function render_output($wgt_miss, $wgt_count, $wgt_format, $wgt_avsize, $wgt_defav, $wgt_dt, $before_item, $after_item, $before_cont, $after_cont, $wgt_white)	 {	
	
		global $DiamondCache;
		
		$cachekey = 'diamond_comments_'.diamond_arr_to_str($wgt_miss).'-'.$wgt_count.'-'.$wgt_format . diamond_arr_to_str($wgt_white).
		'-'.$wgt_avsize.'-'.$wgt_defav.'-'.$wgt_dt.'-'.$before_item.'-'.$after_item.'-'.$before_cont.'-'.
		$after_cont.'-'.$wgt_mtext;
		$output = $DiamondCache->get($cachekey, 'recent-comments');
					
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
		
		$first_comment = __('Hi, this is a comment.<br />To delete a comment, just log in and view the post&#039;s comments. There you will have the option to edit or delete them.');
		$first_comment = get_site_option( 'first_comment', $first_comment );
		$sqlstr = '';
		$blog_list = get_blog_list( 0, 'all' );
		if (($white == 0 && !in_array(1, $wgt_miss)) || ($white == 1 && in_array(1, $wgt_white))) {
			$sqlstr = "SELECT 1 as blog_id, comment_date, comment_id, comment_post_id, comment_content, comment_date_gmt, comment_author, comment_author_email from ".$table_prefix ."comments where comment_approved = 1 and (comment_type = '' or comment_type is null) and comment_content <> '".$first_comment."'";
		}
		$uni = '';
		
		foreach ($blog_list AS $blog) {
			if (($white == 0 && !in_array($blog['blog_id'], $wgt_miss) && $blog['blog_id'] != 1) ||
			($white == 1 && $blog['blog_id'] != 1 && in_array($blog['blog_id'], $wgt_white))) {
				if ($sqlstr != '')
					$uni = ' union ';;	
				$sqlstr .= $uni . " SELECT ".$blog['blog_id']." as blog_id, comment_date, comment_id, comment_post_id, comment_content, comment_date_gmt, comment_author, comment_author_email   from ".$table_prefix .$blog['blog_id']."_comments where comment_approved = 1  and (comment_type = '' or comment_type is null) and comment_content <> '".$first_comment."'";				
			}
		}
		
		$limit = '';
		if ((int)$wgt_count > 0)
			$limit = ' LIMIT 0, '. (int)$wgt_count;
		$sqlstr .= " ORDER BY comment_date_gmt desc " . $limit;		
				
		// echo $sqlstr; 
		
		$output = '';
		 
		$comm_list = $wpdb->get_results($sqlstr, ARRAY_A);			
		
		$output .= $before_cont;
		foreach ($comm_list AS $comm) {
			$output .= $before_item;
			
			$wgt_format = get_format_txt($wgt_format);
			$txt = ($wgt_format == '') ? '<strong>{title}</strong> - {date}' : $wgt_format;			
			
			$p = get_blog_post($comm["blog_id"], $comm["comment_post_id"]);
			$c = $comm['comment_content'];
			
			$av = get_avatar($comm['comment_author_email'], $wgt_avsize, $defav);
			
			if (strlen($c) > 50) 
				$c = mb_substr(strip_tags($c), 0, 51) . '...';
			$txt = str_replace('{title}', '<a href="' .get_blog_permalink($comm["blog_id"], $comm["comment_post_id"]).'#comment-'.$comm["comment_id"].'">'.$c.'</a>' , $txt);
			$txt = str_replace('{title_txt}', $c, $txt);
			$txt = str_replace('{author}', $comm['comment_author'], $txt);
			$txt = str_replace('{avatar}', $av, $txt);			
			$txt = str_replace('{post-title}', '<a href="'. get_blog_permalink($comm["blog_id"], $comm["comment_post_id"]) .'">'.$p->post_title.'</a>' , $txt);			
			$txt = str_replace('{post-title_txt}', $p->post_title , $txt);
			$txt = str_replace('{date}', date_i18n($wgt_dt, strtotime($comm['comment_date'])), $txt);			
			
			$output .= $txt;
			$output .= $after_item;
		}
		$output .= $after_cont;
		
		$output .= $wpdb->print_error(); 
		
		$DiamondCache->add($cachekey, 'recent-comments', $output);		
		
		return $output;
	}
	 
	function widget_controlView($is_admin = false)
	{
		global $DiamondCache;
	
		// Title
		if ($_POST['wgt_comment_hidden']) {
			$option=$_POST['wgt_c_title'];
			update_option('c_wgt_title',$option);		
		}
		$wgt_title=get_option('c_wgt_title');
		
		echo '<input type="hidden" name="wgt_comment_hidden" value="success" />';
		
		echo '<label for="wgt_c_title">' . __('Widget Title', 'diamond') . ':<br /><input id="wgt_c_title" name="wgt_c_title" type="text" value="'.$wgt_title.'" /></label>';
		
		if ($_POST['wgt_comment_hidden']) {
			$DiamondCache->addSettings('recent-comments', 'expire', $_POST['diamond_c_cache']);			
		}
		$dccache=$DiamondCache->getSettings('recent-comments', 'expire');		
		if ($dccache=='')
			$dccache = 120;	
		echo '<br />';
		echo '<label for="diamond_c_cache">' . __('Cache Expire Time (sec)', 'diamond') . ':<br /><input id="diamond_c_cache" name="diamond_c_cache" type="text" value="'.$dccache.'" /></label>';
		
		// Count
		if ($_POST['wgt_comment_hidden']) {
			$option=$_POST['wgt_count'];
			update_option('c_wgt_count',$option);
		}
		$wgt_count=get_option('c_wgt_count');
		echo '<br /><label for="wgt_number">'.__('Comments count', 'diamond').'<br /><input id="wgt_count" name="wgt_count" type="text" value="'.$wgt_count.'" /></label>';		
		
		// miss blogs
		if ($_POST['wgt_comment_hidden']) {	
			$option=$_POST['wgt_miss'];
			$tmp = '';
			$sep = '';
			if (isset($option) && $option != '')
			foreach ($option AS $op) {			
				$tmp .= $sep .$op;
				$sep = ';';
			}
			update_option('c_wgt_miss',$tmp);		
		}
		
		$wgt_miss=get_option('c_wgt_miss');
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
		if ($_POST['wgt_comment_hidden']) {		
			$option=$_POST['c_wgt_white'];
			$tmp = '';
			$sep = '';
			if (isset($option) && $option != '')
			foreach ($option AS $op) {			
				$tmp .= $sep .$op;
				$sep = ';';
			}
			update_option('c_wgt_white',$tmp);		
		}
		
		$wgt_white=get_option('c_wgt_white');
		$miss = split(';',$wgt_white);
		echo '<br /><label for="c_wgt_white">' . __('Whitelist: (The first 50 blogs)','diamond');
		$blog_list = get_blog_list( 0, 50 ); 
		echo '<br />';
		foreach ($blog_list AS $blog) {
			echo '<input id="c_wgt_white_'.$blog['blog_id'].'" name="c_wgt_white[]" type="checkbox" value="'.$blog['blog_id'].'" ';
			if (in_array($blog['blog_id'], $miss)) echo ' checked="checked" ';
			echo ' />';
			echo get_blog_option( $blog['blog_id'], 'blogname' );
			echo '<br />';
		}
		echo '</label>';	
		
		// Format
		if ($_POST['wgt_comment_hidden']) {
			$option=$_POST['wgt_format'];
			if (!$option || trim($option) == '')
				$option = '<strong>{title}</strong> - {date}';
			update_option('c_wgt_format', get_format_code($option));
		}
		$wgt_format=htmlentities(str_replace('\"', '"', get_format_txt(get_option('c_wgt_format'))));
		echo '<label for="wgt_number">' . __('Format string', 'diamond') .': <br /><input id="wgt_format" name="wgt_format" type="text" value="'.$wgt_format.'" /></label><br />';		
		echo '{title} - '. __('The comment\'s content', 'diamond') . '</p><br />';
		echo '{title_txt} - '. __('The comment\'s content', 'diamond').' '.__('(without link)', 'diamond').'<br />';
		echo '{post-title} - '. __('The post\'s title', 'diamond') . '</p><br />';
		echo '{post-title_txt} - '. __('The post\'s title text', 'diamond')	.' '.__('(without link)', 'diamond').'<br />';
		echo '{date} - '.__('The comment\'s date', 'diamond'). '</p><br />';
		echo '{author} - ' . __('The comment\'s author', 'diamond') .'<br />';
		echo '{avatar} - ' . __('Author\'s avatar', 'diamond') .'<br />';
		echo '<br />';	
		
		
		if ($_POST['wgt_comment_hidden']) {
			$option=$_POST['wgtc_avsize'];			
			update_option('wgtc_avsize',$option);		
		}
		$wgtc_avsize=get_option('wgtc_avsize');	
		
		echo '<label for="wgtc_avsize">' . __('Avatar Size (px)', 'diamond') . ':<br /><input id="wgtc_avsize" name="wgtc_avsize" type="text" value="'.$wgtc_avsize.'" /></label>';
		echo '<br />';
		
		
		if ($_POST['wgt_comment_hidden']) {
			$option=$_POST['wgtc_defav'];			
			update_option('wgtc_defav',$option);		
		}
		$wgtc_defav=get_option('wgtc_defav');	
		
		echo '<label for="wgtc_defav">' . __('Default Avatar URL', 'diamond') . ':<br /><input id="wgtc_defav" name="wgtc_defav" type="text" value="'.$wgtc_defav.'" /></label>';
		echo '<br />';
		
		
		
		if ($_POST['wgt_comment_hidden']) {
			$option=$_POST['wgtc_mtext'];			
			update_option('wgtc_mtext',$option);		
		}
		$wgtc_mtext=get_option('wgtc_mtext');	
		
		echo '<label for="wgtc_mtext">' . __('"Read More" link text', 'diamond') . ':<br /><input id="wgtc_mtext" name="wgtc_mtext" type="text" value="'.$wgtc_mtext.'" /></label>';
		echo '<br />';	

		if ($_POST['wgt_comment_hidden']) {
			$option=$_POST['wgtc_dt'];			
			update_option('wgtc_dt',$option);		
		}
		$wgtc_dt=get_option('wgtc_dt');	
		if (!isset($wgtc_dt) || trim($wgtc_dt) =='') {
			$wgtc_dt = 'M. d. Y.';
			update_option('wgtc_dt',$wgtc_dt);				
		}
		
		echo '<label for="wgtc_dt">' . __('DateTime format (<a href="http://php.net/manual/en/function.date.php" target="_blank">manual</a>)', 'diamond') . 
		':<br /><input id="wgtc_dt" name="wgtc_dt" type="text" value="'.
		$wgtc_dt.'" /></label>';
		echo '<br />';			
		
		if (!$is_admin) {
		echo '<br />';		
		
		_e('if you like this widget then', 'diamond');
		echo ': <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal%40amegrant%2ehu&lc=HU&item_name=Diamond%20Multisite%20WordPress%20Widget&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted" target="_blank">';
		_e('Buy me a beer!', 'diamond');
		echo '</a><br />';
		}
	}
	
	}
	$newWidget2 = new DiamondRC ();
	?>