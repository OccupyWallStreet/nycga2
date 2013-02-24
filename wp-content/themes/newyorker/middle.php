<div id="middle">

<div class="sl"></div>
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('right-column') ) : ?>

<?php 
	$my_query = new WP_Query('showposts=1&offset=4');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>

<div class="florida"><?php the_category(', ') ?></div>
<h4><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h4>
<div class="entry">

<div class="hg">
<a href="<?php the_permalink() ?>">
<?php
if (function_exists('vp_get_thumb_url')) {
        $thumb=vp_get_thumb_url($post->post_content, 'sth');
}
?>
<img src="<?php if ($thumb!='') echo $thumb; ?>" alt="<?php the_title(); ?>" />
</a>
</div>

<?php the_excerpt(); ?>
</div>
<div class="hc">{ <em><?php _e('on', 'Detox') ?> <?php the_time('M'); ?><?php the_time('j'); ?> <?php the_time('Y'); ?> | <?php _e('in', 'Detox') ?>: <?php the_category(' | ') ?></em> }</div>

<?php endwhile; ?>


<?php endif; ?>

</div>

<div id="lbar">

<div class="sl"></div>
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('left-column') ) : ?>

<?php 
	$my_query = new WP_Query('showposts=1&offset=5');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>		        
<div class="orlando"><?php the_category(', ') ?></div>
<h4><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h4>
<div class="entry">

<div class="hg">
<a href="<?php the_permalink() ?>">
<?php
if (function_exists('vp_get_thumb_url')) {
        $thumb=vp_get_thumb_url($post->post_content, 'sth');
}
?>
<img src="<?php if ($thumb!='') echo $thumb; ?>" alt="<?php the_title(); ?>" />
</a>
</div>

<?php the_excerpt(); ?>
</div>
<div class="hc">{ <em><?php _e('on', 'Detox') ?> <?php the_time('M'); ?><?php the_time('j'); ?> <?php the_time('Y'); ?> | <?php _e('in', 'Detox') ?>: <?php the_category(' | ') ?></em> }</div>

<?php endwhile; ?>


<?php endif; ?>

</div>

<div id="lrbar">

<div class="sl"></div>
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('center-column') ) : ?>

<?php 
	$my_query = new WP_Query('showposts=1&offset=6');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
<div class="orlando"><?php the_category(', ') ?></div>

<h4><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h4>
<div class="entry">

<div class="hg">
<a href="<?php the_permalink() ?>">
<?php
if (function_exists('vp_get_thumb_url')) {
        $thumb=vp_get_thumb_url($post->post_content, 'sth');
}
?>
<img src="<?php if ($thumb!='') echo $thumb; ?>" alt="<?php the_title(); ?>" />
</a>
</div>

<?php the_excerpt(); ?>
</div>
<div class="hc">{ <em><?php _e('on', 'Detox') ?> <?php the_time('M'); ?><?php the_time('j'); ?> <?php the_time('Y'); ?> | <?php _e('in', 'Detox') ?>: <?php the_category(' | ') ?></em> }</div>

<?php endwhile; ?>


<?php endif; ?>

</div>