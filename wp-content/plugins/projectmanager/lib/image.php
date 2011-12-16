<?php
/**
* Image class for the WordPress plugin ProjectManager
* Main functions are performed by Thumbnail class by Ian Selby
* 
* @author 	Kolja Schleich
* @package	ProjectManager
* @copyright 	Copyright 2008-2009
*/

class ProjectManagerImage 
{
	/**
	 * image filename
	 *
	 * @var string
	 */
	var $image;
	
	
	/**
	 * thumbnail class object
	 *
	 * @var object
	 */
	var $thumbnail;
	
	
	/**
	* Initializes plugin
	*
	* Load thumbnail class and store image in class
	*
	* @param none
	* @return void
	*/
	function __construct($imagefile = false)
	{
		if ( !class_exists("Thumbnail") )
			require_once( dirname (__FILE__) . '/thumbnail.inc.php' );
			
		$this->image = $imagefile;
	}
	function LeagueManagerImage($imagefile)
	{
		$this->__construct($imagefile);
	}
	
	
	/**
	 * get supported file types
	 *
	 * @param none
	 * @return array
	 */
	function getSupportedImageTypes()
	{
		return array( "jpg", "jpeg", "png", "gif" );
	}
	

	/**
	 * check if image type is supported
	 *
	 * @param none
	 * @return boolean
	 */
	function supported()
	{
		if ( in_array($this->getImageType(), $this->getSupportedImageTypes()) )
			return true;
		
		return false;
	}
	
	
	/**
	 * get image type of supplied image
	 *
	 * @param none
	 * @return file extension
	 */
	function getImageType(  )
	{
		$file_info = pathinfo($this->image);
		return strtolower($file_info['extension']);
	}
	
	
	/**
	 * create Thumbnail of Image
	 *
	 * @param array $dims assoziative array of image dimension
	 * @param string $new_image
	 * @param string $chmod chmod of uploaded image file. ignored if empty
	 */
	function createThumbnail( $dims, $new_image, $chmod )
	{
		$thumbnail = new Thumbnail($this->image);
		$thumbnail->resize( $dims['width'], $dims['heigth'] );
		$thumbnail->save($new_image);

		if ( empty($chmod) ) $chmod = '0644';
		$chmod = intval($chmod);
		$chmod = octdec("0".$chmod);
		chmod( $new_image, $chmod );
	}
}

?>
