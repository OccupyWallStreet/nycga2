<?php

add_theme_support( 'automatic-feed-links' );
add_editor_style();
//add_custom_image_header();

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 630;

// Custom Menus
function register_main_menus() {
	register_nav_menus(
		array(
			'primary-nav' => __( 'Primary Nav','themejunkie' ),
			'secondary-nav' => __( 'Secondary Nav','themejunkie' ),
		)
	);
}

if (function_exists('register_nav_menus')) add_action( 'init', 'register_main_menus' );

// Register and deregister Scripts files	
if(!is_admin()) {
	add_action( 'wp_print_scripts', 'my_deregister_scripts', 100 );
}

function my_deregister_scripts() {
		wp_deregister_script( 'jquery' );
        wp_register_script('jquery-lazyload', get_template_directory_uri() . '/includes/js/jquery.lazyload.js', 'jquery');

		wp_enqueue_script('jquery', get_template_directory_uri().'/includes/js/jquery.min.js', false, '1.6.4');
		wp_enqueue_script('jquery-superfish', get_template_directory_uri().'/includes/js/superfish.js', false, '1.4.2');
		wp_enqueue_script('jquery-custom', get_template_directory_uri().'/includes/js/custom.js', false, '1.4.2');
        wp_enqueue_script('jquery-quicksand', get_template_directory_uri().'/includes/js/jquery.quicksand.js', false, '1.2.2');
        wp_enqueue_script('jquery-ui', get_template_directory_uri().'/includes/js/jquery-ui-1.8.5.custom.min.js', false, '1.8.5');
        wp_enqueue_script('jquery-easing', get_template_directory_uri().'/includes/js/jquery.easing.1.3.js', false, '1.3');
        wp_enqueue_script('jquery-prettyPhoto', get_template_directory_uri().'/includes/js/prettyPhoto.js', false, '1.4.2');
        wp_enqueue_script('jquery-slides', get_template_directory_uri().'/includes/js/slides.min.jquery.js', false, '1.1.9');
        wp_enqueue_script('jquery-lazyload');

		if ( is_singular() && get_option('thread_comments') ) wp_enqueue_script( 'comment-reply' );
}

// Register and deregister Style
function my_custom_styles(){
        wp_register_style( 'prettyPhoto', get_template_directory_uri() . '/includes/css/prettyPhoto.css' );
		wp_enqueue_style( 'prettyPhoto' );
}
add_action('init', 'my_custom_styles');

// Exclude Pages from Search Results
function tj_exclude_pages($query) {
        if ($query->is_search) {
        $query->set('post_type', 'post');
                                }
        return $query;
}
add_filter('pre_get_posts','tj_exclude_pages');

// Get limit excerpt
function tj_content_limit($max_char, $more_link_text = '', $stripteaser = 0, $more_file = '') {
    $content = get_the_content($more_link_text, $stripteaser, $more_file);
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    $content = strip_tags($content);

   if (strlen($_GET['p']) > 0) {
      echo "";
      echo $content;
      echo " ...";
   }
   else if ((strlen($content)>$max_char) && ($espacio = strpos($content, " ", $max_char ))) {
        $content = substr($content, 0, $espacio);
        $content = $content;
        echo "";
        echo $content;
        echo " ...";
   }
   else {
      echo "";
      echo $content;
   }
}

// Tabber: Get Most Popular Posts
function tj_tabs_popular( $posts = 5 ) {
	$popular = new WP_Query('orderby=comment_count&posts_per_page='.$posts);
	while ($popular->have_posts()) : $popular->the_post();
?>
	<li>
		<?php the_post_thumbnail('tabber-thumb', array('class' => 'tab-thumb')); ?>
	 	<div class="info">
	 	<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
		<span class="meta"><abbr title="<?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?>"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . __(' ago', 'themejunkie'); ?></abbr> <span class="entry-comment"><?php comments_popup_link( __( '0', 'themejunkie' ), __( '1', 'themejunkie' ), __( '%', 'themejunkie' ) ); ?></span></span></span>
		</div> <!--end .info-->
		<div class="clear"></div>
	</li>
<?php endwhile; 
}

function tj_tabs_latest( $posts = 5 ) {
	$the_query = new WP_Query('showposts='. $posts .'&orderby=post_date&order=desc');	
	while ($the_query->have_posts()) : $the_query->the_post(); 
?>
	<li>
		<?php the_post_thumbnail('tabber-thumb', array('class' => 'tab-thumb')); ?>
		<div class="info">
		<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
		<span class="meta"><abbr title="<?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?>"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . __(' ago', 'themejunkie'); ?></abbr> <span class="entry-comment"><?php comments_popup_link( __( '0', 'themejunkie' ), __( '1', 'themejunkie' ), __( '%', 'themejunkie' ) ); ?></span></span></span>
		</div> <!--end .info-->
		<div class="clear"></div>
	</li>
<?php endwhile; 
}

// Tabber: Get Recent Comments
function tj_tabs_comments( $posts = 5, $size = 35 ) {
	global $wpdb;
	$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,
	comment_post_ID, comment_author, comment_author_email, comment_date_gmt, comment_approved,
	comment_type,comment_author_url,
	SUBSTRING(comment_content,1,65) AS com_excerpt
	FROM $wpdb->comments
	LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
	$wpdb->posts.ID)
	WHERE comment_approved = '1' AND comment_type = '' AND
	post_password = ''
	ORDER BY comment_date_gmt DESC LIMIT ".$posts;
	
	$comments = $wpdb->get_results($sql);
	
	foreach ($comments as $comment) {
	?>
	<li>
		<?php echo get_avatar( $comment, $size ); ?>
	
		<a href="<?php echo get_permalink($comment->ID); ?>#comment-<?php echo $comment->comment_ID; ?>" title="<?php _e('on ', 'themejunkie'); ?> <?php echo $comment->post_title; ?>">
			<?php echo strip_tags($comment->comment_author); ?>: <?php echo strip_tags($comment->com_excerpt); ?>...
		</a>
		<div class="clear"></div>
	</li>
	<?php 
	}
}

if ( !function_exists( 'tj_twitter_script') ) {
	function tj_twitter_script($unique_id,$username,$limit) {
	?>
	<script type="text/javascript">
	<!--//--><![CDATA[//><!--
	
	    function twitterCallback2(twitters) {
	    
	      var statusHTML = [];
	      for (var i=0; i<twitters.length; i++){
	        var username = twitters[i].user.screen_name;
	        var status = twitters[i].text.replace(/((https?|s?ftp|ssh)\:\/\/[^"\s\<\>]*[^.,;'">\:\s\<\>\)\]\!])/g, function(url) {
	          return '<a href="'+url+'">'+url+'</a>';
	        }).replace(/\B@([_a-z0-9]+)/ig, function(reply) {
	          return  reply.charAt(0)+'<a href="http://twitter.com/'+reply.substring(1)+'">'+reply.substring(1)+'</a>';
	        });
	        statusHTML.push( '<li><span class="content">'+status+'</span> <a style="font-size:85%" class="time" href="http://twitter.com/'+username+'/statuses/'+twitters[i].id_str+'">'+relative_time(twitters[i].created_at)+'</a></li>' );
	      }
	      document.getElementById( 'twitter_update_list_<?php echo $unique_id; ?>').innerHTML = statusHTML.join( '' );
	    }
	    
	    function relative_time(time_value) {
	      var values = time_value.split( " " );
	      time_value = values[1] + " " + values[2] + ", " + values[5] + " " + values[3];
	      var parsed_date = Date.parse(time_value);
	      var relative_to = (arguments.length > 1) ? arguments[1] : new Date();
	      var delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
	      delta = delta + (relative_to.getTimezoneOffset() * 60);
	    
	      if (delta < 60) {
	        return 'less than a minute ago';
	      } else if(delta < 120) {
	        return 'about a minute ago';
	      } else if(delta < (60*60)) {
	        return (parseInt(delta / 60)).toString() + ' minutes ago';
	      } else if(delta < (120*60)) {
	        return 'about an hour ago';
	      } else if(delta < (24*60*60)) {
	        return 'about ' + (parseInt(delta / 3600)).toString() + ' hours ago';
	      } else if(delta < (48*60*60)) {
	        return '1 day ago';
	      } else {
	        return (parseInt(delta / 86400)).toString() + ' days ago';
	      }
	    }
	//-->!]]>
	</script>
	<script type="text/javascript" src="http://api.twitter.com/1/statuses/user_timeline/<?php echo $username; ?>.json?callback=twitterCallback2&amp;count=<?php echo $limit; ?>&amp;include_rts=t"></script>
	<?php
	}
}

/* Related Posts */
function tj_related_posts() {
	global $post, $wpdb;
	$backup = $post;  // backup the current object
	$tags = wp_get_post_tags($post->ID);
	$tagIDs = array();
	if ($tags) {
	  $tagcount = count($tags);
	  for ($i = 0; $i < $tagcount; $i++) {
	    $tagIDs[$i] = $tags[$i]->term_id;
	  }
	  
	  $showposts = get_theme_mod('related_postnum');
	  $showposts = !empty($showposts) ? $showposts : 5;
	  
	  $args=array(
	    'tag__in' => $tagIDs,
	    'post__not_in' => array($post->ID),
	    'showposts'=>$showposts,
	    'caller_get_posts'=>1
	  );
	  $my_query = new WP_Query($args);
	  if( $my_query->have_posts() ) { $related_post_found = true; ?>
		<h3><?php _e('Related Articles', 'themejunkie'); ?></h3>
			<ul>		
	    <?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
				<li>
					<a class="title" href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a>
				</li>				
	    <?php endwhile; ?>
			</ul>		
	  <?php }
	}
	
	//show recent posts if no related found
	if(!$related_post_found){ ?>
		<h3><?php _e('Recent Articles', 'themejunkie'); ?></h3>
		<ul>
		<?php
		$posts = get_posts('numberposts='.$showposts.'&offset=0');
		foreach($posts as $post) { ?>
			<li>
				<a class="title" href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a>
			</li>
		<?php } ?>
		</ul>
		
		<?php 
	}
	wp_reset_query();
}

/* Twitter Widget */

function tj_save_tweet_link($id) {
	$url = sprintf('%s?p=%s', home_url().'/', $id);

	add_post_meta($id, 'tweet_trim_url_2', $url);
	
	return $url;
}

function tj_the_tweet_link() {
	if (!$url = get_post_meta(get_the_ID(), 'tweet_trim_url_2', true)) {
	  $url = tj_save_tweet_link(get_the_ID());
	}
	
	if ($old_url = get_post_meta(get_the_ID(), 'tweet_trim_url', true)) {
	  delete_post_meta(get_the_ID(), 'tweet_trim_url');
	}
	
	$output_url = sprintf(
	  'http://twitter.com/home?status=%s%s%s',
	  urlencode(get_the_title()),
	  urlencode(' - '),
	  $url
	);
	$output_url = str_replace('+','%20',$output_url);
	return $output_url;
}

/* Social Connections */
function tj_social_bookmarks() {
	global $wp_query, $post;
	
	$sociable_sites = array (

		array( "name" => "Retweet this post on Twitter",
			'icon' => 'twitter.png',
			'class' => 'twitter_icon',
			'url' => tj_the_tweet_link(),
		),
		
		array( "name" => "Like this post on Facebook",
			'icon' => 'facebook-logo-square.png',
			'class' => 'facebook_icon',
			'url' => 'http://www.facebook.com/share.php?u=PERMALINK&amp;t=TITLE',
		),

	    array( "name" => "StumbleUpon this post",
		    'icon' => 'stumbleupon.png',
			'class' => 'stumbleupon_icon',
		    'url' => 'http://www.stumbleupon.com/submit?url=PERMALINK&amp;title=TITLE',
		),

		array( "name" => "Digg this post",
			'icon' => 'digg-logo.png',
			'class' => 'digg_icon',
			'url' => 'http://digg.com/submit?phase=2&amp;url=PERMALINK&amp;title=TITLE&amp;bodytext=EXCERPT',
		),

		array( "name" => "Bookmark on del.icio.us",
			'icon' => 'delicious.png',
			'class' => 'delicious_icon',
			'url' => 'http://delicious.com/post?url=PERMALINK&amp;title=TITLE&amp;notes=EXCERPT',
		),
		
	);
	
	// Load the post's and blog's data
	$blogname = urlencode(get_bloginfo('name')." ".get_bloginfo('description'));
	$post = $wp_query->post;
	
	
	// Grab the excerpt, if there is no excerpt, create one
	$excerpt = urlencode(strip_tags(strip_shortcodes($post->post_excerpt)));
	if ($excerpt == "") {
		$excerpt = urlencode(substr(strip_tags(strip_shortcodes($post->post_content)),0,250));
	}
	
	// Clean the excerpt for use with links
	$excerpt = str_replace('+','%20',$excerpt);
	$excerpt = str_replace('%0D%0A','',$excerpt);
	$permalink 	= urlencode(get_permalink($post->ID));
	$title = str_replace('+','%20',urlencode($post->post_title));
	
	foreach($sociable_sites as $bookmark) {	
		$url = $bookmark['url'];
		$url = str_replace('TITLE', $title, $url);
		$url = str_replace('BLOGNAME', $blogname, $url);
		$url = str_replace('EXCERPT', $excerpt, $url);
		$url = str_replace('PERMALINK', $permalink, $url);
		
		$output .= '<li class="' .$bookmark['class']. '">';
		$output .= '<a title="' .$bookmark['name']. '" href="' .$url. '">';
		$output .= $bookmark['name'].'</a>';
		$output .= '</li>';
	}

	return '<ul class="clear">'.$output.'</ul>';
}

/* Add custom type RSS*/
function myfeed_request($qv) {
	    if (isset($qv['feed']))
	        $qv['post_type'] = get_post_types();
	    return $qv;
	}
add_filter('request', 'myfeed_request');

/* Get Author's info */
function tj_get_users($users_per_page = 10, $paged = 1, $role = '', $orderby = 'login', $order = 'ASC', $usersearch = '' ) {

	global $blog_id;

	$args = array(
			'number' => $users_per_page,
			'offset' => ( $paged-1 ) * $users_per_page,
			'role' => $role,
			'search' => $usersearch,
			'fields' => 'all_with_meta',
			'blog_id' => $blog_id,
			'orderby' => $orderby,
			'order' => $order
		);


	//Query the user IDs for this page
	$wp_user_search = new WP_User_Query( $args );

	$user_results = $wp_user_search->get_results();
	// $wp_user_search->get_total()

	return $user_results;

}

?>