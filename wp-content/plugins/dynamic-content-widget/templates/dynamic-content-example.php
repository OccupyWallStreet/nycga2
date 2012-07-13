<?php
/**
 * @package DynamicContentWidget
 * Subtemplate: Dynamic content example
 * 
 * The widget generates a local loop, so you can call all of the 
 * regular Wordpress functions from within this template.  
 * 
*/
?>
<h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
<p>
	<?php the_content(); ?>
</p>
<?php 
	if ($post->post_type == 'page') {
?>
<span class="category">Page</span>
<?php 
	} else {
?>
<span class="category">Blog</span>
<?php 
	}
?>
