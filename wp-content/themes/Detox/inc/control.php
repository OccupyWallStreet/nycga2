<?php
remove_action('wp_head', 'rsd_link'); 
remove_action('wp_head', 'wlwmanifest_link');
function fb_replace_wp_version() {
	if ( !is_admin() ) {
		global $wp_version;
		$v = intval( rand(0, 9999) );
		if ( function_exists('the_generator') ) {
			add_filter( 'the_generator', create_function('$a', "return null;") );
			$wp_version = $v;
		} else {
			add_filter( "bloginfo_rss('version')", create_function('$a', "return $v;") );
			$wp_version = $v;
		}
	}
}
if ( function_exists('add_action') ) {
	add_action('init', fb_replace_wp_version, 1);
}
add_action('admin_head', 'my_custom_logo');
function my_custom_logo() {
   echo '
      <style type="text/css">
         #header-logo { background-image: url('.get_bloginfo('template_directory').'/images/favicon.png) !important; }
      </style>
   ';
}
add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
function my_custom_dashboard_widgets() {
   global $wp_meta_boxes;
   wp_add_dashboard_widget('custom_help_widget', 'Welcome to Detox', 'custom_dashboard_help');
   wp_add_dashboard_widget('custom_help_widget2', 'Detox forum', 'custom_dashboard_help2');
}
function custom_dashboard_help() {
   echo '<p>New theme activated, congrats.</p>
                
				<h4>Twitter auto posting:</h4>
				<p>Locate headads.php at /wp-content/detox/inc/,<br />
				and edit the twitter name (milo317) according to yours.</p>
				
        <h4>Advertising areas</h4>
        <p>Go to the theme options at widgets,<br /> 
        <strong>use your ad widget code</strong></p>
        
        <h4>Category & Slider items</h4>
        <p>Go to the theme options at Appearance,<br />
        select your categories, please keep in mind that you need at least 5 posts for the slider categories to work.</p>
        
        <h4>Widgets</h4>
        <p>Front, all sidebars, ad sections and footer columns are fully widgetized.</p>';
}
function custom_dashboard_help2() {
   echo '<p>Need more help? Contact milo317 via her <a href="http://forum.milo317.com">forum</a>.</p>';
}
function custom_colors() {
   echo '<style type="text/css">#wphead{background:#000 !important;border-bottom:5px solid #900;color:#fff;text-shadow:#000 0 1px 1px;}#message{display:none !important;}#footer{background:#000 !important;border-top:5px solid #900;color:#ccc;}#user_info p,#user_info p a,#wphead a{color:#fafafa !important;}</style>';
}
add_action('admin_head', 'custom_colors');
function remove_footer_admin () {
    echo "Thank you for creating with <a href='http://3oneseven.com/'>milo</a>.";
} 
add_filter('admin_footer_text', 'remove_footer_admin'); 
function recent_cmts($num) {
	global $wpdb;
	$query = ("SELECT ID, post_title, comment_author, comment_id, comment_author_email, comment_date, comment_post_ID FROM  $wpdb->posts, $wpdb->comments WHERE $wpdb->posts.ID=$wpdb->comments.comment_post_ID AND $wpdb->comments.comment_approved = '1' AND $wpdb->comments.comment_type = '' AND comment_author != '' ORDER BY $wpdb->comments.comment_date DESC LIMIT $num");
	$result = mysql_query($query);
		while ($data = mysql_fetch_row($result)) {
		echo '<li class="recent-cmts">';
			echo '<img style="float:right; margin-left: 5px; padding: 3px; background:#333;" src="http://www.gravatar.com/avatar.php?gravatar_id=';
			echo md5($data[4]);
			echo '&amp;size=24&amp;default=';
			echo bloginfo('template_url');
			echo '/images/bomb.png';
			echo '" alt="';
			echo $data[2];
			echo '&#39;s Gravatar" height="24" width="24" class="right" />';
			echo '<div style="margin-left:5px;"><a href="';
			echo get_permalink($data[0]);
			echo "#comment-$data[3]";
			echo '" title="';
			echo 'commented on &raquo; ';
			echo $data[1];
			echo '">';
			echo $data[2];
			echo '</a><br /><small>';
			echo $data[5];
			echo '</small></div>';
		echo '</li>';
		}
	}
function wp_pagenavi($before = '', $after = '', $prelabel = '', $nxtlabel = '', $pages_to_show = 5, $always_show = false) {
	global $request, $posts_per_page, $wpdb, $paged;
	if(empty($prelabel)) {
		$prelabel  = '<strong>&laquo;</strong>';
	}
	if(empty($nxtlabel)) {
		$nxtlabel = '<strong>&raquo;</strong>';
	}
	$half_pages_to_show = round($pages_to_show/2);
	if (!is_single()) {
		if(!is_category()) {
			preg_match('#FROM\s(.*)\sORDER BY#siU', $request, $matches);		
		} else {
			preg_match('#FROM\s(.*)\sGROUP BY#siU', $request, $matches);		
		}
		$fromwhere = $matches[1];
		$numposts = $wpdb->get_var("SELECT COUNT(DISTINCT ID) FROM $fromwhere");
		$max_page = ceil($numposts /$posts_per_page);
		if(empty($paged)) {
			$paged = 1;
		}
		if($max_page > 1 || $always_show) {
			echo "$before <div class='Nav'><span>Pages ($max_page): </span>";
			if ($paged >= ($pages_to_show-1)) {
				echo '<a href="'.get_pagenum_link().'">&laquo; First</a> ... ';
			}
			previous_posts_link($prelabel);
			for($i = $paged - $half_pages_to_show; $i  <= $paged + $half_pages_to_show; $i++) {
				if ($i >= 1 && $i <= $max_page) {
					if($i == $paged) {
						echo "<strong class='on'>$i</strong>";
					} else {
						echo ' <a href="'.get_pagenum_link($i).'">'.$i.'</a> ';
					}
				}
			}
			next_posts_link($nxtlabel, $max_page);
			if (($paged+$half_pages_to_show) < ($max_page)) {
				echo ' ... <a href="'.get_pagenum_link($max_page).'">Last &raquo;</a>';
			}
			echo "</div> $after";
		}
	}
}
?>