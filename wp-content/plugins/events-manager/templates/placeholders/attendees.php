<?php
/* @var $EM_Event EM_Event */
$people = array();
foreach($EM_Event->get_bookings() as $EM_Booking){
	$people[$EM_Booking->person->ID] = $EM_Booking->person;
}
?>
<ul class="event-attendees">
	<?php foreach($people as $EM_Person): ?>
		<li><?php echo get_avatar($EM_Person->ID, 50); ?></li>
	<?php endforeach; ?>
</ul>