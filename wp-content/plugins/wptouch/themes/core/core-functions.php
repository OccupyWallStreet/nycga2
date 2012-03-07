<?php 

// WPtouch Core Header Functions

function wptouch_core_header_enqueue() {
	$version = get_bloginfo('version'); 
	if ( !bnc_wptouch_is_exclusive() ) { 
	    wp_enqueue_script('jquery-form');
		wp_enqueue_script('wptouch-core', '' . compat_get_plugin_url( 'wptouch' ) . '/themes/core/core.js', array('jquery'), '1.9.x' );		
		wp_head(); 

	} elseif ( bnc_wptouch_is_exclusive() ) { 
		echo "<script src='" . get_bloginfo('wpurl') . "/wp-includes/js/jquery/jquery.js?wptouch' type='text/javascript' charset='utf-8'></script>\n";
		echo "<script src='" . get_bloginfo('wpurl') . "/wp-includes/js/jquery/jquery.form.js?wptouch' type='text/javascript' charset='utf-8'></script>\n";
		echo "<script src='" . compat_get_plugin_url( 'wptouch' ) . "/themes/core/core.js?wptouch' type='text/javascript' charset='utf-8'></script>\n"; 
	}
	
	do_action( 'wptouch_core_header_enqueue' );
}
  
function wptouch_core_header_home() {
	if (bnc_is_home_enabled()) {
		echo sprintf(__( "%sHome%s", "wptouch" ), '<li><a href="' . get_bloginfo( 'url' ) . '"><img src="' . bnc_get_title_image() . '" alt=""/>','</a></li>');
	}
}
  
function wptouch_core_header_pages() {
	$pages = bnc_wp_touch_get_pages();
	global $blog_id;
	foreach ($pages as $p) {
		if ( file_exists( compat_get_plugin_dir( 'wptouch' ) . '/images/icon-pool/' . $p['icon'] ) ) {
			$image = compat_get_plugin_url( 'wptouch' ) . '/images/icon-pool/' . $p['icon'];	
		} else {
		$image = compat_get_upload_url() . '/wptouch/custom-icons/' . $p['icon'];
	}
		echo('<li><a href="' . get_permalink($p['ID']) . '"><img src="' . $image . '" alt="icon" />' . __($p['post_title']) . '</a></li>');
	}
  }
 
function wptouch_core_header_rss() {
	if (bnc_is_rss_enabled()) {
		echo sprintf(__( "%sRSS Feed%s", "wptouch" ), '<li><a href="' . get_bloginfo('rss2_url') . '"><img src="' . compat_get_plugin_url( 'wptouch' ) . '/images/icon-pool/RSS.png" alt="" />','</a></li>');
	}
}

function wptouch_core_header_email() {
	if (bnc_is_email_enabled()) {
		echo sprintf(__( "%sE-Mail%s", "wptouch" ), '<li><a href="mailto:' . get_bloginfo('admin_email') . '"><img src="' . compat_get_plugin_url( 'wptouch' ) . '/images/icon-pool/Mail.png" alt="" />','</a></li>');
	}
} 
  
function wptouch_core_header_check_use() {
	if (false && function_exists('bnc_is_iphone') && !bnc_is_iphone()) {
		echo '<div class="content post">';
		echo sprintf(__( "%sWarning%s", "wptouch" ), '<a href="#" class="h2">','</a>');
		echo '<div class="mainentry">';
		echo __( "Sorry, this theme is only meant for use on touch smartphones.", "wptouch" );
		echo '</div></div>';
		echo '' .get_footer() . '';
		echo '</body>';
	die; 
	} 
}

function wptouch_core_header_styles() {
	include('core-styles.php' );
}

function wptouch_agent($browser) {
$useragent = $_SERVER['HTTP_USER_AGENT'];
return stristr($useragent,$browser);
	}

function wptouch_twitter_link() {
	echo '<li><a href="javascript:(function(){var%20f=false,t=true,a=f,b=f,u=\'\',w=window,d=document,g=w.open(),p,linkArr=d.getElementsByTagName(\'link\');for(var%20i=0;i%3ClinkArr.length&&!a;i++){var%20l=linkArr[i];for(var%20x=0;x%3Cl.attributes.length;x++){if(l.attributes[x].nodeName.toLowerCase()==\'rel\'){p=l.attributes[x].nodeValue.split(\'%20\');for(y=0;y%3Cp.length;y++){if(p[y]==\'short_url\'||p[y]==\'shorturl\'||p[y]==\'shortlink\'){a=t;}}}if(l.attributes[x].nodeName.toLowerCase()==\'rev\'&&l.attributes[x].nodeValue==\'canonical\'){a=t;}if(a){u=l.href;}}}if(a){go(u);}else{var%20h=d.getElementsByTagName(\'head\')[0]||d.documentElement,s=d.createElement(\'script\');s.src=\'http://api.bit.ly/shorten?callback=bxtShCb&longUrl=\'+encodeURIComponent(window.location.href)+\'&version=2.0.1&login=amoebe&apiKey=R_60a24cf53d0d1913c5708ea73fa69684\';s.charSet=\'utf-8\';h.appendChild(s);}bxtShCb=function(data){var%20rs,r;for(r%20in%20data.results){rs=data.results[r];break;}go(rs[\'shortUrl\']);};function%20go(u){return%20g.document.location.href=(\'http://mobile.twitter.com/home/?status=\'+encodeURIComponent(document.title+\'%20\'+u));}})();" id="otweet"></a></li>';
}

function wptouch_facebook_link() {
	echo "<li><a href=\"javascript:var%20d=document,f='http://www.facebook.com/share',l=d.location,e=encodeURIComponent,p='.php?src=bm&v=4&i=1297484757&u='+e(l.href)+'&t='+e(d.title);1;try{if%20(!/^(.*\.)?facebook\.[^.]*$/.test(l.host))throw(0);share_internal_bookmarklet(p)}catch(z)%20{a=function()%20{if%20(!window.open(f+'r'+p,'sharer','toolbar=0,status=0,resizable=1,width=626,height=436'))l.href=f+p};if%20(/Firefox/.test(navigator.userAgent))setTimeout(a,0);else{a()}}void(0)\" id=\"facebook\"></a></li>";
}

function wptouch_thumb_reflections() {
if (wptouch_agent("iphone")  != FALSE || wptouch_agent("ipod")  != FALSE) {
		echo ".wptouch-post-thumb-wrap{ \n";
		echo "-webkit-box-reflect: below 1px -webkit-gradient(linear, left top, left bottom, from(transparent), color-stop(0.85, transparent), to(white));} \n";
	}
}

function wptouch_tags_link() {
		echo '<a href="#head-tags">' . __( "Tags", "wptouch" ) . '</a>';
	}

function wptouch_cats_link() {
		echo '<a href="#head-cats">' . __( "Categories", "wptouch" ) . '</a>';
}
  
function bnc_get_ordered_cat_list( $num ) {
	global $wpdb;

	if (  wptouch_excluded_cats() ) {
		$excluded_cats = wptouch_excluded_cats();
	} else {
		$excluded_cats = 0;	
	}

	echo '<ul>';
	$sql = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}term_taxonomy INNER JOIN {$wpdb->prefix}terms ON {$wpdb->prefix}term_taxonomy.term_id = {$wpdb->prefix}terms.term_id WHERE taxonomy = 'category' AND {$wpdb->prefix}term_taxonomy.term_id NOT IN ($excluded_cats) AND count >= 1 ORDER BY count DESC LIMIT 0, $num");

	if ( $sql ) {
		foreach ( $sql as $result ) {
			if ( $result ) {
				echo "<li><a href=\"" . get_category_link( $result->term_id ) . "\">" . $result->name . " <span>(" . $result->count . ")</span></a></li>";			
			}
		}
	}
	echo '</ul>';
}

function wptouch_ordered_tag_list( $num ) {
	global $wpdb;

	if (  wptouch_excluded_tags() ) {
		$excluded_tags =  wptouch_excluded_tags();
	} else {
		$excluded_tags = 0;	
	}

	echo '<ul>';
		
	$sql = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}term_taxonomy INNER JOIN {$wpdb->prefix}terms ON {$wpdb->prefix}term_taxonomy.term_id = {$wpdb->prefix}terms.term_id WHERE taxonomy = 'post_tag' AND {$wpdb->prefix}term_taxonomy.term_id NOT IN ($excluded_tags) AND count >= 1 ORDER BY count DESC LIMIT 0, $num");	

	if ( $sql ) {
		foreach ( $sql as $result ) {
			if ( $result ) {
				echo "<li><a href=\"" . get_tag_link( $result->term_id ) . "\">" . $result->name . " <span>(" . $result->count . ")</span></a></li>";			
			}
		}
	}
	echo '</ul>';
}

// WPtouch Core Body Functions

function wptouch_core_body_background() {
	$wptouch_settings = bnc_wptouch_get_settings();
	echo $wptouch_settings['style-background'];
  }
  
function wptouch_core_body_sitetitle() {  
	$str = bnc_get_header_title(); 
	echo stripslashes($str);  
  }

function wptouch_core_body_result_text() {  
	global $is_ajax; if (!$is_ajax) {
			if (is_search()) {
				echo sprintf( __("Search results &rsaquo; %s", "wptouch"), get_search_query() );
			} if (is_category()) {
				echo sprintf( __("Categories &rsaquo; %s", "wptouch"), single_cat_title("", false));
			} elseif (is_tag()) {
				echo sprintf( __("Tags &rsaquo; %s", "wptouch"), single_tag_title("", false));
			} elseif (is_day()) {
				echo sprintf( __("Archives &rsaquo; %s", "wptouch"),  get_the_time('F jS, Y'));
			} elseif (is_month()) {
				echo sprintf( __("Archives &rsaquo; %s", "wptouch"),  get_the_time('F, Y'));
			} elseif (is_year()) {
				echo sprintf( __("Archives &rsaquo; %s", "wptouch"),  get_the_time('Y'));
		}
	}
}

// WPtouch Core Footer Functions

function wptouch_core_else_text() {	
	 global $is_ajax; if (($is_ajax) && !is_search()) {
		echo '' . __( "No more entries to display.", "wptouch" ) . '';
	 } elseif (is_search() && ($is_ajax)) {
		echo '' . __( "No more search results to display.", "wptouch" ) . '';
	 } elseif (is_search() && (!$is_ajax)) {
	 	echo '<div style="padding-bottom:127px">' . __( "No search results results found.", "wptouch" ) . '<br />' . __( "Try another query.", "wptouch" ) . '</div>';
	 } else {
	  echo '<div class="post">
	  	<h2>' . __( "404 Not Found", "wptouch" ) . '</h2>
	  	<p>' . __( "The page or post you were looking for is missing or has been removed.", "wptouch" ) . '</p>
	  </div>';
	}
}

function wptouch_core_footer_switch_link() {	
	echo '<script type="text/javascript">function switch_delayer() { window.location = "' . get_bloginfo( 'url' ) . '/?wptouch_view=normal&wptouch_redirect_nonce=' . wp_create_nonce( 'wptouch_redirect' ) . '&wptouch_redirect=' . urlencode( $_SERVER['REQUEST_URI'] ) .'"}</script>';
	echo '' . __( "Mobile Theme", "wptouch" ) . ' <a id="switch-link" onclick="wptouch_switch_confirmation();" href="javascript:return false;"></a>';
}

// WPtouch Standard Functions
  
// Check if certain plugins are active
function wptouch_is_plugin_active($plugin_filename) {
	$plugins = get_option('active_plugins');
		if( !is_array($plugins) ) settype($plugins,'array');			
		return ( in_array($plugin_filename, $plugins) ) ;
}

//Filter out pingbacks and trackbacks
add_filter('get_comments_number', 'comment_count', 0);
function comment_count( $count ) {
	global $id;
	$comments = get_approved_comments($id);
	$comment_count = 0;
	foreach($comments as $comment){
		if($comment->comment_type == ""){
			$comment_count++;
		}
	}
	return $comment_count;
}

// Stop '0' comment counts in comment bubbles
function wptouch_get_comment_count() {
	global $wpdb;
	global $post;
	
	$sql = $wpdb->prepare( "SELECT count(*) AS c FROM {$wpdb->comments} WHERE comment_type = '' AND comment_approved = 1 AND comment_post_ID = %d", $post->ID );
	$result = $wpdb->get_row( $sql );
	if ( $result ) {
		return $result->c;
	} else {
		return 0;	
	}
}

// Add 'Delete | Spam' links in comments for logged in admins
 function wptouch_moderate_comment_link( $id ) {  
	  if ( current_user_can( 'edit_post' ) ) {  
     echo '<a href="' . admin_url("comment.php?action=editcomment&c=$id") . '">' . __('edit') . '</a>';  
     echo '<a href="' . admin_url("comment.php?action=cdc&c=$id") . '">' . __('del') . '</a>';  
     echo '<a href="' . admin_url("comment.php?action=cdc&dt=spam&c=$id") . '">' . __('spam') . '</a>';  
   }  
 }
 

function wptouch_thumbnail_size( $size ) {
	$size = 'thumbnail';
	return $size;
}

function wptouch_idevice_classes() {
	$iPhone = strstr( $_SERVER['HTTP_USER_AGENT'], 'iPhone' );
	$iPod = strstr( $_SERVER['HTTP_USER_AGENT'], 'iPod' );
	$iOS5 = strstr( $_SERVER['HTTP_USER_AGENT'], 'OS 5_0' );

	if ( $iPhone || $iPod ) {
		echo 'idevice';
	}
	
	if ( $iOS5 && wptouch_use_fixed_header() ) { 
		echo ' ios5';
	}
	
}

// Remove the admin bar when logged in and looking at WPtouch
if ( bnc_wptouch_is_mobile() && function_exists( 'show_admin_bar' ) ) {
	add_filter( 'show_admin_bar', '__return_false' );
}

// WPtouch Filters

add_filter( 'post_thumbnail_size', 'wptouch_thumbnail_size' );
remove_action('wp_head', 'gigpress_head');
remove_filter('the_excerpt', 'do_shortcode');   
remove_filter('the_content', 'do_shortcode');
remove_action( 'wp_default_scripts', array( 'JCP_UseGoogleLibraries', 'replace_default_scripts_action'), 1000);
remove_filter('the_content', 'sociable_display_hook');
remove_filter('the_excerpt', 'sociable_display_hook');
remove_filter('the_content', 'whydowork_adsense_filter', 100);
remove_filter('the_excerpt', 'whydowork_adsense_filter', 100);

// Facebook Like button
remove_filter('the_content', 'Add_Like_Button');

//Sharebar Plugin
remove_filter('the_content', 'sharebar_auto');
remove_action('wp_head', 'sharebar_header');
