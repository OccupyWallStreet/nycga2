<?php
/**
* Front-end - CSS for jQuery smoothSlideshow script
*
* @copyright Copyright 2008-2010  Ade WALKER  (email : info@studiograsshopper.ch)
* @package dynamic_content_gallery
* @version 3.3.5
*
* @info Load user defined styles into the header.
* @info This should ensure XHTML validation.
*
* @since 3.3
* @updated 3.3.3
*/
?>

<?
/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit( __('Sorry, you are not allowed to access this file directly.', DFCG_DOMAIN) );
}
?>
<style type="text/css">
#dfcg-slideshow {
	color:#fff;
	list-style:none;
	}
	
#dfcg-slideshow span {
	display:none;
	}

#dfcg-wrapper {
	display:none;
	margin:0px;
	}
	
#dfcg-wrapper * {
	margin:0;
	padding:0;
	/*overflow:hidden;Added AW */
	}
	
#dfcg-fullsize {
	background:<?php echo $dfcg_options['gallery-background']; ?>;
	border:<?php echo $dfcg_options['gallery-border-thick']; ?>px solid <?php echo $dfcg_options['gallery-border-colour']; ?>;
	height:<?php echo $dfcg_options['gallery-height']; ?>px;
	overflow: hidden;
	padding:0px;
	position:relative;
	z-index:1;/* Fix added in v3.3.3 */
	width:<?php echo $dfcg_options['gallery-width']; ?>px;
	}
	
#dfcg-text {
	background-color:<?php echo $dfcg_options['slide-overlay-color']; ?> !important;
	bottom:0;
	filter:alpha(opacity=70);
	height:0px;
	opacity:.7;
	overflow:hidden;
	padding-bottom:1px;/* Fix added in v3.3.3 - was 5px */
	position:absolute;
	width:<?php echo $dfcg_options['gallery-width']; ?>px;
	z-index:200;
	}

#dfcg-text h3 {
	color:<?php echo $dfcg_options['slide-h2-colour']; ?> !important;
	margin:<?php echo $dfcg_options['slide-h2-margtb']; ?>px <?php echo $dfcg_options['slide-h2-marglr']; ?>px !important;
	padding:<?php echo $dfcg_options['slide-h2-padtb']; ?>px <?php echo $dfcg_options['slide-h2-padlr']; ?>px !important;
	font-size:<?php echo $dfcg_options['slide-h2-size']; ?>px !important;
	font-weight:<?php echo $dfcg_options['slide-h2-weight']; ?> !important;
	}

#dfcg-text p {
	color:<?php echo $dfcg_options['slide-p-colour']; ?> !important;
	font-size: <?php echo $dfcg_options['slide-p-size']; ?>px !important;
	line-height:<?php echo $dfcg_options['slide-p-line-height']; ?>px !important;
	margin:<?php echo $dfcg_options['slide-p-margtb']; ?>px <?php echo $dfcg_options['slide-p-marglr']; ?>px !important;
	padding:<?php echo $dfcg_options['slide-p-padtb']; ?>px <?php echo $dfcg_options['slide-p-padlr']; ?>px !important;
	}
	
#dfcg-text p a, #dfcg-text p a:link, #dfcg-text p a:visited {
	color: <?php echo $dfcg_options['slide-p-a-color']; ?> !important;
	font-weight:<?php echo $dfcg_options['slide-p-a-weight']; ?> !important;
	}

#dfcg-text p a:hover {
	color: <?php echo $dfcg_options['slide-p-ahover-color']; ?> !important;
	font-weight:<?php echo $dfcg_options['slide-p-ahover-weight']; ?> !important;
	}
	
#dfcg-image img {
	position:absolute;
	z-index:25;
	width:auto;
	/*height:<?php //echo $dfcg_options['gallery-height']; ?>px;/* Added AAW - not sure */
	}

.dfcg-imgnav {
	position:absolute;
	width:25%;
	height:<?php echo $dfcg_options['gallery-height']; ?>px;
	cursor:pointer;
	z-index:150;
	}

#dfcg-imgprev {
	left:0;
	background:url(<?php echo DFCG_URL . '/js-jquery-smooth/css/images/fleche1.png'; ?>) left center no-repeat;
	}
	
#dfcg-imgnext {
	right:0;
	background:url(<?php echo DFCG_URL . '/js-jquery-smooth/css/images/fleche2.png'; ?>) right center no-repeat;
	}
	
#dfcg-imglink {
	position:absolute;
	width:100%;
	z-index:100;
	opacity:.01;/* changed v3.3.3 */
	filter:alpha(opacity=1);/* changed v3.3.3 */
	background:#fff;/* added v3.3.3 */
	}
	
.linkhover {
	background:url(images/link.gif) center center no-repeat;
	}
	
#dfcg-thumbnails {
	position: absolute;
	top: -110px;
	left: 0;
	z-index: 999;
	height: 130px;
	}

#slideleft {
	float:left;
	width:20px;
	height:81px;
	background:url(images/scroll-left.gif) center center no-repeat;
	background-color:#222;
	}
	
#slideleft:hover {
	background-color:#333;
	}
	
#slideright {float:right;
	width:20px;
	height:81px;
	background:#222 url(images/scroll-right.gif) center center no-repeat;
	}
	
#slideright:hover {
	background-color:#333;
	}
	
#dfcg-slidearea {
	float:left;
	position:relative;
	width:<?php echo $dfcg_options['gallery-width']; ?>px;
	height:81px;
	overflow:hidden;
	padding-top: 6px;
	}
	
#dfcg-slider {
	position:absolute;
	left:0;
	height:81px;
	}
	
#dfcg-slider img {
	cursor:pointer;
	border:1px solid #fff;
	padding:0px;
	height: 75px;
	}
	
#dfcg-thumbnails .dfcg-carouselBtn {
    position: absolute;
    bottom: -3px;
    right: 20px;
    display: block;
    color: #fff;
    cursor: pointer;
    font-size: 13px;
    padding: 0px 8px 3px;
	}

.dfcg-carouselBtn, .dfcg-sliderContainer {
    background: #000;
    opacity: 0.9;
    filter: alpha(opacity=90);
	}

#dfcg-thumbnails .dfcg-sliderContainer {
    height: 110px;
    position: relative;
    width: <?php echo $dfcg_options['gallery-width']; ?>px;
    padding: 0 5px;
	}
	
#dfcg-sliderInfo {
    color: #fff;
    bottom: 5px;
    position: absolute;
    padding-left: 5px;
	}
</style>
<?php
// CSS option not used
?>