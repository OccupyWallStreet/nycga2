<div id="sec">
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('frontbar') ) : ?>

<h3 class="featured"><?php _e( 'Related <span>Items</span>' ) ?></h3>
<div class="domtab">
<ul class="domtabs">
<li><a title="<?php _e( 'View recent posts' ) ?>" href="#tab1">

<?php 
	$my_query = new WP_Query('showposts=1&offset=1');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
<?php
$category = get_the_category();
echo $category[0]->cat_name;
?>
<?php endwhile; ?>

</a></li>
<li><a title="View recent posts" href="#tab2">

<?php 
	$my_query = new WP_Query('showposts=1&offset=2');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
<?php
$category = get_the_category();
echo $category[0]->cat_name;
?>
<?php endwhile; ?>

</a></li>
<li><a title="<?php _e( 'View recent posts' ) ?>" href="#tab3">

<?php 
	$my_query = new WP_Query('showposts=1&offset=3');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
<?php
$category = get_the_category();
echo $category[0]->cat_name;
?>
<?php endwhile; ?>

</a></li>
<li><a title="<?php _e( 'View recent posts' ) ?>" href="#tab4">

<?php 
	$my_query = new WP_Query('showposts=1&offset=4');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
<?php
$category = get_the_category();
echo $category[0]->cat_name;
?>
<?php endwhile; ?>

</a></li>
</ul>
<div class="clear"><br /></div>

<div><a name="tab1" id="tab1"></a>

<?php 
	$my_query = new WP_Query('showposts=1&offset=1');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
<h3><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h3><br />
<div class="walker"><?php $image = get_the_post_thumbnail($post->ID, 'browse'); ?><a title="<?php _e( 'Read' ) ?> <?php the_title(); ?>" href="<?php the_permalink() ?>"><?php echo $image; ?></a></div>
<div class="entry">
<?php the_content_rss('', FALSE, ' ', 20); ?>
</div>
<div class="read"><a title="<?php _e( 'Read' ) ?> <?php the_title(); ?>" href="<?php the_permalink() ?>"><?php _e( 'Read More' ) ?></a></div>
<?php endwhile; ?>

</div>

<div><a name="tab2" id="tab2"></a>

<?php 
	$my_query = new WP_Query('showposts=1&offset=2');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
<h3><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h3><br />
<div class="walker"><?php $image = get_the_post_thumbnail($post->ID, 'browse'); ?><a title="<?php _e( 'Read' ) ?> <?php the_title(); ?>" href="<?php the_permalink() ?>"><?php echo $image; ?></a></div>
<div class="entry">
<?php the_content_rss('', FALSE, ' ', 20); ?>
</div>
<div class="read"><a title="<?php _e( 'Read' ) ?> <?php the_title(); ?>" href="<?php the_permalink() ?>"><?php _e( 'Read More' ) ?></a></div>
<?php endwhile; ?>

</div>

<div><a name="tab3" id="tab3"></a>

<?php 
	$my_query = new WP_Query('showposts=1&offset=3');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
<h3><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h3><br />
<div class="walker"><?php $image = get_the_post_thumbnail($post->ID, 'browse'); ?><a title="<?php _e( 'Read' ) ?> <?php the_title(); ?>" href="<?php the_permalink() ?>"><?php echo $image; ?></a></div>
<div class="entry">
<?php the_content_rss('', FALSE, ' ', 20); ?>
</div>
<div class="read"><a title="<?php _e( 'Read' ) ?> <?php the_title(); ?>" href="<?php the_permalink() ?>"><?php _e( 'Read More' ) ?></a></div>
<?php endwhile; ?>

</div>

<div><a name="tab4" id="tab4"></a>

<?php 
	$my_query = new WP_Query('showposts=1&offset=4');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
<h3><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h3><br />
<div class="walker">
<?php $image = get_the_post_thumbnail($post->ID, 'browse'); ?><a title="<?php _e( 'Read' ) ?> <?php the_title(); ?>" href="<?php the_permalink() ?>"><?php echo $image; ?></a>
</div>
<div class="entry">
<?php the_content_rss('', FALSE, ' ', 20); ?>
</div>
<div class="read"><a title="<?php _e( 'Read' ) ?> <?php the_title(); ?>" href="<?php the_permalink() ?>"><?php _e( 'Read More' ) ?></a></div>
<?php endwhile; ?>

</div>
</div>

<?php endif; ?>
</div>