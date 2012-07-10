<?php
/*
	Section: Banners	
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates banners, great for product tours.
	Class Name: PageLinesBanners
	Workswith: templates, main, header, morefoot
	Edition: pro
	Cloning: true 
*/

/**
 * Banners Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesBanners extends PageLinesSection {


	var $ptID = 'banners';
	var $taxID = 'banner-sets'; 

	function section_head(){

		?>
		
		<script type="text/javascript"> 
			/* <![CDATA[ */ 
			jQuery(window).load(function() {  
				jQuery('.banner-area.no-pad').each(function(index) {
					var bannerText = jQuery(this).find('.banner-text-pad');
					var bannerTextWrap = bannerText.find('.banner-text-wrap');
				    var textHeight = bannerTextWrap.innerHeight();
					var mediaHeight = jQuery(this).find('.banner-media').height();
					
					if(mediaHeight > textHeight){
						
						var padHeight = (mediaHeight - textHeight ) / 2;
						bannerText.css('padding-top', padHeight);
					}
				});
				
			}); 
			/* ]]> */ 
		</script>
		
		<?php 
	}

	/**
	* PHP that always loads no matter if section is added or not.
	*/
	function section_persistent(){
		
		/* Create Custom Post Type */
			$args = array(
					'label' 			=> __( 'Banners', 'pagelines' ),  
					'singular_label' 	=> __( 'Banner', 'pagelines' ),
					'description' 		=> __( 'For creating banners in banner section layouts.', 'pagelines' ),
					'menu_icon'			=> $this->icon
				);
			$taxonomies = array(
				$this->taxID => array(	
						'label' => __( 'Banner Sets', 'pagelines' ), 
						'singular_label' => __( 'Banner Set', 'pagelines' ), 
					)
			);
			$columns = array(
				'cb' => "<input type=\"checkbox\" />",
				'title' => "Title",
				"banner-description" => "Text",
				"banner-media" => "Media",
				$this->taxID => "Banner Sets"
			);
		
			$this->post_type = new PageLinesPostType( $this->ptID, $args, $taxonomies, $columns, array(&$this, 'banner_column_display') );
	
	
		$this->type_meta_options();

	
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function type_meta_options(){
		/* Meta Options */
			$type_meta_array = array(
				'banner_align' => array(
					'version' => 'pro',
					'type' => 'select',
					'selectvalues'	=> array(
							'banner_right'	=> array('name' => __( 'Banner Right', 'pagelines' ) ),
							'banner_left'	=> array('name' => __( 'Banner Left', 'pagelines' ) )
						), 
					'title' => __( 'Banner Alignment', 'pagelines' ),				
					'shortexp' => __( 'Put the media on the right or the left?', 'pagelines' ),

				),
				'the_banner_image' 	=> array(
						'type'		=> 'image_upload',					
						'title'		=> __( 'Banner Media', 'pagelines' ),
						'shortexp'	=> __( 'Upload an image for the banner.', 'pagelines' )
					),
				'banner_text_width' => array(
						'type' 			=> 'count_select',		
						'count_start'	=> '1',
						'count_number'	=> '100',		
						'title'			=> __( 'Banner Text Width (In %)', 'pagelines' ),
						'shortexp' 		=> __( 'Set the width of the text area as a percentage of full content width.  The media area will fill the rest.', 'pagelines' )
					),
				'the_banner_media' 		=> array(
						'type' => 'textarea',					
						'title' => __( 'Banner Media', 'pagelines' ),
						'shortexp' => __( 'Add HTML Media for the banner, e.g. Youtube embed code. This option is used if there is no image uploaded.', 'pagelines' )
					),
				'banner_text_padding' => array(
					'version' 	=> 'pro',
					'type' 		=> 'text',
					'size'		=> 'small',					
					'title' 	=> __( 'Banner Text Padding', 'pagelines' ),
					'shortexp'	=> __( 'Configure the padding and arrangement of banner text', 'pagelines' ),
					'exp' 	=> __( '(optional) Set the padding for the text area. Use CSS shorthand, for example:<strong> 25px 30px 25px 35px</strong> for top, right, bottom, then left padding.<br/><br/><strong>Heads Up</strong> If you do not set this option, the banner will attempt to vertically align the text for you.', 'pagelines' )

				),
			);

			$post_types = array($this->id); 

			$type_metapanel_settings = array(
					'id' 		=> 'banner-metapanel',
					'name' 		=> __( 'Banner Setup Options', 'pagelines' ),
					'posttype' 	=> $post_types, 
					'hide_tabs'	=> true
				);

			$type_meta_panel =  new PageLinesMetaPanel( $type_metapanel_settings );

			$type_metatab_settings = array(
				'id' 		=> 'banner-type-metatab',
				'name' 		=> __( 'Banner Setup Options', 'pagelines' ),
				'icon' 		=> $this->icon,
			);

			$type_meta_panel->register_tab( $type_metatab_settings, $type_meta_array );
	}



	/**
	*
	* @TODO document
	*
	*/
	function section_optionator( $settings ){
		$settings = wp_parse_args($settings, $this->optionator_default);
		
		$metatab_array = array(
				
				'banner_items' => array(
					'default'	=> '5',
					'version' 	=> 'pro',
					'type' 		=> 'text_small',		
					'title' 	=> __( 'Max Number of Banners', 'pagelines' ),
					'desc' 		=> __( 'Select the default max number of banners.', 'pagelines' ),
					'inputlabel'=> __( 'Number of Banners', 'pagelines' ),
					'exp'		=> __( 'This number will be used as the max number of banners to use in this section.', 'pagelines' )
				),
				'banner_set' => array(
						'default' 		=> null,
						'version'		=> 'pro',
						'type' 			=> 'select_taxonomy',
						'taxonomy_id'	=> $this->taxID,
						'desc'		 	=> __( 'Select Default Banner Set', 'pagelines' ),
						'inputlabel' 	=> __( 'Select Default Banner Set', 'pagelines' ),
						'title' 		=> __( 'Default Banner Set', 'pagelines' ),
						'shortexp' 		=> __( "Select the category (taxonomy) of Banner posts to show", 'pagelines' ),
						'exp' 			=> __( "Select the taxonomy/category of banners to show on this page.", 'pagelines' ),

				),

			);

		$metatab_settings = array(
				'id' 		=> 'banner_page_meta',
				'name' 		=> 'Banners',
				'icon' 		=>  $this->icon,
				'clone_id'	=> $settings['clone_id'], 
				'active'	=> $settings['active']
			);

		register_metatab($metatab_settings, $metatab_array);
	}

	/**
	* Section template.
	*/
   function section_template( $clone_id ) {    

		// Options
			$set = (ploption('banner_set', $this->oset)) ? ploption('banner_set', $this->oset) : null;
			$limit = (ploption('banner_items', $this->oset)) ? ploption('banner_items', $this->oset) : null;
		
		// Actions
			$b = $this->load_pagelines_banners($set, $limit);
			
			if(empty($b)){
				echo setup_section_notify($this, __( "No Banner posts matched this page's criteria", 'pagelines' ) );
				return;
			}
			
			
			$this->draw_banners($b, 'banners ' . $set);
	}


	/**
	*
	* @TODO document
	*
	*/
	function draw_banners($b, $class = ""){ ?>		
		<div class="banner_container fix <?php echo $class;?>">
	<?php 
		
		foreach($b as $bpost) : 
			$oset = array('post_id' => $bpost->ID);
			
			$banner_text_width = (ploption('banner_text_width', $oset)) ? ploption('banner_text_width', $oset) : 50;
			$banner_media_width = 100 - $banner_text_width; // Math
			$banner_align = (ploption('banner_align', $oset)) ? ploption('banner_align', $oset) : 'banner_left';
			
			$pad = ploption('banner_text_padding', $oset);
			$banner_text_padding = ($pad) ? sprintf('padding:%s;', $pad) : "padding: 20px 40px"; 
			
			$pad_flag = ($pad) ? 'with-pad' : 'no-pad';
			
?>		<div class="banner-area pprand-pad <?php echo $banner_align.' '.$pad_flag;?>">
				<div class="banner-text pprand" style="width:<?php echo $banner_text_width; ?>%;">
					<div class="banner-text-pad pprand-pad" style="<?php echo $banner_text_padding;?>">
						<div class="banner-text-wrap">
							<div class="banner-title">
								<h2><?php echo do_shortcode($bpost->post_title); ?></h2>
							</div>
							<div class="banner-content">	
								<?php 
									echo apply_filters( 'the_content', do_shortcode($bpost->post_content).pledit($bpost->ID) ); 
								?>
							</div>
						</div>
					</div>
				</div>
				<div class="banner-media pprand" style="width:<?php echo $banner_media_width; ?>%;" >
					<div class="banner-media-pad pprand-pad">
						<?php echo do_shortcode(self::_get_banner_media( $oset ) );?>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		<?php endforeach;?>
		</div><div class="clear"></div>
<?php }

	

	/**
	*
	* @TODO document
	*
	*/
	function _get_banner_media( $oset ){
		
			
			if(plmeta('the_banner_image', $oset))
				$banner_media = '<img src="'.plmeta('the_banner_image', $oset).'" alt="'.get_the_title().'" />';
			elseif(plmeta('the_banner_media', $oset))
				$banner_media = do_shortcode( plmeta('the_banner_media', $oset) );
			else 
				$banner_media = '';
			
			// Filter output
			return apply_filters('pl_banner_image', $banner_media, $oset);
	}
	
	

	/**
	*
	* @TODO document
	*
	*/
	function load_pagelines_banners($set = null, $limit = null){
	
		$query = array( 'post_type' => $this->ptID, 'orderby' => 'ID' ); 
		
		$query['no_found_rows'] = 1;

		if(isset($set)) 
			$query[$this->taxID] = $set; 
			
		if(isset($limit)) 
			$query['showposts'] = $limit; 

		$q = new WP_Query($query);
		
		if(is_array($q->posts)) 
			return $q->posts;
		else 
			return array();


	}
	

	/**
	*
	* @TODO document
	*
	*/
	function banner_column_display($column){
		global $post;

		switch ($column){
			case "banner-description":
				the_excerpt();
				break;
			case "banner-media":
				if(get_post_meta($post->ID, 'the_banner_image', true )){

					echo '<img src="'.get_post_meta($post->ID, 'the_banner_image', true ).'" style="width: 80px; margin: 10px; border: 1px solid #ccc; padding: 5px; background: #fff" />';	
				}

				break;
			case $this->taxID:
				echo get_the_term_list($post->ID, $this->taxID, '', ', ','');
				break;
		}
	}	
}
