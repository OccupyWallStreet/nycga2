<?php 
/**
 * check if it's a child theme or parent theme and return the correct path
 *
 * @package Custom Community
 * @since 1.8.3
 */
function cc_require_path($path){
	Custom_Community::require_path($path);
}
	
/**
 * get the right img for the slideshow shadow
 *
 * @package Custom Community
 * @since 1.8.3
 */
function cc_slider_shadow() {
	global $cap;
	if ($cap->slideshow_shadow == "shadow") { 
		return "slider-shadow.png"; 
	} else { 
		return "slider-shadow-sharp.png"; 
	}
}  

/**
 *  define new excerpt length
 *
 * @package Custom Community
 * @since 1.8.3
 */
function cc_excerpt_length() {
	global $cap;
	$excerpt_length = 30;
	if($cap->excerpt_length){
		$excerpt_length = $cap->excerpt_length;
	}
	return $excerpt_length;
}

/**
 * change the profile tab order
 *
 * @package Custom Community
 * @since 1.8.3
 */
add_action( 'bp_init', 'cc_change_profile_tab_order' );
function cc_change_profile_tab_order() {
	global $bp, $cap;
	
	if($cap->bp_profiles_nav_order == '')
		return;
	
	$order = $cap->bp_profiles_nav_order;
	$order = str_replace(' ','',$order); 
	$order = explode(",", $order);
	$i = 1;
	
	foreach($order as $item) {
		$bp->bp_nav[$item]['position'] = $i;
		$i ++;
	}
	
}

/**
 * change the groups tab order
 *
 * @package Custom Community
 * @since 1.8.3
 */
add_action('bp_init', 'cc_change_groups_tab_order');
function cc_change_groups_tab_order() {
	global $bp, $cap;

	
	// In BP 1.3, bp_options_nav for groups is keyed by group slug instead of by 'groups', to
	// differentiate it from the top-level groups directories and the groups subtab of member
	// profiles
	$group_slug = isset( $bp->groups->current_group->slug ) ? $bp->groups->current_group->slug : false;
	
	
	if($cap->bp_groups_nav_order == '')
		return;

		
	$order = $cap->bp_groups_nav_order;
	$order = str_replace(' ','',$order); 
	$order = explode(",", $order);
	$i = 1;
	foreach($order as $item) {
		$bp->bp_options_nav[$group_slug][$item]['position'] = $i;
		$i ++;
	}
}


/**
 * find out the right color scheme and create the array of css elements with the hex codes
 *
 * @package Custom Community
 * @since 1.8.3
 */
	
function cc_switch_css(){
	global $cap;
		
	$switch_css =  array(
	'body_bg_color' => 'ffffff',
	'container_bg_color' => 'ffffff',
	'container_alt_bg_color' => 'ededed',
	'details_bg_color' => 'ededed', 
	'details_hover_bg_color' => 'f9f9f9',
	'font_color' => '888888',
	'font_alt_color' => 'afafaf',
	'link_color' => '489ed5',
	);

	if ($cap->style_css != false):;
	switch ($cap->style_css){
        case 'dark':
			$switch_css =  array(
			'body_bg_color' => '333333',
			'container_bg_color' => '181818',
			'container_alt_bg_color' => '333333',
			'details_bg_color' => '181818', 
			'details_hover_bg_color' => '252525',
			'font_color' => '888888',
			'font_alt_color' => '555555',
			'link_color' => 'ffffff',
			);
        break;
        case 'natural':
			$switch_css =  array(
			'body_bg_color' => 'F5E5B3',
			'container_bg_color' => 'FFF9DB',
			'container_alt_bg_color' => 'F5E5B3',
			'details_bg_color' => 'FFF9DB', 
			'details_hover_bg_color' => 'FFE5B3',
			'font_color' => '888888',
			'font_alt_color' => 'aaaaaa',
			'link_color' => 'ff7400',
			);
        	
        break;
        case 'white':
			$switch_css =  array(
			'body_bg_color' => 'ffffff',
			'container_bg_color' => 'ffffff',
			'container_alt_bg_color' => 'ededed',
			'details_bg_color' => 'ededed', 
			'details_hover_bg_color' => 'f9f9f9',
			'font_color' => '888888',
			'font_alt_color' => 'afafaf',
			'link_color' => '489ed5',
			);
        break;
        case 'light':
			$switch_css =  array(
			'body_bg_color' => 'ededed',
			'container_bg_color' => 'ffffff',
			'container_alt_bg_color' => 'ededed',
			'details_bg_color' => 'ffffff', 
			'details_hover_bg_color' => 'f9f9f9',
			'font_color' => '888888',
			'font_alt_color' => 'afafaf',
			'link_color' => '529e81',
			);
        break;
        case 'grey':
			$switch_css =  array(
			'body_bg_color' => 'f1f1f1',
			'container_bg_color' => 'dddddd',
			'container_alt_bg_color' => 'f1f1f1',
			'details_bg_color' => 'dddddd', 
			'details_hover_bg_color' => 'ededed', 
			'font_color' => '555555',
			'font_alt_color' => 'aaaaaa',
			'link_color' => '1f8787',
			);
        break;
        case 'black':
			$switch_css =  array(
			'body_bg_color' => '000000',
			'container_bg_color' => '000000',
			'container_alt_bg_color' => '333333',
			'details_bg_color' => '333333', 
			'details_hover_bg_color' => '181818',
			'font_color' => '888888',
			'font_alt_color' => '555555',
			'link_color' => 'ffffff',
			);
        break;
	    }
	endif;
	return $switch_css;
}
	
/**
 * find out the right color scheme and create the array of css elements with the hex codes
 *
 * @package Custom Community
 * @since 1.8.3
 */
function cc_color_scheme(){
	echo cc_get_color_scheme();
}
	function cc_get_color_scheme(){
		global $cap;
		if(isset( $_GET['show_style']))
			$cap->style_css = $_GET['show_style']; 
			
		switch ($cap->style_css)
	        {
	        case 'dark':
			$color = 'dark';
	        break;
	        case 'natural':
			$color = 'natural';
	        break;
	        case 'white':
			$color = 'white';
	        break;
	        case 'light':
			$color = 'light';
	        break;
	        case 'grey':
			$color = 'grey';
	        break;
	        case 'black':
			$color = 'black';
	        break;
	        default:
			$color = 'grey';
	        break;
	        }
	        return $color; 
	}
	
/**
 * load the array for the top slider depending on the page settings or theme settings
 *
 * @package Custom Community
 * @since 1.8.3
 */	
function cc_slidertop(){
	global $cc_page_options, $cap;

	$cc_page_options = cc_get_page_meta();
	
	$slidercat = '0' ;
	$slider_style = 'default';
	$caption = 'on';
	$slideshow_amount = '4';
	$slideshow_time = '5000';
	$slideshow_orderby = 'DESC';
	$slideshow_post_type = 'post';
	$slideshow_show_page = '';
	
//	echo '<pre>';
//	print_r( $cc_page_options );
//	echo '<pre>';
	
	if($cc_page_options["cc_page_slider_on"] == 1 ){
				
		if( $cc_page_options["cc_page_slider_cat"] != '' && $cc_page_options["cc_page_slider_show_page"] == '' ){
			$slidercat = $cc_page_options["cc_page_slider_cat"];
		}
		if( $cc_page_options["cc_page_slider_style"] != '' ){
			$slider_style = $cc_page_options["cc_page_slider_style"];
		}
		if( $cc_page_options["cc_page_slider_caption"] != '' ){
			$caption = $cc_page_options["cc_page_slider_caption"];
		}
		if( $cc_page_options["cc_page_slider_amount"]  != '' ){
			$slideshow_amount = $cc_page_options["cc_page_slider_amount"];
		}
		if( $cc_page_options["cc_page_slider_time"] != '' ){
			$slideshow_time = $cc_page_options["cc_page_slider_time"];
		}
		if( $cc_page_options["cc_page_slider_orderby"] != '' ){
			$slideshow_orderby = $cc_page_options["cc_page_slider_orderby"];
		}
		if( $cap->$cc_page_options["cc_page_slider_post_type"] != '' ){
			$slideshow_post_type = $cc_page_options["cc_page_slider_post_type"];
		}
		if( $cc_page_options["cc_page_slider_show_page"] != '' ){
			$slideshow_show_page = $cc_page_options["cc_page_slider_show_page"];
		}

	}else{

		if( $cap->slideshow_cat != '' ){
			$slidercat = $cap->slideshow_cat;
		}
		if( $cap->slideshow_style != '' ){
			$slider_style = $cap->slideshow_style;
		}
		if( $cap->slideshow_caption != '' ){
			$caption = $cap->slideshow_caption;
		}
		if( $cap->slideshow_amount != '' ){
			$slideshow_amount = $cap->slideshow_amount;
		}
		if( $cap->slideshow_time != '' ){
			$slideshow_time = $cap->slideshow_time;
		}
		if( $cap->slideshow_orderby != '' ){
			$slideshow_orderby = $cap->slideshow_orderby;
		}
		if( $cap->slideshow_post_type != '' ){
			$slideshow_post_type = $cap->slideshow_post_type;
		}
		if( $cap->slideshow_show_page != '' ){
			$slideshow_show_page = $cap->slideshow_show_page;
		}
		
	}
	
	if($slider_style == 'full width' || $slider_style == 'full-width-image' ){ ?>
		<style type="text/css">
			div#cc_slider-top div.cc_slider .featured .ui-tabs-panel{
			width: 100%;
			}
		</style>
	<?php }
	
	if($slider_style == 'full width' || $slider_style == 'full-width-image' ){
		$atts = array(
			'amount' => $slideshow_amount,
			'category_name' => $slidercat,
			'slider_nav' => 'off',
			'caption' => $caption,
			'caption_width' => '1000',
			'width' => '1000',
			'height' => '250',
			'id' => 'slidertop',
			'time_in_ms' => $slideshow_time,
			'orderby' => $slideshow_orderby,
			'page_id' => $slideshow_show_page,
			'post_type' =>$slideshow_post_type
		);
	} else {
		$atts = array(
			'amount' => '4',
			'category_name' => $slidercat,
			'slider_nav' => 'on',
			'caption' => $caption,
			'id' => 'slidertop',
			'time_in_ms' => $slideshow_time,
			'orderby' => $slideshow_orderby,
			'page_id' => $slideshow_show_page,
			'post_type' =>$slideshow_post_type
 			);					
	}

	$tmp = '<div id="cc_slider-top">';
	$tmp .= slider($atts,$content = null);
	$tmp .= '</div>';
	if($cap->slideshow_shadow != "no shadow"){
		$tmp .= '<div class="slidershadow" style="margin-top:-12px; margin-bottom:-30px;"><img src="'.get_template_directory_uri().'/images/slideshow/'.cc_slider_shadow().'"></img></div>';
	}
		
	return $tmp;

}

/**
 * load the array for the list posts depending on the page settings or theme settings
 *
 * @package Custom Community
 * @since 1.8.3
 */	
function cc_list_posts_on_page(){
	$cc_page_options=cc_get_page_meta(); 
    if(isset($cc_page_options) && $cc_page_options['cc_page_template_on'] == 1){
    
    switch ($cc_page_options['cc_posts_on_page_type'])
        {
        case 'img-mouse-over':
    	$atts = array(
			'amount' => $cc_page_options['cc_page_template_amount'],
			'category_name' => $cc_page_options['cc_page_template_cat'],
			'img_position' => 'mouse_over',
			);
        echo cc_list_posts($atts,$content = null); 
        break;
        case 'img-left-content-right':
		$atts = array(
			'amount' => $cc_page_options['cc_page_template_amount'],
			'category_name' => $cc_page_options['cc_page_template_cat'],
			'img_position' => 'left',
			);
        echo cc_list_posts($atts,$content = null); 
        break;
        case 'img-right-content-left':
		$atts = array(
			'amount' => $cc_page_options['cc_page_template_amount'],
			'category_name' => $cc_page_options['cc_page_template_cat'],
			'img_position' => 'right',
			);
        echo cc_list_posts($atts,$content = null); 
        break;
        case 'img-over-content':
		$atts = array(
			'amount' => $cc_page_options['cc_page_template_amount'],
			'category_name' => $cc_page_options['cc_page_template_cat'],
			'img_position' => 'over',
			);
        echo cc_list_posts($atts,$content = null); 
        break;
        case 'img-under-content':
		$atts = array(
			'amount' => $cc_page_options['cc_page_template_amount'],
			'category_name' => $cc_page_options['cc_page_template_cat'],
			'img_position' => 'under',
			);
        echo cc_list_posts($atts,$content = null); 
        break;
        }
	}
}

?>