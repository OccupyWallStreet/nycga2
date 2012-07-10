<?php
/*
	Section: Boxes
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates boxes and box layouts
	Class Name: PageLinesBoxes
	Workswith: templates, main, header, morefoot
	Cloning: true
*/

/**
 * Boxes Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesBoxes extends PageLinesSection {

	var $taxID = 'box-sets';
	var $ptID = 'boxes';

	/**
	* PHP that always loads no matter if section is added or not.
	*/
	function section_persistent(){
		
		$this->post_type_setup();
		
		$this->post_meta_setup();
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function post_type_setup(){
			$args = array(
					'label' 			=> __('Boxes', 'pagelines'),  
					'singular_label' 	=> __('Box', 'pagelines'),
					'description' 		=> __( 'For creating boxes in box type layouts.', 'pagelines'),
					'menu_icon'			=> $this->icon
				);
			$taxonomies = array(
				$this->taxID => array(	
						'label' => __('Box Sets', 'pagelines'), 
						'singular_label' => __('Box Set', 'pagelines'), 
					)
			);
			$columns = array(
				'cb'	 		=> "<input type=\"checkbox\" />",
				'title' 		=> 'Title',
				'bdescription' 	=> 'Text',
				'bmedia' 		=> 'Media',
				$this->taxID 	=> 'Box Sets'
			);
		
			$this->post_type = new PageLinesPostType( $this->ptID, $args, $taxonomies, $columns, array(&$this, 'column_display'));
		
			$this->post_type->set_default_posts( 'pagelines_default_boxes', $this); // Default 
	}


	/**
	*
	* @TODO document
	*
	*/
	function post_meta_setup(){
		
			$type_meta_array = array(
				
				'box_setup' => array(
					'type'		=> 'multi_option', 
					'title'		=> __('Individual Box Options', 'pagelines'), 
					'shortexp'	=> __('Basic setup options for handling of this box', 'pagelines'),
					'selectvalues'	=> array(
						
						'the_box_icon' 		=> array(
								'version' 	=> 'pro',
								'type' 		=> 'image_upload',					
								'inputlabel' 	=> __( 'Box Image', 'pagelines'),
							), 
						'the_box_icon_link'		=> array(
								'version' => 'pro',
								'type' => 'text',					
								'inputlabel' => __( 'Box Link (Optional)', 'pagelines'),
							), 
						'the_box_icon_target'		=> array(
								'version' => 'pro',
								'type' => 'check',					
								'inputlabel' => __( 'Open link in New Window?', 'pagelines'),
							),
						'box_class' => array(
							'version'		=> 'pro',
							'default'		=> '',
							'type' 			=> 'text',
							'size'			=> 'small',
							'inputlabel' 	=> __( 'Boxes Custom Class', 'pagelines'),
						),
						'box_more_text' => array(
							'version'		=> 'pro',
							'default'		=> '',
							'type' 			=> 'text',
							'size'			=> 'small',
							'inputlabel' 	=> __( 'More Link Text (Shows if link and text is set)', 'pagelines'),
						),
					),
				),
			
			);

			$post_types = array($this->id); 
			
			$type_metapanel_settings = array(
					'id' 		=> 'boxes-metapanel',
					'name' 		=> THEMENAME.' Box Options',
					'posttype' 	=> $post_types,
				);
			
			global $boxes_meta_panel;
			
			$boxes_meta_panel =  new PageLinesMetaPanel( $type_metapanel_settings );
			
			$type_metatab_settings = array(
				'id' 		=> 'boxes-type-metatab',
				'name' 		=> 'Box Setup Options',
				'icon' 		=> $this->icon,
			);

			$boxes_meta_panel->register_tab( $type_metatab_settings, $type_meta_array );
		
	}


	/**
	*
	* @TODO document
	*
	*/
	function section_optionator( $settings ){
		
		$settings = wp_parse_args($settings, $this->optionator_default);
		
			$tab = array(
				'box_setup' => array(
					'type'		=> 'multi_option', 
					'title'		=> __('Box Setup Options', 'pagelines'), 
					'shortexp'	=> __('Basic setup options for handling of boxes.', 'pagelines'),
					'selectvalues'	=> array(
						
						'box_set' => array(
							'version' 		=> 'pro',
							'default'		=> 'default-boxes',
							'type' 			=> 'select_taxonomy',
							'taxonomy_id'	=> $this->taxID,				
							'inputlabel'	=> __( 'Box Set To Show', 'pagelines'),
						), 
						'box_col_number' => array(
							'type' 			=> 'count_select',
							'default'		=> '3',
							'count_number'	=> '5', 
							'count_start'	=> '1',
							'inputlabel' 		=> __( "Boxes Per Row", 'pagelines'),
						), 
						'box_items' => array(
							'version'		=> 'pro',
							'default'		=> '6',
							'type' 			=> 'text_small',
							'size'			=> 'small',
							'inputlabel' 	=> __( 'Maximum Boxes To Show', 'pagelines'),
						),
					),
				),
					
					'box_image_formatting' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Box Image Options', 'pagelines'), 
						'shortexp'	=> __('Options for formatting box images.', 'pagelines'),
						'exp'		=> __('', 'pagelines'),
						'selectvalues'	=> array(
							
							'box_thumb_type' => array(
								'version' 	=> 'pro',
								'type' 		=> 'select',
								'default'	=> 'inline_thumbs',
								'selectvalues'	=> array(
										'inline_thumbs'	=> array('name' => __( 'Image At Left', 'pagelines') ),
										'top_thumbs'	=> array('name' => __( 'Image On Top', 'pagelines') ), 
										'only_thumbs'	=> array('name' => __( "Only The Image, No Text", 'pagelines') )
									), 
								'inputlabel' => __( 'Box Thumb Style (optional - defaults to "At Left")', 'pagelines'),				

							),
							'box_thumb_size' => array(
								'version'		=> 'pro',
								'default'		=> '64',
								'type' 			=> 'text_small',
								'size'			=> 'small',
								'inputlabel' 		=> __( 'Enter the max image size in pixels (optional)', 'pagelines'),
							),
							'box_thumb_frame' => array(
								'version'		=> 'pro',
								'default'		=> '64',
								'type' 			=> 'check',
								'size'			=> 'small',
								'inputlabel' 		=> __( 'Add A Frame To Images', 'pagelines'),
							),
						),
					),
					
					
					'box_ordering' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Box Ordering Options', 'pagelines'), 
						'shortexp'	=> __('Optionally control the ordering of the boxes', 'pagelines'),
						'exp'		=> __('The easiest way to order boxes is using a post type order plugin for WordPress. However, if you would like to do it algorithmically, we have provided these options for you.', 'pagelines'),
						'selectvalues'	=> array(
							
							'box_orderby' => array(
								'type'			=> 'select',
								'default'		=> 'ID',
								'version'		=> 'pro',
								'inputlabel'	=> 'Order Boxes By (If Not With Post Type Order Plugin)',
								'selectvalues' => array(
									'ID' 		=> array('name' => __( 'Post ID (default)', 'pagelines') ),
									'title' 	=> array('name' => __( 'Title', 'pagelines') ),
									'date' 		=> array('name' => __( 'Date', 'pagelines') ),
									'modified' 	=> array('name' => __( 'Last Modified', 'pagelines') ),
									'rand' 		=> array('name' => __( 'Random', 'pagelines') ),							
								)
							),
							'box_order' => array(
									'default' => 'DESC',
									'version'	=> 'pro',
									'type' => 'select',
									'selectvalues' => array(
										'DESC' 		=> array('name' => __( 'Descending', 'pagelines') ),
										'ASC' 		=> array('name' => __( 'Ascending', 'pagelines') ),
									),
									'inputlabel'=> __( 'Select sort order', 'pagelines'),
							),
						),
					),
					
					'box_more_text' => array(
						'version'		=> 'pro',
						'default'		=> '',
						'type' 			=> 'text',
						'size'			=> 'small',
						'inputlabel' 	=> __( 'More Link Text', 'pagelines'),
						'title' 		=> __( 'More Link Text', 'pagelines'),
						'shortexp' 		=> __( 'Enter text for "more" links on linked box elements for this page.', 'pagelines'),
						'exp'			=> __( 'If this option is blank (and not set in defaults), no more text will show.<br/><br/> This option can be overridden in individual box settings.', 'pagelines')
					),
					'box_class' => array(
						'version'		=> 'pro',
						'default'		=> '',
						'type' 			=> 'text',
						'size'			=> 'small',
						'inputlabel' 	=> __( 'Add custom css class to these boxes', 'pagelines'),
						'title' 		=> __( 'Custom CSS class', 'pagelines'),
						'shortexp' 		=> __( 'Add a custom CSS class to this set of boxes.', 'pagelines'),
					),
			);
		
			$tab_settings = array(
					'id' 		=> 'fboxes_meta',
					'name' 		=> 'Boxes',
					'icon' 		=> $this->icon, 
					'clone_id'	=> $settings['clone_id'], 
					'active'	=> $settings['active']
				);

			register_metatab($tab_settings, $tab);
	}

	/**
	* Section template.
	*/
   function section_template( $clone_id = null ) {    
		
		// Options
			$per_row = ( ploption( 'box_col_number', $this->oset) ) ? ploption( 'box_col_number', $this->oset) : 3; 
			$box_set = ( ploption( 'box_set', $this->oset ) ) ? ploption( 'box_set', $this->oset ) : null;
			$box_limit = ploption( 'box_items', $this->oset );
			$this->thumb_type = ( ploption( 'box_thumb_type', $this->oset) ) ? ploption( 'box_thumb_type', $this->oset) : 'inline_thumbs';	
			$this->thumb_size = ploption('box_thumb_size', $this->oset);
			$this->framed = ploption('box_thumb_frame', $this->oset);
			
			
			$class = ( ploption( 'box_class', $this->oset ) ) ? ploption( 'box_class', $this->oset ) : null;
			
		// Actions	
			// Set up the query for this page
				$orderby = ( ploption('box_orderby', $this->oset) ) ? ploption('box_orderby', $this->oset) : 'ID';
				$order = ( ploption('box_order', $this->oset) ) ? ploption('box_order', $this->oset) : 'DESC';
				$params = array( 'orderby'	=> $orderby, 'order' => $order, 'post_type'	=> $this->ptID );
				$params[ 'showposts' ] = ( ploption('box_items', $this->oset) ) ? ploption('box_items', $this->oset) : $per_row;
				$params[ $this->taxID ] = ( ploption( 'box_set', $this->oset ) ) ? ploption( 'box_set', $this->oset ) : null;
				$params[ 'no_found_rows' ] = 1;

				$q = new WP_Query( $params );
				
				if(empty($q->posts)){
					echo setup_section_notify( $this, 'Add Box Posts To Activate.', admin_url('edit.php?post_type='.$this->ptID), 'Add Posts' );
					return;
				}
			
			// Grid Args
				$args = array( 'per_row' => $per_row, 'callback' => array(&$this, 'draw_boxes'), 'class' => $class );

			// Call the Grid
				printf('<div class="fboxes fix">%s</div>', grid( $q, $args ));
		
	}


	/**
	*
	* @TODO document
	*
	*/
	function draw_boxes($p, $args){ 

		setup_postdata($p); 
		
		$oset = array('post_id' => $p->ID);
	 	$box_link = plmeta('the_box_icon_link', $oset);
		$box_icon = plmeta('the_box_icon', $oset);
		$box_target = (plmeta('the_box_icon_target', $oset)) ? 'target="_blank"' : '';
		
		$class = ( plmeta( 'box_class', $oset ) ) ? plmeta( 'box_class', $oset ) : null;
		
		$image = ($box_icon) ? self::_get_box_image( $p, $box_icon, $box_link, $this->thumb_size, $box_target) : '';
	
		$title_text = ($box_link) ? sprintf('<a href="%s">%s</a>', $box_link, $p->post_title ) : $p->post_title; 
	
		$title = do_shortcode(sprintf('<div class="fboxtitle"><h3>%s</h3></div>', $title_text));

		if(plmeta('box_more_text', $oset)){
			$more_text = plmeta('box_more_text', $oset);
		} elseif(ploption('box_more_text', $this->oset)){
			$more_text = ploption('box_more_text', $this->oset);
		}else 
			$more_text = false;
		
		$more_link = ($box_link && $more_text) ? sprintf('<span class="fboxmore-wrap"><a class="fboxmore" href="%s" %s>%s</a></span>', $box_link, $box_target, $more_text) : '';
		
		$more_link = apply_filters('box_more_link', $more_link);
		
		$content = sprintf('<div class="fboxtext">%s %s %s</div>', do_shortcode($p->post_content), pledit( $p->ID ), $more_link);
			
		$info = ($this->thumb_type != 'only_thumbs') ? sprintf('<div class="fboxinfo fix bd">%s%s</div>', $title, $content) : '';				
				
		return sprintf(
			'<div id="%s" class="fbox %s"><div class="media box-media %s"><div class="blocks box-media-pad">%s%s</div></div></div>', 
			'fbox_'.$p->ID, 
			$class, 
			$this->thumb_type, 
			$image, 
			$info
		);
	
	}

	

	/**
	*
	* @TODO document
	*
	*/
	function _get_box_image( $bpost, $box_icon, $box_link = false, $box_thumb_size = false, $box_target){
			global $pagelines_ID;
			
			$frame = ($this->framed) ? 'pl-imageframe' : '';
			
			if($this->thumb_type == 'inline_thumbs'){
				$max_width = ($box_thumb_size) ? $box_thumb_size : 65;
				$image_style = 'max-width: 100%';
				$wrapper_style = sprintf('width: 22%%; max-width:%dpx', $max_width);
				$wrapper_class = sprintf('fboxgraphic img %s', $frame);
			} else {
				$max_width = ($box_thumb_size) ? $box_thumb_size.'px' : '100%';
				$image_style = 'max-width: 100%';
				$wrapper_style = sprintf('max-width:%s', $max_width);
				$wrapper_class = sprintf('fboxgraphic %s', $frame);
			}
			
			// Make the image's tag with url
			$image_tag = sprintf('<img src="%s" alt="%s" style="%s" />', $box_icon, esc_html($bpost->post_title), $image_style);
			
			// If link for box is set, add it
			$image_output = ( $box_link ) ? sprintf('<a href="%s" title="%s" %s>%s</a>', $box_link, esc_html($bpost->post_title), $box_target, $image_tag ) : $image_tag;
			
			$wrapper = sprintf('<div class="%s" style="%s">%s</div>', $wrapper_class, $wrapper_style, $image_output );
			
			// Filter output
			return apply_filters('pl_box_image', $wrapper, $bpost->ID);
	}

	

		/**
		*
		* @TODO document
		*
		*/
		function pagelines_default_boxes($post_type){

			$d = array_reverse( $this->get_default_fboxes() );

			foreach($d as $dp){
				// Create post object
				$default_post = array();
				$default_post['post_title'] = $dp['title'];
				$default_post['post_content'] = $dp['text'];
				$default_post['post_type'] = $post_type;
				$default_post['post_status'] = 'publish';
				if ( defined( 'ICL_LANGUAGE_CODE' ) )
					$default_post['icl_post_language'] = ICL_LANGUAGE_CODE;
				$newPostID = wp_insert_post( $default_post );

				if(isset($dp['media']))
					update_post_meta($newPostID, 'the_box_icon', $dp['media']);

				wp_set_object_terms($newPostID, 'default-boxes', $this->taxID );

				// Add other default sets, if applicable.
				if(isset($dp['set']))
					wp_set_object_terms($newPostID, $dp['set'], $this->taxID, true);

			}
		}


		/**
		*
		* @TODO document
		*
		*/
		function get_default_fboxes(){
			$default_boxes[] = array(
			        				'title' => 'Drag <span class="spamp">&amp;</span> Drop',
					        		'text' 	=> 'PageLines is a drag &amp; drop framework that allows you to completely customize your website with drag &amp; drop.',
									'media' => $this->base_url.'/images/fbox3.png'
			    				);

			$default_boxes[] = array(
			        				'title' => 'Responsive <span class="spamp">&amp;</span> Mobile',
					        		'text' 	=> "Built from the ground up to look great on mobile devices. PageLines utilizes an advanced responsive framework.",
									'media' => $this->base_url.'/images/fbox2.png'
			    				);

			$default_boxes[] = array(
			        				'title'	=> 'Tons of Addons',
			        				'text' 	=> "Load up your own sections, themes and plugins using PageLines' one of a kind extension marketplace.", 
									'media' => $this->base_url.'/images/fbox1.png'
			    				);

			return apply_filters('pagelines_default_boxes', $default_boxes);
		}
	

	/**
	*
	* @TODO document
	*
	*/
	function column_display($column){
		global $post;

		switch ($column){
			case 'bdescription':
				the_excerpt();
				break;
			case 'bmedia':
				if(get_post_meta($post->ID, 'the_box_icon', true ))
					echo '<img src="'.get_post_meta($post->ID, 'the_box_icon', true ).'" style="max-width: 80px; margin: 10px; border: 1px solid #ccc; padding: 5px; background: #fff" />';	
	
				break;
			case $this->taxID:
				echo get_the_term_list($post->ID, 'box-sets', '', ', ','');
				break;
		}
	}
}
