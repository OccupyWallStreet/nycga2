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

class Buddyvents_Document_Template
{
	var $current_document = -1;
	var $document_count;
	var $documents;
	var $document;

	var $in_the_loop;

	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_document_count;

	function __construct( $event_id, $name, $type, $page, $per_page, $max, $search_terms )
	{
		$this->pag_page = isset( $_REQUEST['dpage'] ) ? intval( $_REQUEST['dpage'] 	) : $page;
		$this->pag_num  = isset( $_REQUEST['num'] 	) ? intval( $_REQUEST['num'] 	) : $per_page;

		$this->documents = bpe_get_documents( array( 'event_id' => $event_id, 'name' => $name, 'type' => $type, 'per_page' => $this->pag_num, 'page' => $this->pag_page, 'search_terms' => $search_terms ) );

		if( ! $max || $max >= (int)$this->documents['total'] )
			$this->total_document_count = (int)$this->documents['total'];
		else
			$this->total_document_count = (int)$max;

		$this->documents = $this->documents['documents'];

		if( $max )
		{
			if( $max >= count( $this->documents ) )
				$this->document_count = count( $this->documents );
			else
				$this->document_count = (int)$max;
		}
		else
			$this->document_count = count( $this->documents );
		
		$this->pag_links = paginate_links( array(
			'base' 		=> add_query_arg( array( 'dpage' => '%#%' ) ),
			'format' 	=> '',
			'total' 	=> ceil( $this->total_document_count / $this->pag_num ),
			'current' 	=> $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' 	=> 3
		));
	}

	function has_documents()
	{
		if( $this->document_count )
			return true;

		return false;
	}

	function next_document()
	{
		$this->current_document++;
		$this->document = $this->documents[$this->current_document];

		return $this->document;
	}

	function rewind_documents()
	{
		$this->current_document = -1;
		
		if ( $this->document_count > 0 )
		{
			$this->document = $this->documents[0];
		}
	}

	function documents()
	{
		if ( $this->current_document + 1 < $this->document_count )
		{
			return true;
		}
		elseif( $this->current_document + 1 == $this->document_count )
		{
			do_action( 'bpe_documents_loop_end' );
			$this->rewind_documents();
		}

		$this->in_the_loop = false;
		return false;
	}

	function the_document()
	{
		$this->in_the_loop = true;
		$this->document = $this->next_document();

		if ( 0 == $this->current_document )
			do_action( 'bpe_documents_loop_start' );
	}

}

function bpe_has_documents( $args = '' )
{
	global $document_template, $bpe;

	$search_terms = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
	
	$defaults = array(
		'event_id' 		=> bpe_get_displayed_event( 'id' ),
		'name' 			=> false,
		'type' 			=> false,
		'page' 			=> 1,
		'per_page' 		=> 20,
		'max' 			=> false,
		'search_terms' 	=> $search_terms
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	
	$document_template = new Buddyvents_Document_Template( (int)$event_id, $name, $type, (int)$page, (int)$per_page, (int)$max, $search_terms );
	return apply_filters( 'bpe_has_documents', $document_template->has_documents(), &$document_template );
}

function bpe_documents()
{
	global $document_template;

	return $document_template->documents();
}

function bpe_the_document()
{
	global $document_template;

	return $document_template->the_document();
}

function bpe_get_documents_count()
{
	global $document_template;

	return $document_template->document_count;
}

function bpe_get_total_documents_count()
{
	global $document_template;

	return $document_template->total_document_count;
}

/**
 * Pagination links
 * @since 1.7
 */
function bpe_documents_pagination_links()
{
	echo bpe_get_documents_pagination_links();
}
	function bpe_get_documents_pagination_links()
	{
		global $document_template;
	
		if( ! empty( $document_template->pag_links ) )
			return sprintf( __( 'Page: %s', 'events' ), $document_template->pag_links );
	}

/**
 * Pagination count
 * @since 1.7
 */
function bpe_documents_pagination_count()
{
	echo bpe_get_documents_pagination_count();
}
	function bpe_get_documents_pagination_count()
	{
		global $bp, $document_template;
	
		$from_num = bp_core_number_format( intval( ( $document_template->pag_page - 1 ) * $document_template->pag_num ) + 1 );
		$to_num = bp_core_number_format( ( $from_num + ( $document_template->pag_num - 1 ) > $document_template->total_document_count ) ? $document_template->total_document_count : $from_num + ( $document_template->pag_num - 1 ) );
		$total = bp_core_number_format( $document_template->total_document_count );
	
		return apply_filters( 'bpe_get_documents_pagination_count', sprintf( __( 'Viewing document %1$s to %2$s (of %3$s documents)', 'documents' ), $from_num, $to_num, $total ) );
	}

/**
 * Schedule id
 * @since 1.7
 */
function bpe_document_id( $d = false )
{
	echo bpe_get_document_id( $d );
}
	function bpe_get_document_id( $d = false )
	{
		global $document_template;

		$document = ( isset( $document_template->document ) && empty( $d ) ) ? $document_template->document : $d;

		if( ! isset( $document->id ) )
			return false;

		return apply_filters( 'bpe_get_document_id', $document->id, $document );
	}

/**
 * Schedule event_id
 * @since 1.7
 */
function bpe_document_event_id( $d = false )
{
	echo bpe_get_document_event_id( $d );
}
	function bpe_get_document_event_id( $d = false )
	{
		global $document_template;

		$document = ( isset( $document_template->document ) && empty( $d ) ) ? $document_template->document : $d;

		if( ! isset( $document->event_id ) )
			return false;

		return apply_filters( 'bpe_get_document_event_id', $document->event_id, $document );
	}

/**
 * Schedule name
 * @since 1.7
 */
function bpe_document_name( $d = false )
{
	echo bpe_get_document_name( $d );
}
	function bpe_get_document_name( $d = false )
	{
		return apply_filters( 'bpe_get_document_name', bpe_get_document_name_raw( $d ) );
	}

/**
 * Schedule name raw
 * @since 1.7
 */
function bpe_document_name_raw( $d = false )
{
	echo bpe_get_document_name_raw( $d );
}
	function bpe_get_document_name_raw( $d = false )
	{
		global $document_template;

		$document = ( isset( $document_template->document ) && empty( $d ) ) ? $document_template->document : $d;

		if( ! isset( $document->name ) )
			return false;

		return apply_filters( 'bpe_get_raw_document_name', $document->name, $document );
	}

/**
 * Schedule url
 * @since 1.7
 */
function bpe_document_url( $d = false )
{
	echo bpe_get_document_url( $d );
}
	function bpe_get_document_url( $d = false )
	{
		return apply_filters( 'bpe_get_document_url', esc_url( bp_get_root_domain() . bpe_get_document_url_raw( $d ) ), $d );
	}

/**
 * Schedule url raw
 * @since 1.7
 */
function bpe_document_url_raw( $d = false )
{
	echo bpe_get_document_url_raw( $d );
}
	function bpe_get_document_url_raw( $d = false )
	{
		global $document_template;

		$document = ( isset( $document_template->document ) && empty( $d ) ) ? $document_template->document : $d;

		if( ! isset( $document->url ) )
			return false;

		return apply_filters( 'bpe_get_raw_document_url', $document->url, $document );
	}

/**
 * Schedule description
 * @since 1.7
 */
function bpe_document_description( $d = false )
{
	echo bpe_get_document_description( $d );
}
	function bpe_get_document_description( $d = false )
	{
		return apply_filters( 'bpe_get_document_description', bpe_get_document_description_raw( $d ), $d );
	}

/**
 * Schedule description raw
 * @since 1.7
 */
function bpe_document_description_raw( $d = false )
{
	echo bpe_get_document_description_raw( $d );
}
	function bpe_get_document_description_raw( $d = false )
	{
		global $document_template;

		$document = ( isset( $document_template->document ) && empty( $d ) ) ? $document_template->document : $d;

		if( ! isset( $document->description ) )
			return false;

		return apply_filters( 'bpe_get_raw_document_description', $document->description, $document );
	}

/**
 * Schedule type
 * @since 1.7
 */
function bpe_document_type( $d = false )
{
	echo bpe_get_document_type( $d );
}
	function bpe_get_document_type( $d = false )
	{
		global $document_template;

		$document = ( isset( $document_template->document ) && empty( $d ) ) ? $document_template->document : $d;

		if( ! isset( $document->type ) )
			return false;

		return apply_filters( 'bpe_get_document_type', $document->type, $document );
	}
?>