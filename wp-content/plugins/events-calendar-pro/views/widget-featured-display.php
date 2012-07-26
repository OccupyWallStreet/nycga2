<?php
/**
* This is the template for the output of the events list widget. 
* All the items are turned on and off through the widget admin.
* There is currently no default styling, which is highly needed.
*
* You can customize this view by putting a replacement file of the same name (events-list-load-widget-display.php) in the events/ directory of your theme.
*
* When the template is loaded, the following vars are set: $start, $end, $venue, $address, $city, $state, $province'], $zip, $country, $phone, $cost
* @return string
*/

// Vars set:
// '$event->AllDay',
// '$event->StartDate',
// '$event->EndDate',
// '$event->ShowMapLink',
// '$event->ShowMap',
// '$event->Cost',
// '$event->Phone',

// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }

$event = array();
$tribe_ecp = TribeEvents::instance();
reset($tribe_ecp->metaTags); // Move pointer to beginning of array.
foreach($tribe_ecp->metaTags as $tag){
	$var_name = str_replace('_Event','',$tag);
	$event[$var_name] = tribe_get_event_meta( $post->ID, $tag, true );
}

$event = (object) $event; //Easier to work with.

ob_start();
	post_class($alt_text,$post->ID);
$class = ob_get_contents();
ob_end_clean();
?>
<div class="event">
	<a href="<?php echo get_permalink($post->ID) ?>"><?php echo $post->post_title ?></a>
</div>
<div class="when">
	<?php 
		echo tribe_get_start_date( $post->ID, isset($start) ? $start : null ); 

		if($event->AllDay && $start)
			echo ' <small>('.__('All Day','tribe-events-calendar-pro').')</small>';
	?> 
</div>
<div class="loc">
	<?php
		if ( tribe_get_city() != '' ) {
			echo tribe_get_city() . ', ';
		}
		if (tribe_get_region() != '') {
			echo tribe_get_region() . ', '; 
		}
		if (tribe_get_country() != '') {
			echo tribe_get_country(); 
		}
	?>
</div>
<div class="event_body">
	<?php the_content('... More');?>
</div>
<?php $alt_text = ( empty( $alt_text ) ) ? 'alt' : ''; ?>
