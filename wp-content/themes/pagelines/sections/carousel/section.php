<?php
/*
	Section: Carousel
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates a flickr, nextgen, or featured image carousel.
	Class Name: PageLinesCarousel
	Cloning: true
	Workswith: content, header, footer
	Edition: pro
*/

/**
 * Carousel Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesCarousel extends PageLinesSection {
	

	/**
	* Load js
	*/
	function section_styles(){
		wp_enqueue_script( 'jcarousel', $this->base_url.'/jcarousel.js', array( 'jquery'), null, true);
	}
	
	

		/**
		*
		* @TODO document
		*
		*/
		function section_head( $clone_id = null ) {   

			$carousel_class = ( isset( $clone_id ) && $clone_id != 1 ) ? 'crsl' . $clone_id : 'crsl';

			$num_items = ( ploption('carousel_display_items', $this->oset) ) ? ploption('carousel_display_items', $this->oset) : 9;
			$scroll_items = ( ploption('carousel_scroll_items', $this->oset) ) ? ploption('carousel_scroll_items', $this->oset) : 6;
			$anim_speed = ( ploption('carousel_animation_speed', $this->oset) ) ? ploption('carousel_animation_speed', $this->oset) : 800;
			$callback = ( 0 != ploption('carousel_scroll_time', $this->oset) ) ? ',initCallback: mycarousel_initCallback' : '';			
			$auto = ( 0 != ploption('carousel_scroll_time', $this->oset) ) ? round( ploption('carousel_scroll_time', $this->oset) ) / 1000 : 0;
			
			
			$carousel_args = sprintf('wrap: "circular", visible: %s, easing: "%s", scroll: %s, animation: %s, auto: %s %s', $num_items, 'swing', $scroll_items, $anim_speed, $auto, $callback);
			?>
	<script type="text/javascript">
	/* <![CDATA[ */
	<?php if ( 0 != ploption('carousel_scroll_time', $this->oset) ) : ?>

	/**
	*
	* @TODO document
	*
	*/
	function mycarousel_initCallback(carousel)
	{
	    // Disable autoscrolling if the user clicks the prev or next button.
	    carousel.buttonNext.bind('click', function() {
	        carousel.startAuto(0);
	    });

	    carousel.buttonPrev.bind('click', function() {
	        carousel.startAuto(0);
	    });

	    // Pause autoscrolling if the user moves with the cursor over the clip.
	    carousel.clip.hover(function() {
	        carousel.stopAuto();
	    }, function() {
	        carousel.startAuto();
	    });
	};
<?php endif; ?>
		jQuery(document).ready(function () {
			<?php printf('jQuery(".%s").show().jcarousel({%s});', $carousel_class, $carousel_args); ?>
			jQuery(".jcarousel-prev, .jcarousel-next").disableTextSelect().hover(function(){ 
				jQuery(this).fadeTo('fast', 1); },
				function(){ jQuery(this).fadeTo('fast', 0.5);}
			);
			
		
		});
	/* ]]> */
	</script>
	<?php }
	

	/**
	*
	* @TODO document
	*
	*/
	function section_optionator( $settings ){
		$settings = wp_parse_args($settings, $this->optionator_default);
		
			$metatab_array = array(
					'carousel_numbers' => array(
							'type' 		=> 'text_multi',
							'inputsize'	=> 'small',
							'selectvalues'=> array(
								'carousel_items'			=> array('inputlabel'=>__( 'Total Carousel Items', 'pagelines' ) ),
								'carousel_display_items'	=> array('inputlabel'=>__( 'Displayed Carousel Items', 'pagelines' ) , 'default' => 7),
								'carousel_scroll_items'		=> array('inputlabel'=>__( 'Scrolled Carousel Items', 'pagelines' ) , 'default' => 4),
								'carousel_animation_speed'	=> array('inputlabel'=>__( 'Transition Speed (milliseconds)', 'pagelines' ) , 'default' => 800),
								'carousel_scroll_time'		=> array('inputlabel'=>__( 'Autoscroll Speed (milliseconds)', 'pagelines' ) , 'default' => 0),
							),
							'title' 	=> __( 'Carousel Display and Scroll', 'pagelines' ),
							'shortexp' 	=> __( 'The total numbers for total, shown and scrolled images', 'pagelines' ),
							'exp' 		=> __( 'Use this option to control the number of carousel items, the total shown, and the number scrolled at one time.', 'pagelines' )
					),
					'carousel_mode' => array(
						'version' => 'pro',
						'type' => 'select',
						'default'	=> 'posts',
						'selectvalues'=> array(
							'posts' 		=> array( 'name' => __( 'Post Thumbnails (posts)', 'pagelines' ) ),							
							'flickr'		=> array( 'name' => __( 'Flickr', 'pagelines' ) ),
							'ngen_gallery' 	=> array( 'name' => __( 'NextGen Gallery', 'pagelines' ) ), 
							'hook'			=> array( 'name' => __( 'Hook: "pagelines_carousel_list"', 'pagelines' ) )
						),					
						'title' 	=> __( 'Carousel Image/Link Mode (Carousel Page Template)', 'pagelines' ),
						'shortexp' 	=> __( 'Select the mode that the carousel should use for its thumbnails.', 'pagelines' ),
						'exp'		=> __( '<strong> Post Thumbnails (default)</strong> - Uses links and thumbnails from posts <br/><strong>Flickr</strong> - Uses thumbs from FlickrRSS plugin.<br/><strong>NextGen Gallery</strong> - Uses an image gallery from the NextGen Gallery Plugin', 'pagelines' )
					),
					'carousel_image_dimensions' => array(
							'type' 		=> 'text_multi',
							'inputsize'	=> 'small',
							'selectvalues'=> array(
								'carousel_image_width'		=> array('inputlabel'=>__( 'Max Image Width (in pixels)', 'pagelines' ), 'default'	=> 64),
								'carousel_image_height'		=> array('inputlabel'=>__( 'Max Image Height (in pixels)', 'pagelines' ), 'default' => 64),
							),
							'title' 	=> __( 'Carousel Image Dimensions (Posts Mode Only)', 'pagelines' ),
							'shortexp' 	=> __( 'Control the dimensions of the carousel images', 'pagelines' ),
							'exp' 		=> __( 'Use this option to control the max height and width of the images in the carousel. You may have to use this option in conjunction with the scroll items option.<br/><br/> For the FlickrRSS and NextGen Gallery modes, image sizes are set by Flickr thumb sizes and the NextGen Gallery plugin respectively.', 'pagelines' )
					),
					'carousel_post_id' => array(
						'default'		=> '', 
						'type' 			=> 'select_taxonomy',
						'taxonomy_id'	=> 'category',		
						'title'			=> __( 'Posts Mode - Select Post Category', 'pagelines' ), 
						'shortexp'		=> __( 'The category slug to pull posts from', 'pagelines' ),
						'inputlabel' 	=> __( 'Select Category for Carousel', 'pagelines' ),
						'exp' 			=> __( 'Posts Mode - Select the default category for carousel post images.  If not set, the carousel will get the most recent posts.', 'pagelines' )
					),
					'carousel_ngen_gallery' => array(
						'version' => 'pro',
						'type' => 'text',					
						'title' => __( 'NextGen Gallery ID For Carousel (Carousel Page Template / NextGen Mode)', 'pagelines' ),
						'shortexp' => __( 'Enter the ID of the NextGen Image gallery for the carousel.', 'pagelines' ), 
						'exp'		=> __( '<strong>Note:</strong>The NextGen Gallery and carousel template must be selected.', 'pagelines' )
					)					
				);
			
			$metatab_settings = array(
					'id' 		=> 'carousel_meta',
					'name'	 	=> 'Carousel',
					'icon' 		=> $this->icon,
					'clone_id'	=> $settings['clone_id'], 
					'active'	=> $settings['active']
				);
			
			register_metatab($metatab_settings, $metatab_array);
	}
	
   function section_template( $clone_id ) { 
		
		$carousel_class = (isset($clone_id) && $clone_id != 1) ? 'crsl'.$clone_id : 'crsl';
	
		// Set Up Variables
		$carouselitems = (ploption('carousel_items', $this->oset)) ? ploption('carousel_items', $this->oset) : 30;
		$carousel_post_id = (ploption('carousel_post_id', $this->oset)) ? ploption('carousel_post_id', $this->oset) : null;
		$carousel_image_width = (ploption('carousel_image_width', $this->oset)) ? ploption('carousel_image_width', $this->oset) : 64;
		$carousel_image_height = (ploption('carousel_image_height', $this->oset)) ? ploption('carousel_image_height', $this->oset) : 64;
		$cmode = (ploption('carousel_mode', $this->oset)) ? ploption('carousel_mode', $this->oset): null;
		$ngen_id = (ploption('carousel_ngen_gallery', $this->oset)) ? ploption('carousel_ngen_gallery', $this->oset) : 1;
		
		
	if( ($cmode == 'flickr' && !function_exists('get_flickrRSS')) || ($cmode == 'ngen_gallery' && !function_exists('nggDisplayRandomImages')) ){
	
		echo setup_section_notify($this, __("The <strong>plugin</strong> for the selected carousel mode needs to be activated (FlickrRSS or NextGen Gallery).", 'pagelines'), admin_url().'plugins.php', 'Setup Plugin');
	
	} else {
	?>		
	<div class="<?php echo $carousel_class;?> thecarousel">
		<ul id="mycarousel" class="mycarousel">
			<?php 
			
			if(function_exists('nggDisplayRandomImages')  && $cmode == 'ngen_gallery'){
		
				echo do_shortcode('[nggallery id='.$ngen_id.' template=plcarousel]');
				
			}elseif(function_exists('get_flickrRSS') && $cmode == 'flickr'){
			
				if(!function_exists('get_and_delete_option')):  // fixes instantiation within the function in the plugin :/
					get_flickrRSS( array(
						'num_items' => $carouselitems, 
						'html' => '<li><a href="%flickr_page%" title="%title%"><img src="%image_square%" alt="%title%"/><span class="list-title">%title%</span></a></li>'	
					));
				endif;
			
			}elseif($cmode == 'hook')
				pagelines_register_hook('pagelines_carousel_list');
				
			else{
			
				$carousel_post_query = 'numberposts='.$carouselitems;
				
				if($carousel_post_id) 
					$carousel_post_query .= '&category_name='.$carousel_post_id;
				
				$recentposts = get_posts($carousel_post_query);
				
				foreach($recentposts as $cid => $c){
				
					$a = array();
				
					if(has_post_thumbnail($c->ID)){
						$img_data = wp_get_attachment_image_src( get_post_thumbnail_id( $c->ID ));
					
						$a['img'] = ($img_data[0] != '') ? $img_data[0] : $this->base_url.'/post-blank.jpg';
					
						$a['width'] = $img_data[1];
						$a['height'] = $img_data[2];
						
					} else {
						$a['img'] = $this->base_url.'/post-blank.jpg';
						$a['width'] = 100;
						$a['height'] = 100;
					}
						
					$args = array(
						'title'			=> $c->post_title,
						'link'			=> get_permalink($c->ID), 
						'img'			=> $a['img'], 
						'maxheight'		=> $carousel_image_height,
						'maxwidth'		=> $carousel_image_width, 
						'height'		=> $a['height'], 
						'width'			=> $a['width']
					);
					
					
					
					echo $this->carousel_item($args);
				}
				
			} ?>
		</ul>
	</div>
		
<?php  

		}
	}


	/**
	*
	* @TODO document
	*
	*/
	function carousel_item( $args ){
		
		$d = array(
			'title'			=> '', 
			'link'			=> '', 
			'height'		=> '100', 
			'width'			=> '100',
			'maxheight'		=> '100',
			'maxwidth'		=> '100',
			'img'			=> '', 
			'class'			=> '',
		);
		
		$a = wp_parse_args($args, $d);
		
		$img_style = sprintf('style="max-height: %spx; max-width: %spx;"', $a['maxheight'], $a['maxwidth']);
		
		$img = sprintf('<img src="%s" width="%s" height="%s" %s />', $a['img'], $a['width'], $a['height'], $img_style);

		$link = sprintf('<a class="carousel_image_link" href="%s">%s<span class="list-title">%s</span></a>', $a['link'], $img, $a['title']);
		
		$out = sprintf('<li class="list-item fix">%s</li>', $link);
		
		return $out;
	}
}
