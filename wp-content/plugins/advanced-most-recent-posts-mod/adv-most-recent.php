<?php
/*
Plugin Name: Advanced Most Recent Posts Mod
Plugin URI: http://trepmal.com/plugins/advanced-most-recent-posts-mod/
Description: Display most recent posts from selected categories or current category or all posts with thumbnail images (optional).
Version: 1.6.5.2
Author: Kailey Lampert
Author URI: http://kaileylampert.com
*/

class yg_recent_posts extends WP_Widget {

	function yg_recent_posts() {
		//Load Language
		load_plugin_textdomain( 'adv-recent-posts', false, dirname( plugin_basename( __FILE__ ) ) .  '/lang' );
		$widget_ops = array( 'description' => __( 'Shows most recent posts. You can customize it easily.', 'adv-recent-posts' ) );
		//Create widget
		$this->WP_Widget( 'advancedrecentposts', __( 'Advanced Recent Posts', 'adv-recent-posts' ), $widget_ops );
	}

	function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );
		echo $before_widget;
		$title = empty( $instance[ 'title' ] ) ? '' : apply_filters( 'widget_title', $instance[ 'title' ] );
		$link = empty( $instance[ 'link' ]) ? '' : $instance[ 'link' ];
		$parameters = array(
				'title' 	=> $title,
				'link' 		=> $instance[ 'link' ],
				'hideposttitle' => $instance[ 'hideposttitle' ],
				'separator' => $instance[ 'separator' ],
				'afterexcerpt' => $instance[ 'afterexcerpt' ],
				'afterexcerptlink' => $instance[ 'afterexcerptlink' ],
				'show_type' => $instance[ 'show_type' ],
				'shownum' 	=> (int) $instance[ 'shownum' ],
				'postoffset' => (int) $instance[ 'postoffset' ],
				'reverseorder' => (int) $instance[ 'reverseorder' ],
				'excerpt' 	=> (int) $instance[ 'excerpt' ],
				'excerptlengthwords' => (int) $instance[ 'excerptlengthwords' ],
				'actcat' 	=> (bool) $instance[ 'actcat' ],
				'cats' 		=> esc_attr( $instance[ 'cats' ] ),
				'cusfield' 	=> esc_attr( $instance[ 'cusfield' ] ),
				'w' 		=> (int) $instance[ 'width' ],
				'h' 		=> (int) $instance[ 'height' ],
				'firstimage' => (bool) $instance[ 'firstimage' ],
				'atimage' 	=>(bool) $instance[ 'atimage' ],
				'defimage' 	=> esc_url( $instance[ 'defimage' ] ),
				'showauthor' => (bool) $instance[ 'showauthor' ],
				'showtime' 	=> (bool) $instance[ 'showtime' ],
				'format' 	=> esc_attr( $instance[ 'format' ] ),
				'spot' 		=> esc_attr( $instance[ 'spot' ] ),
			);

		if ( !empty( $title ) &&  !empty( $link ) ) {
				echo $before_title . '<a href="' . $link . '">' . $title . '</a>' . $after_title;
		}
		else if ( !empty( $title ) ) {
			 echo $before_title . $title . $after_title;
		}
        //print recent posts
		yg_recentposts($parameters);
		echo $after_widget;

  } //end of widget()
	
	//Update widget options
  function update($new_instance, $old_instance) {

		$instance = $old_instance;
		//get old variables
		$instance['title'] = esc_attr($new_instance['title']);
		$instance['link'] = esc_attr($new_instance['link']);
		$instance['hideposttitle'] = $new_instance['hideposttitle'] ? 1 : 0;
		$instance['separator'] = $new_instance['separator'];
		$instance['afterexcerpt'] = $new_instance['afterexcerpt'];
		$instance['afterexcerptlink'] = $new_instance['afterexcerptlink'] ? 1 : 0;
		$instance['show_type'] = $new_instance['show_type'];

		$instance['shownum'] = isset($new_instance['show-num']) ? (int) abs($new_instance['show-num']) : (int) abs($new_instance['shownum']);
	//	if ($instance['shownum'] > 20) $instance['shownum'] = 20;
		unset($instance['show-num']);

		$instance['postoffset'] = (int) abs($new_instance['postoffset']);
		$instance['reverseorder'] = $new_instance['reverseorder'] ? 1 : 0;

		$instance['excerpt'] = isset($new_instance['excerpt-length']) ? (int) abs($new_instance['excerpt-length']) : (int) abs($new_instance['excerpt']);
		unset($instance['excerpt-length']);

		$instance['excerptlengthwords'] = (int) abs($new_instance['excerptlengthwords']);

		$instance['cats'] = esc_attr($new_instance['cats']);
		$instance['actcat'] = $new_instance['actcat'] ? 1 : 0;

		$instance['cusfield'] = isset($new_instance['cus-field']) ? esc_attr($new_instance['cus-field']) : esc_attr($new_instance['cusfield']);
		unset($instance['cus-field']);

		$instance['width'] = esc_attr($new_instance['width']);
		$instance['height'] = esc_attr($new_instance['height']);
		$instance['firstimage'] = $new_instance['first-image'] ? 1 : 0;
		$instance['atimage'] = $new_instance['atimage'] ? 1 : 0;
		$instance['defimage'] = esc_url($new_instance['def-image']);
		$instance['showauthor'] = $new_instance['showauthor'] ? 1 : 0;
		$instance['showtime'] = $new_instance['showtime'] ? 1 : 0;
		$instance['format'] = esc_attr($new_instance['format']);
 		$instance['spot'] = esc_attr($new_instance['spot']);
 		unset($instance['spot1']);
 		unset($instance['spot2']);
 		unset($instance['spot3']);
//die($new_instance['spot']);
		return $instance;
 
	} //end of update()
	
	//Widget options form
  function form($instance) {

		if (isset($instance['spot1'])) $instance['spot'] = $instance['spot1'];
		if (isset($instance['show-num'])) $instance['shownum'] = $instance['show-num'];
		if (isset($instance['excerpt-length'])) $instance['excerpt'] = $instance['excerpt-length'];
		if (isset($instance['cus-field'])) $instance['cusfield'] = $instance['cus-field'];

		$instance = wp_parse_args( (array) $instance, yg_recentposts_defaults() );
		
		$title 		= esc_attr($instance['title']);
		$link 		= esc_attr($instance['link']);
		$hideposttitle = $instance['hideposttitle'];
		$separator 	= $instance['separator'];
		$afterexcerpt = $instance['afterexcerpt'];
		$afterexcerptlink = $instance['afterexcerptlink'];
		$show_type 	= $instance['show_type'];
		$shownum 	= (int) $instance['shownum'];
		$postoffset	= (int) $instance['postoffset'];
		$reverseorder = $instance['reverseorder'];
		$excerpt = (int) $instance['excerpt'];
		$excerptlengthwords = (int) $instance['excerptlengthwords'];
		$cats 		= esc_attr($instance['cats']);
		$actcat 	= (bool) $instance['actcat'];
		$cus_field 	= esc_attr($instance['cusfield']);
		$width 		= esc_attr($instance['width']);
		$height 	= esc_attr($instance['height']);
		$firstimage	= (bool) $instance['firstimage'];
		$atimage 	= (bool) $instance['atimage'];
		$defimage 	= esc_url($instance['defimage']);
		$showauthor	= (bool) $instance['showauthor'];
		$showtime 	= (bool) $instance['showtime'];
		$format 	= esc_attr($instance['format']);
		$spot 		= esc_attr($instance['spot']);
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' );?> 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Title Link:');?> 
				<input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo $link; ?>" />
			</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hideposttitle'); ?>" name="<?php echo $this->get_field_name('hideposttitle'); ?>"<?php checked( $hideposttitle ); ?> />
			<label for="<?php echo $this->get_field_id('hideposttitle'); ?>"><?php _e('Hide post title?', 'adv-recent-posts');?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('separator'); ?>"><?php _e('Separator:');?> 
				<input class="widefat" id="<?php echo $this->get_field_id('separator'); ?>" name="<?php echo $this->get_field_name('separator'); ?>" type="text" value="<?php echo $separator; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('afterexcerpt'); ?>"><?php _e('After Excerpt:');?> 
				<input class="widefat" id="<?php echo $this->get_field_id('afterexcerpt'); ?>" name="<?php echo $this->get_field_name('afterexcerpt'); ?>" type="text" value="<?php echo $afterexcerpt; ?>" />
			</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('afterexcerptlink'); ?>" name="<?php echo $this->get_field_name('afterexcerptlink'); ?>"<?php checked( $afterexcerptlink ); ?> />
			<label for="<?php echo $this->get_field_id('afterexcerptlink'); ?>"><?php _e('Link \'After Expcerpt\' to post?', 'adv-recent-posts');?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('show_type'); ?>"><?php _e('Show:');?> 
				<select class="widefat" id="<?php echo $this->get_field_id('show_type'); ?>" name="<?php echo $this->get_field_name('show_type'); ?>">
				<?php
					global $wp_post_types;
					foreach($wp_post_types as $k=>$pt) {
						if($pt->exclude_from_search) continue;
						echo '<option value="' . $k . '"' . selected($k,$show_type,true) . '>' . $pt->labels->name . '</option>';
					}
				?>
				</select>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('shownum'); ?>"><?php _e('Number of posts to show:');?> 
				<input id="<?php echo $this->get_field_id('shownum'); ?>" name="<?php echo $this->get_field_name('shownum'); ?>" type="text" value="<?php echo $shownum; ?>" size ="3" /><br />
				<small><?php _e('(at most 20)','adv-recent-posts'); ?></small>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('postoffset'); ?>"><?php _e('Number of Posts to skip:');?> 
				<input id="<?php echo $this->get_field_id('postoffset'); ?>" name="<?php echo $this->get_field_name('postoffset'); ?>" type="text" value="<?php echo $postoffset; ?>" size ="3" /><br />
				<small><?php _e('(e.g. "1" will skip the most recent post)','adv-recent-posts'); ?></small>
			</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('reverseorder'); ?>" name="<?php echo $this->get_field_name('reverseorder'); ?>"<?php checked( $reverseorder ); ?> />
			<label for="<?php echo $this->get_field_id('reverseorder'); ?>"><?php _e('Show posts in reverse order?', 'adv-recent-posts');?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('excerpt-length'); ?>"><?php _e('Excerpt length (letters):', 'adv-recent-posts');?> 
				<input id="<?php echo $this->get_field_id('excerpt-length'); ?>" name="<?php echo $this->get_field_name('excerpt-length'); ?>" type="text" value="<?php echo $excerpt; ?>" size ="3" /><br />
				<small>(<?php _e('0 - Don\'t show excerpt', 'adv-recent-posts');?>)</small>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('excerptlengthwords'); ?>"><?php _e('Excerpt length (words):', 'adv-recent-posts');?> 
				<input id="<?php echo $this->get_field_id('excerptlengthwords'); ?>" name="<?php echo $this->get_field_name('excerptlengthwords'); ?>" type="text" value="<?php echo $excerptlengthwords; ?>" size ="3" /><br />
				<small>(<?php _e('0 - Use letter-excerpt', 'adv-recent-posts');?>)</small>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('cus-field'); ?>"><?php _e('Thumbnail Custom Field Name:', 'adv-recent-posts');?> 
				<input id="<?php echo $this->get_field_id('cus-field'); ?>" name="<?php echo $this->get_field_name('cus-field'); ?>" type="text" value="<?php echo $cus_field; ?>" size ="20" /> 
			</label><br />
		 	<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:', 'adv-recent-posts');?> <input id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" size ="3" /></label>px<br />
			<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:', 'adv-recent-posts');?> <input id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" size ="3" /></label>px
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('first-image'); ?>" name="<?php echo $this->get_field_name('first-image'); ?>"<?php checked( $firstimage ); ?> />
			<label for="<?php echo $this->get_field_id('first-image'); ?>"><?php _e('Get first image of post', 'adv-recent-posts');?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('atimage'); ?>" name="<?php echo $this->get_field_name('atimage'); ?>"<?php checked( $atimage ); ?> />
			<label for="<?php echo $this->get_field_id('atimage'); ?>"><?php _e('Get first attached image of post', 'adv-recent-posts');?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('def-image'); ?>"><?php _e('Default image:', 'adv-recent-posts');?> 
				<input class="widefat" id="<?php echo $this->get_field_id('def-image'); ?>" name="<?php echo $this->get_field_name('def-image'); ?>" type="text" value="<?php echo $defimage; ?>" /><br />
				<small>(<?php _e('if there is no thumbnail, use this', 'adv-recent-posts');?></small>
			</label>
		</p>	
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('showauthor'); ?>" name="<?php echo $this->get_field_name('showauthor'); ?>"<?php checked( $showauthor ); ?> />
			<label for="<?php echo $this->get_field_id('showauthor'); ?>"><?php _e('Show Author', 'adv-recent-posts');?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('showtime'); ?>" name="<?php echo $this->get_field_name('showtime'); ?>"<?php checked( $showtime ); ?> />
			<label for="<?php echo $this->get_field_id('showtime'); ?>"><?php _e('Show Post Timestamp', 'adv-recent-posts');?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('format'); ?>"><?php _e('Time format:', 'adv-recent-posts');?> 
				<input class="widefat" id="<?php echo $this->get_field_id('format'); ?>" name="<?php echo $this->get_field_name('format'); ?>" type="text" value="<?php echo $format; ?>" /><br />
				<small>(<?php _e('<a href="http://www.php.net/manual/en/function.date.php">PHP style</a> - leave as default unless you know what you\'re doing.', 'adv-recent-posts');?>)</small>
			</label>
		</p>
		<p>
			<label>Put time</label><br />
				<select class="widefat" id="<?php echo $this->get_field_id('spot'); ?>" name="<?php echo $this->get_field_name('spot'); ?>">
				<?php
					$spots = array( 'spot1' => 'Before Title', 'spot2' => 'After Title', 'spot3' => 'After Separator' );
					foreach($spots as $s => $l) {
						echo '<option value="' . $s . '"' . selected( $s, $spot, true ) . '>' . $l . '</option>';
					}
				?>
				</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('cats'); ?>"><?php _e('Categories:', 'adv-recent-posts');?> 
				<input class="widefat" id="<?php echo $this->get_field_id('cats'); ?>" name="<?php echo $this->get_field_name('cats'); ?>" type="text" value="<?php echo $cats; ?>" /><br />
				<small>(<?php _e('Category IDs, separated by commas.', 'adv-recent-posts');?>)</small>
			</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('actcat'); ?>" name="<?php echo $this->get_field_name('actcat'); ?>"<?php checked( $actcat ); ?> />
			<label for="<?php echo $this->get_field_id('actcat'); ?>"> <?php _e('Get posts from current category', 'adv-recent-posts');?></label>
		</p>
		<?php
	} //end of form
}

add_action( 'widgets_init', create_function('', 'return register_widget("yg_recent_posts");') );
//Register Widget

// Show recent posts function
function yg_recentposts_defaults() {
$defaults = array( 	'title' => __( 'Recent Posts', 'adv-recent-posts' ),
					'link' => get_bloginfo( 'url' ) . '/blog/',
					'hideposttitle' => 0,
					'separator' => ': ', 
					'afterexcerpt' => '...',
					'afterexcerptlink' => 0,
					'show_type' => 'post', 
					'postoffset' => 0, 
					'limit' => 10, 
					'shownum' => 10, 
					'reverseorder' => 0, 
					'excerpt' => 0, 
					'excerptlengthwords' => 0, 
					'actcat' => 0, 
					'cats' => '', 
					'cusfield' => '', 
					'width' => '', 
					'height' => '', 
					'w' => '', 
					'h' => '', 
					'firstimage' => 0, 
					'showauthor' => 0, 
					'showtime' => 0, 
					'atimage' => 0,
					'defimage' => '',
					'format' => 'm/d/Y', 
					'spot' => 'spot1' );
	return $defaults;
}
add_shortcode( 'amrp' , 'yg_recentposts_sc');
function yg_recentposts_sc( $atts ) {

	$defaults = yg_recentposts_defaults();
// 	$defaults['limit'] = $defaults['shownum'];
// 	unset($shownum);
	$args = shortcode_atts($defaults, $atts);
	return yg_recentposts( $args, false );
	
}
function yg_wordexcerpt() {
}

function yg_recentposts($args = '', $echo = true) {
	global $wpdb;
	$defaults = yg_recentposts_defaults();
	//$defaults = array('separator' => ': ','show_type' => 'post', 'limit' => 10, 'excerpt' => 0, 'actcat' => 0, 'cats'=>'', 'cusfield' =>'', 'w' => 48, 'h' => 48, 'firstimage' => 0, 'showauthor' => 0, 'showtime' => 0, 'atimage' => 0, 'defimage' => '', 'format' => 'm/d/Y', 'spot' => 'spot1');
	
	$args = wp_parse_args( $args, $defaults );
	extract($args);
	
	$hideposttitle = (bool) $hideposttitle;
	$separator = $separator;
	$afterexcerpt = $afterexcerpt;
	$afterexcerptlink = (bool) $afterexcerptlink;
	$show_type = $show_type;

	$shownum = (int) abs($shownum);
	if(isset($limit) && $shownum == 10) $shownum = (int) $limit;
	$postoffset = (int) abs($postoffset);
	$reverseorder = (int) abs($reverseorder);
	$firstimage = (bool) $firstimage;
	$showauthor = (bool) $showauthor;
	$showtime = (bool) $showtime;

	$spot = esc_attr($spot);

	$atimage = (bool) $atimage;
	$defimage = esc_url($defimage);
	$format = esc_attr($format);
	$time = '';
	$width = (int) $width;
	$height = (int) $height;
	$w = (int) $w;
	$h = (int) $h;
	if ($width > $w) {
		$width = $width; $height = $height;
	} else {
		$width = $w; $height = $h;	
	}
	
	$excerptlength = (int) abs($excerpt);
	$excerptlengthwords = (int) abs($excerptlengthwords);
	$excerpt = '';
	$cats = str_replace(" ", "", esc_attr($cats));
	if (($shownum < 1 ) || ($shownum > 20)) $shownum = 10;
	
	/*$postlist = wp_cache_get('yg_recent_posts'); //Not yet
	if ( false === $postlist ) {
	*/
		if (($actcat) && (is_category())) {
			$cats = get_query_var('cat');
		}
		if (($actcat) && (is_single())) {
			$cats = '';
			foreach (get_the_category() as $catt) {
				$cats .= $catt->cat_ID.' '; 
			}
			$cats = str_replace(" ", ",", trim($cats));
		}
		
		if (!intval($cats)) $cats='';
		$query = "cat=$cats&showposts=$shownum&post_type=$show_type&offset=$postoffset";
		$posts = get_posts($query); //get posts
		if ($reverseorder) $posts = array_reverse($posts);
		$postlist = '';
		$height = $height ? ' height: ' . $height .'px;' : '';
		$width = $width ? ' width: ' . $width . 'px;' : '';
		$hw = (!empty($height) || !empty($width)) ? 'style="'.$width.$height.'"' : '';
	 
		foreach ($posts as $post) {
			if ($showtime) { $time = ' '. date($format,strtotime($post->post_date)); } 
			$post_title = stripslashes($post->post_title);
			if ($excerptlength) {
				$excerpt = $post->post_excerpt;
				$text = $post->post_content;
				$text = strip_shortcodes( $text );
				$text = str_replace(']]>', ']]&gt;', $text);
				$text = strip_tags($text);
				$excerpt_length = 100;
				$words = explode(' ', $text, $excerpt_length + 1);
				if ( '' == $excerpt ) {
					if (count($words) > $excerpt_length) {
						array_pop($words);
						$text = implode(' ', $words);
					}
					$excerpt = $text;
				}
				$afterexcerpt_html = '';
				if ($afterexcerptlink) $afterexcerpt_html = '<a href="' . get_permalink($post->ID) . '">' . $afterexcerpt . '</a>';
				else $afterexcerpt_html = $afterexcerpt;
				if ($excerptlengthwords > 0 ) {
					$words = array_splice($words, 0, $excerptlengthwords);
					$excerpt = implode(' ', $words);
				}elseif(strlen($excerpt) > $excerptlength) {
					$excerpt = mb_substr($excerpt, 0, $excerptlength);
				}
				$excerpt = $separator . ($spot == 'spot3' ? '<span class="date">'.$time.'</span> ' : '') . $excerpt . $afterexcerpt_html;
			}
			$image = '';
			$img = '';
			if ($cusfield) {
				$cusfield = esc_attr($cusfield);
				$img = get_post_meta($post->ID, $cusfield, true);
			}
	
			if (!$img && $firstimage) {
				$match_count = preg_match_all("/<img[^']*?src=\"([^']*?)\"[^']*?>/", $post->post_content, $match_array, PREG_PATTERN_ORDER);		
				$img = count($match_array['1']) > 0 ? $match_array[1][0] : false;
			}
			if (!$img && $atimage) {
				$p = array(
					'post_type' => 'attachment',
					'post_mime_type' => 'image',
					'numberposts' => 1,
					'order' => 'ASC',
					'orderby' => 'menu_order ID',
					'post_status' => null,
					'post_parent' => $post->ID
				 );
				$attachments = get_posts($p);
				if ($attachments) {
					$imgsrc = wp_get_attachment_image_src($attachments[0]->ID, 'thumbnail');
					$img = $imgsrc[0];
				}			 
			 }
				 
			if (!$img && $defimage)
				$img = $defimage;
				 
			if ($img)
				$image = '<a href="' . get_permalink($post->ID) . '" title="'. $post_title .'" ><img src="' . $img . '" title="' . $post_title . '" class="recent-posts-thumb" ' . $hw . ' /></a>';
	  
			$postlist .=  '<li>'.($spot == 'spot1' ? '<span class="date">'.$time.'</span> ' : '');
			$postlist .= $image;
			if (!$hideposttitle) $postlist .= '<a href="' . get_permalink($post->ID) . '" title="'. $post_title .'" >' . $post_title .'</a>';
				$author_data = get_userdata($post->post_author);
			$postlist .= ($showauthor ? ' by '.$author_data->display_name : '') . ($spot == 'spot2' ? ' <span class="date">'.$time.'</span>' : '') . $excerpt . "</li>";
		}// end foreach()
		
		/*
		wp_cache_set('yg_recent_posts', $postlist);
	}*/

	if ($echo)
		echo '<ul class="advanced-recent-posts">' . $postlist . '</ul>';
	else
		return '<ul class="advanced-recent-posts">' . $postlist . '</ul>';
}
?>