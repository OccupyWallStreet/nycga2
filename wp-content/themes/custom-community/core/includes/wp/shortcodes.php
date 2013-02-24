<?php 
// shortcode horizontal line
function h_line($atts) { 

    global $cap;
    
    switch ($cap->style_css)
        {
        case __('dark','cc') :
            $color = '333333';
            break;
        case __('natural','cc') :
            $color = 'f5e5b3';
            break;
        case __('white','cc') :
            $color = 'dddddd';
            break;
        case __('light','cc') :
            $color = 'ededed';
            break;
        case __('grey','cc') :
            $color = 'f1f1f1';
            break;
        case __('black','cc') :
            $color = '333333';
            break;
        default:   
            $color = 'f1f1f1';
            break;
        }

    extract(shortcode_atts(array(
        'color' => $color,
        'css'   => ''
    ), $atts));

    $tmp = '<div style="'.$css.'width:100%; border-top:1px solid #'.$color.'; margin:0; padding:0; height:1px;"></div>';
    return $tmp;
}
add_shortcode('cc_h_line', 'h_line');

// shortcode facebook like button
function facebook_like() { 
    $pageURL = 'http';

    if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    
    $pageURL .= "://";
    
    if (!empty($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
 
    $tmp = '<iframe src="http://www.facebook.com/plugins/like.php?href='.$pageURL.'&layout=standard&show_faces=true&width=450&action=like&colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" class="facebook_like"></iframe>';
    
    return $tmp;
}
add_shortcode('cc_facebook_like', 'facebook_like');


function get_dynamic_sidebar($index = 1) {
    $sidebar_contents = "";
    ob_start();
    dynamic_sidebar($index);
    $sidebar_contents = ob_get_contents();
    ob_end_clean();
    return $sidebar_contents;
}

// blockquote_left = add a quotation, left floated
function blockquote_left($atts, $content = null) { 
    return '<span class="cc_blockquote cc_blockquote_left">"'.$content.'"</span>';
}
add_shortcode('cc_blockquote_left', 'blockquote_left');

// blockquote_right = add a quotation, right floated
function blockquote_right($atts, $content = null) { 
    return '<span class="cc_blockquote cc_blockquote_right">"'.$content.'"</span>';
}
add_shortcode('cc_blockquote_right', 'blockquote_right');

// button = add a button with custom text and link
function button($atts,$content = null) { 
    extract(shortcode_atts(array(
        'link'   => '',
        'target' => ''
    ), $atts)); 
    return '<a href="'.$link.'" target="'.$target.'" class="button">'.$content.'</a>';
}
add_shortcode('cc_button', 'button');

// break = horizontal line / enter
function horline($atts, $content = null) { 
    return '<br />';
}
add_shortcode('cc_break', 'horline');

// clear = reset all css from the elements before
function clear($atts, $content = null) { 
    return '<div class="clear"></div>';
}
add_shortcode('cc_clear', 'clear');

// col_end = end of a column shortcode for advanced use (hierarchical mode)
function col_end(){
    return '</div>';
}
add_shortcode('cc_col_end', 'col_end');

// full_width_col = full width column
function full_width_col($atts,$content = null) { 
    extract(shortcode_atts(array(
        'background_color' => 'none',
        'border_color'     => 'transparent', 
        'radius'           => '0', 
        'shadow_color'     => 'transparent',
        'height'           => 'auto', 
        'background_image' => 'none',
        'hierarchical'     => 'off', 
    ), $atts)); 
    
    if($height != 'auto'){ $height = $height.'px'; }
    if($background_color != 'none'){ $background_color = '#'.$background_color; }
    if($border_color != 'transparent'){ $border_color = '#'.$border_color; }
    if($shadow_color != 'transparent'){ $shadow_color = '#'.$shadow_color; }
    
    $add=''; 
    if($background_color !='none' || $border_color !='transparent' || $shadow_color !='transparent' || $background_image !='none') { $add='padding:2%; width:95.6%;'; }
    $add_bg='';
    if($background_image !='none') { $add_bg='background-image:url('.$background_image.');'; }
    $tmp = '<div class="full_width_col"
                style="background-color:'.$background_color.'; 
                    border: 1px solid; border-color:'.$border_color.';
                    -moz-border-radius:'.$radius.'px; 
                    -webkit-border-radius:'.$radius.'px; 
                    border-radius:'.$radius.'px;
                    -moz-box-shadow: 2px 2px 2px '.$shadow_color.';
                    -webkit-box-shadow: 2px 2px 2px '.$shadow_color.';
                    box-shadow: 2px 2px 2px '.$shadow_color.';
                    '.$add_bg.'height:'.$height.';'.$add.'">';
    if($hierarchical == 'off'){
        $tmp .= $content;
        $tmp .= '</div><div class="clear"></div>';
    }
    return $tmp;
}
add_shortcode('cc_full_width_col', 'full_width_col');

// half_col_left = half column, left floated
function half_col_left($atts,$content = null) { 
    extract(shortcode_atts(array(
        'background_color' => 'none',
        'border_color'     => 'transparent', 
        'radius'           => '0', 
        'shadow_color'     => 'transparent', 
        'height'           => 'auto',
        'background_image' => 'none',
        'hierarchical'     => 'off',
    ), $atts)); 
    
    if($height != 'auto'){ $height = $height.'px'; }
    if($background_color != 'none'){ $background_color = '#'.$background_color; }
    if($border_color != 'transparent'){ $border_color = '#'.$border_color; }
    if($shadow_color != 'transparent'){ $shadow_color = '#'.$shadow_color; }
    
    $add=''; 
    if($background_color !='none' || $border_color !='transparent' || $shadow_color !='transparent' || $background_image !='none') { $add='padding:2%; width:44%;'; }
    $add_bg='';
    if($background_image !='none') { $add_bg='background-image:url('.$background_image.');'; }
    $tmp = '<div class="half_col_left" 
                style="background:'.$background_color.'; 
                        border: 1px solid; border-color:'.$border_color.';
                        -moz-border-radius:'.$radius.'px; 
                        -webkit-border-radius:'.$radius.'px; 
                        border-radius:'.$radius.'px;
                        -moz-box-shadow: 2px 2px 2px '.$shadow_color.';
                        -webkit-box-shadow: 2px 2px 2px '.$shadow_color.';
                        box-shadow: 2px 2px 2px '.$shadow_color.';'.$add_bg.'
                        height:'.$height.';'.$add.'">';
    if($hierarchical == 'off'){
        $tmp .= $content;
        $tmp .= '</div>';
    }
    return $tmp;
}
add_shortcode('cc_half_col_left', 'half_col_left');

// half_col_right = half column, right floated
function half_col_right($atts,$content = null) { 
    extract(shortcode_atts(array(
        'background_color' => 'none',
        'border_color'     => 'transparent', 
        'radius'           => '0', 
        'shadow_color'     => 'transparent', 
        'height'           => 'auto',
        'background_image' => 'none',
        'hierarchical'     => 'off',
    ), $atts)); 
    
    if($height != 'auto'){ $height = $height.'px'; }
    if($background_color != 'none'){ $background_color = '#'.$background_color; }
    if($border_color != 'transparent'){ $border_color = '#'.$border_color; }
    if($shadow_color != 'transparent'){ $shadow_color = '#'.$shadow_color; }
    
    $add=''; 
    if($background_color !='none' || $border_color !='transparent' || $shadow_color !='transparent' || $background_image !='none') { $add='padding:2%; width:44%;'; }
    $add_bg='';
    if($background_image !='none') { $add_bg='background-image:url('.$background_image.');'; }
    $tmp = '<div class="half_col_right"
                style="background:'.$background_color.'; 
                        border: 1px solid; border-color:'.$border_color.';
                        -moz-border-radius:'.$radius.'px; 
                        -webkit-border-radius:'.$radius.'px; 
                        border-radius:'.$radius.'px;
                        -moz-box-shadow: 2px 2px 2px '.$shadow_color.';
                        -webkit-box-shadow: 2px 2px 2px '.$shadow_color.';
                        box-shadow: 2px 2px 2px '.$shadow_color.';'.$add_bg.'
                        height:'.$height.';'.$add.'">';
    if($hierarchical == 'off'){
        $tmp .= $content;
        $tmp .= '</div><div class="clear"></div>';
    }
    return $tmp;
    
}
add_shortcode('cc_half_col_right', 'half_col_right');

// third_col = one third column, left floated
function third_col($atts,$content = null) { 
    extract(shortcode_atts(array(
        'background_color' => 'none',
        'border_color'     => 'transparent', 
        'radius'           => '0', 
        'shadow_color'     => 'transparent', 
        'height'           => 'auto',
        'background_image' => 'none',
        'hierarchical'     => 'off',
    ), $atts)); 
    
    if($height != 'auto'){ $height = $height.'px'; }
    if($background_color != 'none'){ $background_color = '#'.$background_color; }
    if($border_color != 'transparent'){ $border_color = '#'.$border_color; }
    if($shadow_color != 'transparent'){ $shadow_color = '#'.$shadow_color; }
    
    $add=''; 
    if($background_color !='none' || $border_color !='transparent' || $shadow_color !='transparent' || $background_image !='none') { $add='padding:2%; width:27%;'; } 
    $addborder='';
    if($border_color !='transparent') { $addborder ='border:1px solid '.$border_color.'; margin-right:2.7%;'; }
    $add_bg='';
    if($background_image !='none') { $add_bg='background-image:url('.$background_image.');'; }
    $tmp = '<div class="third_col" 
                style="background:'.$background_color.';'.$addborder.' 
                        -moz-border-radius:'.$radius.'px; 
                        -webkit-border-radius:'.$radius.'px; 
                        border-radius:'.$radius.'px;
                        -moz-box-shadow: 2px 2px 2px '.$shadow_color.';
                        -webkit-box-shadow: 2px 2px 2px '.$shadow_color.';
                        box-shadow: 2px 2px 2px '.$shadow_color.';'.$add_bg.'
                        height:'.$height.';'.$add.'">';
    if($hierarchical == 'off'){
        $tmp .= $content;
        $tmp .= '</div>';
    }
    return $tmp;
}
add_shortcode('cc_third_col', 'third_col');

// third_col_right = one third column, right floated
function third_col_right($atts,$content = null) { 
    extract(shortcode_atts(array(
        'background_color' => 'none',
        'border_color'     => 'transparent', 
        'radius'           => '0', 
        'shadow_color'     => 'transparent', 
        'height'           => 'auto',
        'background_image' => 'none',
        'hierarchical'     => 'off',
    ), $atts)); 
    
    if($height != 'auto'){ $height = $height.'px'; }
    if($background_color != 'none'){ $background_color = '#'.$background_color; }
    if($border_color != 'transparent'){ $border_color = '#'.$border_color; }
    if($shadow_color != 'transparent'){ $shadow_color = '#'.$shadow_color; }
    
    $add=''; 
    if($background_color !='none' || $border_color !='transparent' || $shadow_color !='transparent' || $background_image !='none') { $add='padding:2%; width:27%;'; }
    $addborder='';
    if($border_color !='transparent') { $addborder ='border:1px solid '.$border_color.';'; }
    $add_bg='';
    if($background_image !='none') { $add_bg='background-image:url('.$background_image.');'; }
    $tmp = '<div class="third_col_right" 
                style="background:'.$background_color.';'.$addborder.' 
                        -moz-border-radius:'.$radius.'px; 
                        -webkit-border-radius:'.$radius.'px; 
                        border-radius:'.$radius.'px;
                        -moz-box-shadow: 2px 2px 2px '.$shadow_color.';
                        -webkit-box-shadow: 2px 2px 2px '.$shadow_color.';
                        box-shadow: 2px 2px 2px '.$shadow_color.';'.$add_bg.'
                        height:'.$height.';'.$add.'">';
    if($hierarchical == 'off'){
        $tmp .= $content;
        $tmp .= '</div><div class="clear"></div>';
    }
    return $tmp;
}
add_shortcode('cc_third_col_right', 'third_col_right');

// list posts
function cc_list_posts($atts,$content = null) {
    global $cap, $cc_page_options, $post, $cc_js;
    $tmp = '';
    
    extract(shortcode_atts(array(
        'amount'        => '12',
        'category__in'  => array(),
        'category_name' => '0',
        'img_position'  => 'mouse_over',
        'height'        => 'auto',
        'page_id'       => '',
        'post_type'     => 'post',
        'orderby'       => '',
        'order'         => '',
        'year'          => '',
        'monthnum'      => ''
    ), $atts));

    switch ($img_position){
        case 'left':
            $img_position = 'posts-img-left-content-right';
            break;
        case 'right':
            $img_position = 'posts-img-right-content-left';
            break;
        case 'over':
            $img_position = 'posts-img-over-content';
            break;
        case 'under':
            $img_position = 'posts-img-under-content';
            break;
        case 'mouse_over':
            $img_position = 'boxgrid';
            break;
        }
        
   if (empty($category__in)) {
        $category__in = array();
        $categories = get_categories();
        foreach ($categories as $category) {
            $category__in[] = $category->term_id;
        }
    } else if (!is_array($category__in)) {
        $category__in = explode(',', $category__in);
    }

    if($page_id != ''){
        $page_id = explode(',',$page_id);
    }
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $args = array(
        'orderby'        => $orderby,
        'order'          => $order,
        'post_type'      => $post_type,
        'post__in'       => $page_id,
        'year'           => $year,
        'monthnum'       => $monthnum,
        'category__in'   => $category__in,
        'category_name'   => $category_name,
        'posts_per_page' => $amount,
        'paged'          => $paged
    );

    remove_all_filters('posts_orderby');
    query_posts($args);

    if (have_posts()) {
        $thePath = array();
        $pattern = "/(?<=src=['|\"])[^'|\"]*?(?=['|\"])/i";
        while (have_posts()) : the_post();
            if($img_position == 'boxgrid'){
                $thumb   = get_the_post_thumbnail( $post->ID, 'post-thumbnail' );
                preg_match($pattern, $thumb, $thePath);
                if(!isset($thePath[0])){
                    $thePath[0] = get_template_directory_uri().'/images/slideshow/noftrdimg-222x160.jpg';
                }
                $tmp .= '<div class="boxgrid captionfull" style="background: transparent url('.$thePath[0].') repeat scroll 0 0; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; " title="'. get_the_title().'">';
                $tmp .= '<a href="'. get_permalink().'" title="'. get_the_title().'"><img src="'.$thePath[0].'" /></a>';
                $tmp .= '<div class="cover boxcaption">';
                $tmp .= '<h3><a href="'. get_permalink().'" title="'. get_the_title().'">'. get_the_title().'</a></h3>';
                $tmp .= '<p class="hidden-phone"><a href="'. get_permalink().'" title="'. get_the_title().'">'.substr(get_the_excerpt(), 0, 100).'...</a></p>';
                $tmp .= '</div>';
                $tmp .= '</div>'; 
            } else {
                $tmp .= '<div class="listposts '.$img_position.'">';
                if($img_position != 'posts-img-under-content') $tmp .= '<a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_post_thumbnail().'</a>';
                $tmp .= '<h3><a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a></h3>';
                if($height != 'auto'){ $height = str_replace('px','',$height).'px'; }
                $tmp .= '<p style="height:'.$height.';">'. get_the_excerpt().'</p>';
                if($img_position == 'posts-img-under-content') $tmp .= '<a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_post_thumbnail().'</a>';
                $tmp .= '</div>';
                if($img_position == 'posts-img-left-content-right' || $img_position == 'posts-img-right-content-left') $tmp .= '<div class="clear"></div>';    
            }
        endwhile;
    }
    
    $tmp .='<div class="clear"></div>';
    
    if($img_position == 'boxgrid'){
        $cc_js['list_posts'] = true;
    }

    wp_reset_query();
    
    return '<div class="list-posts-all phone-hidden">'.$tmp.'</div>&nbsp;';
}
add_shortcode('cc_list_posts', 'cc_list_posts');


// nothing
// empty = just to display shortcodes without execution - needed for demos.
function nothing($atts,$content = null) {  
    return $content;
}
add_shortcode('cc_empty', 'nothing');
