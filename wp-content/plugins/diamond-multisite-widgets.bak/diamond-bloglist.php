<?php
class DiamondBL {

	function DiamondBL() {
		add_action('widgets_init', array($this, 'init_diamondBL'));
	}
		 
		function init_diamondBL() {	
		
		add_shortcode('diamond-bloglist', array($this, 'diamond_bloglist_handler'));
		
		if ( !function_exists('register_sidebar_widget') ||
		!function_exists('register_widget_control') )
		return;
		 
		register_sidebar_widget(array(__('Diamond Bloglist', 'diamond'),'widgets'),array($this, 'widget_endView'));
		register_widget_control(array(__('Diamond Bloglist', 'diamond'), 'widgets'), array($this, 'widget_controlView'));		
		 
	}

	function diamond_bloglist_handler(  $atts, $content = null  ) {
				
		 extract( shortcode_atts( array(
		'exclude' => '',
		'count' => '',
		'format'	 => '',
		'avatar_size' => '',
		'default_logo' => '',
		'date_format' => '',
		'before_item' =>'',
		'after_item' => '',
		'before_content' => '',
		'more_text' => '',
		'after_content' => '',
		'order_by' => '',
		'order' => '',
		'whitelist' => '',
		'comment_age' => '',
		'min_post_count' => ''
		
		), $atts ) );

		return $this->render_output(split(',',$exclude), $count, html_entity_decode($format), $avatar_size, $default_logo, $date_format, html_entity_decode($before_item), html_entity_decode($after_item), html_entity_decode($before_content), html_entity_decode($after_content), $more_text, $order_by, $order, split(',', $whitelist), $min_post_count, $comment_age);
	}
	

	function widget_endView($args)
	{		
		$bloglist_options = get_option('diamond_bloglist_options');
		extract($bloglist_options);
		$wgt_title = $diamond_bloglist_title;
		$wgt_count = $diamond_bloglist_count;		
		$wgt_miss = split(';', $diamond_bloglist_miss);		
		$wgt_format = $diamond_bloglist_format;		
		$wgt_avsize = $diamond_bloglist_avsize;		
		$wgt_mtext = $diamond_bloglist_mtext;		
		$wgt_defav = $diamond_bloglist_defav;		
		$wgt_dt = $diamond_bloglist_dt;	
		$wgt_white = split(';', $diamond_bloglist_white);
		$min_post_count = $diamond_bloglist_min_post_count; 
		$comment_age = $wgt_diamond_bloglist_comment_age;
	
		//print_r($bloglist_options);
		
		extract($args);
		
		$output = '';
		
		$output .= $before_widget.$before_title.$wgt_title. $after_title;		
	
		$output .= $this->render_output($wgt_miss, $wgt_count, $wgt_format, $wgt_avsize, $wgt_defav, $wgt_dt, '<li>', '</li>', '<ul>', '</ul>', $wgt_mtext, $diamond_bloglist_order, $diamond_bloglist_order_by, $wgt_white, $min_post_count, $comment_age) ;
		
		$output .=  $after_widget;
		
		echo $output;
	}
	
	
	function render_output($wgt_miss, $wgt_count, $wgt_format, $wgt_avsize, $wgt_defav, $wgt_dt, $before_item, $after_item, $before_cont, $after_cont, $wgt_mtext, $ord, $ordb, $wgt_white, $min_post_count, $comment_age)	 {		
		
		
		global $DiamondCache;
		
		$cachekey = 'diamond_bloglist_'.diamond_arr_to_str($wgt_miss).'-'.$wgt_count.'-'.$wgt_format .diamond_arr_to_str($wgt_white).
		'-'.$wgt_avsize.'-'.$wgt_defav.'-'.$wgt_dt.'-'.$before_item.'-'.$after_item.'-'.$before_cont.'-'.
		$after_cont.'-'.$wgt_mtext.'-'.$ord.'-'.$ordb.'-'.$min_post_count.'-'.$comment_age;
		$output = $DiamondCache->get($cachekey, 'bloglist');
					
		if ($output != false)
			return $output;					
	
		global $switched;		
		global $wpdb;
		$table_prefix = $wpdb->base_prefix;
		
		if (!isset($wgt_dt) || trim($wgt_dt) =='') 
			$wgt_dt = __('M. d. Y.', 'diamond');
		
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
			
		if (!isset($wgt_miss) || $wgt_miss == '' || (count($wgt_count) == 1 &&  $wgt_miss[0] == ''))
			$wgt_miss = array ();		

		$white = 0;
		if (isset($wgt_white) && $wgt_white != '' && count($wgt_white) > 0 && $wgt_white[0] && $wgt_white[0]!='')
			$white = 1;					
		
		$sqlstr = '';
		
		$felt = '';$sep = '';
		
		if ($white == 0) {
			if (count($wgt_miss) > 0) {
				$felt = ' AND blog_id NOT IN (';
				foreach ($wgt_miss AS $m) {
					$felt .= $sep . $m;			
					$sep = ', ';
				}
				$felt .= ') ';
			}
		} else {			
			$felt = ' AND blog_id IN (';
			foreach ($wgt_white AS $m) {
				$felt .= $sep . $m;			
				$sep = ', ';
			}
			$felt .= ') ';		
		}
		
		//print_r($wgt_miss);

		if ($ord!=4) {
			
			$sqlstr = "SELECT blog_id, registered, last_updated from ".$table_prefix ."blogs where  public = 1	AND spam = 0 AND archived = '0' AND deleted = 0 "	. $felt;	
			
			$limit = '';
			if ((int)$wgt_count > 0)
				$limit = ' LIMIT 0, '. (int)$wgt_count;
			
			
			 
			if (!$ord || $ord=='')
				$ord = 0;
				
			$sqlstr .= " ORDER BY ";
			switch ($ord)	 {
				default:  $sqlstr .= "path ";
					break;
				case 1:  $sqlstr .= "registered ";
					break;
				case 2:  $sqlstr .= "last_updated ";			
					break;					
			}
			
		} else {
			 //Order by number of comments from last x days
			$sqlstr = "SELECT blog_id, registered, last_updated from ".$table_prefix ."blogs where  public = 1	AND spam = 0 AND archived = '0' AND deleted = 0 "	. $felt;
			$blog_list_temp = $wpdb->get_results($sqlstr, ARRAY_A);
			echo $wpdb->print_error();
			
			
			$sqlstr = '';
			$age_set = '';
			if ($comment_age != '' && (int)$comment_age>0) {
				$age_set = "where datediff(now(), comment_date)<".$comment_age; 
			}
			
			foreach ($blog_list_temp as $blog) {
				$tbprefix = ($blog['blog_id']==1) ? '' : $blog['blog_id'].'_';
				$tbprefix = $table_prefix.$tbprefix;
				if ($sqlstr!='') $sqlstr .= ' union ';
				$sqlstr .= " select ".$blog['blog_id']." as blog_id, '".$blog['registered']."' as registered, '".$blog['last_updated']."' as last_updated, count(comment_ID) as comment_count from ".$tbprefix."comments ".$age_set." group by blog_id";
			}
			$sqlstr .= " order by comment_count ";
			$limit = '';
			if ((int)$wgt_count > 0)
				$limit = ' LIMIT 0, '. (int)$wgt_count;
			
		}
		
		if (!$ordb || $ordb=='')
			$ordb = 0;
		
		
		switch ($ordb)	 {
			default:  $sqlstr .= "asc ";
				break;
			case 1:  $sqlstr .= "desc ";
				break;		
		}
		
		$sqlstr .= $limit;
		//echo $sqlstr;
		$blog_list_temp = $wpdb->get_results($sqlstr, ARRAY_A);
		echo $wpdb->print_error(); 
		//print_r($blog_list);
		
		$output = '';
		$output .=  $before_cont;		
		
		if ($ord == 3)	 {
			foreach ($blog_list_temp as $blog) {			
				switch_to_blog($blog['blog_id']);		
				$count = wp_count_posts()->publish;			
				restore_current_blog();	
				$blog_list[$count]  = $blog;			
				$blog_list[$count]['count'] = $count;					
			}
			
			ksort($blog_list);
			if ($ordb == 1)  {
				$blog_list = array_reverse($blog_list);
			}
		}
		else
			$blog_list = $blog_list_temp;
		
		foreach ($blog_list as $blog) {
			
			$wgt_format = get_format_txt($wgt_format);
			$txt = ($wgt_format == '') ? '<b>{title}</b>' : $wgt_format;			
			
			$title = '';$desc = '';$burl = '';$pcount = 0;$avatar = '';
			switch_to_blog($blog['blog_id']);					
				if (strpos($txt, '{title}') !== false || strpos($txt, '{title_txt}') !== false)
					$title = get_bloginfo('name');
				if (strpos($txt, '{description}') !== false)
					$desc = get_bloginfo('description');	
				$burl = get_bloginfo('url');
				if (strpos($txt, '{postcount}') !== false || (int)$min_post_count>0)
					$pcount = wp_count_posts()->publish;

				if (strpos($txt, '{avatar}') !== false)
					$avatar = get_avatar(get_bloginfo('admin_email'), $wgt_avsize);	
				
			restore_current_blog();
			
			
			
			if ((int)$min_post_count<=$pcount) {
			
				$output .=  $before_item;
				//@TODO add trailing shash only if in subdir mode 
				$txt = str_replace('{title}', '<a href="' . $burl .'/">'. $title .'</a>' , $txt);
				$txt = str_replace('{more}', '<a href="' . $burl .'/">'.$wgt_mtext.'</a>' , $txt);
				$txt = str_replace('{title_txt}', $title , $txt);
				$txt = str_replace('{reg}', date_i18n($wgt_dt, strtotime($blog['registered'])), $txt);
				$txt = str_replace('{last_update}', date_i18n($wgt_dt, strtotime($blog['last_updated'])), $txt);
				$txt = str_replace('{description}', $desc, $txt);
				$txt = str_replace('{postcount}', $pcount , $txt);
				$txt = str_replace('{comment_count}', $blog['comment_count'] , $txt);
				$txt = str_replace('{avatar}', $avatar , $txt);
				
				$output .=  $txt;
				$output .=  $after_item;
			}
		}
		$output .=  $after_cont;
		
		$output .=  $wpdb->print_error();
		
		$DiamondCache->add($cachekey, 'bloglist', $output);		
		
		return $output; 
		
	}
	
	 
	function widget_controlView($is_admin = false)
	{
		global $DiamondCache;
	
		$options = get_option('diamond_bloglist_options');
		// Title
		if ($_POST['diamond_bloglist_hidden']) {
			$option=$_POST['wgt_title'];
			$options['diamond_bloglist_title'] = $option;		
		} 
		$wgt_title = $options['diamond_bloglist_title'];
		
		echo '<input type="hidden" name="diamond_bloglist_hidden" value="success" />';
		
		echo '<label for="wgt_title">' . __('Widget Title', 'diamond') . ':<br /><input id="wgt_title" name="wgt_title" type="text" value="'.$wgt_title.'" /></label>';
		
		if ($_POST['diamond_bloglist_hidden']) {
			$DiamondCache->addSettings('bloglist', 'expire', $_POST['diamond_b_cache']);			
		}
		$dccache=$DiamondCache->getSettings('bloglist', 'expire');		
		if ($dccache=='')
			$dccache = 120;	
		echo '<br />';
		echo '<label for="diamond_b_cache">' . __('Cache Expire Time (sec)', 'diamond') . ':<br /><input id="diamond_b_cache" name="diamond_b_cache" type="text" value="'.$dccache.'" /></label>';
		
		// Count
		if ($_POST['diamond_bloglist_hidden'])	 {
			$option=$_POST['wgt_count'];
			$options['diamond_bloglist_count'] = $option;
		}
		$wgt_count = $options['diamond_bloglist_count'];
		echo '<br /><label for="wgt_number">' .__('Blogs count', 'diamond') . ':<br /><input id="wgt_count" name="wgt_count" type="text" value="'.$wgt_count.'" /></label>';		
		
		// miss blogs
		if ($_POST['diamond_bloglist_hidden']) {		
			$option=$_POST['wgt_miss'];
			$tmp = '';
			$sep = '';
			if (isset($option) && $option != '')
			foreach ($option AS $op) {			
				$tmp .= $sep .$op;
				$sep = ';';
			}
			$options['diamond_bloglist_miss'] = $tmp;		
		}
		
		$wgt_miss=$options['diamond_bloglist_miss'];
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
		
		
		
		// whitelist
		if ($_POST['diamond_bloglist_hidden']) {		
			$option=$_POST['wgt_white'];
			$tmp = '';
			$sep = '';
			if (isset($option) && $option != '')
			foreach ($option AS $op) {			
				$tmp .= $sep .$op;
				$sep = ';';
			}
			$options['diamond_bloglist_white'] = $tmp;		
		}
		
		$wgt_miss=$options['diamond_bloglist_white'];
		$miss = split(';',$wgt_miss);
		echo '<br /><label for="wgt_white">' . __('Whitelist: (The first 50 blogs)','diamond');
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
		if ($_POST['diamond_bloglist_hidden']) {
			$option=$_POST['wgt_format'];
			if (!isset($option) || $option == '')
				$option = '<strong>{title}</strong>';
			$options['diamond_bloglist_format'] = get_format_code($option);
		}
		$wgt_format= htmlentities(str_replace('\"', '"', get_format_txt($options['diamond_bloglist_format'])));
		echo '<label for="wgt_number">' . __('Format string', 'diamond') .':<br /><input id="wgt_format" name="wgt_format" type="text" value="'.$wgt_format.'" /></label><br />';		
		echo '{title} - '. __('The blog\'s title', 'diamond').'<br />';
		echo '{title_txt} - '. __('The blog\'s title', 'diamond').' '.__('(without link)', 'diamond').'<br />';
		echo '{description} - '. __('The blog\'s description', 'diamond').'<br />';		
		echo '{reg} - ' . __('The registration\'s date', 'diamond') .'<br />';
		echo '{last_update} - ' . __('The blog\'s last update date', 'diamond') .'<br />';
		echo '{avatar} - ' . __('Author\'s avatar', 'diamond') .'<br />';
		echo '{postcount} - ' . __('The blog\'s posts count', 'diamond') .'<br />';		
		echo '{comment_count} - ' . __('The blog\'s comment count - only works with Order by comment count', 'diamond') .'<br />';		
		echo '{more} - '. __('The "Read More" link', 'diamond') .'<br />';
		echo '<br />';
		
		if ($_POST['diamond_bloglist_hidden'])	
			$options['diamond_bloglist_order'] = $_POST['diamond_bloglist_order'];			
		
		if (!$options['diamond_bloglist_order'] || $options['diamond_bloglist_order']=='')	
			$options['diamond_bloglist_order'] = 0;
		$dor=$options['diamond_bloglist_order'];	
		
		if ($_POST['diamond_bloglist_hidden'])	
			$options['diamond_bloglist_order_by'] = $_POST['diamond_bloglist_order_by'];			
		
		if (!$options['diamond_bloglist_order_by'] || $options['diamond_bloglist_order_by']=='')	
			$options['diamond_bloglist_order_by'] = 0;
		$dorb=$options['diamond_bloglist_order_by'];	
		
		
		echo '<label for="diamond_bloglist_order">' . __('Sort Order', 'diamond') . ':<br />';
		echo'</label>';
		echo '<select id="diamond_bloglist_order" name="diamond_bloglist_order">';
		echo '<option value="0" '. (($dor == 0)? 'selected="selected"' : '') . '>'.__('By Domain', 'diamond').'</option>';
		echo '<option value="1" '. (($dor == 1)? 'selected="selected"' : '') . '>'.__('By Reg. Date', 'diamond').'</option>';
		echo '<option value="2" '. (($dor == 2)? 'selected="selected"' : '') . '>'.__('By Last Update', 'diamond').'</option>';
		echo '<option value="3" '. (($dor == 3)? 'selected="selected"' : '') . '>'.__('By Post Count', 'diamond').'</option>';
		echo '<option value="4" '. (($dor == 4)? 'selected="selected"' : '') . '>'.__('By Comment Count', 'diamond').'</option>';
		echo '</select>';
		
		echo '<select id="diamond_bloglist_order_by" name="diamond_bloglist_order_by">';
		echo '<option value="0" '. (($dorb == 0)? 'selected="selected"' : '') . '>'.__('Ascending', 'diamond').'</option>';
		echo '<option value="1" '. (($dorb == 1)? 'selected="selected"' : '') . '>'.__('Descending', 'diamond').'</option>';
		echo '</select>';
		
		echo '<br />';
		echo '<br />';
		
		
		if ($_POST['diamond_bloglist_hidden'])	 {
			$option=$_POST['wgt_mtext'];
			if (!isset($option) || $option == '')
				$option = __('Read More', 'diamond');
			$options['diamond_bloglist_mtext'] = $option;		
		}
		$wgt_mtext= $options['diamond_bloglist_mtext'];	
		
		echo '<label for="wgt_mtext">' . __('"Read More" link text', 'diamond') . 
		':<br /><input id="wgt_mtext" name="wgt_mtext" type="text" value="'.
		$wgt_mtext.'" /></label>';
		echo '<br />';	
		
		if ($_POST['diamond_bloglist_hidden'])	 {
			$option=$_POST['wgt_dt'];			
			$options['diamond_bloglist_dt'] = $option;		
		}
		$wgt_dt= $options['diamond_bloglist_dt'];	
		if (!isset($wgt_dt) || trim($wgt_dt) =='') {
			$wgt_dt = __('M. d. Y.', 'diamond');
			$options['diamond_bloglist_dt'] = $wgt_dt;				
		}
		
		
		echo '<label for="wgt_dt">' . __('DateTime format (<a href="http://php.net/manual/en/function.date.php" target="_blank">manual</a>)', 'diamond') . 
		':<br /><input id="wgt_dt" name="wgt_dt" type="text" value="'.
		$wgt_dt.'" /></label>';
		echo '<br />';	

		
		if ($_POST['diamond_bloglist_hidden'])	 {
			$option=$_POST['wgt_min_post_count'];			
			$options['diamond_bloglist_min_post_count'] = $option;		
		}
		$wgt_diamond_bloglist_min_post_count= $options['diamond_bloglist_min_post_count'];	
		if (!isset($wgt_diamond_bloglist_min_post_count) || trim($wgt_diamond_bloglist_min_post_count) =='') {
			$wgt_diamond_bloglist_min_post_count = '';
			$options['diamond_bloglist_min_post_count'] = $wgt_diamond_bloglist_min_post_count;				
		}
		
		
		echo '<label for="wgt_min_post_count">' . __('The minimum number of posts that the blog should contain to be listed', 'diamond') . 
		':<br /><input id="wgt_min_post_count" name="wgt_min_post_count" type="text" value="'.
		$wgt_diamond_bloglist_min_post_count.'" /></label>';
		echo '<br />';	
		
		
		if ($_POST['diamond_bloglist_hidden'])	 {
			$option=$_POST['wgt_comment_age'];			
			$options['diamond_bloglist_comment_age'] = $option;		
		}
		$wgt_diamond_bloglist_comment_age= $options['diamond_bloglist_comment_age'];	
		if (!isset($wgt_diamond_bloglist_comment_age) || trim($wgt_diamond_bloglist_comment_age) =='') {
			$wgt_diamond_bloglist_comment_age = '';
			$options['diamond_bloglist_comment_age'] = $wgt_diamond_bloglist_comment_age;				
		}
		
		
		echo '<label for="wgt_comment_age">' . __('The maximum ages of comments in days if using order by comment count', 'diamond') . 
		':<br /><input id="wgt_comment_age" name="wgt_comment_age" type="text" value="'.
		$wgt_diamond_bloglist_comment_age.'" /></label>';
		echo '<br />';	
		
		
		if ($_POST['diamond_bloglist_hidden'])
			update_option('diamond_bloglist_options', $options);				
		
		if (!$is_admin) {
			echo '<br />';
			_e('if you like this widget then', 'diamond');
			echo ': <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal%40amegrant%2ehu&lc=HU&item_name=Diamond%20Multisite%20WordPress%20Widget&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted" target="_blank">';
			_e('Buy me a beer!', 'diamond');
			echo '</a><br />';
		}
	}
	
	}
	
	$bloglistObj = new DiamondBL ();
 ?>