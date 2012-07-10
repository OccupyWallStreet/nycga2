<?php

// ========================
// = Theme Custom Widgets =
// ========================

if(VPRO && class_exists('WP_Widget')){

	// Grand Child Navigation
		class PageLines_GrandChild extends WP_Widget {
	
		   function PageLines_GrandChild() {
			   $widget_ops = array('description' => __( 'Creates a third tier navigation (Grandchild). Shows on pages when there are three levels; based on page heirarchy.', 'pagelines' ) );
			   parent::WP_Widget(false, $name = __('PageLines Pro - Grandchild Nav', 'pagelines'), $widget_ops);    
		   }
	
		   function widget($args, $instance) {        
			   extract( $args );
		
				// THE TEMPLATE
				global $post;
				if( isset($post) && property_exists($post, 'ancestors') ) $ancestors_array = $post->ancestors;
				else $ancestors_array = array();

				if( isset($post) && !is_search() && ($post->post_parent && wp_list_pages("title_li=&child_of=".$post->ID."&echo=0")) || count($ancestors_array) >= 2):?>
					<div id="grandchildnav" class="widget">

						<div class="winner">	
							<h3 class="widget-title">
							<?php 
									if(count($ancestors_array)==1){
										$subnavpost = get_post($post->ID); 
										$children = wp_list_pages('title_li=&child_of='.$post->ID.'&echo=0&sort_column=menu_order');
									}else{
										$reverse_ancestors = array_reverse($ancestors_array);
										$subnavpost = get_post($reverse_ancestors[1]);
										$children =  wp_list_pages('title_li=&child_of='.$reverse_ancestors[1].'&echo=0&sort_column=menu_order');
									}?>

								<?php echo $subnavpost->post_title;	?>
							</h3>

								<ul>
								<?php if ($children) { echo $children;}?>

								</ul>

						</div>
					</div>
				<?php endif;
				
		   }
	
		   function update($new_instance, $old_instance) {                
			   return $new_instance;
		   }
	
		   function form($instance) {    	   
				echo '<p>' . __('There are no options for this widget.','pagelines') . '</p>';
		   }
	
		} 
		
		
	register_widget('PageLines_GrandChild');
}
