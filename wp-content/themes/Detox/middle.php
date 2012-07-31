<div id="lbar">

<div class="sl"></div>
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('left-column') ) : ?>

		        
<div class="orlando"><?php the_category(', ') ?></div>
<?php 
	$slidecat = get_option('Detox_left_category'); 
	$my_query = new WP_Query('category_name= '. $slidecat .'&showposts=2&offset=0');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>

<h4><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h4>
<div class="entry">
<div class="alignright">
<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
<?php $image = get_the_post_thumbnail($post->ID, 'browse'); ?>
<?php echo $image; ?>
</a>
</div>
<?php the_excerpt(__(''));?>
</div>
<div class="clearfix"></div><hr class="clear" />
<div class="read"><a title="<?php _e( 'Read more here', 'Detox') ?>" href="<?php the_permalink() ?>"><?php _e( 'Read on', 'Detox') ?> </a></div>
<div class="sl"></div>
<?php endwhile; ?>
<div class="navigation">
<?php _e( 'More of', 'Detox') ?> <?php the_category(', ') ?>
</div>

<?php endif; ?>

<div class="sl"></div>
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('ad-column2') ) : ?>
<a href="http://3oneseven.com/"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/logos.png" class="aligncenter" alt="theme by milo" /></a>
<?php endif; ?>

</div>

<div id="lrbar">
<a name="sidebar"></a>

<div class="sl"></div>
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('center-column') ) : ?>

<div class="orlando"><?php the_category(', ') ?></div>
<?php 
	$slidecat = get_option('Detox_middle_category'); 
	$my_query = new WP_Query('category_name= '. $slidecat .'&showposts=2&offset=0');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>

<h4><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h4>
<div class="entry">
<div class="alignright">
<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
<?php $image = get_the_post_thumbnail($post->ID, 'browse'); ?>
<?php echo $image; ?>
</a>
</div>
<?php the_excerpt(__(''));?>
</div>
<div class="clearfix"></div><hr class="clear" />
<div class="read"><a title="<?php _e( 'Read more here', 'Detox') ?>" href="<?php the_permalink() ?>"><?php _e( 'Read on', 'Detox') ?> </a></div>
<div class="sl"></div>
<?php endwhile; ?>
<div class="navigation">
<?php _e( 'More of', 'Detox') ?> <?php the_category(', ') ?>
</div>

<?php endif; ?>

<div class="sl"></div>
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('ad-column3') ) : ?>
<a href="http://3oneseven.com/"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/logos.png" class="aligncenter" alt="theme by milo" /></a>
<?php endif; ?>

</div>

<div id="middle">

<div class="sl"></div>
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('right-column') ) : ?>

<div class="florida"><?php the_category(', ') ?></div>
<?php 
	$slidecat = get_option('Detox_right_category'); 
	$my_query = new WP_Query('category_name= '. $slidecat .'&showposts=2&offset=0');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>

<h4><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h4>
<div class="entry">
<div class="alignright">
<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
<?php $image = get_the_post_thumbnail($post->ID, 'browse'); ?>
<?php echo $image; ?>
</a>
</div>
<?php the_excerpt(__(''));?>
</div>
<div class="clearfix"></div><hr class="clear" />
<div class="read"><a title="<?php _e( 'Read more here', 'Detox') ?>" href="<?php the_permalink() ?>"><?php _e( 'Read on', 'Detox') ?> </a></div>
<div class="sl"></div>

<?php endwhile; ?>
<div class="navigation">
<?php _e( 'More of', 'Detox') ?> <?php the_category(', ') ?>
</div>

<?php endif; ?>

<div class="sl"></div>
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('ad-column4') ) : ?>
<a href="http://3oneseven.com/"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/logos.png" class="aligncenter" alt="theme by milo" /></a>
<?php endif; ?>
</div>