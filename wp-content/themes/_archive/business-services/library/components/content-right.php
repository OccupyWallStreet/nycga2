<?php
	$sidebar_category = get_option('dev_businessservices_sidebar_cat');
	$sidebar_number = get_option('dev_businessservices_sidebar_number');
	$sidebar_title = get_option('dev_businessservices_sidebar_title');
?>
<div class="content-right">
	<?php

	if ($sidebar_title == ""){
		$sidebar_title = "Set this title in theme options";
	}

	?>
<h3><?php echo $sidebar_title; ?></h3>
<?php

if ($sidebar_category){

?>
<?php query_posts('category_name='. $sidebar_category . '&showposts='. $sidebar_number . ''); ?>
			  <?php while (have_posts()) : the_post(); ?>

<div class="vblk">
	<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
		<div class="image-wrap" style="height:100px;width:312px;border:1px solid #DBDBDB;padding:5px;text-align:center;">
		<div class="attach-post-image alignleft" style="height:90px;width:300px;display:block;	border:1px solid #DBDBDB;padding:5px;background:url('<?php the_post_image_url($sidebar_image_display); ?>') center center no-repeat;">&nbsp;</div></div></a>

</div>
			<?php endwhile; 
		
			?>
			<?php
			}
			?>
</div>

