<?php 
	$homecontent = get_option('dev_gallery_homecontent');
	$gallery_header = get_option('dev_gallery_header');
	$gallery_header = get_option('dev_gallery_header');
	$gallery_description = get_option('dev_gallery_description');	
	$gallery_subheader = get_option('dev_gallery_subheader');
?>
<?php if ($homecontent == "yes") {?>
<div id="content-home">
<h2>
<?php
	echo stripslashes($gallery_header);
?>
</h2>

<div class="content-description">
<?php
	echo stripslashes($gallery_description);
?>
</div>

<h4>
<?php
	echo stripslashes($gallery_subheader);
?>
</h4>
</div>
<?php } ?>
