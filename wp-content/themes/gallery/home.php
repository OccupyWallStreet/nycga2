<?php get_header() ?>
<?php 
	$slideshow = get_option('dev_gallery_slideshow');
	$slideshowsize = get_option('dev_gallery_slideshowsize');
	$catnavigation = get_option('dev_gallery_catnavigation');
	$catnavigation_position = get_option('dev_gallery_catnavigation_position');
	$socialbuttons = get_option('dev_gallery_socialbuttons');
	$homecontent = get_option('dev_gallery_homecontent');
?>
<?php if ($catnavigation == "yes") {
	 	locate_template( array( '/library/components/navigation-gallery.php' ), true ); 
} 

if ($slideshow == "yes") {
	if ($slideshowsize == "full") { 
		locate_template( array( '/library/components/slideshow.php' ), true );
	} else { locate_template( array( '/library/components/slideshow-partial.php' ), true ); } 
}

locate_template( array( '/library/components/option-socialbuttons.php' ), true );

if (($homecontent == "") && ($socialbuttons == "") && ($slideshow == "")){
	locate_template( array( '/library/components/slideshow-partial.php' ), true ); 
}

locate_template( array( '/library/components/option-content.php' ), true );

?>

<?php get_footer() ?>
