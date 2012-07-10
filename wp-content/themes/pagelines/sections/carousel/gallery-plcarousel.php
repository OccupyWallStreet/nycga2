<?php 
/*
Template Page for the gallery overview

Follow variables are useable :

	$gallery     : Contain all about the gallery
	$images      : Contain all images, path, title
	$pagination  : Contain the pagination content

 You can check the content when you insert the tag <?php var_dump($variable) ?>
 If you would like to show the timestamp of the image ,you can use <?php echo $exif['created_timestamp'] ?>
*/
 
if (!defined ('ABSPATH')) 
	die ('No direct access allowed');

if (!empty ($gallery)) : 
	
	foreach ( $images as $image ) : ?>
	
	<li id="ngg-image-<?php echo $image->pid ?>" class="list-item ngg-gallery-thumbnail-box" <?php echo $image->style ?> >
		<a href="<?php echo $image->imageURL ?>" title="<?php echo $image->description ?>" <?php echo $image->thumbcode ?> >
			<?php if ( !$image->hidden ) { ?>
				<img title="<?php echo $image->alttext ?>" alt="<?php echo $image->alttext ?>" src="<?php echo $image->thumbnailURL ?>"/>
			<?php } ?>
		</a>
	</li>
	
<?php
	endforeach; 
endif; 

