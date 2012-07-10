<?php
/*
	Section: QuickSlider
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A responsive slider that is easy to use and setup.
	Class Name: PageLinesQuickSlider	
	Cloning: true
	Workswith: main, templates, sidebar_wrap
*/

/**
 * Main section class
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesQuickSlider extends PageLinesSection {

	var $default_limit = 2;

	/**
	 * Load styles and scripts
	 */
	function section_styles(){
		wp_enqueue_script( 'flexslider', $this->base_url.'/flexslider/jquery.flexslider-min.js', array( 'jquery' ) );
	}
	
	function section_head($clone_id){
		
		$animation = (ploption('quick_transition', $this->oset) == 'slide_v' || ploption('quick_transition', $this->oset) == 'slide_h') ? 'slide' : 'fade';
		$transfer = (ploption('quick_transition', $this->oset) == 'slide_v') ? 'vertical' : 'horizontal';
		
		$slideshow = (ploption('quick_slideshow', $this->oset)) ? 'true' : 'false';
		
		$clone_class = 'pl-clone'.$clone_id;
		
		$control_nav = (!ploption('quick_nav', $this->oset) || ploption('quick_nav', $this->oset) == 'both' || ploption('quick_nav', $this->oset) == 'control_only') ? 'true' : 'false';
		$direction_nav = (!ploption('quick_nav', $this->oset) || ploption('quick_nav', $this->oset) == 'both' || ploption('quick_nav', $this->oset) == 'arrow_only') ? 'true' : 'false';
		?>
<script>
jQuery(window).load(function() {var theSlider = jQuery('.flexslider.<?php echo $clone_class;?>');theSlider.flexslider({ controlsContainer: '.fs-nav-container',animation: '<?php echo $animation;?>', slideDirection: '<?php echo $transfer;?>', slideshow: <?php echo $slideshow;?>, directionNav: <?php echo $direction_nav;?>, controlNav: <?php echo $control_nav;?>}); });</script>	
<?php }

	/**
	* Section template.
	*/
   function section_template( $clone_id ) { 
	
	$control_nav = (!ploption('quick_nav', $this->oset) || ploption('quick_nav', $this->oset) == 'both' || ploption('quick_nav', $this->oset) == 'control_only') ? 'true' : 'false';
	
	$nav_class = ($control_nav) ? 'control-nav' : 'no-control-nav';
	?>
	<div class="flexwrap animated fadeIn <?php echo 'wrap-'.$nav_class;?>">
		<div class="fslider">
		<div class="flexslider <?php echo 'pl-clone'.$clone_id;?>">
		  <ul class="slides">
			
			<?php
			
			$slides = (ploption('quick_slides', $this->oset)) ? ploption('quick_slides', $this->oset) : $this->default_limit;
			
			$output = '';
			for($i = 1; $i <= $slides; $i++){
			
				if(ploption('quick_image_'.$i, $this->oset)){
					
					$the_text = ploption('quick_text_'.$i, $this->tset);
					
					$tlocation = ploption('quick_text_location_'.$i, $this->oset);
					
					if($tlocation == 'right_top')
						$caption_style = 'right:0; bottom: auto; top:0;';	
					elseif($tlocation == 'left_bottom')
						$caption_style = 'left:0; bottom:0; top: auto;';
					elseif($tlocation == 'left_top')
						$caption_style = 'left:0; bottom:auto; top: 0;';	
					else
						$caption_style = 'right:0; bottom:0; top: auto;';
						
							
					$text = ($the_text) ? sprintf('<p class="flex-caption" style="%s">%s</p>', $caption_style, $the_text) : '';
					
					$img = sprintf('<img src="%s" />', ploption( 'quick_image_'.$i, $this->tset ) );
					
					$slide = (ploption('quick_link_'.$i, $this->oset)) ? sprintf('<a href="%s">%s</a>', ploption('quick_link_'.$i, $this->oset), $img ) : $img;						
					$output .= sprintf('<li>%s %s</li>',$slide, $text);
				}
			}
			
			if($output == ''){
				$this->do_defaults();
			} else {
				echo $output;
				
			}
			
			
			?>
		  </ul>
		</div>
		</div>
		<div class="fs-nav-container <?php echo $nav_class;?>"></div>
	</div>
	
		<?php 
	}

	function do_defaults(){
		
		printf(
			'<li><img src="%s" /></li><li><img src="%s" /></li><li><img src="%s" /></li>', 
			$this->images.'/image3.jpg', 
			$this->images.'/image1.jpg', 
			$this->images.'/image2.jpg'
		);
	}

	/**
	 *
	 * Page-by-page options for PostPins
	 *
	 */
	function section_optionator( $settings ){
		$settings = wp_parse_args( $settings, $this->optionator_default );
		
			$array = array(); 
			
			$array['quick_slides'] = array(
				'type' 			=> 'count_select',
				'count_start'	=> 1, 
				'count_end'		=> 10,
				'default'		=> '3',
				'inputlabel' 	=> __( 'Number of Slides to Configure', 'pagelines' ),
				'title' 		=> __( 'Number of Slides', 'pagelines' ),
				'shortexp' 		=> __( 'Enter the number of QuickSlider slides. <strong>Default is 3</strong>', 'pagelines' ),
				'exp' 			=> __( "This number will be used to generate slides and option setup.", 'pagelines' ),
		
			);
			
			$array['quick_transition'] = array(
				'type' 			=> 'select',
				'selectvalues' => array(
					'fade' 			=> array('name' => __( 'Use Fading Transition', 'pagelines' ) ),
					'slide_h' 		=> array('name' => __( 'Use Slide/Horizontal Transition', 'pagelines' ) ),						
				),
				'inputlabel' 	=> __( 'Select Transition Type', 'pagelines' ),
				'title' 		=> __( 'Slider Transition Type', 'pagelines' ),
				'shortexp' 		=> __( 'Configure the way slides transfer to one another.', 'pagelines' ),
				'exp' 			=> __( "", 'pagelines' ),
		
			);
			
			$array['quick_nav'] = array(
				'type' 			=> 'select',
				'selectvalues' => array(
					'both' 			=> array('name' => __( 'Use Both Arrow and Slide Control Navigation', 'pagelines' ) ),
					'none'			=> array('name' => __( 'No Navigation', 'pagelines' ) ),	
					'control_only'	=> array('name' => __( 'Slide Controls Only', 'pagelines' ) ),	
					'arrow_only'	=> array('name' => __( 'Arrow Navigation Only', 'pagelines' ) ),						
				),
				'inputlabel' 	=> __( 'Slider Navigation', 'pagelines' ),
				'title' 		=> __( 'Slider Navigation mode', 'pagelines' ),
				'shortexp' 		=> __( 'Configure the navigation for this slider.', 'pagelines' ),
				'exp' 			=> __( "", 'pagelines' ),
		
			);
			
			$array['quick_slideshow'] = array(
				'type' 			=> 'check',
				
				'inputlabel' 	=> __( 'Animate Slideshow Automatically?', 'pagelines' ),
				'title' 		=> __( 'Automatic Slideshow?', 'pagelines' ),
				'shortexp' 		=> __( 'Autoplay the slides, transitioning every 7 seconds.', 'pagelines' ),
				'exp' 			=> __( "", 'pagelines' ),
		
			);
			
			global $post_ID;
			
			$oset = array('post_id' => $post_ID, 'clone_id' => $settings['clone_id'], 'type' => $settings['type']);
			
			$slides = (ploption('quick_slides', $oset)) ? ploption('quick_slides', $oset) : $this->default_limit;
			
			for($i = 1; $i <= $slides; $i++){
				
				
				$array['quick_slide_'.$i] = array(
					'type' 			=> 'multi_option',
					'selectvalues' => array(
						'quick_image_'.$i 	=> array(
							'inputlabel' 	=> __( 'Slide Image', 'pagelines' ), 
							'type'			=> 'image_upload'
						),
						'quick_text_'.$i 	=> array(
							'inputlabel'	=> __( 'Slide Text', 'pagelines' ), 
							'type'			=> 'textarea'
						),	
						'quick_link_'.$i 	=> array(
							'inputlabel'	=> __( 'Slide Link URL', 'pagelines' ), 
							'type'			=> 'text'
						),	
						'quick_text_location_'.$i 	=> array(
							'inputlabel'	=> __( 'Slide Text Location', 'pagelines' ), 
							'type'			=> 'select', 
							'selectvalues'	=> array(
								'right_bottom'	=> array('name'=> 'Right/Bottom'),
								'right_top'		=> array('name'=> 'Right/Top'),
								'left_bottom'	=> array('name'=> 'Left/Bottom'),
								'left_top'		=> array('name'=> 'Left/Top')
							)
						),
					),
					'title' 		=> __( 'QuickSlider Slide ', 'pagelines' ) . $i,
					'shortexp' 		=> __( 'Setup options for slide number ', 'pagelines' ) . $i,
					'exp'			=> __( 'For best results all images in the slider should have the same dimensions.', 'pagelines')
				);
				
			}
				
			

			$metatab_settings = array(
					'id' 		=> 'quickslider_options',
					'name' 		=> __( 'QuickSlider', 'pagelines' ),
					'icon' 		=> $this->icon, 
					'clone_id'	=> $settings['clone_id'], 
					'active'	=> $settings['active']
				);

			register_metatab( $metatab_settings, $array );

	}

}
