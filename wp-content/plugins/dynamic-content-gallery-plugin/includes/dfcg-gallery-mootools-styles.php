<?php
/**
* Front-end - CSS for Mootools
*
* @copyright Copyright 2008-2010  Ade WALKER  (email : info@studiograsshopper.ch)
* @package dynamic_content_gallery
* @version 3.3.5
*
* @info Load user defined styles into the header.
* @info This should ensure XHTML validation.
*
* @since 3.2
* @updated 3.3.4
*/
?>

<?
/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit( __('Sorry, you are not allowed to access this file directly.', DFCG_DOMAIN) );
}
?>


<style type="text/css">
.imageElement {
	visibility: hidden;
	}
	
#myGallery, #myGallerySet, #flickrGallery {
	background: #000 url('<?php echo WP_PLUGIN_URL; ?>/dynamic-content-gallery-plugin/js-mootools/css/img/loading-bar-black.gif') no-repeat center;
	border: <?php echo $dfcg_options['gallery-border-thick']; ?>px solid <?php echo $dfcg_options['gallery-border-colour']; ?>;
	height: <?php echo $dfcg_options['gallery-height']; ?>px;
	width: <?php echo $dfcg_options['gallery-width']; ?>px;
	}

.jdGallery .slideInfoZone {
	background-color: <?php echo $dfcg_options['slide-overlay-color']; ?> !important;
	height: <?php echo $dfcg_options['slide-height']; ?>px;
	}

.jdGallery .slideInfoZone h2 {
	color: <?php echo $dfcg_options['slide-h2-colour']; ?> !important;
	font-size: <?php echo $dfcg_options['slide-h2-size']; ?>px !important;
	font-weight: <?php echo $dfcg_options['slide-h2-weight']; ?> !important;
	margin: <?php echo $dfcg_options['slide-h2-margtb']; ?>px <?php echo $dfcg_options['slide-h2-marglr']; ?>px !important;
	padding: <?php echo $dfcg_options['slide-h2-padtb']; ?>px <?php echo $dfcg_options['slide-h2-padlr']; ?>px !important; 
	}

.jdGallery .slideInfoZone p {
	color: <?php echo $dfcg_options['slide-p-colour']; ?> !important;
	font-size: <?php echo $dfcg_options['slide-p-size']; ?>px !important;
	line-height: <?php echo $dfcg_options['slide-p-line-height']; ?>px !important;
	margin: <?php echo $dfcg_options['slide-p-margtb']; ?>px <?php echo $dfcg_options['slide-p-marglr']; ?>px !important;
	padding: <?php echo $dfcg_options['slide-p-padtb']; ?>px <?php echo $dfcg_options['slide-p-padlr']; ?>px !important;
	}

.jdGallery .slideInfoZone p a, .jdGallery .slideInfoZone p a:link, .jdGallery .slideInfoZone p a:visited {
	color: <?php echo $dfcg_options['slide-p-a-color']; ?> !important;
	font-weight:<?php echo $dfcg_options['slide-p-a-weight']; ?> !important;
	}

.jdGallery .slideInfoZone p a:hover {
	color: <?php echo $dfcg_options['slide-p-ahover-color']; ?> !important;
	font-weight:<?php echo $dfcg_options['slide-p-ahover-weight']; ?> !important;
	}
	
.jdGallery .slideElement {
	background-color: <?php echo $dfcg_options['gallery-background']; ?>;
	}

</style>