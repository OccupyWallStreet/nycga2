<?php

// Custom Menus
function register_main_menus() {
	register_nav_menus(
		array(
			'primary-nav' => __( 'Primary Nav' ),
			'secondary-nav' => __( 'Secondary Nav' ),
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

		wp_enqueue_script('jquery', get_bloginfo('template_url').'/includes/js/jquery.min.js', false, '1.7.2');
        wp_enqueue_script('jquery-ui', get_template_directory_uri().'/includes/js/jquery-ui-1.8.5.custom.min.js', false, '1.8.5');
		wp_enqueue_script('jquery-superfish', get_bloginfo('template_url').'/includes/js/superfish.js', false, '1.4.2');
        wp_enqueue_script('jquery-slider', get_bloginfo('template_url').'/includes/js/slides.min.jquery.js', false, '1.1.9');
		wp_enqueue_script('jquery-fancybox', get_bloginfo('template_url').'/includes/fancybox/jquery.fancybox.js', false, '2.0.4');
        wp_enqueue_script('jquery-custom', get_bloginfo('template_url').'/includes/js/custom.js', false, '1.4.2');
        wp_enqueue_script('html5', get_bloginfo('template_url').'/includes/js/html5.js', false, '1.0');

		if ( is_singular() && get_option('thread_comments') ) wp_enqueue_script( 'comment-reply' );
}

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

// Exclude Pages from Search Results
function tj_exclude_pages($query) {
        if ($query->is_search) {
        $query->set('post_type', 'post');
                                }
        return $query;
}
add_filter('pre_get_posts','tj_exclude_pages');

// Twitter Widget

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
	  
	  $showposts = 5;
	  $showposts = !empty($showposts) ? $showposts : 5;
	  
	  $args=array(
	    'tag__in' => $tagIDs,
	    'post__not_in' => array($post->ID),
	    'showposts'=>$showposts,
	    'caller_get_posts'=>1
	  );
	  $my_query = new WP_Query($args);
	  if( $my_query->have_posts() ) { $related_post_found = true; ?>
		<h3><?php _e('Related Posts', 'themejunkie'); ?></h3>
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
		<h3><?php _e('Recent Posts', 'themejunkie'); ?></h3>
		<ul>
		<?php
		$posts = get_posts('numberposts=5&offset=0');
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
	$url = sprintf('%s?p=%s', get_bloginfo('url').'/', $id);

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

		array( "name" => "Twitter",
			'icon' => 'twitter.png',
			'class' => 'twitter_icon',
			'url' => tj_the_tweet_link(),
		),
		
		array( "name" => "Facebook",
			'icon' => 'facebook-logo-square.png',
			'class' => 'facebook_icon',
			'url' => 'http://www.facebook.com/share.php?u=PERMALINK&amp;t=TITLE',
		),

	    array( "name" => "Stumble",
		    'icon' => 'stumbleupon.png',
			'class' => 'stumbleupon_icon',
		    'url' => 'http://www.stumbleupon.com/submit?url=PERMALINK&amp;title=TITLE',
		),

		array( "name" => "Digg",
			'icon' => 'digg-logo.png',
			'class' => 'digg_icon',
			'url' => 'http://digg.com/submit?phase=2&amp;url=PERMALINK&amp;title=TITLE&amp;bodytext=EXCERPT',
		),

		array( "name" => "Del.icio.us",
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

	return '<ul>'.$output.'</ul>';
}



/* Auto Thumb */

// This is for AJAX fetching of the auto thumbnail preview on the admin post add/edit screens
if(isset($_POST['tj_autothumb']) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
  header('Content-type: text/plain');
  die(fetch_video_thumbnail_url(stripslashes($_POST['tj_autothumb'])));
}
//Add Thumb Style
add_action('admin_head', 'myposttype_admin_css');
function myposttype_admin_css() {
 		echo '<link type="text/css" rel="stylesheet" href="'.get_bloginfo( 'template_directory').'/thumb/thumb-style.css" media="screen" />';
}
add_action('admin_head-post-new.php', 'tj_newpost_head', 100);
add_action('admin_head-post.php', 'tj_newpost_head', 100);
function tj_newpost_head() {
?>
  <style type="text/css">
    #tj_autothumb_preview {
        display: none;
        margin: 0
    }
    #tj_autothumb_preview img {
        display: block;
        margin-bottom: 15px
    }
    #tj_autothumb_preview .howto {
        color: #999999;
        font-size: 13px;
        font-style: normal;
    }
    #tj_autothumb_preview .howto strong{
        color: #333333;
    }
  </style>
  <script type="text/javascript">
    jQuery(function($){
      $('<p id="tj_autothumb_preview"><img src=""/><small class="howto"><?php _e('<strong>Automatic Thumbnail</strong><br/>1. The automatic thumbnail function only works for videos from <strong>YouTube</strong> and <strong>Vimeo</strong>.<br/>2. The automatic thumbnail is used when you do not manually upload a Featured Image yourself.', 'themejunkie') ?></small></p>').insertAfter($('#tj_video_embed').parents('table.form-table'));
      $('#tj_autothumb_preview img').load(function(){$('#tj_autothumb_preview').animate({height: 'show', opacity: 'show'}, 500)});
      $('#tj_video_embed').bind('input', function(){
        if('' != (val = $.trim($(this).val())))
          $.ajax({
            type: 'post',
            data: {tj_autothumb:val},
            complete: function(xhr,status){
              if('' != (response = $.trim(xhr.responseText))){
                $('#tj_autothumb_preview img').attr('src', '<?php bloginfo('stylesheet_directory') ?>/functions/thumb.php?src=' + encodeURIComponent(response) + '&w=216&h=120&zc=1');
                $('#tj_video_img_url').val('<?php bloginfo('stylesheet_directory') ?>/functions/thumb.php?src=' + encodeURIComponent(response) + '&w=216&h=120&zc=1');
              }else{
                $('#tj_autothumb_preview').animate({height: 'hide', opacity: 'hide'}, 500, function(){$('#tj_autothumb_preview img').removeAttr('src')});
              }
             }
          });
        else
          $('#tj_autothumb_preview').animate({height: 'hide', opacity: 'hide'}, 500, function(){$('#tj_autothumb_preview img').removeAttr('src')});
      }).triggerHandler('input');
    });
  </script><?php
}
function fetch_video_thumbnail_url($input) {
    $input = htmlspecialchars_decode(trim((stripos($input, '<iframe') !== false || stripos($input, '<embed') !== false) && preg_match('#src="([^"]+)"#i', $input, $match) ? $match[1] : $input), ENT_QUOTES);
    $out = false;
    if(filter_var($input, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) !== false && false !== ($url_parts=parse_url($input)) && (stripos($url_parts['host'], 'youtube.com') !== false || stripos($url_parts['host'], 'youtu.be') !== false || stripos($url_parts['host'], 'vimeo.com') !== false)) {
        $url_query = array();if(isset($url_parts['query']))parse_str($url_parts['query'],$url_query);
        $id = isset($url_query['v']) ? $url_query['v'] : (isset($url_query['clip_id']) ? $url_query['clip_id'] : reset(explode('?', end(array_filter(explode('/', $input))))));

    if(stripos($url_parts['host'], 'youtube.com') !== false || stripos($url_parts['host'], 'youtu.be') !== false) {
        if(false !== ($contents = @file_get_contents("http://gdata.youtube.com/feeds/api/videos/$id?v=2&alt=jsonc"))) {
            $obj = json_decode($contents, true);
            $out = $obj['data']['thumbnail']['hqDefault'];
        }
    }elseif(stripos($url_parts['host'], 'vimeo.com') !== false) {
        if(false !== ($contents = @file_get_contents("http://vimeo.com/api/v2/video/$id.php"))) {
            $obj = unserialize($contents);
            $out = $obj[0]['thumbnail_large'];
        }
        }
    }
    return $out;
}
function _remove_script_version( $src ){
	$parts = explode( '?', $src );
	return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );



?>