<?php
/*================================================================================
bp-global-adminbar-css - global multisite buddypress adminbar sync with main blog
developed by Richie_KS from WPMU DEV 2010
================================================================================*/

/* original code from jonas john */
if( !function_exists('colourCreator') ) {
function colourCreator($colour, $per)
{
    $colour = substr( $colour, 1 ); // Removes first character of hex string (#)
    $rgb = ''; // Empty variable
    $per = $per/100*255; // Creates a percentage to work with. Change the middle figure to control colour temperature

    if  ($per < 0 ) // Check to see if the percentage is a negative number
    {
        // DARKER
        $per =  abs($per); // Turns Neg Number to Pos Number
        for ($x=0;$x<3;$x++)
        {
            $c = hexdec(substr($colour,(2*$x),2)) - $per;
            $c = ($c < 0) ? 0 : dechex($c);
            $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
        }
    }
    else
    {
        // LIGHTER
        for ($x=0;$x<3;$x++)
        {
            $c = hexdec(substr($colour,(2*$x),2)) + $per;
            $c = ($c > 255) ? 'ff' : dechex($c);
            $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
        }
    }
    return '#'.$rgb;
}
         }


if( !function_exists( 'buddypress_global_adminbar_css' )):
function buddypress_global_adminbar_css() {
$ms_bg = get_site_option('multisite_adminbar_bg_color');
$ms_hover_bg = get_site_option('multisite_adminbar_hover_bg_color');
?>
<?php if( $ms_bg ) { print "<style type='text/css'>"; ?>
div#wp-admin-bar, div#wpadminbar { z-index: 9999; background: <?php echo $ms_bg; ?> none !important; }
div#wpadminbar .quicklinks > ul > li { border-right: 1px solid <?php echo colourCreator($ms_bg,-20); ?> !important; }
#wpadminbar .quicklinks > ul > li > a, #wpadminbar .quicklinks > ul > li > .ab-empty-item, #wpadminbar .quicklinks .ab-top-secondary > li a {
   border-right: 0px none !important;
   border-left: 0px none !important;
}
#wpadminbar .ab-top-secondary {
  background: <?php echo colourCreator($ms_bg,-10); ?> none !important;
}
#wpadminbar .quicklinks .ab-top-secondary > li {
  border-left: 1px solid <?php echo colourCreator($ms_bg,20); ?> !important;
  }

div#wp-admin-bar ul.main-nav li:hover, div#wp-admin-bar ul.main-nav li.sfhover, div#wp-admin-bar ul.main-nav li ul li.sfhover {
background: <?php echo $ms_hover_bg; ?> none !important; }
#wp-admin-bar .padder { background: transparent none !important; }
<?php print "</style>"; ?>
<?php } }
add_action('wp_enqueue_scripts', 'buddypress_global_adminbar_css'); // init global wp_head
add_action('admin_enqueue_scripts', 'buddypress_global_adminbar_css'); // init global admin_head
endif;
?>