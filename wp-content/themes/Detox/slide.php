<div id="mow">
<h5><?php _e( 'Featured', 'Detox') ?></h5>

<div id="mygallery" class="stepcarousel">

<div class="belt">
<?php 
	$slidecat = get_option('Detox_slicer_category'); 
	$my_query = new WP_Query('category_name= '. $slidecat .'&showposts=8&offset=0');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>

<div class="panel">
<div class="lead">
<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
<?php $image = get_the_post_thumbnail($post->ID, 'browse'); ?>
<?php echo $image; ?>
</a>
</div>
<h4>
<a href="<?php the_permalink() ?>" title="<?php _e('Read the full story here:'); ?> <?php the_title_attribute(); ?>"><?php the_title() ?></a>
</h4>

<div class="read"><a title="<?php _e( 'Read more here', 'Detox') ?>" href="<?php the_permalink() ?>"><?php _e( 'Read on', 'Detox') ?> </a></div>

</div>

<?php endwhile; ?>

</div>
</div>
</div>