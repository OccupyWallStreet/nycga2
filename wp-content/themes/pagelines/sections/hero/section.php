<?php
/*
	Section: Hero
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A responsive full width image and text area with button.
	Class Name: PLheroUnit	
	Workswith: templates, main, header, morefoot, content
	Cloning: true
*/

/*
 * Main section class
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PLheroUnit extends PageLinesSection {
    
    var $tabID = 'herounit_meta';
    

	function section_optionator( $settings ){
		
		$settings = wp_parse_args($settings, $this->optionator_default);

		
		$option_array = array(

				'pagelines_herounit_text' => array(
						'type' 				=> 'multi_option',
						'inputlabel' 		=> 'Enter text for your Hero section',
						'title' 			=> $this->name.' Text',	
						'selectvalues'	=> array(
							'pagelines_herounit_title' => array(
								'type'  => 'text',
								'inputlabel'=>'Heading', 
							),
							'pagelines_herounit_tagline' => array(
								'type'   => 'textarea',
								'inputlabel'=>'Subtext'
							)
						),				
						'shortexp' 			=> 'The text for the Hero section Header and Subtext content.',
						'exp' 				=> 'The title is used for the heading, and the subtext is placed directly beneath it.'

				),
				'pagelines_herounit_image' => array(
					'type' 			=> 'image_upload',
					'imagepreview' 	=> '270',
					'inputlabel' 	=> 'Upload custom image',
					'title' 		=> $this->name.' Image',						
					'shortexp' 		=> 'Input Full URL to your custom Hero image.',
					'exp' 			=> 'Places a custom image to the right of the call to action and text.'
				),
				'pagelines_herounit_cta' => array(
					'type'		=> 'multi_option', 
					'title'		=> __('Hero Action Button', 'pagelines'), 
					'shortexp'	=> __('Enter the options for the Hero button', 'pagelines'),
					'selectvalues'	=> array(
						'herounit_button_link' => array(
							'type' => 'text',
							'inputlabel' => 'Button link destination (URL - Required)',
						),
						'herounit_button_text' => array(
							'type' 			=> 'text',
							'inputlabel' 	=> 'Hero Button Text',					
						),		
						'herounit_button_target' => array(
							'type'			=> 'check',
							'default'		=> false,
							'inputlabel'	=> 'Open link in new window.',
						),
						'herounit_button_theme' => array(
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
					),
				),
				'pagelines_herounit_widths' => array(
					'type'		=> 'multi_option', 
					'title'		=> __('Content Widths', 'pagelines'), 
					'shortexp'	=> __('Select the width of the image and text areas', 'pagelines'),
					'selectvalues'	=> array(
						'herounit_left_width' => array(
							'type'			=> 'select',
							'default'		=> 'span6',
							'inputlabel'	=> 'Text Area Width',
							'selectvalues'	=> array(
								'span3'	 => array('name' => '25%'), 
								'span4'	 => array('name' => '33%'), 
								'span6'	 => array('name' => '50%'), 
								'span8'	 => array('name' => '66%'), 
								'span9'	 => array('name' => '75%'), 
								'span7'	 => array('name' => '90%'), 
							),
						),
						'herounit_right_width' => array(
							'type'			=> 'select',
							'default'		=> 'span6',
							'inputlabel'	=> 'Image Area Width',
							'selectvalues'	=> array(
								'span3'	 => array('name' => '25%'), 
								'span4'	 => array('name' => '33%'), 
								'span6'	 => array('name' => '50%'), 
								'span8'	 => array('name' => '66%'), 
								'span9'	 => array('name' => '75%'), 
								'span7'	 => array('name' => '90%'), 
							),
						),
					),
				),						
		);
		
		$metatab_settings = array(
				'id' 		=> $this->tabID,
				'name' 		=> 'Hero Unit',
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

		$hero_lt_width = ploption( 'herounit_left_width', $this->oset );
			 if ( ! $hero_lt_width )$hero_lt_width = 'span6';
		$hero_rt_width = ploption( 'herounit_right_width', $this->oset );
			if ( ! $hero_rt_width )$hero_rt_width = 'span6';
   		$hero_title = ploption( 'pagelines_herounit_title', $this->tset );
		$hero_tag = ploption( 'pagelines_herounit_tagline', $this->tset );
		$hero_img = ploption( 'pagelines_herounit_image', $this->tset );
		$hero_butt_link = ploption( 'herounit_button_link', $this->oset );
		$hero_butt_text = ploption( 'herounit_button_text', $this->oset );
		$hero_butt_target = ( ploption( 'herounit_button_target', $this->oset ) ) ? ' target="_blank"': '';
		$hero_butt_theme = ploption( 'herounit_button_theme', $this->oset );

   		if($hero_title)	{ ?>

	   	<div class="pl-hero-wrap row">

	   	<?php
	   	if($hero_lt_width)
			printf('<div class="pl-hero %s">',$hero_lt_width);
			?>
				<?php

					if($hero_title)
						printf('<h1 class="m-bottom">%s</h1>',$hero_title);
					
					if($hero_tag)
		  				printf('<p>%s</p>',$hero_tag);
	  			
	  			    if($hero_butt_link)
					printf('<a %s class="btn btn-%s btn-large" href="%s">%s</a> ', $hero_butt_target, $hero_butt_theme, $hero_butt_link, $hero_butt_text);
	  			?>
			</div>

	   	<?php
	   	if($hero_rt_width)
			printf('<div class="pl-hero-image %s">',$hero_rt_width);
			?>
				<?php 
				    
					if($hero_img)
						printf('<div class="hero_image"><img class="pl-imageframe" src="%s" /></div>', apply_filters( 'pl_hero_image', $hero_img ) );
					
				?>
			</div>

		</div>

		<?php

		} else
			echo setup_section_notify($this, __('Set Hero page options to activate.', 'pagelines') );
	}

}
