<?php

/*--Theme-Junkie Shortcodes
------------------------------------------------------------------------*/


/*--Column Shortcodes
-------------------------------------------------------------------------------*/

function tj_one_third( $atts, $content = null ) {
   return '<div class="one_third">' . do_shortcode($content) . '</div>';
}

add_shortcode('one_third', 'tj_one_third');

function tj_one_third_last( $atts, $content = null ) {
   return '<div class="one_third column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('one_third_last', 'tj_one_third_last');

function tj_two_third( $atts, $content = null ) {
   return '<div class="two_third">' . do_shortcode($content) . '</div>';
}

add_shortcode('two_third', 'tj_two_third');

function tj_two_third_last( $atts, $content = null ) {
   return '<div class="two_third column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('two_third_last', 'tj_two_third_last');

function tj_one_half( $atts, $content = null ) {
   return '<div class="one_half">' . do_shortcode($content) . '</div>';
}

add_shortcode('one_half', 'tj_one_half');

function tj_one_half_last( $atts, $content = null ) {
   return '<div class="one_half column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('one_half_last', 'tj_one_half_last');

function tj_one_fourth( $atts, $content = null ) {
   return '<div class="one_fourth">' . do_shortcode($content) . '</div>';
}

add_shortcode('one_fourth', 'tj_one_fourth');

function tj_one_fourth_last( $atts, $content = null ) {
   return '<div class="one_fourth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('one_fourth_last', 'tj_one_fourth_last');

function tj_three_fourth( $atts, $content = null ) {
   return '<div class="three_fourth">' . do_shortcode($content) . '</div>';
}

add_shortcode('three_fourth', 'tj_three_fourth');

function tj_three_fourth_last( $atts, $content = null ) {
   return '<div class="three_fourth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('three_fourth_last', 'tj_three_fourth_last');

function tj_one_fifth( $atts, $content = null ) {
   return '<div class="one_fifth">' . do_shortcode($content) . '</div>';
}

add_shortcode('one_fifth', 'tj_one_fifth');

function tj_one_fifth_last( $atts, $content = null ) {
   return '<div class="one_fifth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('one_fifth_last', 'tj_one_fifth_last');

function tj_two_fifth( $atts, $content = null ) {
   return '<div class="two_fifth">' . do_shortcode($content) . '</div>';
}

add_shortcode('two_fifth', 'tj_two_fifth');

function tj_two_fifth_last( $atts, $content = null ) {
   return '<div class="two_fifth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}
add_shortcode('two_fifth_last', 'tj_two_fifth_last');

function tj_three_fifth( $atts, $content = null ) {
   return '<div class="three_fifth">' . do_shortcode($content) . '</div>';
}

add_shortcode('three_fifth', 'tj_three_fifth');

function tj_three_fifth_last( $atts, $content = null ) {
   return '<div class="three_fifth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('three_fifth_last', 'tj_three_fifth_last');

function tj_four_fifth( $atts, $content = null ) {
   return '<div class="four_fifth">' . do_shortcode($content) . '</div>';
}

add_shortcode('four_fifth', 'tj_four_fifth');

function tj_four_fifth_last( $atts, $content = null ) {
   return '<div class="four_fifth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('four_fifth_last', 'tj_four_fifth_last');

function tj_one_sixth( $atts, $content = null ) {
   return '<div class="one_sixth">' . do_shortcode($content) . '</div>';
}

add_shortcode('one_sixth', 'tj_one_sixth');

function tj_one_sixth_last( $atts, $content = null ) {
   return '<div class="one_sixth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('one_sixth_last', 'tj_one_sixth_last');

function tj_five_sixth( $atts, $content = null ) {
   return '<div class="five_sixth">' . do_shortcode($content) . '</div>';
}

add_shortcode('five_sixth', 'tj_five_sixth');

function tj_five_sixth_last( $atts, $content = null ) {
   return '<div class="five_sixth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('five_sixth_last', 'tj_five_sixth_last');


/*-----------------------------------------------------------------------------------*/
/*	Buttons
/*-----------------------------------------------------------------------------------*/


function tj_button( $atts, $content = null ) {
	
	extract(shortcode_atts(array(
		'url'     	 => '#',
		'target'     => '_self',
		'style'   => 'white',
		'size'	=> 'small'
    ), $atts));
	
   return '<a class="button '.$size.' '.$style.'" href="'.$url.'">' . do_shortcode($content) . '</a>';
}

add_shortcode('button', 'tj_button');


/*-----------------------------------------------------------------------------------*/
/*	Alerts
/*-----------------------------------------------------------------------------------*/


function tj_alert( $atts, $content = null ) {
	
	extract(shortcode_atts(array(
		'style'   => 'white'
    ), $atts));
	
   return '<div class="alert '.$style.'">' . do_shortcode($content) . '</div>';
}

add_shortcode('alert', 'tj_alert');


/*-----------------------------------------------------------------------------------*/
/*	Toggle Shortcodes
/*-----------------------------------------------------------------------------------*/

function tj_toggle( $atts, $content = null ) {
	
    extract(shortcode_atts(array(
		'title'    	 => 'Title goes here',
		'state'		 => 'open'
    ), $atts));

	
	$out .= "<div data-id='".$state."' class=\"toggle\"><h4>".$title."</h4><div class=\"toggle-inner\">".do_shortcode($content)."</div></div>";
	
    return $out;
	
}

add_shortcode('toggle', 'tj_toggle');


/*-----------------------------------------------------------------------------------*/
/*	Tabs Shortcodes
/*-----------------------------------------------------------------------------------*/

function tj_tabs( $atts, $content = null ) {
	
	extract(shortcode_atts(array(), $atts));
	
	global $tab_counter_1;
	global $tab_counter_2;
	
	$tab_counter_1++;
	$tab_counter_2++;
	
	$out .= '<div class="tabs"><div class="tab-inner">';
	
	$out .= '<ul class="tab-nav clear">';
	
	$count = 1;
	
	foreach ($atts as $tab) {
		if($count == 1){$first = 'first';}else{$first = '';}
		$out .= '<li class="'.$first.'"><a title="' .$tab. '" href="#tab-' . $tab_counter_1 . '">' .$tab. '</a></li>';
		$tab_counter_1++;
		$count++;
	}
	$out .= '</ul>';

	$out .= do_shortcode($content) .'</div></div>';
	
	return $out;
	
}

add_shortcode('tabs', 'tj_tabs');


/*-----------------------------------------------------------------------------------*/
/*	Tab Panes Shortcodes
/*-----------------------------------------------------------------------------------*/

function tabpanes( $atts, $content = null ) {
	
	global $tab_counter_2;
	
	$out .= '<div class="tab" id="tab-' . $tab_counter_2 . '">' . do_shortcode($content) .'</div>';
	
	$tab_counter_2++;
	
	return $out;
}

add_shortcode('tab', 'tabpanes');

function tj_has_tabs($posts) {
	
    if ( empty($posts) )
        return $posts;

    $found = false;

    foreach ($posts as $post) {
		
        if ( stripos($post->post_content, '[tabs') )
            $found = true;
            break;
			
		if ( stripos($post->post_content, '[toggle') )
            $found = true;
            break;
    }

    if ($found) {
		
		wp_enqueue_script('jquery-ui-custom');
	}
	
    return $posts;
	
}

add_action('the_posts', 'tj_has_tabs');



?>