<?php

class DiamondPF {

	function DiamondPF() {
			add_action('init', array($this, 'init_diamondPF'));			
			add_filter('generate_rewrite_rules', array($this, 'diamond_feed_rewrite'));
	}		 
	
	function diamond_feed_rewrite($wp_rewrite) {
		$feed_rules = array(
		'feed/(.+)' => 'index.php?feed=' . $wp_rewrite->preg_index(1),
		'(.+).xml' => 'index.php?feed='. $wp_rewrite->preg_index(1)
		);
		$wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;		
		return $wp_rewrite->rules;
}

		
	function init_diamondPF() {
		$address = get_option('diamond_feed_address');
		if (!$address || $address == '')
			$address = 'networkrss';
		add_feed('networkrss', array($this, 'diamond_post_create_feed'));		 		
	}
	
	function diamond_post_create_feed() {
		
		$wgt_count=get_option('diamond_post_feed_count');		
		$wgt_miss= split(';', get_option('diamond_post_feed_miss'));		
		$wgt_format = get_option('diamond_post_feed_format');				
		$wgt_white= split(';', get_option('diamond_post_feed_white'));		
	
		$this->render_output($wgt_miss, $wgt_count, $wgt_format, $wgt_white) ;		
	}
	
	
	function render_output($wgt_miss, $wgt_count, $wgt_format, $wgt_white)	 {	
			
		global $switched;		
		global $wpdb;
		$table_prefix = $wpdb->base_prefix;		
		
		 header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
		
		if (!isset($wgt_miss) || $wgt_miss == '')
			$wgt_miss = array ();
			
		$white = 0;
		if (isset($wgt_white) && $wgt_white != '' && count($wgt_white) > 0 && $wgt_white[0] && $wgt_white[0]!='')
			$white = 1;			
			
		echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
		
		?><rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/" >
<channel>
	<title><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
	<link><?php self_link(); ?></link>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<description><?php bloginfo_rss("description") ?></description>
	<language><?php echo get_option('rss_language'); ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency><?php			
			
		$sqlstr = '';
		$blog_list = get_blog_list( 0, 'all' );
		if (($white == 0 && !in_array(1, $wgt_miss)) || ($white == 1 && in_array(1, $wgt_white))) {
			$sqlstr = "SELECT 1 as blog_id, id, post_date_gmt, post_type from ".$table_prefix ."posts where post_status = 'publish' and post_type = 'post' ";
		}
		$uni = '';
		
		foreach ($blog_list AS $blog) {
			if (($white == 0 && !in_array($blog['blog_id'], $wgt_miss) && $blog['blog_id'] != 1) ||
			($white == 1 && $blog['blog_id'] != 1 && in_array($blog['blog_id'], $wgt_white))) {
				if ($sqlstr != '')
					$uni = ' union ';;	
				$sqlstr .= $uni . " SELECT ".$blog['blog_id']." as blog_id, id, post_date_gmt, post_type from ".$table_prefix .$blog['blog_id']."_posts  where post_status = 'publish' and post_type = 'post' ";				
			}
		}
		
		$limit = '';
		if ((int)$wgt_count > 0)
			$limit = ' LIMIT 0, '. (int)$wgt_count;
		else	
			$limit = ' LIMIT 0, 100';
		$sqlstr .= " ORDER BY post_date_gmt desc " . $limit;			
		 
		$post_list = $wpdb->get_results($sqlstr, ARRAY_A);		
		
		foreach ($post_list AS $post) {
			
			
			$txt = ($wgt_format == '') ? '{excerpt}' : $wgt_format;
			
			$p = get_blog_post($post["blog_id"], $post["id"]);			
			
			$ex = $p->post_excerpt;
			//if (!isset($ex) || trim($ex) == '')
				//$ex = substr(strip_tags($p->post_content), 0, 65) . '...';
			echo "\r";							?>
	<item>
		<title><![CDATA[<?php echo $p->post_title ?>]]></title>
		<link><?php echo get_blog_permalink($post["blog_id"], $post["id"]) ?></link>
		<dc:creator><?php echo get_userdata($p->post_author)->nickname ?></dc:creator>
		<guid isPermaLink="false"><?php echo $p->guid ?></guid>
		<pubDate><?php echo date(DATE_RFC822, strtotime($p->post_date)) ?></pubDate><?php

			//	echo '<content:encoded><![CDATA[' . $p->post_content . ']]></content:encoded>';
			
			$txt = str_replace('{content}', $p->post_content , $txt);			
			$txt = str_replace('{excerpt}', $ex , $txt);			
			$txt = str_replace('{blog}', get_blog_option($post["blog_id"], 'blogname') , $txt);		
		echo "\r";	?>
		<description><![CDATA[<?php echo $txt ?>]]></description>			
	</item><?php }	 echo "\r"; ?>
</channel>
</rss><?php
	}

	 
	function feed_adminPage()
	{	
		
		echo '<a href="' . get_bloginfo( 'url' ). '?feed=networkrss" target="_blank">' . __('The feed\'s link', 'diamond') . '</a>';
		echo '<br />';
		
			echo '<input type="hidden" name="diamond_post_feed_hidden" value="success" />';
		
		// Count
		if ($_POST['diamond_post_feed_hidden'])	{
			$option=$_POST['diamond_post_feed_count'];
			update_option('diamond_post_feed_count',$option);
		}
		$wgt_count=get_option('diamond_post_feed_count');
		echo '<br /><label for="diamond_post_feed_number">' .__('Posts count', 'diamond') . ':<br /><input id="diamond_post_feed_count" name="diamond_post_feed_count" type="text" value="'.$wgt_count.'" /></label>';		
		
		// miss blogs
		if ($_POST['diamond_post_feed_hidden']) {		
			$option=$_POST['diamond_post_feed_miss'];
			$tmp = '';
			$sep = '';
			if (isset($option) && $option != '')
			foreach ($option AS $op) {			
				$tmp .= $sep .$op;
				$sep = ';';
			}
			update_option('diamond_post_feed_miss',$tmp);		
		}
		
		$wgt_miss=get_option('diamond_post_feed_miss');
		$miss = split(';',$wgt_miss);
		echo '<br /><label for="diamond_post_feed_miss">' . __('Exclude blogs: (The first 50 blogs)','diamond');
		$blog_list = get_blog_list( 0, 50 ); 
		echo '<br />';
		foreach ($blog_list AS $blog) {
			echo '<input id="diamond_post_feed_miss_'.$blog['blog_id'].'" name="diamond_post_feed_miss[]" type="checkbox" value="'.$blog['blog_id'].'" ';
			if (in_array($blog['blog_id'], $miss)) echo ' checked="checked" ';
			echo ' />';
			echo get_blog_option( $blog['blog_id'], 'blogname' );
			echo '<br />';
		}
		echo '</label>';		
	

		// whitelist
		if ($_POST['diamond_post_feed_hidden']) {		
			$option=$_POST['diamond_post_feed_white'];
			$tmp = '';
			$sep = '';
			if (isset($option) && $option != '')
			foreach ($option AS $op) {			
				$tmp .= $sep .$op;
				$sep = ';';
			}
			update_option('diamond_post_feed_white',$tmp);		
		}
		
		$wgt_miss=get_option('diamond_post_feed_white');
		$miss = split(';',$wgt_miss);
		echo '<br /><label for="diamond_post_feed_white">' . __('Whitelist: (The first 50 blogs)','diamond');
		$blog_list = get_blog_list( 0, 50 ); 
		echo '<br />';
		foreach ($blog_list AS $blog) {
			echo '<input id="diamond_post_feed_white_'.$blog['blog_id'].'" name="diamond_post_feed_white[]" type="checkbox" value="'.$blog['blog_id'].'" ';
			if (in_array($blog['blog_id'], $miss)) echo ' checked="checked" ';
			echo ' />';
			echo get_blog_option( $blog['blog_id'], 'blogname' );
			echo '<br />';
		}
		echo '</label>';		
	
		
		// Format
		if ($_POST['diamond_post_feed_hidden'])	 {
			$option=$_POST['diamond_post_feed_format'];			
			update_option('diamond_post_feed_format',$option);
		}
		$wgt_format=get_option('diamond_post_feed_format');
		echo '<label for="wgt_number">' . __('Format string', 'diamond') .':<br /><input id="wgt_format" name="diamond_post_feed_format" type="text" value="'.$wgt_format.'" /></label><br />';		
		
		echo '{excerpt} - '. __('The post\'s excerpt', 'diamond').'<br />';		
		echo '{content} - '. __('The post\'s content', 'diamond').'<br />';		
		echo '{blog} - '. __('The post\'s blog name', 'diamond') .'<br />';
		
		echo '<br />';			
		
	}
	
	}
	$feedObj = new DiamondPF ();
?>