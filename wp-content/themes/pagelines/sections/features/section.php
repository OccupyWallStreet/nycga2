<?php
/*
	Section: Features
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates a feature slider and custom post type
	Class Name: PageLinesFeatures
	Workswith: templates, main, header, morefoot	
	Cloning: true
	Edition: pro
	Tax: feature-sets
*/

/**
 * Features Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesFeatures extends PageLinesSection {

	var $taxID = 'feature-sets';
	var $ptID = 'feature';

	/**
	* PHP that always loads no matter if section is added or not.
	*/
	function section_persistent(){
		$this->post_type_setup();
		
		$this->post_meta_setup();	 
		
		$options = array(
			'posts_feature_control' => array(
					'default'	=> false,
					'version'	=> 'pro',
					'type'		=> 'check',
					'inputlabel'=> __( 'Enable Feature Metapanel on Posts?', 'pagelines' ),
					'title'		=> __( 'Feature Section - Enable "Post-As-Feature" Control Metapanel', 'pagelines' ),
					'shortexp'	=> __( 'Shows as metapanel for controlling the appearance of posts inside the feature slider.', 'pagelines' ),
					'exp'		=> __( 'If you are using the feature slider in posts or post category mode, you may want to control the appearance of the posts inside the feature. Enable this option to show the posts metapanel.', 'pagelines' )
				),
		);
		
		pl_global_option( array( 'menu' => 'advanced', 'options' => $options, 'location' => 'top' ) );

	}

	/**
	* Load js
	*/
	function section_styles(){
		wp_enqueue_script( 'cycle', $this->base_url . '/script.cycle.js', array( 'jquery'), '2.9994', true);
	}
	
	/**
	*
	* @TODO document
	*
	*/
	function section_head( $clone_id ) {   
		
		global $pagelines_ID;

		$oset = array( 'post_id' => $pagelines_ID, 'clone_id' => $clone_id );

		$f = $this->post_set[ $clone_id ] = $this->pagelines_features_set( $clone_id ); 	
	
		$feffect = ( ploption( 'feffect', $oset ) ) ? ploption( 'feffect', $oset ) : 'fade';
		$timeout = ( ploption( 'timeout', $oset ) ) ? ploption( 'timeout', $oset ) : 0;
		$speed   = ( ploption( 'fspeed', $oset ) ) ? ploption( 'fspeed', $oset ) : 1500;
		$fsync   = ( ploption( 'fremovesync', $oset ) ) ? 0 : 1;
		$autostop = ( has_filter( 'pagelines_feature_autostop' ) ) ? ', autostop: 1, autostopCount: ' . apply_filters( 'pagelines_feature_autostop', 0) : '';
		$playpause = ( ploption('feature_playpause', $oset ) ) ? true : false;
		$fmode = ploption( 'feature_nav_type', $oset );
		
		$clone_class = sprintf( 'fclone%s', $clone_id );
		
		$selector = sprintf( '#cycle.%s', $clone_class );
		$fnav_selector = sprintf( '#featurenav.%s', 'fclone'.$clone_id );
		$playpause_selector = sprintf( '.playpause.%s', 'fclone'.$clone_id );
		
		$args = sprintf( "slideResize: 0, fit: 1,  fx: '%s', sync: %d, timeout: %d, speed: %d, cleartype: true, cleartypeNoBg: true, pager: '%s' %s", $feffect, $fsync, $timeout, $speed, $fnav_selector, $autostop );
		
		$this->_feature_css( $clone_id, $oset );
		
		$wrap_class = '.'.$clone_class."_wrap";
		
?><script type="text/javascript">/* <![CDATA[ */ jQuery(document).ready(function () {
	
<?php if( ! ploption( 'feature_height_mode', $oset ) || ploption( 'feature_height_mode', $oset ) == 'aspect' ) :
		
		printf( '$aspect%s = %s;', $clone_id,(ploption( 'feature_aspect', $oset ) ) ? ploption( 'feature_aspect', $oset ) : 2.5 );
		
		printf( '$width_area%s = "%s #feature-area";', $clone_id, $wrap_class );
		
		printf( '$height_selectors%s = "%s";', $clone_id, $this->selectors( '', $wrap_class.' ' ) );
	?>
	
	$the_width<?php echo $clone_id;?> = jQuery($width_area<?php echo $clone_id;?>).width();
		
	$new_height<?php echo $clone_id;?> = $the_width<?php echo $clone_id;?> / $aspect<?php echo $clone_id;?>;
	jQuery($height_selectors<?php echo $clone_id;?>).height($new_height<?php echo $clone_id;?>);
	
	jQuery(window).resize(function() {
		$response_width<?php echo $clone_id;?> = jQuery($width_area<?php echo $clone_id;?>).width();
		$new_height<?php echo $clone_id;?> = $response_width<?php echo $clone_id;?> / $aspect<?php echo $clone_id;?>;
		jQuery($height_selectors<?php echo $clone_id;?>).height($new_height<?php echo $clone_id;?>);
		
	});
<?php 

	endif;
	//Feature Cycle Setup
	printf( "jQuery('%s').cycle({ %s });", $selector, $args );
	
	$this->_js_feature_loop( $fmode, $f, $clone_class );

	if( $playpause ):
	?>	
	
		var cSel = '<?php echo $selector;?>';
		var ppSel = '<?php echo $playpause_selector;?>';
		
		jQuery(ppSel).click(function() { 
			if (jQuery(ppSel).hasClass('pause')) {  
				jQuery(cSel).cycle('pause'); jQuery(ppSel).removeClass('pause').addClass('resume');
			} else { 
				jQuery(ppSel).removeClass('resume').addClass('pause'); jQuery(cSel).cycle('resume', true);
			}
		});
	<?php endif;?>
	
	
});

/* ]]> */ </script>
<?php }


	/**
	*
	* @TODO document
	*
	*/
	function _feature_css( $clone_id, $oset){

		$height = ( ploption( 'feature_stage_height', $oset ) ) ? ploption( 'feature_stage_height', $oset ).'px' : '380px';	
		
		$selectors = $this->selectors( $clone_id );
		$css = sprintf( '%s{height:%s;}', $selectors, $height );	
		inline_css_markup( 'feature-css', $css );
	}

	function selectors( $clone_id, $prepend = ''){
		
		$base = array( '.fset_height', '#feature_slider .text-bottom .fmedia .dcol-pad', '#feature_slider .text-bottom .feature-pad', '#feature_slider .text-none .fmedia .dcol-pad');
		
		$selectors = array();
		
		foreach($base as $sel){
			if( isset( $clone_id ) && $clone_id != 1 && $clone_id != '' )
				$selectors[] = sprintf('%s.clone_%s %s', $prepend, $clone_id, $sel);
			else 
				$selectors[] = $prepend.$sel;
		}
		
		return join( ',', $selectors );
	}

	/**
	*
	* @TODO document
	*
	*/
	function _js_feature_loop( $fmode, $fposts = array(), $clone_class ){
	
		$count = 1;
		$link_js = '';
		$cat_css = '';
		foreach( $fposts as $fid => $f ){
			$oset = array( 'post_id' => $f->ID );
			$feature_name = ( ploption( 'feature-name', $oset ) ) ? ploption( 'feature-name', $oset ) : $f->post_title;
			
			$feature_thumb = ploption( 'feature-thumb', $oset );
			
			if ( ! $feature_thumb )
				$feature_thumb = ploption( 'feature-background-image', $oset );
			
			if ( ( ploption( 'feature_source', $this->oset ) == 'posts' || ploption( 'feature_source', $this->oset ) == 'posts_all' ) ) {
				
				if ( plmeta( 'feature-thumb', $oset ) )
					$feature_thumb = plmeta( 'feature-thumb', $oset );
				elseif( plmeta( 'feature-background-image', $oset ) )
					$feature_thumb = plmeta( 'feature-background-image', $oset );
				elseif( has_post_thumbnail( $f->ID ) ) {
					$feature_thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $f->ID ) );
					$feature_thumb = $feature_thumb[0];
				} else {				
					$feature_thumb = apply_filters( 'pagelines-feature-cat-default-thumb', $this->base_url . '/images/fthumb3.png', $f );
				}
			} 
			
			if ( ! $feature_thumb )
					$feature_thumb = $this->base_url . '/images/fthumb3.png' ;
			
			if( $fmode == 'names' || $fmode == 'thumbs' ){
				//echo "\n".' // '.$fmode.'!!!'."\n";
				if( $fmode == 'names' )
					$replace_value = esc_js( $feature_name );
			
				elseif ( $fmode == 'thumbs' )
					$replace_value = sprintf( "<span class='nav_thumb' style='background-image: url(%s);'><span class='nav_overlay'>&nbsp;</span></span>", $feature_thumb );
		
				$replace_js = sprintf( 'jQuery(this).html("%s");', $replace_value );
			} else
				$replace_js = '';
			
			$link_title = sprintf( 'jQuery(this).attr("title", "%s");', esc_js( $feature_name ) );
		
			$link_js .= sprintf( 'if(jQuery(this).html() == "%s") { %s %s }', $count, $link_title, $replace_js );
		
			$count++; 
		}	
		printf( 'jQuery("div#featurenav.%s").children("a").each(function() { %s });', $clone_class, $link_js );
	}


	/**
	*
	* @TODO document
	*
	*/
	function section_template( $clone_id ) {    

		// $this->set set in pagelines_feature_set, better way to do this?
		$this->draw_features( $this->post_set[ $clone_id ], $this->set, $clone_id );
	}


	/**
	*
	* @TODO document
	*
	*/
	function pagelines_features_set( $clone_id ){
	
		if( ploption( 'feature_set', $this->oset ) )
			$this->set = ploption( 'feature_set', $this->oset );
		elseif ( ploption( 'feature_default_tax', $this->oset ) )
			$this->set = ploption( 'feature_default_tax', $this->oset );
		else 
			$this->set = null;

		$limit = ploption( 'feature_items', $this->oset );

		$order = ploption( 'feature_order', $this->oset );
		
		$orderby = ploption( 'feature_orderby', $this->oset );
		
		$source = ( ploption( 'feature_source', $this->oset ) == 'posts' || ploption( 'feature_source', $this->oset ) == 'posts_all') ? ploption( 'feature_source', $this->oset ) : 'customtype';	
	
		$category = ( $source == 'posts' ) ? ploption( 'feature_category', $this->oset ) : '';			
		$f = $this->load_pagelines_features( array( 'set' => $this->set, 'limit' => $limit, 'orderby' => $orderby, 'order' => $order, 'source' => $source, 'category' => $category ) ); 
		
		return $f;		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function load_pagelines_features( $args ) {
		$defaults = array(
			
			'query'		=>	array(),
			'set'		=>	null,
			'limit'		=>	null,
			'order'		=>	'DESC',
			'orderby'	=>	'ID',
			'source'	=>	null,
			'category'	=>	null
		);
		
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );

		$query['no_found_rows'] = 1;
		$query['showposts'] = 5;
		$query['orderby']	= $orderby;

		$query['order']		= $order;
	
		if($source == 'posts' || $source == 'posts_all' ){
		
			$query['post_type'] = 'post';
		
			if( $category )
				$query['cat'] = $category;
		} else {		
			$query['post_type'] = $this->ptID; 
		
			if( isset( $set ) ) 
				$query[ $this->taxID ] = $set;	
		}
	
		if( isset( $limit ) ) 
			$query['showposts'] = $limit; 
		elseif( $source == 'posts' || $source == 'posts_all' )
			$query['showposts'] = get_option( 'posts_per_page' );

		$q = new WP_Query( $query );
		if( is_array( $q->posts ) ) 
			return array_slice( $q->posts, 0, $query['showposts'] );
		else 
			return array();
	}


	/**
	*
	* @TODO document
	*
	*/
	function draw_features($f, $class, $clone_id = null) {     
	
	// Setup 
		global $post; 
		global $pagelines_ID;
		global $pagelines_layout;
		$current_page_post = $post;

		if( empty( $f ) ){
			echo setup_section_notify( $this, __( "No Feature posts matched this page's criteria", 'pagelines' ) );
			return;
		}

	// Options 
		$feature_source = ploption( 'feature_source', $this->oset );
		$timeout = ploption( 'timeout', $this->oset );
		$playpause = ploption( 'feature_playpause', $this->oset );
		$feature_nav_type = ploption( 'feature_nav_type', $this->oset );
	   
	// Refine
		$no_nav = ( isset( $f ) && count( $f ) == 1 ) ? ' nonav' : '';
		$footer_nav_class = $class. ' '. $feature_nav_type . $no_nav;
		$cycle_selector = "fclone" . $clone_id;
?>		
	<div id="feature_slider" class="<?php echo $cycle_selector.'_wrap '. $class;?> fix">
		<div id="feature-area" class="fset_height">
			<div id="cycle" class="<?php echo $cycle_selector;?>">
			<?php
			
				foreach( $f as $post ) : 
						
						// Setup For Std WP functions
						setup_postdata( $post ); 

						$oset = array( 'post_id' => $post->ID );

						$feature_style = ( ploption( 'feature-style', $oset ) ) ? ploption( 'feature-style', $oset ) : 'text-left';
						
						$feature_style = apply_filters( 'pagelines-feature-style', $feature_style );
						
						$flink_text = ( ploption( 'feature-link-text', $oset) ) ? __( ploption('feature-link-text', $oset ) ) : __( 'More', 'pagelines' );
						
						if ( $feature_source == 'posts' || $feature_source == 'posts_all' ) {
							
							$feature_background_image = '';
							
							if( plmeta( 'feature-background-image', $oset ) )
								$feature_background_image = plmeta( 'feature-background-image', $oset );
							elseif ( has_post_thumbnail( $post->ID ) ) {
								$feature_background_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail'  );
								$feature_background_image = $feature_background_image[0];
							} else {
								
								$feature_background_image = apply_filters( 'pagelines-feature-cat-default-image', $this->base_url.'/images/feature1.jpg', $post );
								
							}
							
							$background_class = 'bg_cover';
							
						} else {
							
							$feature_background_image = ploption( 'feature-background-image', $oset);
							
							$background_class = 'bg_cover';
							
						}

							
						$feature_design = ( ploption( 'feature-design', $oset ) ) ? ploption( 'feature-design', $oset ) : '';
						
						if ( $feature_source == 'posts' || $feature_source == 'posts_all' )
							setup_postdata( $post );
						
					
						if( plmeta( 'feature-link-url', $oset ) )
							$action = plmeta( 'feature-link-url', $oset );
						elseif( ploption( 'feature-link-url', $oset ) )
							$action = ploption( 'feature-link-url', $oset );
						elseif($feature_source == 'posts' || $feature_source == 'posts_all')
							$action = get_permalink();
						else
							$action = '';
					
						$fcontent_class = ( ploption( 'fcontent-bg', $oset ) ) ? ploption( 'feature-bg', $oset ) : '';
						
						$media_image = ploption( 'feature-media-image', $oset );

						$feature_media = ploption( 'feature-media', $oset ); 
						$feature_media_full = ploption( 'feature-media-full', $oset ); 
						
						$feature_wrap_markup = ( $feature_style == 'text-none' && isset( $action ) ) ? 'a' : 'div';
						$feature_wrap_link = ( $feature_style == 'text-none' && isset( $action ) ) ? sprintf( 'href="%s"', $action ) : '';
						
						$more_link = ( $feature_style != 'text-none' && $action ) ? sprintf( ' <a class="plmore" href="%s" >%s</a>', $action, $flink_text ) : '';
						
						$background_css = ( $feature_background_image ) ? sprintf('style="background-image: url(\'%s\');"', $feature_background_image ) : '';

						printf( '<div id="%s" class="fcontainer %s %s fix" >', 'feature_'.$post->ID, $feature_style, $feature_design ); 
						
						printf( '<%s class="feature-wrap fset_height %s" %s %s >', $feature_wrap_markup, $background_class, $feature_wrap_link, $background_css ); 
						
						if( $feature_wrap_markup != 'a' ) :
						
							if($feature_media && $feature_media_full): 
								echo $feature_media;	
							else: 
						
						?>
							
								<div class="feature-pad fset_height fix">
									<div class="fcontent scale_text fset_height <?php echo $fcontent_class;?>">
										<div class="dcol-pad fix">
												<?php
												
													
													pagelines_register_hook( 'pagelines_feature_text_top', $this->id ); // Hook 
													
													$link = ( $feature_source == 'posts' || $feature_source == 'posts_all' ) ? sprintf( '<a href="%s">%s</a>', $action, $post->post_title ) : $post->post_title;
													
													$title = sprintf( '<div class="fheading"> <h2 class="ftitle">%s</h2> </div>', $link );
													
													$content = ( $feature_source == 'posts' || $feature_source == 'posts_all' ) ? apply_filters( 'pagelines_feature_output', custom_trim_excerpt( get_the_excerpt(), '30' ) ) : do_shortcode( get_the_content() ); 
											
													printf(
														'%s<div class="ftext"><div class="fexcerpt">%s%s%s</div></div>', 
														$title,
														$content, 
														$more_link,
														pledit( $post->ID )
													);
													
												pagelines_register_hook( 'pagelines_fcontent_after', $this->id ); // Hook ?>
										</div>
									</div>
						
									<div class="fmedia fset_height" style="">
										<div class="dcol-pad">
											<?php 
											
											pagelines_register_hook( 'pagelines_feature_media_top', $this->id ); // Hook 
											
											if( $media_image )											
												printf( '<div class="media-frame"><img src="%s" /></div>', $media_image );
											
											elseif( $feature_media )
												echo do_shortcode( $feature_media );	
												?>
										</div>
									</div>
									<?php pagelines_register_hook( 'pagelines_feature_after', $this->id ); // Hook ?>
									<div class="clear"></div>
								</div>
							
							<?php 
							endif;
							endif;
							
						printf( '</%s>', $feature_wrap_markup ); 
					echo '</div>';					
					endforeach; 
							
					$post = $current_page_post;
				 ?>
			</div>
		</div>
			<?php 
				
				pagelines_register_hook( 'pagelines_feature_nav_before', $this->id ); // Hook
				
				$playpause = ( $timeout != 0 && $playpause) ? sprintf( '<span class="playpause pause %s"><span>&nbsp;</span></span>', $cycle_selector ) : '';
				
				$nav = sprintf( '<div id="featurenav" class="%s subtext fix"></div>', $cycle_selector );
				
				printf( '<div id="feature-footer" class="%s fix"><div class="feature-footer-pad">%s%s<div class="clear"></div></div></div>', $footer_nav_class, $playpause, $nav );
?>	
	</div>
	<div class="clear"></div>
<?php
	}

	/**
	*
	* @TODO document
	*
	*/
	function post_meta_setup(){
		
			$pt_tab_options = array(
					'feature_styling' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Feature Design Style', 'pagelines'), 
						'shortexp'	=> __('The basic styling of the feature', 'pagelines'),
						'selectvalues'	=> array(
							'feature-style' => array(
									'type' 	=> 'select',					
									'inputlabel' => __( 'Feature Text Position', 'pagelines' ),
									'selectvalues' => array(
										'text-left'		=> array( 'name' => __( 'Text On Left', 'pagelines' ) ),
										'text-right' 	=> array( 'name' => __( 'Text On Right', 'pagelines' ) ),
										'text-bottom' 	=> array( 'name' => __( 'Text On Bottom', 'pagelines' ) ),
										'text-none' 	=> array( 'name' => __( 'Full Width Background Image - No Text - Links Entire BG', 'pagelines' ) )
									),
								),
							'feature-design' => array(
									'type'			=> 'select',
									'inputlabel' 		=> __( 'Feature Design Style', 'pagelines' ),
									'selectvalues'	=> array(
										'fstyle-darkbg-overlay' => array( 'name' => __( 'White Text - Dark Feature Background - Transparent Text Overlay (Default)', 'pagelines' ) ),
										'fstyle-lightbg'		=> array( 'name' => __( 'Black Text - Light Feature Background with Border - No Overlay', 'pagelines' ) ),
										'fstyle-darkbg'			=> array( 'name' => __( 'White Text - Dark Feature Background - No Overlay', 'pagelines' ) ),
										'fstyle-nobg'			=> array( 'name' => __( 'Black Text - No Feature Background - No Overlay', 'pagelines' ) ),
									),
								),
						),
					),
					'feature_media_stuff' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Feature Media', 'pagelines'), 
						'shortexp'	=> __('The media that the feature will use', 'pagelines'),
						'selectvalues'	=> array(
							'feature-background-image' => array(
									'inputlabel' 		=> __( 'Full Size - Feature Background Image', 'pagelines' ),
									'type' 			=> 'image_upload'
								),

							'feature-media-image' => array(
									'version' 		=> 'pro',
									'type' 			=> 'image_upload',					
									'inputlabel' 		=> __( 'Media Area - Image (optional)', 'pagelines' ),
								),
							'feature-media' => array(
									'version' 		=> 'pro',
									'type' 			=> 'textarea',					
									'inputlabel' 	=> __( 'Feature HTML (video embeds etc, In Media Area)', 'pagelines' ),
								),
							'feature-media-full' => array(
									'version' 		=> 'pro',
									'type' 			=> 'check',					
									'inputlabel' 	=> __( 'Make Feature HTML Full Width', 'pagelines' ),
								),
							'feature-thumb' => array(
									'inputlabel'	=> __( 'Thumb Navigation - Upload Feature Thumbnail (50px by 30px)', 'pagelines' ),
									'type' 			=> 'image_upload'
								),
							'feature-name' => array(
									'default'		=> '',
									'inputlabel'	=> __( 'Names Navigation - Enter Text', 'pagelines' ),
									'type' 			=> 'text'
								),
						),
					),
					
					
					'feature-link-url' => array(
							'shortexp' 			=> __( 'Adding a URL here will add a link to your feature slide', 'pagelines' ),
							'title' 			=> __( 'Feature Link URL', 'pagelines' ),
							'label'				=> __( 'Enter Feature Link URL', 'pagelines' ),
							'type' 				=> 'text', 
							'exp'				=> __( 'Adds a "More" link to your text. If you have "Full Width Background Image" mode selected, the entire slide will be linked.', 'pagelines' )
						),
					'feature-link-text' => array(
							'default'		=> 'More',
							'shortexp' 		=> __( 'Enter the text you would like in your feature link', 'pagelines' ),
							'title' 		=> __( 'Link Text', 'pagelines' ),
							'label'			=> __( 'Enter Feature Link Text', 'pagelines' ),
							'type' 			=> 'text', 
							'size'			=> 'small'
						),
					
		
			);
			
			$posts_mode = ( ploption('posts_feature_control') ) ? true : false;
			
			// Add options for correct post type.
			$post_types = ( $posts_mode ) ? array( $this->ptID, 'post' ) : array( $this->ptID );
			
			$pt_panel = array(
					'id' 		=> 'feature-metapanel',
					'name' 		=> __( 'Feature Setup Options', 'pagelines' ),
					'posttype' 	=> $post_types, 
					'hide_tabs'	=> true
				);
			
			$pt_panel =  new PageLinesMetaPanel( $pt_panel );
			
			
			$pt_tab = array(
				'id' 		=> 'feature-type-metatab',
				'name' 		=> __( 'Feature Setup Options', 'pagelines' ),
				'icon' 		=> $this->icon,
			);
			
			$pt_panel->register_tab( $pt_tab, $pt_tab_options );
		
	}


	/**
	*
	* @TODO document
	*
	*/
	function post_type_setup(){
		
			$args = array(
					'label' 			=> __( 'Features', 'pagelines' ),  
					'singular_label' 	=> __( 'Feature', 'pagelines' ),
					'description' 		=> __( 'For setting slides on the feature page template', 'pagelines' ),
					'taxonomies'		=> array( $this->taxID ), 
					'menu_icon'			=> $this->icon
				);	
			$taxonomies = array(
				$this->taxID => array(	
						'label' => __( 'Feature Sets', 'pagelines' ), 
						'singular_label' => __( 'Feature Set', 'pagelines' ), 
					)
			);
			$columns = array(
				'cb' 					=> "<input type=\"checkbox\" />",
				'title' 				=> __( 'Title', 'pagelines' ),
				"feature-description" 	=> __( 'Text', 'pagelines' ),
				"feature-media" 		=> __( 'Media', 'pagelines' ),
				$this->taxID			=> __( 'Feature Sets', 'pagelines' )
			);
		
		
			$this->post_type = new PageLinesPostType( $this->ptID, $args, $taxonomies, $columns, array( &$this, 'column_display' ) );
		
			$this->post_type->set_default_posts( 'update_default_posts', $this );
			
	}


	/**
	*
	* @TODO document
	*
	*/
	function section_optionator( $settings ){
		$settings = wp_parse_args( $settings, $this->optionator_default );
		
			$page_metatab_array = array(
					'feature_setup' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Feature Section Setup', 'pagelines'), 
						'shortexp'	=> __('Basic section setup for features', 'pagelines'),
						
						'docslink'		=> 'http://www.pagelines.com/docs/feature-slider', 
						'vidtitle'		=> __( 'View Feature Documentation', 'pagelines' ),
						'selectvalues'	=> array(

							'feature_set' => array(
								'version' 		=> 'pro',
								'default'		=> 'default-features',
								'type' 			=> 'select_taxonomy',
								'taxonomy_id'	=> $this->taxID,				
								'inputlabel'	=> __( 'Select Feature Set', 'pagelines' ),
							),
							'feature_nav_type' => array(
								'default'	=> 'thumbs',
								'version'	=> 'pro',
								'type'		=> 'select',
								'selectvalues' => array(
									'nonav' 		=> array( 'name' => __( 'No Navigation', 'pagelines' ) ),
									'dots' 			=> array( 'name' => __( 'Squares or Dots', 'pagelines' ) ),
									'names' 		=> array( 'name' => __( 'Feature Names', 'pagelines' ) ),
									'thumbs' 		=> array( 'name' => __( 'Feature Thumbs (50px by 30px)', 'pagelines' ) ),								
									'numbers'		=> array( 'name' => __( 'Numbers', 'pagelines' ) ),
								),
								'inputlabel'	=> __( 'Feature Navigation Mode', 'pagelines' ),
							),
							'feature_items' 	=> array(
								'version' 		=> 'pro',
								'default'		=> 5,
								'type' 			=> 'text_small',
								'inputlabel'	=> __( 'Max number of features to show', 'pagelines' ),
							),
							
						),
					),
					'feature_handling' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Feature Section Height and Responsive Control', 'pagelines'), 
						'shortexp'	=> __('Dimensions and Responsive Handling', 'pagelines'),
						'selectvalues'	=> array(

							'feature_height_mode' => array(
									'default' 		=> 'aspect',
									'version'		=> 'pro',
									'type' 			=> 'select',
									'selectvalues' => array(
										'static' 		=> array('name' => __( 'Static Height (Height is always the same)', 'pagelines' ) ),
										'aspect' 		=> array('name' => __( 'Aspect Height (Height based on width)', 'pagelines' ) ),						
									),
									'inputlabel' 	=> __( 'Select Height Mode (The way feature height is managed)', 'pagelines' )
							),
							'feature_stage_height' => array(
									'default' 		=> '380',
									'version'		=> 'pro',
									'type' 			=> 'text_small',
									'inputlabel' 	=> __( 'Static Mode - Height (In Pixels)', 'pagelines' ),
							),
							'feature_aspect' => array(
									'default' 		=> '1.77',
									'version'		=> 'pro',
									'type' 			=> 'text_small',
									'inputlabel' 	=> __( 'Aspect Mode - Ratio (Width/Height - 16:9 would be 1.777)', 'pagelines' ),
							),
						),
					),
					'feature_transitions' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Feature Transitions and Effects', 'pagelines'), 
						'shortexp'	=> __('Options for managing feature transitions', 'pagelines'),
						'selectvalues'	=> array(
							'feffect' => array(
									'default'	=> 'fade',
									'version'	=> 'pro',
									'type'		=> 'select_same',
									'selectvalues' => array('blindX', 'blindY', 'blindZ', 'cover', 'curtainX', 'curtainY', 'fade', 'fadeZoom', 'growX', 'growY', 'none', 'scrollUp', 'scrollDown', 'scrollLeft', 'scrollRight', 'scrollHorz', 'scrollVert','shuffle','slideX','slideY','toss','turnUp','turnDown','turnLeft','turnRight','uncover','wipe','zoom'),
									'inputlabel'=> __( 'Select Transition Effect', 'pagelines' ),
								),
							'timeout' => array(
									'default'	=> '0',
									'version'	=> 'pro',
									'type'		=> 'text_small',
									'inputlabel'=> __( 'Timeout (ms - 0 means manual transition, 5000 = 5 seconds)', 'pagelines' ),
								),
							'fspeed' => array(
									'default'	=> 1500,
									'version'	=> 'pro',
									'type'		=> 'text_small',
									'inputlabel'=> __( 'Transition Speed (ms - e.g. 1500 = 1.5 seconds)', 'pagelines' ),
								),
							
							'feature_playpause' => array(
									'default'	=> false,
									'version'	=> 'pro',
									'type'		=> 'check',
									'inputlabel'=> __( 'Show play pause button? (Auto Scrolling Only)', 'pagelines' ),
								),
						),
					),
					'feature_source' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Feature Source and Order (Advanced)', 'pagelines'), 
						'shortexp'	=> __('Advanced options for feature sources and their order. Important: Enable feature panel under settings > advanced for use with posts.', 'pagelines'),
						'selectvalues'	=> array(
							'feature_source'	=> array(
									'default'	=> 'featureposts',
									'version'	=> 'pro',
									'type'		=> 'select',
									'selectvalues' 		=> array(
										'featureposts' 	=> array('name' => __( 'Feature Posts (custom post type)', 'pagelines' ) ),
										'posts' 		=> array('name' => __( 'Use Post Category', 'pagelines' ) ),
										'posts_all' 	=> array('name' => __( 'Use all Posts', 'pagelines' ) ),
									),
									'inputlabel'	=> __( 'Select Feature Post Source (Optional - Defaults to Custom Post Type)', 'pagelines' ),
								),
							'feature_category'		=> array(
									'default'		=> 1,
									'version'		=> 'pro',
									'type'			=> 'select',
									'selectvalues'	=> $this->get_cats(),
									'inputlabel'	=> __( 'Select Post Category (Post category source only)', 'pagelines' ),
								),
							
							'feature_orderby' => array(
									'default' => 'ID',
									'version'	=> 'pro',
									'type' => 'select',
									'selectvalues' => array(
										'ID' 			=> array('name' => __( 'Post ID (default)', 'pagelines' ) ),
										'title' 		=> array('name' => __( 'Title', 'pagelines' ) ),
										'date' 			=> array('name' => __( 'Date', 'pagelines' ) ),
										'modified' 		=> array('name' => __( 'Last Modified', 'pagelines' ) ),
										'rand' 			=> array('name' => __( 'Random', 'pagelines' ) ),							
									),
									'inputlabel'	=> __( 'Select sort method (If not using Post Type Order plugin)', 'pagelines' ),
								),

							'feature_order' => array(
									'default'	=> 'DESC',
									'version'	=> 'pro',
									'type'		=> 'select',
									'selectvalues'	=> array(
										'DESC' 		=> array('name' => __( 'Descending', 'pagelines' ) ),
										'ASC' 		=> array('name' => __( 'Ascending', 'pagelines' ) ),
									),
									'inputlabel'	=> __( 'Select sort order (If not using Post Type Order plugin)', 'pagelines' ),
								),
						),
					),
					
					 
					
					
				);

			$metatab_settings = array(
					'id' 		=> 'feature_meta',
					'name' 		=> __( 'Features', 'pagelines' ),
					'icon' 		=> $this->icon, 
					'clone_id'	=> $settings['clone_id'], 
					'active'	=> $settings['active']
				);

			register_metatab( $metatab_settings, $page_metatab_array );

	}
	

	/**
	*
	* @TODO document
	*
	*/
	function update_default_posts(){

		$posts = array_reverse( $this->default_posts() );

		foreach( $posts as $p ){
			// Create post object
			$default = array();
			$default['post_title'] = $p['title'];
			$default['post_content'] = $p['text'];
			$default['post_type'] = $this->ptID;
			$default['post_status'] = 'publish';
			
			if ( defined( 'ICL_LANGUAGE_CODE' ) )
				$default_post['icl_post_language'] = ICL_LANGUAGE_CODE;
				
			$newPostID = wp_insert_post( $default );

			update_post_meta( $newPostID, 'feature-thumb', $p['thumb'] );
			update_post_meta( $newPostID, 'feature-link-url', $p['link'] );
			update_post_meta( $newPostID, 'feature-style', $p['style'] );
			update_post_meta( $newPostID, 'feature-media', $p['media'] );
			update_post_meta( $newPostID, 'feature-background-image', $p['background'] );
			update_post_meta( $newPostID, 'feature-design', $p['fcontent-design'] );
			wp_set_object_terms( $newPostID, 'default-features', $this->taxID );
		}
	}


	/**
	*
	* @TODO document
	*
	*/
	function default_posts( ){

		$posts = array(
				'1' => array(
			        	'title' 			=> 'PageLines',
			        	'text' 				=> 'Welcome to PageLines Framework!',
			        	'media' 			=> '',
						'style'				=> 'text-none',
			        	'link' 				=> '#fake_link',
						'background' 		=> $this->base_url.'/images/feature1.jpg',
						'name'				=> 'Intro',
						'fcontent-design'	=> '',
						'thumb'				=> $this->base_url.'/images/fthumb1.png'
			    ),
				'2' => array(
					 	'title' 		=> 'Drag &amp; Drop Design',
			        	'text' 			=> 'Welcome to a professional WordPress framework by PageLines. Designed for you in San Francisco, California.',
			        	'media' 		=> '',
			        	'style'			=> 'text-none',
						'link' 			=> '#fake_link',
						'background' 	=> $this->base_url.'/images/feature2.jpg',
						'name'			=>	'Design',
						'fcontent-design'	=> '',
						'thumb'				=> $this->base_url.'/images/fthumb2.png'
			    ), 
				'3' => array(
					 	'title' 		=> 'The PageLines Store',
			        	'text' 			=> 'Buy and sell drag and drop sections, plugins and themes. The first ever "app-store" for web design.',
			        	'media' 		=> '',
			        	'style'			=> 'text-none',
						'link' 			=> '#fake_link',
						'background' 	=> $this->base_url.'/images/feature3.jpg',
						'name'			=>	'Design',
						'fcontent-design'	=> '',
						'thumb'				=> $this->base_url.'/images/fthumb3.png'
			    ), 
				'4' => array(
					 	'title' 		=> 'Page-by-Page Options',
			        	'text' 			=> 'Want to do something totally unique? PageLines offers options for almost everything in an intuitive and easy to use format.',
			        	'media' 		=> '',
			        	'style'			=> 'text-none',
						'link' 			=> '#fake_link',
						'background' 	=> $this->base_url.'/images/feature4.jpg',
						'name'			=>	'Design',
						'fcontent-design'	=> '',
						'thumb'				=> $this->base_url.'/images/fthumb4.png'
			    ), 
				'5' => array(
					 	'title' 		=> 'Design Control',
			        	'text' 			=> 'Use advanced typography and color control to fine tune your design with point and click simplicity.',
			        	'media' 		=> '',
			        	'style'			=> 'text-none',
						'link' 			=> '#fake_link',
						'background' 	=> $this->base_url.'/images/feature5.jpg',
						'name'			=>	'Design',
						'fcontent-design'	=> '',
						'thumb'				=> $this->base_url.'/images/fthumb5.png'
			    )
		);

		return apply_filters('pagelines_default_features', array_reverse($posts));
	}


	/**
	*
	* @TODO document
	*
	*/
	function get_cats() {
	
		$cats = get_categories();
		foreach( $cats as $cat )
			$categories[ $cat->cat_ID ] = array( 'name' => $cat->name );
			
		return ( isset( $categories) ) ? $categories : array();
	}


	/**
	*
	* @TODO document
	*
	*/
	function column_display( $column ){

		global $post;

		switch ( $column ){
			case "feature-description":
				the_excerpt();
				break;
			case "feature-media":
			 	if( m_pagelines( 'feature-media', $post->ID ) )
					em_pagelines( 'feature-media', $post->ID );
				elseif( m_pagelines( 'feature-media-image', $post->ID ) )
					echo '<img src="'.m_pagelines('feature-media', $post->ID).'" style="max-width: 200px; max-height: 200px" />'; 
				elseif(m_pagelines('feature-background-image', $post->ID))
					echo '<img src="'.m_pagelines('feature-background-image', $post->ID).'" style="max-width: 200px; max-height: 200px" />'; 
				
				break;
			case $this->taxID:
				echo get_the_term_list( $post->ID, $this->taxID, '', ', ','' );
				break;
		}
	}
}
