<?php
/*
	Section: Highlight
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Adds a highlight sections with a splash image and 2-big lines of text.
	Class Name: PageLinesHighlight
	Workswith: templates, main, header, morefoot, sidebar1, sidebar2, sidebar_wrap
	Edition: pro
	Cloning: true
*/

/**
 * Highlight Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesHighlight extends PageLinesSection {
	
	var $tabID = 'highlight_meta';
	

	/**
	*
	* @TODO document
	*
	*/
	function section_optionator( $settings ){
		
		$settings = wp_parse_args($settings, $this->optionator_default);
		
		$metatab_array = array(
			
			'hl_options' => array(
				'version' 		=> 'pro',
				'type' 			=> 'multi_option',
				'title' 		=> 'Highlight Header Text (Optional)',
				'shortexp' 		=> 'Add the main header text for the highlight section.',
				'selectvalues'	=> array(
					'_highlight_head' => array(
						'version' 		=> 'pro',
						'type' 			=> 'text',
						'size'			=> 'big',		
						'inputlabel' 	=> 'Highlight Header Text (Optional)',
					),
					'_highlight_subhead' => array(
						'version' 		=> 'pro',
						'type' 			=> 'text',
						'size'			=> 'big',		
						'inputlabel' 	=> 'Highlight Subheader Text (Optional)',
					),

					'_highlight_splash' => array(
						'version' 		=> 'pro',
						'type' 			=> 'image_upload',	
						'inputlabel'	=> 'Upload Splash Image'
					),
					'_highlight_splash_position' => array(
						'version' 		=> 'pro',
						'type' 			=> 'select',		
						'inputlabel' 		=> 'Highlight Image Position',
						'selectvalues'=> array(
							'top'			=> array( 'name' => 'Top' ),
							'bottom'	 	=> array( 'name' => 'Bottom' )
						),
					),
					'_highlight_image_frame' => array(
						'type' 				=> 'check',		
						'inputlabel' 		=> 'Add frame to image?'
					),
				)
			)
				
		);
		
		$metatab_settings = array(
				'id' 		=> $this->tabID,
				'name' 		=> 'Highlight',
				'icon' 		=> $this->icon, 
				'clone_id'	=> $settings['clone_id'], 
				'active'	=> $settings['active']
			);
		
		register_metatab($metatab_settings, $metatab_array);
	}

	/**
	*
	* @TODO document
	*
	*/
	function section_template( $clone_id ) { 

		$h_head = ploption('_highlight_head', $this->tset);
		$h_subhead = ploption('_highlight_subhead', $this->tset);
		$h_splash = ploption('_highlight_splash', $this->tset);
		$h_splash_position = ploption('_highlight_splash_position', $this->oset);
		
		$frame_class = (ploption('_highlight_image_frame', $this->oset)) ? 'pl-imageframe' : '';
	
	if($h_head || $h_subhead || $h_splash){?>
		<div class="highlight-area">
			<?php 
			
				if( $h_splash_position == 'top' && $h_splash)
					printf('<div class="highlight-splash hl-image-top %s"><img src="%s" alt="" /></div>', $frame_class, $h_splash);
					
				if($h_head)
					printf('<h1 class="highlight-head">%s</h1>', __( $h_head, 'pagelines' ) );
				
				if($h_subhead)
					printf('<h3 class="highlight-subhead subhead">%s</h3>', __( $h_subhead, 'pagelines' ) );
					
				if( $h_splash_position != 'top' && $h_splash)
					printf('<div class="highlight-splash hl-image-bottom %s"><img src="%s" alt="" /></div>', $frame_class, apply_filters( 'pl_highlight_splash', $h_splash ) );
			?> 
		</div>
	<?php 
		} else
			echo setup_section_notify($this, __('Set highlight page options to activate.', 'pagelines') );
	}
}
