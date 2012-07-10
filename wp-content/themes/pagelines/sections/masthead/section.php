<?php
/*
	Section: Masthead
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A responsive full width splash and text area. Great for getting big ideas across quickly.
	Class Name: PLMasthead	
	Workswith: templates, main, header, morefoot
*/

/**
 * Main section class
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PLMasthead extends PageLinesSection {
    
    var $tabID = 'masthead_meta';
	
	function section_optionator( $settings ){
		
		$settings = wp_parse_args($settings, $this->optionator_default);
		
		$option_array = array(
				'pagelines_masthead_splash_multi' => array(
					'type' 				=> 'multi_option',
					'title' 			=> __('Masthead Splash Options','pagelines'),	
					'shortexp'	=> __('Enter the options for the masthead splash image. If no options are specified, no image will be shown.', 'pagelines'),
					'selectvalues'	=> array(
						'pagelines_masthead_img' => array(
							'type' 			=> 'image_upload',
							'imagepreview' 	=> '270',
							'inputlabel' 	=> 'Upload custom image',
						),
						'pagelines_masthead_imgalt' => array(
							'type' 			=> 'text',
							'inputlabel' 	=> 'Masthead Image Alt',	
						),
					)			
				),
				'pagelines_masthead_text' => array(
						'type' 				=> 'text_multi',
						'layout'			=> 'full',
						'inputlabel' 		=> 'Enter text for your masthead banner section',
						'title' 			=> $this->name.' Text',	
						'selectvalues'	=> array(
							'pagelines_masthead_title'		=> array('inputlabel'=>'Title', 'default'=> ''),
							'pagelines_masthead_tagline'	=> array('inputlabel'=>'Tagline', 'default'=> '')
						),				
						'shortexp' 			=> 'The text for the masthead section',

				),
				'masthead_button_multi_1' => array(
					'type'		=> 'multi_option', 
					'title'		=> __('Masthead Action Button 1', 'pagelines'), 
					'shortexp'	=> __('Enter the options for the masthead button. If no options are specified, no button will be shown.', 'pagelines'),
					'selectvalues'	=> array(
						'masthead_button_link_1' => array(
							'type' => 'text',
							'inputlabel' => 'Enter the link destination (URL - Required)',

						),
						'masthead_button_text_1' => array(
							'type' 			=> 'text',
							'inputlabel' 	=> 'Masthead Button Text',					
						 ),
						
						'masthead_button_target_1' => array(
							'type'			=> 'check',
							'default'		=> false,
							'inputlabel'	=> 'Open link in new window.',
						),
						'masthead_button_theme_1' => array(
							'type'			=> 'select',
							'default'		=> false,
							'inputlabel'	=> 'Select Button Color',
							'selectvalues'	=> array(
								'primary'	=> array('name' => 'Blue'), 
								'warning'	=> array('name' => 'Orange'), 
								'important'	=> array('name' => 'Red'), 
								'success'	=> array('name' => 'Green'), 
								'info'		=> array('name' => 'Light Blue'), 
								'reverse'	=> array('name' => 'Grey'), 
							),
						),
					)
				),
				'masthead_button_multi_2' => array(
					'type'		=> 'multi_option', 
					'title'		=> __('Masthead Action Button 2', 'pagelines'), 
					'shortexp'	=> __('Enter the options for the masthead button. If no options are specified, no button will be shown.', 'pagelines'),
					'selectvalues'	=> array(
						'masthead_button_link_2' => array(
							'type' => 'text',
							'inputlabel' => 'Enter the link destination (URL - Required)',

						),
						'masthead_button_text_2' => array(
							'type' 			=> 'text',
							'inputlabel' 	=> 'Masthead Button Text',					
						 ),
						
						'masthead_button_target_2' => array(
							'type'			=> 'check',
							'default'		=> false,
							'inputlabel'	=> 'Open link in new window.',
						),
						'masthead_button_theme_2' => array(
							'type'			=> 'select',
							'default'		=> false,
							'inputlabel'	=> 'Select Button Color',
							'selectvalues'	=> array(
								'primary'	=> array('name' => 'Blue'), 
								'warning'	=> array('name' => 'Orange'), 
								'important'	=> array('name' => 'Red'), 
								'success'	=> array('name' => 'Green'), 
								'info'		=> array('name' => 'Light Blue'), 
								'reverse'	=> array('name' => 'Grey'), 
							),
						),
					)
				),
				'masthead_menu' => array(
					'shortexp'	=> __('Choose a Wordpress menu to display (optional)', 'pagelines'),
						'type' 			=> 'select_menu',
						'title'			=> 'Masthead Menu',
						'inputlabel' 	=> 'Select Masthead Menu',
					),
				'masthead_meta' => array(
					'shortexp'	=> __('Enter text to be shown on Masthead (optional)', 'pagelines'),
						'type' 			=> 'textarea',
						'title'			=> 'Masthead Meta',
						'inputlabel' 	=> 'Enter Masthead Meta Text',
					),
				
			);
		
		$metatab_settings = array(
				'id' 		=> $this->tabID,
				'name' 		=> 'Masthead',
				'icon' 		=> $this->icon, 
				'clone_id'	=> $settings['clone_id'], 
				'active'	=> $settings['active']
			);
		
		register_metatab($metatab_settings, $option_array);
	}

	/**
	* Section template.
	*/
   function section_template( $clone_id ) { 
   		$mast_title = ploption('pagelines_masthead_title', $this->oset);
   		$mast_img = ploption('pagelines_masthead_img', $this->oset);
		$mast_imgalt = ploption('pagelines_masthead_imgalt', $this->oset);
		$mast_tag = ploption('pagelines_masthead_tagline', $this->oset);
		$mast_menu = (ploption('masthead_menu', $this->oset)) ? ploption('masthead_menu', $this->oset) : null;
		$masthead_meta = ploption('masthead_meta', $this->oset);
		
		
		// A Responsive, Drag &amp; Drop Platform for Beautiful Websites
		
		$classes = ($mast_img) ? 'with-splash' : '';

	if($mast_title){ ?>
	
	<header class="jumbotron masthead <?php echo $classes;?>">
	  	<?php

	  		if($mast_img)
	  			printf('<div class="splash"><img class="masthead-img" src="%s" alt="%s"/></div>',$mast_img, $mast_imgalt);

	  	?>

	  <div class="inner">
	  	<?php
	
	  		if($mast_title)
	  			printf('<h1 class="masthead-title">%s</h1>',$mast_title);
	
			if($mast_tag)
	  			printf('<p class="masthead-tag">%s</p>',$mast_tag);

	  	?>
	    
	    <p class="download-info">

	    <?php
			for ($i = 1; $i <= 2; $i++){
				$butt_link = ploption('masthead_button_link_'.$i, $this->oset); // Flag
				
				$butt_text = (ploption('masthead_button_text_'.$i, $this->oset)) ? ploption('masthead_button_text_'.$i, $this->oset) : __('Start Here', 'pagelines');
				
				$target = ( ploption( 'masthead_button_target_'.$i, $this->oset ) ) ? 'target="_blank"' : '';
				$btheme = ( ploption( 'masthead_button_theme_'.$i, $this->oset ) ) ? ploption( 'masthead_button_theme_'.$i, $this->oset ) : 'primary';

				if($butt_link)
					printf('<a %s class="btn btn-%s btn-large" href="%s">%s</a> ', $target, $btheme, $butt_link, $butt_text);
			}
			
	    ?> 
	
	    </p>
	  </div>
		<div class="mastlinks">
			<?php
			
			if($mast_menu)
				wp_nav_menu( 
					array(
						'menu_class'  => 'quick-links', 
						'menu' => $mast_menu,
						'container' => null, 
						'container_class' => '', 
						'depth' => 1, 
						'fallback_cb'=>''
					) 
				);
			
			
			if($masthead_meta)
				printf( '<div class="quick-links mastmeta">%s</div>', do_shortcode($masthead_meta) );
			
			?>
			
			
		</div>
	</header>
	<hr class="soften" />

		<?php 

		} else
			echo setup_section_notify($this, __('Set Masthead page options to activate.', 'pagelines') );

	}


}
