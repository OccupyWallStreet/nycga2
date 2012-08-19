<?php
global $bp, $EM_Notices;
echo $EM_Notices;
if( user_can($bp->displayed_user->id,'edit_events') ){
	?>
	<h4><?php _e('My Events', 'dbem'); ?></h4>
	<?php
	$events = EM_Events::get(array('owner'=>$bp->displayed_user->id));
	if( count($events) > 0 ){
		$args = array(
			'format_header' => get_option('dbem_bp_events_list_format_header'),
			'format' => get_option('dbem_bp_events_list_format'),
			'format_footer' => get_option('dbem_bp_events_list_format_footer'),
			'owner' => $bp->displayed_user->id
		);
		echo EM_Events::output($events, $args);
	}else{
		?>
		<p><?php _e('No Events', 'dbem'); ?>. <a href="<?php echo $bp->events->link . 'my-events/edit/'; ?>"><?php _e('Add Event','dbem'); ?></a></p>
		<?php
	}
}
?>