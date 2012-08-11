<?php

/*

  FILE STRUCTURE:

- REGISTER WIDGETIZED AREAS
- DEREGISTER SOME DEFAULT WIDGETS
- DEREGISTER SOME DEFAULT WIDGETS
- THEME WIDGETS:
  * Search Widget
  * Recent Posts (particular category)
  * Flickr Widget
  * Popular Posts Widget
  * Ad Banners
  * WYSIWYG Widget

*/

/* REGISTER WIDGETIZED AREAS */
/*------------------------------------------------------------------*/

if ( function_exists('register_sidebar') )
    register_sidebars(1,array('name' => 'Front Page Widgets','before_widget' => '<div class="widget">','after_widget' => '</div><!--/widget-->','before_title' => '<h3 class="hl">','after_title' => '</h3>'));
	register_sidebars(1,array('name' => 'Single Post Widgets','before_widget' => '<div class="widget">','after_widget' => '</div><!--/widget-->','before_title' => '<h3 class="hl">','after_title' => '</h3>'));
	register_sidebars(1,array('name' => 'Archive Widgets','before_widget' => '<div class="widget">','after_widget' => '</div><!--/widget-->','before_title' => '<h3 class="hl">','after_title' => '</h3>'));

/* CHECK for WIDGET-READY AREAS (Thanks to Chaos Kaizer http://blog.kaizeku.com/) */
/*------------------------------------------------------------------*/

function is_sidebar_active( $index = 1){
	$sidebars	= wp_get_sidebars_widgets();
	$key		= (string) 'sidebar-'.$index;
 
	return (isset($sidebars[$key]));
}

/* DEREGISTER SOME DEFAULT WIDGETS */
/*------------------------------------------------------------------*/

function bizz_deregister_widgets(){
    unregister_widget('WP_Widget_Search');         
}
add_action('widgets_init', 'bizz_deregister_widgets');  

/* THEME WIDGETS */
/*------------------------------------------------------------------*/

// =============================== Search Widget ======================================

class SearchW extends WP_Widget {

	function SearchW() {
	//Constructor
	
		$widget_ops = array('classname' => 'widget SearchW', 'description' => 'Widget to display general site search form' );
		$control_ops = array('width' => 400);
		$this->WP_Widget('widget_SearchW', 'Bizz &rarr; Search Widget', $widget_ops, $control_ops);
	}
 
	function widget($args, $instance) {
	// prints the widget

		extract($args, EXTR_SKIP);
						
		?>
		
		<div class="widget">
		
		<form method="get" id="searchform" class="search" action="<?php bloginfo('url'); ?>">
            <div>
            <input type="text" class="field" name="s" id="s"  value="<?php echo get_option('bizzthemes_search_name'); ?>" onfocus="if (this.value == '<?php echo get_option('bizzthemes_search_name'); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php echo get_option('bizzthemes_search_name'); ?>';}" />
            <button><span><!----></span></button>
		    <input type="hidden" class="submit" name="submit" />
			</div>
		</form>
		
		</div>

		<?php
						
	}
 
	function update($new_instance, $old_instance) {
	//save the widget
	
		$instance = $old_instance; 
		return $instance;
	}
 
}
register_widget('SearchW');

// =============================== Recent Posts (particular category) ======================================

class RecentPostsCat extends WP_Widget {

	function RecentPostsCat() {
	//Constructor
	
		$widget_ops = array('classname' => 'widget recposts', 'description' => 'List of recent posts from particular category' );
		$control_ops = array('width' => 400);
		$this->WP_Widget('widget_recent_cat', 'Bizz &rarr; Recent Posts from Category', $widget_ops, $control_ops);
	}
 
	function widget($args, $instance) {
	// prints the widget

		extract($args, EXTR_SKIP);
 
		echo $before_widget;
		$wid_title = empty($instance['wid_title']) ? '&nbsp;' : apply_filters('widget_wid_title', $instance['wid_title']);
		$category = empty($instance['category']) ? '&nbsp;' : apply_filters('widget_category', $instance['category']);
		$rec_number = empty($instance['rec_number']) ? '&nbsp;' : apply_filters('widget_rec_number', $instance['rec_number']);
		$rec_date = empty($instance['rec_date']) ? '&nbsp;' : apply_filters('widget_rec_date', $instance['rec_date']);
		
		if ( $instance['wid_title'] )
			echo $before_title . $wid_title . $after_title;
 				        
		?>
			
			<ul>
			
		    <?php  
			    if (is_paged()) $is_paged = true;
				query_posts('posts_per_page='.$rec_number.'&order=DESC&cat='.$category.'&caller_get_posts=1');
				global $post; setup_postdata($post);
				if (have_posts()) : $count = 0;
				while (have_posts()) : the_post(); $postcount++; 
			?>
			
			<li>
			    <div class="rec-title">
				    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</div>
			    <div class="rec-excerpt"><?php echo bm_better_excerpt(130, ' [...] '); ?></div>
				<div class="rec-date"><?php the_time(''.$rec_date.'') ?></div>
			</li>
			
			<?php endwhile; endif; wp_reset_query(); ?>
			
			</ul>
				
			<?php
				
		echo $after_widget;
		
	}
 
	function update($new_instance, $old_instance) {
	//save the widget
	
		$instance = $old_instance;
		$instance['wid_title'] = strip_tags($new_instance['wid_title']);
		$instance['category'] = strip_tags($new_instance['category']);
		$instance['rec_number'] = strip_tags($new_instance['rec_number']);
		$instance['rec_date'] = strip_tags($new_instance['rec_date']);
 
		return $instance;
	}
 
	function form($instance) {
	//widgetform in backend

		$instance = wp_parse_args( (array) $instance, array( 'wid_title' => '', 'category' => '', 'rec_number' => '3', 'rec_date' => 'M d, h a' ) );
		$wid_title = strip_tags($instance['wid_title']);
		$category = strip_tags($instance['category']);
		$rec_number = strip_tags($instance['rec_number']);
		$rec_date = strip_tags($instance['rec_date']);
?>
			<p><label for="<?php echo $this->get_field_id('wid_title'); ?>">Widget Title: <input class="widefat" id="<?php echo $this->get_field_id('wid_title'); ?>" name="<?php echo $this->get_field_name('wid_title'); ?>" type="text" value="<?php echo attribute_escape($wid_title); ?>" /></label></p>
			<p>
			<label for="<?php echo $this->get_field_id('category'); ?>">Include Category:
			<select name="<?php echo $this->get_field_name('category'); ?>" id="<?php echo $this->get_field_id('category') ?>" class="widefat">
				<option value="-99"<?php  selected(-99, (int) $instance['category']); ?>>-- All Categories --</option>
				<?php
				$categories	= get_terms('category');
				foreach ( $categories as $cat ) {
					echo '<option value="' . $cat->term_id .'"';
					selected((int) $cat->term_id, (int) $instance['category']);
					echo '>' . $cat->name . '</option>';
				}
				?>
			</select>
		    </label>
			</p>
			<p><label for="<?php echo $this->get_field_id('rec_number'); ?>">Total number of posts to show: <input class="widefat" id="<?php echo $this->get_field_id('rec_number'); ?>" name="<?php echo $this->get_field_name('rec_number'); ?>" type="text" value="<?php echo attribute_escape($rec_number); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('rec_date'); ?>">Select <a href="http://si2.php.net/manual/en/function.date.php">date format</a>: <input class="widefat" id="<?php echo $this->get_field_id('rec_date'); ?>" name="<?php echo $this->get_field_name('rec_date'); ?>" type="text" value="<?php echo attribute_escape($rec_date); ?>" /></label></p>
			
<?php
	}
}
register_widget('RecentPostsCat');

// =============================== Flickr Widget ======================================
class FlickrW extends WP_Widget {

	function FlickrW() {
		$widget_ops = array('description' => 'Widget to display Flickr photostream.' );

		parent::WP_Widget(false, __('Bizz - Flickr', 'bizzthemes'),$widget_ops);      
	}

	function widget($args, $instance) {  
		extract( $args );
		$wid_title = empty($instance['wid_title']) ? '&nbsp;' : apply_filters('widget_wid_title', $instance['wid_title']);
		$wid_flickr_id = empty($instance['wid_flickr_id']) ? '&nbsp;' : apply_filters('widget_wid_flickr_id', $instance['wid_flickr_id']);
		$wid_flickr_num = empty($instance['wid_flickr_num']) ? '&nbsp;' : apply_filters('widget_wid_flickr_num', $instance['wid_flickr_num']);
		$wid_flickr_type = empty($instance['wid_flickr_type']) ? '&nbsp;' : apply_filters('widget_wid_flickr_type', $instance['wid_flickr_type']);
		$wid_flickr_sorting = empty($instance['wid_flickr_sorting']) ? '&nbsp;' : apply_filters('widget_wid_flickr_sorting', $instance['wid_flickr_sorting']);
		
		echo $before_widget;
		
		if ( $instance['wid_title'] )
			echo $before_title . $wid_title . $after_title;
			
		?>
		
        <div class="wrap flickr">
            <div class="fix"></div>
            <script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=<?php echo $wid_flickr_num; ?>&amp;display=<?php echo $wid_flickr_sorting; ?>&amp;size=s&amp;layout=x&amp;source=<?php echo $wid_flickr_type; ?>&amp;<?php echo $wid_flickr_type; ?>=<?php echo $wid_flickr_id; ?>"></script>        
            <div class="fix"></div>
        </div>

	   <?php			
	   echo $after_widget;
   }

   function update($new_instance, $old_instance) {                
       return $new_instance;
   }

   function form($instance) {        		
		$instance = wp_parse_args( (array) $instance, array( 'wid_title' => 'Flickr Photostream', 'wid_flickr_id' => '38982010@N00', 'wid_flickr_num' => '6', 'wid_flickr_type' => '', 'wid_flickr_sorting' => '' ) );
		$wid_title = strip_tags($instance['wid_title']);
		$wid_flickr_id = strip_tags($instance['wid_flickr_id']);
		$wid_flickr_num = strip_tags($instance['wid_flickr_num']);
		$wid_flickr_type = strip_tags($instance['wid_flickr_type']);
		$wid_flickr_sorting = strip_tags($instance['wid_flickr_sorting']);
		
		?>
		<p>
		    <label for="<?php echo $this->get_field_id('wid_title'); ?>"><?php _e('Widget Title:','bizzthemes'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('wid_title'); ?>" name="<?php echo $this->get_field_name('wid_title'); ?>" type="text" value="<?php echo attribute_escape($wid_title); ?>" />
		</p>
        <p>
            <label for="<?php echo $this->get_field_id('wid_flickr_id'); ?>"><?php _e('Flickr ID (<a href="http://www.idgettr.com">idGettr</a>):','bizzthemes'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('wid_flickr_id'); ?>" value="<?php echo $wid_flickr_id; ?>" class="widefat" id="<?php echo $this->get_field_id('wid_flickr_id'); ?>" />
        </p>
       	<p>
            <label for="<?php echo $this->get_field_id('wid_flickr_num'); ?>"><?php _e('Number:','bizzthemes'); ?></label>
            <select name="<?php echo $this->get_field_name('wid_flickr_num'); ?>" class="widefat" id="<?php echo $this->get_field_id('wid_flickr_num'); ?>">
                <?php for ( $i = 1; $i < 11; $i += 1) { ?>
                <option value="<?php echo $i; ?>" <?php if($wid_flickr_num == $i){ echo "selected='selected'";} ?>><?php echo $i; ?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('wid_flickr_type'); ?>"><?php _e('Type:','bizzthemes'); ?></label>
            <select name="<?php echo $this->get_field_name('wid_flickr_type'); ?>" class="widefat" id="<?php echo $this->get_field_id('wid_flickr_type'); ?>">
                <option value="user" <?php if($wid_flickr_type == "user"){ echo "selected='selected'";} ?>><?php _e('User', 'bizzthemes'); ?></option>
                <option value="group" <?php if($wid_flickr_type == "group"){ echo "selected='selected'";} ?>><?php _e('Group', 'bizzthemes'); ?></option>            
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('wid_flickr_sorting'); ?>"><?php _e('Sorting:','bizzthemes'); ?></label>
            <select name="<?php echo $this->get_field_name('wid_flickr_sorting'); ?>" class="widefat" id="<?php echo $this->get_field_id('wid_flickr_sorting'); ?>">
                <option value="latest" <?php if($wid_flickr_sorting == "latest"){ echo "selected='selected'";} ?>><?php _e('Latest', 'bizzthemes'); ?></option>
                <option value="random" <?php if($wid_flickr_sorting == "random"){ echo "selected='selected'";} ?>><?php _e('Random', 'bizzthemes'); ?></option>            
            </select>
        </p>
		<?php
	}
} 

register_widget('FlickrW');

// =============================== Popular Posts Widget ======================================

class PopularW extends WP_Widget {

	function PopularW() {
	//Constructor
	
		$widget_ops = array('classname' => 'widget PopularW', 'description' => 'Widget to display most popular posts' );
		$control_ops = array('width' => 400);
		$this->WP_Widget('widget_PopularW', 'Bizz &rarr; Popular Posts', $widget_ops, $control_ops);
	}
 
	function widget($args, $instance) {
	// prints the widget

		extract($args, EXTR_SKIP);
		
		$wid_title = empty($instance['wid_title']) ? '&nbsp;' : apply_filters('widget_wid_title', $instance['wid_title']);
		$wid_pop_num = empty($instance['wid_pop_num']) ? '&nbsp;' : apply_filters('widget_wid_pop_num', $instance['wid_pop_num']);
		
		if ( !empty($instance['wid_pop_num']) ) { $popnumber = $wid_pop_num; } else { $popnumber = '10'; }
		
		echo $before_widget;
		
		if ( $instance['wid_title'] )
			echo $before_title . $wid_title . $after_title;
		
		?>
			
			<div class="popular">
			
			    <ul>
				<?php
				global $wpdb;
				$now = gmdate("Y-m-d H:i:s",time());
				$lastmonth = gmdate("Y-m-d H:i:s",gmmktime(date("H"), date("i"), date("s"), date("m")-12,date("d"),date("Y")));
				$popularposts = "SELECT ID, post_title, COUNT($wpdb->comments.comment_post_ID) AS 'stammy' FROM $wpdb->posts, $wpdb->comments WHERE comment_approved = '1' AND $wpdb->posts.ID=$wpdb->comments.comment_post_ID AND post_status = 'publish' AND post_date < '$now' AND post_date > '$lastmonth' AND comment_status = 'open' GROUP BY $wpdb->comments.comment_post_ID ORDER BY stammy DESC LIMIT $popnumber";
				$posts = $wpdb->get_results($popularposts);
				$popular = '';
				if($posts){
                foreach($posts as $post){
	                $post_title = stripslashes($post->post_title);
		            $guid = get_permalink($post->ID);
					$first_post_title=substr($post_title,0,30);
				?>
		        <li>
                    <a href="<?php echo $guid; ?>" title="<?php echo $post_title; ?>"><?php echo $first_post_title; ?></a> [...]
                    <br style="clear:both" />
                </li>
				<?php } } ?>
				</ul>
			
			</div>

		<?php
				
		echo $after_widget;
		
	}
 
	function update($new_instance, $old_instance) {
	//save the widget
	
		$instance = $old_instance;
		$instance['wid_title'] = strip_tags($new_instance['wid_title']);
		$instance['wid_pop_num'] = strip_tags($new_instance['wid_pop_num']);
 
		return $instance;
	}
 
	function form($instance) {
	//widgetform in backend

		$instance = wp_parse_args( (array) $instance, array( 'wid_title' => 'Popular Posts', 'wid_pop_num' => '10' ) );
		$wid_title = strip_tags($instance['wid_title']);
		$wid_pop_num = strip_tags($instance['wid_pop_num']);

?>

			<p><label for="<?php echo $this->get_field_id('wid_title'); ?>">Widget Title: <input class="widefat" id="<?php echo $this->get_field_id('wid_title'); ?>" name="<?php echo $this->get_field_name('wid_title'); ?>" type="text" value="<?php echo attribute_escape($wid_title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_pop_num'); ?>">Number of Popular Posts: <input class="widefat" id="<?php echo $this->get_field_id('wid_pop_num'); ?>" name="<?php echo $this->get_field_name('wid_pop_num'); ?>" type="text" value="<?php echo attribute_escape($wid_pop_num); ?>" /></label></p>

<?php
	}
}
register_widget('PopularW');

// =============================== Ad Banners ======================================

class SmallAdsW extends WP_Widget {

	function SmallAdsW() {
	//Constructor
	
		$widget_ops = array('classname' => 'widget SmallAdsW', 'description' => 'Image Ad Banners' );
		$control_ops = array('width' => 400);
		$this->WP_Widget('widget_SmallAdsW', 'Bizz &rarr; Image Ad Banners', $widget_ops, $control_ops);
	}
 
	function widget($args, $instance) {
	// prints the widget

		extract($args, EXTR_SKIP);
 
		echo $before_widget;
		$wid_title = empty($instance['wid_title']) ? '&nbsp;' : apply_filters('widget_wid_title', $instance['wid_title']);
		$wid_img_url_1 = empty($instance['wid_img_url_1']) ? '&nbsp;' : apply_filters('widget_wid_img_url_1', $instance['wid_img_url_1']);
		$wid_dest_url_1 = empty($instance['wid_dest_url_1']) ? '&nbsp;' : apply_filters('widget_wid_dest_url_1', $instance['wid_dest_url_1']);
		$wid_img_url_2 = empty($instance['wid_img_url_2']) ? '&nbsp;' : apply_filters('widget_wid_img_url_2', $instance['wid_img_url_2']);
		$wid_dest_url_2 = empty($instance['wid_dest_url_2']) ? '&nbsp;' : apply_filters('widget_wid_dest_url_2', $instance['wid_dest_url_2']);
		$wid_img_url_3 = empty($instance['wid_img_url_3']) ? '&nbsp;' : apply_filters('widget_wid_img_url_3', $instance['wid_img_url_3']);
		$wid_dest_url_3 = empty($instance['wid_dest_url_3']) ? '&nbsp;' : apply_filters('widget_wid_dest_url_3', $instance['wid_dest_url_3']);
		$wid_img_url_4 = empty($instance['wid_img_url_4']) ? '&nbsp;' : apply_filters('widget_wid_img_url_4', $instance['wid_img_url_4']);
		$wid_dest_url_4 = empty($instance['wid_dest_url_4']) ? '&nbsp;' : apply_filters('widget_wid_dest_url_4', $instance['wid_dest_url_4']);
		$wid_img_url_5 = empty($instance['wid_img_url_5']) ? '&nbsp;' : apply_filters('widget_wid_img_url_5', $instance['wid_img_url_5']);
		$wid_dest_url_5 = empty($instance['wid_dest_url_5']) ? '&nbsp;' : apply_filters('widget_wid_dest_url_5', $instance['wid_dest_url_5']);
		$wid_img_url_6 = empty($instance['wid_img_url_6']) ? '&nbsp;' : apply_filters('widget_wid_img_url_6', $instance['wid_img_url_6']);
		$wid_dest_url_6 = empty($instance['wid_dest_url_6']) ? '&nbsp;' : apply_filters('widget_wid_dest_url_6', $instance['wid_dest_url_6']);
		
		if ( $instance['wid_title'] )
			echo $before_title . $wid_title . $after_title;
 				        
		?>						
			<div class="ad-box">
			    <?php 
				if ( !empty($instance['wid_img_url_1']) ) { ?>
				    <a href="<?php echo $wid_dest_url_1; ?>"><img src="<?php echo $wid_img_url_1; ?>" alt="" /></a>
				<?php } 
				if ( !empty($instance['wid_img_url_2']) ) { ?>
				    <a href="<?php echo $wid_dest_url_2; ?>"><img src="<?php echo $wid_img_url_2; ?>" alt="" /></a>
				<?php }
				if ( !empty($instance['wid_img_url_3']) ) { ?>
				    <a href="<?php echo $wid_dest_url_3; ?>"><img src="<?php echo $wid_img_url_3; ?>" alt="" /></a>
				<?php }
				if ( !empty($instance['wid_img_url_4']) ) { ?>
				    <a href="<?php echo $wid_dest_url_4; ?>"><img src="<?php echo $wid_img_url_4; ?>" alt="" /></a>
				<?php }
				if ( !empty($instance['wid_img_url_5']) ) { ?>
				    <a href="<?php echo $wid_dest_url_5; ?>"><img src="<?php echo $wid_img_url_5; ?>" alt="" /></a>
				<?php }
				if ( !empty($instance['wid_img_url_6']) ) { ?>
				    <a href="<?php echo $wid_dest_url_6; ?>"><img src="<?php echo $wid_img_url_6; ?>" alt="" /></a>
				<?php } ?>
			</div>
							
			<?php
				
		echo $after_widget;
		
	}
 
	function update($new_instance, $old_instance) {
	//save the widget
	
		$instance = $old_instance;
		$instance['wid_title'] = strip_tags($new_instance['wid_title']);
		$instance['wid_img_url_1'] = strip_tags($new_instance['wid_img_url_1']);
		$instance['wid_dest_url_1'] = strip_tags($new_instance['wid_dest_url_1']);
		$instance['wid_img_url_2'] = strip_tags($new_instance['wid_img_url_2']);
		$instance['wid_dest_url_2'] = strip_tags($new_instance['wid_dest_url_2']);
		$instance['wid_img_url_3'] = strip_tags($new_instance['wid_img_url_3']);
		$instance['wid_dest_url_3'] = strip_tags($new_instance['wid_dest_url_3']);
		$instance['wid_img_url_4'] = strip_tags($new_instance['wid_img_url_4']);
		$instance['wid_dest_url_4'] = strip_tags($new_instance['wid_dest_url_4']);
		$instance['wid_img_url_5'] = strip_tags($new_instance['wid_img_url_5']);
		$instance['wid_dest_url_5'] = strip_tags($new_instance['wid_dest_url_5']);
		$instance['wid_img_url_6'] = strip_tags($new_instance['wid_img_url_6']);
		$instance['wid_dest_url_6'] = strip_tags($new_instance['wid_dest_url_6']);
 
		return $instance;
	}
 
	function form($instance) {
	//widgetform in backend

		$instance = wp_parse_args( (array) $instance, array( 'wid_title' => '', 'wid_img_url_1' => '', 'wid_dest_url_1' => 'http://',  'wid_img_url_2' => '', 'wid_dest_url_2' => 'http://', 'wid_img_url_3' => '', 'wid_dest_url_3' => 'http://', 'wid_img_url_4' => '', 'wid_dest_url_4' => 'http://', 'wid_img_url_5' => '', 'wid_dest_url_5' => 'http://', 'wid_img_url_6' => '', 'wid_dest_url_6' => 'http://' ) );
		$wid_title = strip_tags($instance['wid_title']);
		$wid_img_url_1 = strip_tags($instance['wid_img_url_1']);
		$wid_dest_url_1 = strip_tags($instance['wid_dest_url_1']);
		$wid_img_url_2 = strip_tags($instance['wid_img_url_2']);
		$wid_dest_url_2 = strip_tags($instance['wid_dest_url_2']);
		$wid_img_url_3 = strip_tags($instance['wid_img_url_3']);
		$wid_dest_url_3 = strip_tags($instance['wid_dest_url_3']);
		$wid_img_url_4 = strip_tags($instance['wid_img_url_4']);
		$wid_dest_url_4 = strip_tags($instance['wid_dest_url_4']);
		$wid_img_url_5 = strip_tags($instance['wid_img_url_5']);
		$wid_dest_url_5 = strip_tags($instance['wid_dest_url_5']);
		$wid_img_url_6 = strip_tags($instance['wid_img_url_6']);
		$wid_dest_url_6 = strip_tags($instance['wid_dest_url_6']);
?>
			<p><label for="<?php echo $this->get_field_id('wid_title'); ?>">Widget Title: <input class="widefat" id="<?php echo $this->get_field_id('wid_title'); ?>" name="<?php echo $this->get_field_name('wid_title'); ?>" type="text" value="<?php echo attribute_escape($wid_title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_img_url_1'); ?>">Ad Image URL 1: <input class="widefat" id="<?php echo $this->get_field_id('wid_img_url_1'); ?>" name="<?php echo $this->get_field_name('wid_img_url_1'); ?>" type="text" value="<?php echo attribute_escape($wid_img_url_1); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_dest_url_1'); ?>">Ad Destination URL 1: <input class="widefat" id="<?php echo $this->get_field_id('wid_dest_url_1'); ?>" name="<?php echo $this->get_field_name('wid_dest_url_1'); ?>" type="text" value="<?php echo attribute_escape($wid_dest_url_1); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_img_url_2'); ?>">Ad Image URL 2: <input class="widefat" id="<?php echo $this->get_field_id('wid_img_url_2'); ?>" name="<?php echo $this->get_field_name('wid_img_url_2'); ?>" type="text" value="<?php echo attribute_escape($wid_img_url_2); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_dest_url_2'); ?>">Ad Destination URL 2: <input class="widefat" id="<?php echo $this->get_field_id('wid_dest_url_2'); ?>" name="<?php echo $this->get_field_name('wid_dest_url_2'); ?>" type="text" value="<?php echo attribute_escape($wid_dest_url_2); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_img_url_3'); ?>">Ad Image URL 3: <input class="widefat" id="<?php echo $this->get_field_id('wid_img_url_3'); ?>" name="<?php echo $this->get_field_name('wid_img_url_3'); ?>" type="text" value="<?php echo attribute_escape($wid_img_url_3); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_dest_url_3'); ?>">Ad Destination URL 3: <input class="widefat" id="<?php echo $this->get_field_id('wid_dest_url_3'); ?>" name="<?php echo $this->get_field_name('wid_dest_url_3'); ?>" type="text" value="<?php echo attribute_escape($wid_dest_url_3); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_img_url_4'); ?>">Ad Image URL 4: <input class="widefat" id="<?php echo $this->get_field_id('wid_img_url_4'); ?>" name="<?php echo $this->get_field_name('wid_img_url_4'); ?>" type="text" value="<?php echo attribute_escape($wid_img_url_4); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_dest_url_4'); ?>">Ad Destination URL 4: <input class="widefat" id="<?php echo $this->get_field_id('wid_dest_url_4'); ?>" name="<?php echo $this->get_field_name('wid_dest_url_4'); ?>" type="text" value="<?php echo attribute_escape($wid_dest_url_4); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_img_url_5'); ?>">Ad Image URL 5: <input class="widefat" id="<?php echo $this->get_field_id('wid_img_url_5'); ?>" name="<?php echo $this->get_field_name('wid_img_url_5'); ?>" type="text" value="<?php echo attribute_escape($wid_img_url_5); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_dest_url_5'); ?>">Ad Destination URL 5: <input class="widefat" id="<?php echo $this->get_field_id('wid_dest_url_5'); ?>" name="<?php echo $this->get_field_name('wid_dest_url_5'); ?>" type="text" value="<?php echo attribute_escape($wid_dest_url_5); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_img_url_6'); ?>">Ad Image URL 6: <input class="widefat" id="<?php echo $this->get_field_id('wid_img_url_6'); ?>" name="<?php echo $this->get_field_name('wid_img_url_6'); ?>" type="text" value="<?php echo attribute_escape($wid_img_url_6); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_dest_url_6'); ?>">Ad Destination URL 6: <input class="widefat" id="<?php echo $this->get_field_id('wid_dest_url_6'); ?>" name="<?php echo $this->get_field_name('wid_dest_url_6'); ?>" type="text" value="<?php echo attribute_escape($wid_dest_url_6); ?>" /></label></p>
			
<?php
	}
}
register_widget('SmallAdsW');

// =============================== WYSIWYG Widget ======================================

class WysiwygW extends WP_Widget {

	function WysiwygW() {
	//Constructor
	
		$widget_ops = array('classname' => 'widget WysiwygW', 'description' => 'Widget to display Rich wysiwyg textarea content' );
		$control_ops = array('width' => 400);
		$this->WP_Widget('WysiwygW', 'Bizz &rarr; Rich Textarea', $widget_ops, $control_ops);
	
	}
 
	function widget($args, $instance) {
	// prints the widget

		extract($args, EXTR_SKIP);
		
		$wid_title = empty($instance['wid_title']) ? '&nbsp;' : apply_filters('widget_wid_title', $instance['wid_title']);
		$wid_wysiwyg_c = empty($instance['wid_wysiwyg_c']) ? '&nbsp;' : apply_filters('widget_wid_wysiwyg_c', $instance['wid_wysiwyg_c']);
		
		echo $before_widget;
		
		if ( $instance['wid_title'] )
			echo $before_title . $wid_title . $after_title;
		
		?>
		
			<div class="editor_content">
			
			    <?php $boxc1 = str_replace('<br>', ''.addslashes('<br/>').'', ''.stripslashes($wid_wysiwyg_c).''); ?>
			    <?php echo $wid_wysiwyg_c; ?>
				
			</div>

		<?php
				
		echo $after_widget;
		
	}
 
	function update($new_instance, $old_instance) {
	//save the widget
	
		$instance = $old_instance;
		$instance['wid_title'] = strip_tags($new_instance['wid_title']);
		$instance['wid_wysiwyg_c'] = $new_instance['wid_wysiwyg_c'];
 
		return $instance;
	}
 
	function form($instance) {
	//widgetform in backend

		$instance = wp_parse_args( (array) $instance, array( 'wid_title' => '', 'wid_wysiwyg_c' => '' ) );
		$wid_title = strip_tags($instance['wid_title']);
		$wid_wysiwyg_c = $instance['wid_wysiwyg_c'];
		
	?>
	
			<p><label for="<?php echo $this->get_field_id('wid_title'); ?>">Widget Title: <input class="widefat" id="<?php echo $this->get_field_id('wid_title'); ?>" name="<?php echo $this->get_field_name('wid_title'); ?>" type="text" value="<?php echo attribute_escape($wid_title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_wysiwyg_c'); ?>" class="wysiwyg_label">Widget Content: <span class="richtext">Rich Textarea</span> <textarea class="xwysiwyg widefat" id="<?php echo $this->get_field_id('wid_wysiwyg_c'); ?>" name="<?php echo $this->get_field_name('wid_wysiwyg_c'); ?>" type="text" cols="20" rows="12"><?php echo attribute_escape($wid_wysiwyg_c); ?></textarea></label></p>
	<?php

	}
}

register_widget('WysiwygW');

// =============================== Twitter Widget ======================================

class Twidget extends WP_Widget {

	function Twidget() {
	//Constructor
	
		$widget_ops = array('classname' => 'widget twidget', 'description' => 'Twitter Updates Stream' );
		$control_ops = array('width' => 400);
		$this->WP_Widget('widget_twidget', 'Bizz &rarr; Twitter', $widget_ops, $control_ops);
	}
 
	function widget($args, $instance) {
	// prints the widget

		extract($args, EXTR_SKIP);
		
		$wid_title = empty($instance['wid_title']) ? '&nbsp;' : apply_filters('widget_wid_title', $instance['wid_title']);
		$wid_account = empty($instance['wid_account']) ? '&nbsp;' : apply_filters('widget_wid_account', $instance['wid_account']);
		$wid_show = empty($instance['wid_show']) ? '&nbsp;' : apply_filters('widget_wid_show', $instance['wid_show']);
		$wid_follow = empty($instance['wid_follow']) ? '&nbsp;' : apply_filters('widget_wid_follow', $instance['wid_follow']);
		$wid_id = $args['widget_id'];
		
		echo $before_widget;
		
		?>
		
		<div id="twitter">
		    <?php 
			if ( $instance['wid_title'] )
				echo $before_title . $wid_title . $after_title; 
			?>
			<div class="twitter-top"><!----></div>
			<?php hosted_twitter_script($wid_id,$wid_account,$wid_show); ?>
			<div class="website">
			<div class="follow-text"><a href="http://www.twitter.com/<?php echo $wid_account; ?>/" title="<?php echo $wid_follow; ?>"><?php echo $wid_follow; ?></a></div>
			</div>
		</div>
		
		<?php
		
		echo $after_widget;
		
	}
 
	function update($new_instance, $old_instance) {
	//save the widget
	
		$instance = $old_instance;
		$instance['wid_title'] = strip_tags($new_instance['wid_title']);
		$instance['wid_account'] = strip_tags($new_instance['wid_account']);
		$instance['wid_show'] = strip_tags($new_instance['wid_show']);
		$instance['wid_follow'] = strip_tags($new_instance['wid_follow']);
 
		return $instance;
	}
 
	function form($instance) {
	//widgetform in backend

		$instance = wp_parse_args( (array) $instance, array( 'wid_title' => 'Twitter Updates', 'wid_account' => 'BizzThemes',  'wid_show' => '3', 'wid_follow' => 'Follow Us on Twitter' ) );
		$wid_title = strip_tags($instance['wid_title']);
		$wid_account = strip_tags($instance['wid_account']);
		$wid_show = strip_tags($instance['wid_show']);
		$wid_follow = strip_tags($instance['wid_follow']);

?>

			<p><label for="<?php echo $this->get_field_id('wid_title'); ?>">Widget Title: <input class="widefat" id="<?php echo $this->get_field_id('wid_title'); ?>" name="<?php echo $this->get_field_name('wid_title'); ?>" type="text" value="<?php echo attribute_escape($wid_title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_account'); ?>">Account: <input class="widefat" id="<?php echo $this->get_field_id('wid_account'); ?>" name="<?php echo $this->get_field_name('wid_account'); ?>" type="text" value="<?php echo attribute_escape($wid_account); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_show'); ?>"># of Updates to Show: <input class="widefat" id="<?php echo $this->get_field_id('wid_show'); ?>" name="<?php echo $this->get_field_name('wid_show'); ?>" type="text" value="<?php echo attribute_escape($wid_show); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_follow'); ?>">Follow us Name: <input class="widefat" id="<?php echo $this->get_field_id('wid_follow'); ?>" name="<?php echo $this->get_field_name('wid_follow'); ?>" type="text" value="<?php echo attribute_escape($wid_follow); ?>" /></label></p>
			
<?php
	}
}
register_widget('Twidget');

// =============================== Contact Form Widget ======================================

class Cwidget extends WP_Widget {

	function Cwidget() {
	//Constructor
	
		$widget_ops = array('classname' => 'widget cwidget', 'description' => 'Simple contact form' );
		$control_ops = array('width' => 400);
		$this->WP_Widget('widget_cwidget', 'Bizz &rarr; Contact Form', $widget_ops, $control_ops);
	}
 
	function widget($args, $instance) {
	// prints the widget

		extract($args, EXTR_SKIP);
		
		$wid_title = empty($instance['wid_title']) ? '&nbsp;' : apply_filters('widget_wid_title', $instance['wid_title']);
		$wid_email = empty($instance['wid_email']) ? '&nbsp;' : apply_filters('widget_wid_email', $instance['wid_email']);
		$wid_trans1 = empty($instance['wid_trans1']) ? '&nbsp;' : apply_filters('widget_wid_trans1', $instance['wid_trans1']);
		$wid_trans2 = empty($instance['wid_trans2']) ? '&nbsp;' : apply_filters('widget_wid_trans2', $instance['wid_trans2']);
		$wid_trans3 = empty($instance['wid_trans3']) ? '&nbsp;' : apply_filters('widget_wid_trans3', $instance['wid_trans3']);
		$wid_trans5 = empty($instance['wid_trans5']) ? '&nbsp;' : apply_filters('widget_wid_trans5', $instance['wid_trans5']);
		$wid_trans6 = empty($instance['wid_trans6']) ? '&nbsp;' : apply_filters('widget_wid_trans6', $instance['wid_trans6']);
		$wid_trans7 = empty($instance['wid_trans7']) ? '&nbsp;' : apply_filters('widget_wid_trans7', $instance['wid_trans7']);
		$wid_trans9 = empty($instance['wid_trans9']) ? '&nbsp;' : apply_filters('widget_wid_trans9', $instance['wid_trans9']);
		$wid_trans10 = empty($instance['wid_trans10']) ? '&nbsp;' : apply_filters('widget_wid_trans10', $instance['wid_trans10']);
		$wid_trans11 = empty($instance['wid_trans11']) ? '&nbsp;' : apply_filters('widget_wid_trans1', $instance['wid_trans11']);
		$wid_trans12 = empty($instance['wid_trans12']) ? '&nbsp;' : apply_filters('widget_wid_trans1', $instance['wid_trans12']);
		$wid_trans13 = empty($instance['wid_trans13']) ? '&nbsp;' : apply_filters('widget_wid_trans1', $instance['wid_trans13']);
		$wid_trans14 = empty($instance['wid_trans14']) ? '&nbsp;' : apply_filters('widget_wid_trans1', $instance['wid_trans14']);
		$wid_trans15 = empty($instance['wid_trans15']) ? '&nbsp;' : apply_filters('widget_wid_trans1', $instance['wid_trans15']);
		$wid_trans16 = empty($instance['wid_trans16']) ? '&nbsp;' : apply_filters('widget_wid_trans1', $instance['wid_trans16']);
		$wid_trans17 = empty($instance['wid_trans17']) ? '&nbsp;' : apply_filters('widget_wid_trans1', $instance['wid_trans17']);
		$wid_trans18 = empty($instance['wid_trans18']) ? '&nbsp;' : apply_filters('widget_wid_trans1', $instance['wid_trans18']);
		$wid_trans19 = empty($instance['wid_trans19']) ? '&nbsp;' : apply_filters('widget_wid_trans1', $instance['wid_trans19']);
		$wid_id = $args['widget_id'];
		
		echo $before_widget;
		
		?>
		
		<div class="cform">
		    
			<?php 
			if ( $instance['wid_title'] )
				echo $before_title . $wid_title . $after_title;
			?>
			
			<?php bizz_contact_form($wid_email,$wid_trans1,$wid_trans2,$wid_trans3,$wid_trans5,$wid_trans6,$wid_trans7,$wid_trans9,$wid_trans10,$wid_trans11,$wid_trans12,$wid_trans13,$wid_trans14,$wid_trans15,$wid_trans16,$wid_trans17,$wid_trans18,$wid_trans19,$wid_id); ?>
					
		</div>
		
		<?php
		
		echo $after_widget;
		
	}
 
	function update($new_instance, $old_instance) {
	//save the widget
	
		$instance = $old_instance;
		$instance['wid_title'] = strip_tags($new_instance['wid_title']);
		$instance['wid_email'] = strip_tags($new_instance['wid_email']);
		$instance['wid_trans1'] = strip_tags($new_instance['wid_trans1']);
		$instance['wid_trans2'] = strip_tags($new_instance['wid_trans2']);
		$instance['wid_trans3'] = strip_tags($new_instance['wid_trans3']);
		$instance['wid_trans5'] = strip_tags($new_instance['wid_trans5']);
		$instance['wid_trans6'] = strip_tags($new_instance['wid_trans6']);
		$instance['wid_trans7'] = strip_tags($new_instance['wid_trans7']);
		$instance['wid_trans9'] = strip_tags($new_instance['wid_trans9']);
		$instance['wid_trans10'] = strip_tags($new_instance['wid_trans10']);
		$instance['wid_trans11'] = strip_tags($new_instance['wid_trans11']);
		$instance['wid_trans12'] = strip_tags($new_instance['wid_trans12']);
		$instance['wid_trans13'] = strip_tags($new_instance['wid_trans13']);
		$instance['wid_trans14'] = strip_tags($new_instance['wid_trans14']);
		$instance['wid_trans15'] = strip_tags($new_instance['wid_trans15']);
		$instance['wid_trans16'] = strip_tags($new_instance['wid_trans16']);
		$instance['wid_trans17'] = strip_tags($new_instance['wid_trans17']);
		$instance['wid_trans18'] = strip_tags($new_instance['wid_trans18']);
		$instance['wid_trans19'] = strip_tags($new_instance['wid_trans19']);
 
		return $instance;
	}
 
	function form($instance) {
	//widgetform in backend

		$instance = wp_parse_args( (array) $instance, array( 
		    'wid_title' => '', 
			'wid_email' => '',  
			'wid_trans1' => 'This field is required. Please enter a value.',
			'wid_trans2' => 'Invalid email address.',
			'wid_trans3' => 'Contact Form Submission from ',
			'wid_trans5' => 'From: ',
			'wid_trans6' => 'Reply-To: ',
			'wid_trans7' => 'You emailed ',
			'wid_trans9' => 'You forgot to enter your',
			'wid_trans10' => 'You entered an invalid',
			'wid_trans11' => '<b>Thanks!</b> Your email was successfully sent.',
			'wid_trans12' => 'There was an error submitting the form.',
			'wid_trans13' => 'E-mail has not been setup properly. Please add your contact e-mail!',
			'wid_trans14' => 'Name<span>*</span>',
			'wid_trans15' => 'Email<span>*</span>',
			'wid_trans16' => 'Message<span>*</span>',
			'wid_trans17' => 'Send a copy to yourself',
			'wid_trans18' => 'If you want to submit this form, do not enter anything in this field',
			'wid_trans19' => 'Submit',
		) );
		$wid_title = strip_tags($instance['wid_title']);
		$wid_email = strip_tags($instance['wid_email']);
		$wid_trans1 = strip_tags($instance['wid_trans1']);
		$wid_trans2 = strip_tags($instance['wid_trans2']);
		$wid_trans3 = strip_tags($instance['wid_trans3']);
		$wid_trans5 = strip_tags($instance['wid_trans5']);
		$wid_trans6 = strip_tags($instance['wid_trans6']);
		$wid_trans7 = strip_tags($instance['wid_trans7']);
		$wid_trans9 = strip_tags($instance['wid_trans9']);
		$wid_trans10 = strip_tags($instance['wid_trans10']);
		$wid_trans11 = strip_tags($instance['wid_trans11']);
		$wid_trans12 = strip_tags($instance['wid_trans12']);
		$wid_trans13 = strip_tags($instance['wid_trans13']);
		$wid_trans14 = strip_tags($instance['wid_trans14']);
		$wid_trans15 = strip_tags($instance['wid_trans15']);
		$wid_trans16 = strip_tags($instance['wid_trans16']);
		$wid_trans17 = strip_tags($instance['wid_trans17']);
		$wid_trans18 = strip_tags($instance['wid_trans18']);
		$wid_trans19 = strip_tags($instance['wid_trans19']);

?>

			<p><label for="<?php echo $this->get_field_id('wid_title'); ?>"><b>Widget Title</b>: <input class="widefat" id="<?php echo $this->get_field_id('wid_title'); ?>" name="<?php echo $this->get_field_name('wid_title'); ?>" type="text" value="<?php echo esc_attr($wid_title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_email'); ?>"><b>Your Email</b>: <input class="widefat" id="<?php echo $this->get_field_id('wid_email'); ?>" name="<?php echo $this->get_field_name('wid_email'); ?>" type="text" value="<?php echo esc_attr($wid_email); ?>" /></label></p>
			<p>
			    <label><b>Form Labels</b>: </label>
				<input class="widefat spb" id="<?php echo $this->get_field_id('wid_trans15'); ?>" name="<?php echo $this->get_field_name('wid_trans15'); ?>" type="text" value="<?php echo esc_attr($wid_trans15); ?>" />
				<input class="widefat spb" id="<?php echo $this->get_field_id('wid_trans16'); ?>" name="<?php echo $this->get_field_name('wid_trans16'); ?>" type="text" value="<?php echo esc_attr($wid_trans16); ?>" />
				<input class="widefat spb" id="<?php echo $this->get_field_id('wid_trans17'); ?>" name="<?php echo $this->get_field_name('wid_trans17'); ?>" type="text" value="<?php echo esc_attr($wid_trans17); ?>" />
				<input class="widefat spb" id="<?php echo $this->get_field_id('wid_trans18'); ?>" name="<?php echo $this->get_field_name('wid_trans18'); ?>" type="text" value="<?php echo esc_attr($wid_trans18); ?>" />
				<input class="widefat spb" id="<?php echo $this->get_field_id('wid_trans19'); ?>" name="<?php echo $this->get_field_name('wid_trans19'); ?>" type="text" value="<?php echo esc_attr($wid_trans19); ?>" />
			</p>
			<p>
			    <label><span class="translate">Translations</span></label>
			</p>
			<p>
			    <label class="tog"><b>Email Template Translations</b>: </label>
				<input class="widefat spb tog" id="<?php echo $this->get_field_id('wid_trans5'); ?>" name="<?php echo $this->get_field_name('wid_trans5'); ?>" type="text" value="<?php echo esc_attr($wid_trans5); ?>" />
				<input class="widefat spb tog" id="<?php echo $this->get_field_id('wid_trans6'); ?>" name="<?php echo $this->get_field_name('wid_trans6'); ?>" type="text" value="<?php echo esc_attr($wid_trans6); ?>" />
				<input class="widefat spb tog" id="<?php echo $this->get_field_id('wid_trans7'); ?>" name="<?php echo $this->get_field_name('wid_trans7'); ?>" type="text" value="<?php echo esc_attr($wid_trans7); ?>" />
			</p>
			<p>
			    <label class="tog"><b>Error Translations</b>:</label>
			    <input class="widefat spb tog" id="<?php echo $this->get_field_id('wid_trans1'); ?>" name="<?php echo $this->get_field_name('wid_trans1'); ?>" type="text" value="<?php echo esc_attr($wid_trans1); ?>" />
				<input class="widefat spb tog" id="<?php echo $this->get_field_id('wid_trans2'); ?>" name="<?php echo $this->get_field_name('wid_trans2'); ?>" type="text" value="<?php echo esc_attr($wid_trans2); ?>" />
				<input class="widefat spb tog" id="<?php echo $this->get_field_id('wid_trans3'); ?>" name="<?php echo $this->get_field_name('wid_trans3'); ?>" type="text" value="<?php echo esc_attr($wid_trans3); ?>" />
			</p>
			<p>
			    <label class="tog"><b>Live Error Translations</b>:</label>
				<input class="widefat spb tog" id="<?php echo $this->get_field_id('wid_trans9'); ?>" name="<?php echo $this->get_field_name('wid_trans9'); ?>" type="text" value="<?php echo esc_attr($wid_trans9); ?>" />
				<input class="widefat spb tog" id="<?php echo $this->get_field_id('wid_trans10'); ?>" name="<?php echo $this->get_field_name('wid_trans10'); ?>" type="text" value="<?php echo esc_attr($wid_trans10); ?>" />
				<input class="widefat spb tog" id="<?php echo $this->get_field_id('wid_trans11'); ?>" name="<?php echo $this->get_field_name('wid_trans11'); ?>" type="text" value="<?php echo esc_attr($wid_trans11); ?>" />
				<input class="widefat spb tog" id="<?php echo $this->get_field_id('wid_trans12'); ?>" name="<?php echo $this->get_field_name('wid_trans12'); ?>" type="text" value="<?php echo esc_attr($wid_trans12); ?>" />
				<input class="widefat spb tog" id="<?php echo $this->get_field_id('wid_trans13'); ?>" name="<?php echo $this->get_field_name('wid_trans13'); ?>" type="text" value="<?php echo esc_attr($wid_trans13); ?>" />
				<input class="widefat spb tog" id="<?php echo $this->get_field_id('wid_trans14'); ?>" name="<?php echo $this->get_field_name('wid_trans14'); ?>" type="text" value="<?php echo esc_attr($wid_trans14); ?>" />
			</p>
			
<?php
	}
}
register_widget('Cwidget');

// =============================== Tabs Widget ======================================

class Tabs_widget extends WP_Widget {

   function Tabs_widget() {
	//Constructor
	
		$widget_ops = array('classname' => 'widget tabs_widget', 'description' => 'Tabs widget containing the Popular posts, Latest Posts, Recent comments and a Tag cloud.' );
		$control_ops = array('width' => 400);
		$this->WP_Widget('widget_tabs_widget', 'Bizz &rarr; Tabs', $widget_ops, $control_ops);
	}
 
	function widget($args, $instance) {
	// prints the widget

		extract($args, EXTR_SKIP);
		
		$wid_number = empty($instance['wid_number']) ? '&nbsp;' : apply_filters('widget_wid_number', $instance['wid_number']);
		$wid_thumb = empty($instance['wid_thumb']) ? '&nbsp;' : apply_filters('widget_wid_thumb', $instance['wid_thumb']);
		$wid_id = $args['widget_id'];
		
		echo $before_widget;
		
		?>
<script type="text/javascript">
jQuery(document).ready(function() {
	// Tabs	
	var tag_cloud_class = '#tagcloud'; 
	var tag_cloud_height = jQuery('#tagcloud').height();
	jQuery('.tabs').each(function(){
		jQuery(this).children('li').children('a:first').addClass('selected'); // Add .selected class to first tab on load
	});
	jQuery('.inside > *').hide();
	jQuery('.inside > *:first-child').show();
	jQuery('.tabs li a').click(function(evt){ // Init Click funtion on Tabs
	    var clicked_tab_ref = jQuery(this).attr('href'); // Strore Href value
		jQuery(this).parent().parent().children('li').children('a').removeClass('selected'); //Remove selected from all tabs
		jQuery(this).addClass('selected');
		jQuery(this).parent().parent().parent().children('.inside').children('*').hide();
		jQuery('.inside ' + clicked_tab_ref).fadeIn(500);
		 evt.preventDefault();
	});

});
</script>

 		<div class="widtabs">
           
            <ul class="tabs clearfix">
                <li class="popular"><a href="#tab-pop"><?php _e('Popular', 'bizzthemes'); ?></a></li>
                <li class="latest"><a href="#tab-latest"><?php _e('Latest', 'bizzthemes'); ?></a></li>
                <li class="comments"><a href="#tab-comm"><?php _e('Comments', 'bizzthemes'); ?></a></li>
                <li class="tags"><a href="#tab-tags"><?php _e('Tags', 'bizzthemes'); ?></a></li>
            </ul>
            
            <div class="fix"></div>
            
            <div class="inside">
			
                <ul id="tab-pop" class="list">            
                    <?php if ( function_exists('bizz_tabs_popular') ) bizz_tabs_popular($wid_number, $wid_thumb); ?>                    
                </ul>
                <ul id="tab-latest" class="list">
                    <?php if ( function_exists('bizz_tabs_latest') ) bizz_tabs_latest($wid_number, $wid_thumb); ?>                    
                </ul>	
                <ul id="tab-comm" class="list">
                    <?php if ( function_exists('bizz_tabs_comments') ) bizz_tabs_comments($wid_number, $wid_thumb); ?>                    
                </ul>	
                <div id="tab-tags" class="list">
                    <?php wp_tag_cloud('smallest=12&largest=20'); ?>
                </div>
				
            </div><!-- /.inside -->
			
        </div><!-- /.widtabs -->
    
        <?php
		 
		echo $after_widget;
   }
   
   function update($new_instance, $old_instance) {
	//save the widget
	
		$instance = $old_instance;
		$instance['wid_number'] = strip_tags($new_instance['wid_number']);
		$instance['wid_thumb'] = $new_instance['wid_thumb'];
 
		return $instance;
	}
 
	function form($instance) {
	//widgetform in backend

		$instance = wp_parse_args( (array) $instance, array( 'wid_number' => '5', 'wid_thumb' => '35' ) );
		$wid_number = $instance['wid_number'];
		$wid_thumb = $instance['wid_thumb'];
		
	?>
	
			<p><label for="<?php echo $this->get_field_id('wid_number'); ?>">Total number of posts to show: <input class="widefat" id="<?php echo $this->get_field_id('wid_number'); ?>" name="<?php echo $this->get_field_name('wid_number'); ?>" type="text" value="<?php echo esc_attr($wid_number); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('wid_thumb'); ?>">Thumbnail Size (0=disable): <input class="widefat" id="<?php echo $this->get_field_id('wid_thumb'); ?>" name="<?php echo $this->get_field_name('wid_thumb'); ?>" type="text" value="<?php echo esc_attr($wid_thumb); ?>" /></label></p>
	        
	<?php
	
	}

}
register_widget('Tabs_widget');

?>