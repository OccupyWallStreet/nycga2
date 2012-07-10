<?php
/**
 *	Handle Images. Awesomely.
 *	
 *
 * 	Inspired from the tutorial by Jarrod Oberto. Thanks Jarrod.
 * 	http://www.jarrodoberto.com/articles/2011/09/image-resizing-made-easy-with-php
 * 
 * @since $Id$
 * @package wp-ui
 * @subpackage class-imager
 **/



/**
* Images main class
*/
class wpuiImager
{
	private $src, $info, $width, $height, $process, $options;
	
	
	function __construct( $file, $width, $height, $mode=auto, $format='png' )
	{

		$is_Windows = strtoupper( substr(php_uname('s'), 0, 3 )) == 'WIN';
		$slash = ( $is_Windows ) ? '\\' : '/';
		
		$this->info = getimagesize( $file );
		
		if ( ! is_array( $this->info ) ) return;
		
		$this->src = $this->open( $file );
		// echo '<pre>';
		// var_export($this->info);
		// echo '</pre>';

		$this->options = get_option( 'wpUI_options' );
		if ( ! isset( $this->options ) || ! isset( $this->options['enable_cache']))
		return;


		$this->width = imagesx( $this->src );
		$this->height = imagesy( $this->src );
		
		if ( (  $this->width / $this->height ) != 1 ) {
			$mode = 'crop';
		}
		
		$filestr = md5(str_replace( $slash, '', strrchr( $file, $slash ) ) . '_' . $width . '_' . $height . '_' . $mode );
		
		$cachedir = wpui_adjust_path( WP_CONTENT_DIR . '/uploads/wp-ui/cache/' );
		is_dir( $cachedir ) || @mkdir( $cachedir, 0755, true );
		
		$storestr =  $cachedir . $filestr . '.' . $format;

		if ( file_exists( $storestr ) ) {
			$this->output( $storestr, $format );
		} else {
			$this->resize( $width, $height, $mode );
			$this->save( $storestr, 100, $format );
			$this->output( $storestr, $format );
			
		}

	}
	
	private function open( $file ) {
		$filext = $this->info[ 2 ];
		switch( $filext ) {
			case IMAGETYPE_JPEG:
				$image = @imagecreatefromjpeg( $file );
				break;
			case IMAGETYPE_GIF:	
				$image = @imagecreatefromgif( $file );
				break;
			case IMAGETYPE_PNG:
				$image = @imagecreatefrompng( $file );
				break;
			default:
				$image = false;
				break;
		}
		return $image;		
	}
	
	public function resize( $nWidth, $nHeight, $option='auto' ) {
		$options = $this->getSize( $nHeight, $nWidth, $option );
		
		$this->process = imagecreatetruecolor( $options[ 0 ], $options[ 1 ]);
		$this->process = $this->alpha( $this->process );
		imagecopyresampled( $this->process, $this->src, 0, 0, 0, 0, $options[ 0 ], $options[ 1 ], $this->width, $this->height );
		
		if  ( $option == 'crop' )
			$this->getCrop( $options[ 0 ], $options[ 1 ], $nWidth, $nHeight );

	}
	
	public function getSize( $nHeight, $nWidth, $option ) {
		
		switch( $option ) {
			case 'exact':
				$width = $nWidth;
				$height = $nHeight;
			break;
			case 'portrait':
				$width = $this->getSizeFixedHeight( $nHeight );
				$height = $nHeight;
			break;
			case 'landscape':
				$width = $nWidth;
				$height = $this->getSizeFixedWidth( $nWidth );
			break;
			case 'auto':
				$autoD = $this->getSizeAuto( $nWidth, $nHeight );
				$width = $autoD[0];
				$height = $autoD[1];
			break;
			case 'crop':
				$opts = $this->getCropSize( $nWidth, $nHeight );
				$width = $opts[0];
				$height = $opts[1];				
			break;
		}	
		return array( $width, $height );
		
		
		
	}
	
	private function getCropSize( $nWidth, $nHeight ) {
		$hRatio = $this->height / $nHeight;
		$wRatio = $this->width / $nWidth;
		
		$ratio = ( $hRatio < $wRatio ) ? $hRatio : $wRatio;
		
		$width = $this->width / $ratio;
		$height = $this->height / $ratio;
		
		return array( $width, $height );		
	}
	
	private function getSizeFixedHeight( $nHeight ) {
		return $nHeight * ( $this->width / $this->height );
	}
	
	private function getSizeFixedWidth( $nWidth ) {
		return $nWidth * ( $this->height / $this->width );		
	}
	
	private function getSizeAuto( $nWidth, $nHeight ) {
		if ( $this->height < $this->width ) {
			$oWidth = $nWidth;
			$oHeight = $this->getSizeFixedWidth( $nWidth );
		} elseif ( $this->height > $this->width ) {
			$oWidth = $this->getSizeFixedHeight( $nHeight );
			$oHeight = $nHeight;
		} else {
			if ( $nHeight < $nWidth ) {
				$oWidth = $nWidth;
				$oHeight = $this->getSizeFixedWidth( $nWidth );
			} elseif( $nHeight > $nWidth ) {
				$oHeight = $nHeight;
				$oWidth = $this->getSizeFixedHeight( $nHeight );
			} else {
				$oWidth = $nWidth;
				$oHeight = $nHeight;
			}		
		}
		return array( $oWidth, $oHeight );	
	}
	
	
	private function getCrop( $width, $height, $nWidth, $nHeight ) {
		
		$cX = ( $width / 2 ) - ( $nWidth / 2 );
		$cY = ( $height / 2 ) - ( $nHeight / 2 );
		
		$crop = $this->process;
		
		$this->process = imagecreatetruecolor( $nWidth, $nHeight );
		// $this->process = $this->alpha( $this->process );
		imagecopyresampled( $this->process, $crop, 0, 0 , $cX, $cY, $nWidth, $nHeight, $nWidth, $nHeight );		
	}

	public function alpha( $temp ) {
		imagealphablending( $temp, false );
		$alpha = imagecolorallocatealpha( $temp, 255, 0, 255, 127 );
		// imagecolortransparent( $temp, 0, 0, 0, 127 );
		imagefill( $temp, 0, 0, $alpha );
		imagesavealpha( $temp, true );
		return $temp;		
	}


	public function save( $path, $quality="100", $type='png' ) {
		
		if ( $path ) {
		$filext = strrchr( $path, '.' );
		$filext = strtolower( $filext );
		} else  {
			$filext = $type;
		}
		
		switch( $filext ) {
			case '.jpg':
			case '.jpeg':
				if ( imagetypes() && IMG_JPG ) {
					imagejpeg( $this->process, $path, $quality );
				}
				break;
			case '.gif':
				if ( imagetypes() && IMG_GIF ) {
					imagegif( $this->process, $path );
				}
			break;
			case '.png':
				$scaleQuality = round(( $quality/100) * 9 );
				$invert = 9 - $scaleQuality;
				// $this->process = $this->alpha( $this->process );
				if( imagetypes() & IMG_PNG ) {
					imagepng( $this->process, $path, $invert );
				}				
			break;
			
		}
	}
	
	
	public function output( $file, $type ) {
		// $pwdir = dirname( __FILE__ ) . '/cache';
		// if ( ! is_dir( $pwdir ) ) {
		// 	mkdir( $pwdir, 0755, true );
		// }
		header( "Content-type: image/$type" );
		header( "Content-length: " . filesize($file) );
		readfile($file);
		// $this->save( false, '100', '.' . $type );
		exit;
	}
	
	
	
}

add_action( 'template_redirect', 'wpui_output_the_image' );		

define( 'WPUI_IMAGE_ALLOW_CROSS_DOMAIN', FALSE );

function wpui_output_the_image() {
	$src = get_query_var( 'wpui-image' );
	
	if ( !empty( $src ) ) {
	if ( ( stristr( $src, site_url() ) === FALSE ) || WPUI_IMAGE_ALLOW_CROSS_DOMAIN ) {
		return;
	} else {
		if ( ( stripos( $src, 'http:') === 0 ) || ( stripos( $src, 'https:') === 0 ) ) {
			$src = addslashes( $src );
			$path = wpui_adjust_path( str_ireplace( site_url() . '/wp-content', WP_CONTENT_DIR,  $src ) );
		}
	}
	$width = ( isset($_GET[ 'width' ]) ) ? $_GET[ 'width' ] : '100';
	$height = ( isset($_GET[ 'height' ]) ) ? $_GET[ 'height' ] : '100';
	$mode = ( isset($_GET[ 'mode' ]) ) ?  $_GET[ 'mode' ] : 'auto';
	$newImg = new wpuiImager( $src, $width, $height, $mode );
	exit();
	}
} // END function wpui_add_queries

?>