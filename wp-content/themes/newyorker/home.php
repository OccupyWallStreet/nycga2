<?php get_header(); ?>

<?php get_template_part('feature'); ?>

<div class="col4">
<?php 
	$my_query = new WP_Query('showposts=1&offset=0');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>		        
<div class="orlando"><?php the_category(', ') ?></div>

<h4><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h4>
<div class="entry">

<?php the_excerpt(); ?>

</div>
<div class="hc">{ <em><?php _e('on', 'Detox') ?> <?php the_time('M'); ?><?php the_time('j'); ?> <?php the_time('Y'); ?> | <?php _e('in', 'Detox') ?>: <?php the_category(' | ') ?></em> }</div>
<?php endwhile; ?>
</div>

<div class="col5">
<?php 
	$my_query = new WP_Query('showposts=1&offset=1');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>		        
<div class="orlando"><?php the_category(', ') ?></div>

<h4><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h4>
<div class="entry">

<?php the_excerpt(); ?>
</div>
<div class="hc">{ <em><?php _e('on', 'Detox') ?> <?php the_time('M'); ?><?php the_time('j'); ?> <?php the_time('Y'); ?> | <?php _e('in', 'Detox') ?>: <?php the_category(' | ') ?></em> }</div>
<?php endwhile; ?>
</div>

<div class="col6">
<?php 
	$my_query = new WP_Query('showposts=1&offset=2');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>		        
<div class="orlando"><?php the_category(', ') ?></div>

<h4><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h4>
<div class="entry">

<?php the_excerpt(); ?>
</div>
<div class="hc">{ <em><?php _e('on', 'Detox') ?> <?php the_time('M'); ?><?php the_time('j'); ?> <?php the_time('Y'); ?> | <?php _e('in', 'Detox') ?>: <?php the_category(' | ') ?></em> }</div>
<?php endwhile; ?>
</div>

<div class="col7">
<?php 
	$my_query = new WP_Query('showposts=1&offset=3');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>		        
<div class="orlando"><?php the_category(', ') ?></div>

<h4><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h4>
<div class="entry">

<?php the_excerpt(); ?>
</div>
<div class="hc">{ <em><?php _e('on', 'Detox') ?> <?php the_time('M'); ?><?php the_time('j'); ?> <?php the_time('Y'); ?> | <?php _e('in', 'Detox') ?>: <?php the_category(' | ') ?></em> }</div>
<?php endwhile; ?>
</div>

<?php get_template_part('middle'); ?>

<div class="col4">
<?php 
	$my_query = new WP_Query('showposts=1&offset=7');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>		        
<div class="orlando"><?php the_category(', ') ?></div>

<h4><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h4>
<div class="entry">

<?php the_excerpt(); ?>

</div>
<div class="hc">{ <em><?php _e('on', 'Detox') ?> <?php the_time('M'); ?><?php the_time('j'); ?> <?php the_time('Y'); ?> | <?php _e('in', 'Detox') ?>: <?php the_category(' | ') ?></em> }</div>
<?php endwhile; ?>
</div>

<div class="col5">
<?php 
	$my_query = new WP_Query('showposts=1&offset=8');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>		        
<div class="orlando"><?php the_category(', ') ?></div>

<h4><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h4>
<div class="entry">

<?php the_excerpt(); ?>
</div>
<div class="hc">{ <em><?php _e('on', 'Detox') ?> <?php the_time('M'); ?><?php the_time('j'); ?> <?php the_time('Y'); ?> | <?php _e('in', 'Detox') ?>: <?php the_category(' | ') ?></em> }</div>
<?php endwhile; ?>
</div>

<div class="col6">
<?php 
	$my_query = new WP_Query('showposts=1&offset=9');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>		        
<div class="orlando"><?php the_category(', ') ?></div>

<h4><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h4>
<div class="entry">

<?php the_excerpt(); ?>
</div>
<div class="hc">{ <em><?php _e('on', 'Detox') ?> <?php the_time('M'); ?><?php the_time('j'); ?> <?php the_time('Y'); ?> | <?php _e('in', 'Detox') ?>: <?php the_category(' | ') ?></em> }</div>
<?php endwhile; ?>
</div>

<div class="col7">
<?php 
	$my_query = new WP_Query('showposts=1&offset=10');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>		        
<div class="orlando"><?php the_category(', ') ?></div>

<h4><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h4>
<div class="entry">

<?php the_excerpt(); ?>
</div>
<div class="hc">{ <em><?php _e('on', 'Detox') ?> <?php the_time('M'); ?><?php the_time('j'); ?> <?php the_time('Y'); ?> | <?php _e('in', 'Detox') ?>: <?php the_category(' | ') ?></em> }</div>
<?php endwhile; ?>
</div>

<?php get_footer(); ?>