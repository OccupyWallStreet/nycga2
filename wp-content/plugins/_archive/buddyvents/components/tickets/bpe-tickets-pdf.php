<?php 
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

require( EVENT_ABSPATH .'components/tickets/library/fpdf/fpdf.php' );

class Buddyvents_PDF_Tickets extends FPDF
{
	/**
	 * PHP5 Constructor
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @param	object	$event
	 * @param	string	$orientation
	 * @param	string	$unit
	 * @param	array 	$format
	 * @param	int		$margin
	 */
	function __construct( $event = false, $orientation = 'L', $unit = 'pt', $format = array( '210', '595' ), $margin = 10 )
	{
		if( ! $event )
			return false;
		
		$this->FPDF( $orientation, $unit, $format );
		
		$this->event = $event;
		
		$this->SetTopMargin( $margin );
		$this->SetLeftMargin( $margin );
		$this->SetRightMargin( $margin );
		
		$this->SetAutoPageBreak( true, $margin );
	}
	
	/**
	 * Set PDF meta data
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @uses bp_core_get_user_displayname()
	 */
	function SetMetaData()
	{
		global $bpe;
		
		$this->SetAuthor( bp_core_get_user_displayname( $this->event->user_id ) );
		$this->SetCreator( sprintf( 'Buddyvents v%s', Buddyvents::VERSION ) );
		$this->SetTitle( sprintf( __( 'Ticket for %s', 'events' ), $this->event->name ) );
		$this->SetSubject( __( 'Event Ticket', 'events' ) );
	}
	
	/**
	 * Finalize a PDF
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @param	string	$output
	 * 
	 * @uses	sanitize_file_name()
	 */
	function Finalize( $output = 'D', $name )
	{
		$ticket_name = $this->event->id .'-'. time() .'-'. $name;
		
		if( $output == 'F' )
			$file = EVENT_ABSPATH .'components/tickets/pdf-cache/'. strtolower( sanitize_file_name( $ticket_name .'.pdf' ) );
		else
			$file = strtolower( sanitize_file_name( $ticket_name .'.pdf' ) );
	
		// let's show it
		$this->Output( $file, $output );
		
		return $file;
	}
	
	/**
	 * Place the QR code image
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @uses	bpe_get_event_link()
	 * @uses	wp_hash()
	 * 
	 * @param	string	$email
	 */
	function QRCode( $email )
	{
		$url = 'http://chart.googleapis.com/chart?chid='. md5( uniqid( rand(), true) ) .'&cht=qr&chs=90x90&chl='. bpe_get_event_link( $this->event ) .'check-in-out/'. wp_hash( $email ) .'/&choe=UTF-8&chld=L|2';
		$this->Image( $url, 500, 20, 90, 90, 'PNG');
	}
}