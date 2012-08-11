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

class Buddyvents_PDF_Invoices extends FPDF
{
	/**
	 * PHP5 Constructor
	 * 
	 * @package	Tickets
	 * @since 	2.0
	 * 
	 * @param	string	$orientation
	 * @param	string	$unit
	 * @param	string	$format
	 */
	function __construct( $orientation = 'P', $unit = 'mm', $format = 'A4' )
	{
		$this->FPDF( $orientation, $unit, $format );
		$this->SetAutoPageBreak( true, 40 );
	}

	/**
	 * Display the PDF header
	 * 
	 * @package	Tickets
	 * @since 	2.0
	 * 
	 * @global 	object	$bp		BuddyPress settings
	 * @global 	object	$bpe	Buddyvents settings
	 * 
	 * @uses	bp_core_get_user_displayname()
	 * @uses 	bpe_get_option()
	 * @uses	bp_get_root_domain()
	 * @uses	get_user_by_email()
	 * @uses	bp_get_user_meta()
	 */
	function Header()
	{
		global $bpe, $bp;

		$this->SetDrawColor( 150 );

		if( bpe_get_option( 'invoice_logo', 'url' ) )
			$this->Image( bp_get_root_domain() . bpe_get_option( 'invoice_logo', 'url' ), 20, 3 );

		$this->SetFont( 'Helvetica', '', 10 );
		$this->SetXY( 130, 35 ); 
		$this->Cell( 25, 5, __( 'Date: ', 'events' ), 0, 0, 'L' );
		$this->Cell( 50, 5, $this->invoice_date, 0, 1, 'R' );
		$this->SetXY( 130, $this->GetY() ); 
		$this->Cell( 25, 5, __( 'Client No: ', 'events' ), 0, 0, 'L' );
		$this->Cell( 50, 5, $this->client_number, 0, 1, 'R' );
		$this->SetXY( 130, $this->GetY() ); 
		$this->Cell( 25, 5, __( 'Invoice No: ', 'events' ), 0, 0, 'L' );
		$this->Cell( 50, 5, $this->invoice_number, 0, 1, 'R' );
		
		$user = get_user_by_email( bp_get_option( 'admin_email' ) );
		$address = bp_get_user_meta( $user->ID, 'bpe_billing_address', true );
		
		if( ! empty( $address ) )
		{
			$this->SetFont( 'Helvetica', '', 7 );

			if( $address['company'] ) 
				$adr[] = $address['company'];
			else
				$adr[] = bp_core_get_user_displayname( $uid );
				
			if( $address['street'] )
				$adr[] = utf8_decode( $address['street'] );
			if( $address['postcode'] )
				$adr[] = utf8_decode( $address['postcode'] );
			if( $address['city'] )
				$adr[] = utf8_decode( $address['city'] );
			if ( $address['country'] ) 
				$adr[] = utf8_decode( $address['country'] );
				
			$addr_header = join( ' - ', (array)$adr );
			
			$width = $this->GetStringWidth( $addr_header );
			
			$this->Cell( 0, 5, $addr_header, 0, 1 );
			$x = $this->GetX() + 1;
			$y = $this->GetY();
			$this->Line( $x, $y, $x + $width, $y );
			$this->Cell( 0, 2, '', 0, 1 );
		}
	
		$this->SetFont( 'Helvetica', '', 11 );

		if( $this->client_company ) 
			$this->Cell( 0, 5, utf8_decode( $this->client_company ), 0, 1 );
		else
			$this->Cell( 0, 5, utf8_decode( $this->client_name ), 0, 1 );
			
		if( $this->client_street )
			$this->Cell( 0, 5, utf8_decode( $this->client_street ), 0, 1 );
		if( $this->client_postcode )
			$this->Cell( 0, 5, utf8_decode( $this->client_postcode ), 0, 1 );
		if( $this->client_city )
			$this->Cell( 0, 5, utf8_decode( $this->client_city ), 0, 1 );
		if ( $this->client_country ) 
			$this->Cell( 0, 5, utf8_decode( $this->client_country ), 0, 1 );

		$this->Ln( 20 );
		$this->SetFont( 'Helvetica', 'B', 14 );
		$this->Cell( 0, 5, __( 'Your Invoice', 'events' ), 0, 1 );

		$this->Ln( 5 );
		$this->SetFontSize( 10 );
		$this->Cell( 10, 7, __( 'Pos', 'events' ), 0, 0, 'L' );
		$this->Cell( 80, 7, __( 'Description', 'events' ), 0, 0, 'L' );
		$this->Cell( 30, 7, __( 'Qty', 'events' ), 0, 0, 'C' );
		$this->Cell( 30, 7, __( 'Single Price', 'events' ), 0, 0, 'C' );
		$this->Cell( 30, 7, __( 'Commission', 'events' ), 0, 1, 'R' );
	}

	/**
	 * Set a product row
	 * 
	 * @package	Tickets
	 * @since 	2.0
	 * 
	 * @param	int		$pos
	 * @param	string	$description
	 * @param	float	$single_price
	 * @param	int		$quantity
	 * @param	float	$commission
	 */
	function SetProductLine( $pos, $description, $single_price, $quantity, $commission )
	{
		$this->SetDrawColor( 150 );
		
		$x = $this->GetX();
		$y = $this->GetY();
		
		$this->Cell( 10, 10, $pos, 0, 0, 'L' );
		$this->MultiCell( 80, 5, $this->format_text( $description ), 0, 'L' );
		$this->SetXY( $x + 90, $y );
		$this->Cell( 30, 10, $quantity, 0, 0, 'C' );
		$this->Cell( 30, 10, $this->currency .' '. number_format( $single_price, 2 ), 0, 0, 'C' );
		$this->Cell( 30, 10, $this->currency .' '. number_format( $commission, 2 ), 0, 1, 'R' );
	}
	
	/**
	 * Set the totals
	 * 
	 * @package	Tickets
	 * @since 	2.0
	 * 
	 * @param	float	$subtotal
	 * 
	 * @uses	bpe_get_option()
	 */
	function SetEnd( $subtotal )
	{
		$this->SetLineWidth( 0.2 );
		$this->SetDrawColor( 150 );

		$this->SetX( 120 ); 
		
		if( ! bpe_get_option( 'invoice_tax' ) ) :
			$this->SetFont( 'Helvetica', 'B', 10 );
			$this->Cell( 50, 9, __( 'Total (gross)', 'events' ), 'T', 0, 'L' );
			$this->Cell( 30, 9, $this->currency .' '. number_format( (float)$subtotal, 2 ), 'T', 1, 'R' );
		else :
			$this->Cell( 50, 9, __( 'Sub-Total (net)', 'events' ), 'T', 0, 'L' );
			$this->Cell( 30, 9, $this->currency .' '. number_format( (float)$subtotal, 2 ), 'T', 1, 'R' );
	
			$this->SetX( 120 ); 
			$this->Cell( 50, 9, sprintf( __( 'VAT %s', 'events' ), bpe_get_option( 'invoice_tax' ) .'%' ), 0, 0, 'L' );
			
			$vat_amount = $subtotal * ( bpe_get_option( 'invoice_tax' ) / 100 );
			$this->Cell( 30, 9, $this->currency .' '. number_format( (float)$vat_amount, 2 ), 0, 1, 'R' );
	
			$this->SetX( 120 ); 
			$this->SetFont( 'Helvetica', 'B', 10 );
			$this->Cell( 50, 9, __( 'Total (gross)', 'events' ), 'T', 0, 'L' );
			$this->Cell( 30, 9, $this->currency .' '. number_format( ( (float)$vat_amount + (float)$subtotal ), 2 ), 'T', 1, 'R' );
		endif;
		
		$this->Ln( 30 );
		$this->MultiCell( 0, 0, str_replace( '{SETTLE_DATE}', $this->settle_date(), utf8_decode( bpe_get_option( 'invoice_message' ) ) ) );
	}
	
	/**
	 * Calculate the settle date
	 * 
	 * @package	Tickets
	 * @since 	2.0
	 * 
	 * @uses	bpe_get_option()
	 */
	function settle_date()
	{
		return date( bpe_get_option( 'date_format' ), strtotime( '+ '. bpe_get_option( 'invoice_settle_date' ) .'days' ) );
	}

	/**
	 * Display the PDF footer
	 * 
	 * @package	Tickets
	 * @since 	2.0
	 * 
	 * @uses	bpe_get_option()
	 */
	function Footer()
	{
		$this->SetFont( 'Helvetica', '',8 );
		$this->SetTextColor( 150 );
		$this->SetLineWidth( 0.2 );
		$this->SetDrawColor( 150 );

		$this->SetXY( 20, -40 );

		$x = $this->GetX();
		$y = $this->GetY(); 

		$this->SetXY( $x, $y );
		$this->MultiCell( 45, 5, $this->format_text( bpe_get_option( 'invoice_footer1' ) ), 0, 'L' );
		$this->SetXY( $x + 45, $y );
		$this->MultiCell( 45, 5, $this->format_text( bpe_get_option( 'invoice_footer2' ) ), 0, 'L' );
		$this->SetXY( $x + 90, $y );
		$this->MultiCell( 45, 5, $this->format_text( bpe_get_option( 'invoice_footer3' ) ), 0, 'L' );
		$this->SetXY( $x + 135, $y );
		$this->MultiCell( 45, 5, $this->format_text( bpe_get_option( 'invoice_footer4' ) ), 0, 'R' );
		
		$this->SetFont( 'Helvetica', 'I',8 );
		$this->Cell( 0,10, sprintf( __( 'Page %s/{nb}', 'events' ), $this->PageNo() ), 0, 0, 'C' );
	}
	
	/**
	 * Format text for use in a PDF
	 * 
	 * @package	Tickets
	 * @since 	2.0
	 * 
	 * @param	string	$text
	 * @return 	string	$text
	 */
	function format_text( $text )
	{
		$text = stripslashes( $text );
		$text = str_replace( '<br />', "\n", $text );
		$text = str_replace( "\t", '', $text );
		
		return utf8_decode( trim( $text ) ); 
	}

}
?>