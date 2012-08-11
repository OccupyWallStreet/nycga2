<?php
/*
Plugin Name: DynamicWP Featured Post
Plugin URI: http://www.dynamicwp.net/plugins/free-plugin-dynamicwp-featured-post/
Description: The Plugin will create list of featured posts, displayed as a widget. credit to http://jquery.malsup.com/cycle/ for the jquery.cycle.js plugin
Author: Reza Erauansyah
Version: 1.0
Author URI: http://www.dynamicwp.net
*/

// =============================== Cube Widget ======================================



class DynamicWP_featured extends WP_Widget {

   function DynamicWP_featured() {
	   $widget_ops = array('description' => 'Populate your sidebar with posts from a tag category.' );
       parent::WP_Widget(false, __('Featured Posts', 'DWPPlugin'),$widget_ops);      
   }
   

   function widget($args, $instance) {  
   




	extract( $args );
    $tag_id = $instance['tag_id'];
    $num = $instance['num'];
	$num = ($num) ? $num : 3;
	$title = $instance['title'];
    $length = $instance['length'];
	$length = ($length) ? $length : 20;
    $height = $instance['height'];
    $height = ($height) ? $height : 96;

     $tag_name = get_term_by('id', $tag_id, 'post_tag');
     $string = "tag=" . $tag_name->name ."&showposts=$num";
     $posts = get_posts($string);
	 
	 if($title == ''){ $title = 'Featured Posts'; } 
	 
     global $post, $posts;
     ?>
     
	 	<?php 
			$myid = $args['widget_id'];
			$linkss = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 
			echo "<script type=\"text/javascript\" charset=\"utf-8\" src=\"".$linkss."jquery.cycle.js\"></script>";
			echo "<script type=\"text/javascript\" charset=\"utf-8\">
			/* <![CDATA[ */
				$(document).ready(function($){
					$('#dwp-featured-post-".$myid."').cycle({ 
						fx: 'scrollVert',
						next:   'a#dwp-featuered-next', 
						prev:   'a#dwp-feateured-prev'
					 });      
				 
				});
			/* ]]> */
			</script>";
			echo $before_widget;
			if($title) echo $before_title . $title . $after_title;
		?>

        <ul style="margin: 0; margin-top: 4px; border-bottom: 2px solid #DFDFDF; padding: 0; position: relative; list-style: none;" id="dwp-featured-post-<?php echo $myid; ?>">
                    
            <?php if ($posts) : $count = 0; ?>
            <?php foreach ($posts as $post) : setup_postdata($post); $count++; ?>
            <?php 
				$text = get_the_excerpt();

				$text = strip_shortcodes( $text );

				$text = apply_filters('the_content', $text);
				$text = str_replace(']]>', ']]>', $text);
				$text = strip_tags($text);
				$excerpt_length = apply_filters('excerpt_length', $length);
				$words = explode(' ', $text, $excerpt_length + 1);
				if (count($words) > $excerpt_length) {
					array_pop($words);
					array_push($words, '...');
					$text = implode(' ', $words);
				}
				
				$first_img = '';
				ob_start();
				ob_end_clean();
				$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
				$first_img = $matches [1] [0];
			?>
			<li style="margin: 0; padding-top: 0; padding: 0; background: transparent; bottom: 0; min-height: <?php echo $height; ?>px; list-style: none; border: none;">
					<?php the_time('j M Y') ?> | <?php comments_popup_link('0 Comments', '1 Comment', '% Comments'); ?><br /><br />
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a><br />
					<?php if ($first_img) {?>
						<img src="<?php echo $first_img; ?>" alt="img" width="40" height="40" style="float: left; margin: 4px 4px 2px 0;"/>
					<?php } ?>
					<?php echo $text; ?>
					<div style="display: block; clear: both;"></div>
			</li>
                <!-- Post Ends -->
                
            <?php endforeach; else: ?>
            <?php endif; ?>
         </ul>
		 <div style="margin-top: 4px;">
			<span style="font-size: 9px;">
				widget by <a href="http://www.dynamicwp.net">DynamicWP</a>
			</span>
		 	<a href="" id="dwp-featuered-next" style="margin: 0; padding: 0; background: transparent; float: right;margin-left: 4px;"><img style="margin: 0; padding: 0; background: transparent;" src="<?php echo $linkss.'/images/next.png'; ?>" alt="next" /></a>
			<a href="" id="dwp-feateured-prev" style="margin: 0; padding: 0; background: transparent;float: right;"><img style="margin: 0; padding: 0; background: transparent;" src="<?php echo $linkss.'/images/prev.png'; ?>" alt="prev" /></a>
<br />
			
		</div>
		<div style="display: block; clear: both;"></div>

			<?php echo $after_widget; ?>
            
            
            <?php
			
           
		   	
            
   }

   function update($new_instance, $old_instance) {                
       return $new_instance;
   }

   function form($instance) {        
       
       $tag_id = esc_attr($instance['tag_id']);
       $num = esc_attr($instance['num']);
       $title = esc_attr($instance['title']);
       $length = esc_attr($instance['length']);
       $height = esc_attr($instance['height']);
     

       ?>
       

		<p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','DWPPlugin'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
		</p>
		<p>
        <label for="<?php echo $this->get_field_id('num'); ?>"><?php _e('Number of displayed posts:','DWPPlugin'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('num'); ?>" name="<?php echo $this->get_field_name('num'); ?>" type="text" value="<?php if(attribute_escape($num)){echo attribute_escape($num);}else { echo '3';} ?>" />
        </p>
		<p>
        <label for="<?php echo $this->get_field_id('lenght'); ?>"><?php _e('Length of displayed posts:','DWPPlugin'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('length'); ?>" name="<?php echo $this->get_field_name('length'); ?>" type="text" value="<?php if(attribute_escape($length)){echo attribute_escape($length);}else { echo '20';} ?>" />
        </p>
		<p>
        <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('height of displayed posts:','DWPPlugin'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php if(attribute_escape($height)){echo attribute_escape($height);}else { echo '96';} ?>" />
        </p>
        <p>
	   	   <label for="<?php echo $this->get_field_id('tag_id'); ?>"><?php _e('Media Tag:','DWPPlugin'); ?></label>
	       <?php $tags = get_tags(); print_r($cats); ?>
	       <select name="<?php echo $this->get_field_name('tag_id'); ?>" class="widefat" id="<?php echo $this->get_field_id('tag_id'); ?>">
           <option value="">-- Please Select --</option>
			<?php
			
           	foreach ($tags as $tag){
           	?><option value="<?php echo $tag->term_id; ?>" <?php if($tag_id == $tag->term_id){ echo "selected='selected'";} ?>><?php echo $tag->name . ' (' . $tag->count . ')'; ?></option><?php
           	}
           ?>
           </select>
       </p>

      <?php
   }

} 



function mypuncfeatured(){
	if( !is_admin()){
	   wp_deregister_script('jquery');
	   wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"), false, '');
	   wp_enqueue_script('jquery');
	}
}

if(!is_admin()){
   add_action('wp_head', 'mypuncfeatured', 1);
}

// get WP to load our widget
function init_dwp_featured_post(){
	register_widget('DynamicWP_featured');
}
add_action("widgets_init", "init_dwp_featured_post");


?>
