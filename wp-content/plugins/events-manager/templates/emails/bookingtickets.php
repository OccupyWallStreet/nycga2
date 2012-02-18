<?php foreach($EM_Booking->get_tickets_bookings() as $EM_Ticket_Booking): ?>
<?php
/* @var $EM_Ticket_Booking EM_Ticket_Booking */
echo $EM_Ticket_Booking->get_ticket()->name; 
?>

Quantity: <?php echo $EM_Ticket_Booking->get_spaces(); ?>

Price: <?php echo em_get_currency_symbol(!get_option('dbem_smtp_html'))." ". number_format($EM_Ticket_Booking->get_price(),2); ?>


<?php endforeach; ?>