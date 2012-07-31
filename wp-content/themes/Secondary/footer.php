<?php
if (BP_INSTALLED) {
	get_template_part('lbp');
} else {
	get_template_part('le');
}
?>

<div id="cats">
<h3><?php _e( 'Top<span>ics</span>' ) ?></h3>
<ul class="fix">
<?php wp_list_cats('sort_column=name&optioncount=0'); ?>
</ul>
</div>

<div id="s_footer">

<div class="col">
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('footer-column1') ) : ?>
	
<h3><?php _e( 'Pop<span>ular</span>' ) ?></h3>

<?php $result = $wpdb->get_results("SELECT comment_count,ID,post_title FROM $wpdb->posts ORDER BY comment_count DESC LIMIT 0 , 3"); 
foreach ($result as $post) { 
setup_postdata($post);
$postid = $post->ID; 
$title = $post->post_title; 
$commentcount = $post->comment_count; 
if ($commentcount != 0) { ?> 

<h4><a href="<?php echo get_permalink($postid); ?>" title="<?php echo $title ?>">
<?php echo $title ?></a></h4> {<?php echo $commentcount ?> Comments}
<a href="<?php the_permalink() ?>" title="Read more">Read on </a>
<?php } } ?>


<?php endif; ?>
</div>

<div class="col">
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('footer-column2') ) : ?>

<h3><?php _e( 'Lin<span>ks</span>' ) ?></h3>
<ul>
<?php get_links('-1', '<li>', '</li>', '<br />', FALSE, 'id', FALSE, FALSE, 7, FALSE); ?>
</ul>

<?php endif; ?>                   
</div>

<div class="col">
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('footer-column3') ) : ?>

<h3><?php _e( 'Au<span>thors</span>' ) ?></h3>
<ul>
<?php wp_list_authors
('exclude_admin=0&show_fullname=1&hide_empty=1&feed_image=' .
get_bloginfo('template_directory') . '/images/feed.png&feed=XML'); ?>
</ul>

<?php endif; ?>
</div>

<div class="col2">
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('footer-column4') ) : ?>

<h3><?php _e( 'Me<span>ta</span>' ) ?></h3>
<ul>
<?php wp_register(); ?>
<li><?php wp_loginout(); ?></li>
<?php wp_meta(); ?>
</ul>
</div>

<?php endif; ?>
</div>	

<div id="footer">
<p>
&copy; <?php echo date("Y")." ";  ?> <?php bloginfo('name'); ?> | 
<a href="http://3oneseven.com/" title="design by milo">design by milo</a> | 
<?php _e( 'With' ) ?> <?php echo get_num_queries(); ?> <?php _e( 'queries in' ) ?> <?php timer_stop(1); ?> <?php _e( 'seconds' ) ?>
</p>

</div>
</div>

<?php wp_footer(); ?>

</body>
</html>