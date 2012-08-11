<?php
$get_block_style = get_option('tn_buddysocial_blog_featured_style'); ?>
<?php if($get_block_style == "" || $get_block_style == "gallery") { ?>
<?php locate_template( array('lib/templates/wp-template/slideshow.php'), true); ?>
<?php } else if($get_block_style == "article") { ?>
<?php locate_template( array('lib/templates/wp-template/article.php'), true); ?>
<?php } ?>