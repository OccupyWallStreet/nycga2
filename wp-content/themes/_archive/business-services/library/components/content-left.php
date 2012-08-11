<?php
	$featured_category = get_option('dev_businessservices_feature_cat');
	$featured_number = get_option('dev_businessservices_feature_number');
	$featured_title = get_option('dev_businessservices_feature_title');
?>
<div class="content-left">
	<?php

	if ($featured_title == ""){
		$featured_title = "Set this title in theme options";
	}

	?>
<h3><?php echo $featured_title; ?></h3>
<?php

if ($featured_category){

?>
<?php query_posts('category_name='. $featured_category . '&showposts='. $featured_number . ''); ?>
			  <?php while (have_posts()) : the_post(); ?>
<div class="sbox">
<div class="simg">
<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
	<div class="image-wrap" style="height:150px;width:272px;border:1px solid #DBDBDB;padding:5px;text-align:center;">
	<div class="attach-post-image alignleft" style="height:140px;width:260px;display:block;	border:1px solid #DBDBDB;padding:5px;background:url('<?php the_post_image_url($spotlight_image_display); ?>') center center no-repeat;">&nbsp;</div></div></a>
</div>
<p><?php the_title(); ?></p>
</div>
			<?php endwhile; 
		
			?>
			<?php

			}

			?>
</div>
