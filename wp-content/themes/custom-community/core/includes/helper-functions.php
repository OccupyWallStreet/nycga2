<?php

/**
 * options "Add scripts to head" and "add scripts to footer"
 *
 * @package Custom Community
 * @since 1.12
 */
// add to head function - hooks the stuff to bp_head
function cc_cap_add_to_head() {
	global $cap;
	echo $cap->add_to_head;
}
add_action('bp_head', 'cc_cap_add_to_head', 20); 

// add to footer function - hooks the stuff to wp_footer
function cc_cap_add_to_footer() {
	global $cap;
	echo $cap->add_to_footer;
}
add_action('wp_footer', 'cc_cap_add_to_footer', 20); 


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
	if ($cap->slideshow_shadow == "shadow" || $cap->slideshow_shadow == __("shadow",'cc') ) { 
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

	if($cap->bp_profiles_nav_order == ''){
		$cap->bp_default_navigation = true;
		return;
	}
	$order = $cap->bp_profiles_nav_order;
	$order = str_replace(' ','',trim($order)); 
	$order = explode(",", $order);
	$i = 1;
	
	$bp->bp_nav = cc_filter_custom_menu($bp->bp_nav, $order);
	
	foreach($order as $item) {
		// check this such component actually exists
		if(!bp_is_active($item)){
			continue;
		}
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
	
	
	if($cap->bp_groups_nav_order == ''){
		$cap->bp_default_navigation = true;
		return;
	}

		
	$order = $cap->bp_groups_nav_order;
	$order = str_replace(' ','',$order); 
	$order = explode(",", $order);
	$i = 1;
	
	$bp->bp_options_nav[$group_slug] = cc_filter_custom_menu($bp->bp_options_nav[$group_slug], $order);
	if(!empty($bp->bp_options_nav[$group_slug])){
		foreach($order as $item) {
			if(!array_key_exists($item, $bp->bp_options_nav[$group_slug])){
				continue;
			}
			$bp->bp_options_nav[$group_slug][$item]['position'] = $i;
			$i ++;
		}
	}
}
/**
 * Remove menu items wihich not included to custom list
 * @param array $menu default menu
 * @param array $custom_items list of items
 * @return array new menu items
 */
function cc_filter_custom_menu($menu, $custom_items){
	if(is_array($custom_items) && is_array($menu)){
		return array_intersect_key($menu, array_flip($custom_items));
	}
	return $menu;
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
						'body_bg_color'          => 'ffffff',
						'container_bg_color'     => 'ffffff',
						'container_alt_bg_color' => 'ededed',
						'details_bg_color'       => 'ededed', 
						'details_hover_bg_color' => 'f9f9f9',
						'font_color'             => '888888',
						'font_alt_color'         => 'afafaf',
						'link_color'             => '489ed5',
					);

	if ($cap->style_css != false){
		switch ($cap->style_css){
	        case __('dark','cc'):
				$switch_css =  array(
									'body_bg_color'          => '333333',
									'container_bg_color'     => '181818',
									'container_alt_bg_color' => '333333',
									'details_bg_color'       => '181818', 
									'details_hover_bg_color' => '252525',
									'font_color'             => '888888',
									'font_alt_color'         => '555555',
									'link_color'             => 'ffffff',
								);
	        break;
	        case __('natural','cc'):
				$switch_css =  array(
									'body_bg_color'          => 'F5E5B3',
									'container_bg_color'     => 'FFF9DB',
									'container_alt_bg_color' => 'F5E5B3',
									'details_bg_color'       => 'FFF9DB', 
									'details_hover_bg_color' => 'FFE5B3',
									'font_color'             => '888888',
									'font_alt_color'         => 'aaaaaa',
									'link_color'             => 'ff7400',
								);
	        	
	        break;
	        case __('white','cc'):
				$switch_css =  array(
									'body_bg_color'          => 'ffffff',
									'container_bg_color'     => 'ffffff',
									'container_alt_bg_color' => 'ededed',
									'details_bg_color'       => 'ededed', 
									'details_hover_bg_color' => 'f9f9f9',
									'font_color'             => '888888',
									'font_alt_color'         => 'afafaf',
									'link_color'             => '489ed5',
								);
	        break;
	        case __('light','cc'):
				$switch_css =  array(
									'body_bg_color'          => 'ededed',
									'container_bg_color'     => 'ffffff',
									'container_alt_bg_color' => 'ededed',
									'details_bg_color'       => 'ffffff', 
									'details_hover_bg_color' => 'f9f9f9',
									'font_color'             => '888888',
									'font_alt_color'         => 'afafaf',
									'link_color'             => '529e81',
								);
	        break;
	        case __('grey','cc'):
				$switch_css =  array(
									'body_bg_color'          => 'f1f1f1',
									'container_bg_color'     => 'dddddd',
									'container_alt_bg_color' => 'f1f1f1',
									'details_bg_color'       => 'dddddd', 
									'details_hover_bg_color' => 'ededed', 
									'font_color'             => '555555',
									'font_alt_color'         => 'aaaaaa',
									'link_color'             => '1f8787',
								);
	        break;
	        case __('black','cc'):
				$switch_css =  array(
									'body_bg_color'          => '000000',
									'container_bg_color'     => '000000',
									'container_alt_bg_color' => '333333',
									'details_bg_color'       => '333333', 
									'details_hover_bg_color' => '181818',
									'font_color'             => '888888',
									'font_alt_color'         => '555555',
									'link_color'             => 'ffffff',
								);
	        break;
		}
	}
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
			
		switch ($cap->style_css){
	        case __('dark','cc'):
				$color = 'dark';
	        	break;
	        case __('natural','cc'):
				$color = 'natural';
	        	break;
	        case __('white','cc'):
				$color = 'white';
	        	break;
	        case __('light','cc'):
				$color = 'light';
	        	break;
	        case __('grey','cc'):
				$color = 'grey';
	        	break;
	        case __('black','cc'):
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
	
	$slidercat           = '0' ;
	$slider_style        = 'default';
	$caption             = 'on';
	$slideshow_amount    = '4';
	$slideshow_time      = '5000';
	$slideshow_orderby   = 'DESC';
	$slideshow_post_type = 'post';
	$slideshow_show_page = '';
    $is_allowed_direct_link = __('no', 'cc');
	
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
		if( $cc_page_options["cc_page_allow_direct_link"] != '' && $cc_page_options["cc_page_allow_direct_link"]=='yes'){
			$is_allowed_direct_link= __('yes', 'cc');
		}

	} else {

		if( $cap->slideshow_cat != '' ){
			$slidercat = $cap->slideshow_cat;
		}
		if( $cap->slideshow_style != '' ){
			$slider_style = $cap->slideshow_style;
		}
		if( $cap->slideshow_caption != '' ){
			switch ($cap->slideshow_caption) {
				case __('on','cc'):
				case 'on':
					$caption = 'on';
					break;
				case __('off','cc'):
				case 'off':
					$caption = 'off';
					break;
				default:
					$caption = 'on';
					break;
			}
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
        if($cap->slideshow_direct_links == 'yes'){
            $is_allowed_direct_link = __('yes', 'cc');
        }
	}
    if($cap->cc_responsive_enable){
        $slide_width = 1200;
    } else {
        $slide_width = $cap->website_width;
    }
    $same_attrs = array(
        'category__in'      => $slidercat,
        'caption'           => $caption,
        'id'                => 'slidertop',
        'time_in_ms'        => $slideshow_time,
        'orderby'           => $slideshow_orderby,
        'page_id'           => $slideshow_show_page,
        'post_type'         => $slideshow_post_type,
        'allow_direct_link' => $is_allowed_direct_link    
    );
	if($slider_style == __('full width','cc') || $slider_style == 'full-width-image' ){
		$atts = array(
					'amount'            => $slideshow_amount,
					'slider_nav'        => 'off',
					'caption_width'     => $slide_width,
					'width'             => $slide_width,
					'height'            => '250',
					     
				);
	} else {
		$atts = array(
					'amount'            => '4',
					'slider_nav'        => 'on',
                    );					
	}
    $atts = array_merge($atts, $same_attrs);
	$tmp = '<div id="cc_slider-top" class="hidden-phone row-fluid">';
	$tmp .= slider($atts, $content = null);
	$tmp .= '</div>';
	if($cap->slideshow_shadow != "no shadow" && $cap->slideshow_shadow != __("no shadow",'cc')){
		$tmp .= '<div class="slidershadow hidden-phone span10"><img src="'.get_template_directory_uri().'/images/slideshow/'.cc_slider_shadow().'"></img></div>';
	}
	$tmp .='<div class="clear"></div>';
	return $tmp;

}
/**
 * load the array for the list posts depending on the page settings or theme settings
 *
 * @package Custom Community
 * @since 1.8.3
 */	
function cc_list_posts_on_page(){
	$cc_page_options = cc_get_page_meta(); 
    if(isset($cc_page_options) && $cc_page_options['cc_page_template_on'] == 1){
    $atts = array(
                'amount'        => $cc_page_options['cc_page_template_amount'],
                'category__in' => $cc_page_options['cc_page_template_cat']
            );
    
    switch ($cc_page_options['cc_posts_on_page_type']){
        case 'img-mouse-over':
	    	$atts['img_position'] = 'mouse_over'; 
	        break;
        case 'img-left-content-right':
            $atts['img_position'] = 'left'; 
	        break;
        case 'img-right-content-left':
            $atts['img_position'] = 'right';
	        break;
        case 'img-over-content':
             $atts['img_position'] = 'over';
	        break;
        case 'img-under-content':
            $atts['img_position'] = 'under';
	        break;
        }
        echo cc_list_posts($atts,$content = null); 
	}
}

/**
 * Display shortcode and other page specific js in the footer only if required
 * 
 * @package Custom Community
 * @since 1.9
 */
add_action('wp_footer', 'cc_footer_js', 99);
function cc_footer_js(){
	global $cap, $cc_js;
        
	if(empty($cc_js))
		return;

	$js = '';

	if(!empty($cc_js) && count($cc_js) > 0){
		$js .= '<script type="text/javascript">';

		// Slideshow or slider
		if(isset($cc_js['slideshow'])){
			foreach ($cc_js['slideshow'] as $key => $params) {
				$js .= 'jQuery("#featured'.$params['id'].'").tabs({fx:{opacity: "toggle"}}).tabs("rotate", '.$params['time_in_ms'].', true);
						jQuery("#featured'.$params['id'].'").hover(
							function(){jQuery("#featured'.$params['id'].'").tabs("rotate",0,true);},
							function(){jQuery("#featured'.$params['id'].'").tabs("rotate",'.$params['time_in_ms'].',true);
						});';
			}
		}

		// Image effects (reflects)
		if(isset($cc_js['img_effect'])){
			foreach ($cc_js['img_effect'] as $key => $params) {
				$js .= 'jQuery("#img_effect'.$params['id'].'").reflect({height:'.$params['rheight'].',opacity:'.$params['ropacity'].'});';
			}
		}

		// Accordion
		if(isset($cc_js['accordion'])){
			foreach ($cc_js['accordion'] as $key => $params) {
				$js .= 'jQuery("#accordion'.$params['id'].' div.swap'.$params['id'].'").hide();
						jQuery("#accordion'.$params['id'].' h3").click(function(){
							jQuery(this).nextUntil("h3", "div.swap'.$params['id'].'").slideToggle("slow").siblings("div.swap'.$params['id'].':visible").slideUp("slow");
							jQuery(this).toggleClass("active");
							jQuery(this).siblings("h3").removeClass("active");
						});';
			}
		}

		// List posts
		if(isset($cc_js['list_posts'])){
			if ($cc_js['list_posts'] === true){
				$js .= 'jQuery(".boxgrid.captionfull").hover(function(){
							jQuery(".cover", this).stop().animate({top:"-90px"},{queue:false,duration:160});
						}, function(){
							jQuery(".cover", this).stop().animate({top:"0px"},{queue:false,duration:160});
						});';
			}
		}

		$js .= '</script>';
	}

	echo $js;
}

/**
 * Get style from option
 */
function cc_get_magazine_style($magazine_style = FALSE){
    if($magazine_style){
        $args = '';
        switch ($magazine_style){
            case __('img-right-content-left', 'cc'):
                $args = 'right';                            
            break;
            case __('img-left-content-right', 'cc'):
                $args = 'left';                            
            break;
            case __('img-over-content', 'cc'):
                $args = 'over';                            
            break;
            case __('img-under-content', 'cc'):
                $args = 'under';                            
            break;
            case __('img-mouse-over', 'cc'):
                $args = 'mouse_over';                            
            break;
        }
    }
    return $args;
}