<?php
global $EM_Event;
//Headers
$labels = apply_filters('em_csv_bookings_headers',array(
	'Booking ID',
	'Name',
	'Email',
	'Phone',
	'Date',
	'Status',
	'Ticket Name',
	'Spaces',
	'Price',
	'Comment'
));
$file = sprintf(__('Booking details for "%s" as of %s','dbem'),$event_name, date_i18n('D d M Y h:i', current_time('timestamp'))) .  "\n";
$file = '"'. implode('","', $labels). '"' .  "\n";

//Rows
foreach( $EM_Event->get_bookings()->bookings as $EM_Booking ) {
	/* @var $EM_Booking EM_Booking */
	foreach( $EM_Booking->get_tickets_bookings() as $EM_Ticket_Booking){
		/* @var $EM_Ticket EM_Ticket */
		/* @var $EM_Ticket_Booking EM_Ticket_Booking */
		$EM_Ticket = $EM_Ticket_Booking->get_ticket();
		$row = array(
			$EM_Booking->id,
			$EM_Booking->person->get_name(),
			$EM_Booking->person->user_email,
			$EM_Booking->person->phone,
			date('Y-m-d h:i', $EM_Booking->timestamp),
			$EM_Booking->get_status(),
			$EM_Ticket->name,
			$EM_Ticket_Booking->get_spaces(),
			$EM_Ticket_Booking->get_price(),
			$EM_Booking->comment
		);
		//Display all values
		foreach($row as $value){
			$value = str_replace('"', '""', $value);
			$value = str_replace("=", "", $value);
			$file .= '"' .  preg_replace("/\n\r|\r\n|\n|\r/", ".     ", $value) . '",';
		}
		$file = apply_filters('em_csv_bookings_loop_after', $file, $EM_Ticket_Booking, $EM_Booking);
		$file .= "\n";
	}
}

// $file holds the data
echo $file;