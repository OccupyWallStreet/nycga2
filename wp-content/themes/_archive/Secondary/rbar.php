<div id="r_sidebar">
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('rbar') ) : ?>

<h3><?php _e( 'Sub<span>scribe</span>' ) ?></h3>
<div class="feed">
<ul>
<li><a href="<?php bloginfo('rss2_url'); ?>" title="Full content RSS feed"><?php _e( 'Content' ) ?> (RSS)</a></li>
<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="Full comments RSS feed"><?php _e( 'Comments' ) ?> (RSS)</a></li>
<?php wp_list_cats('sort_column=name&optioncount=0&hierarchical=0&feed=RSS'); ?>
</ul>
</div>

<?php if ( is_single()  ) { ?>

<?php
if(isset($_GET['author_name'])) :
$curauth = get_userdatabylogin($author_name);
else :
$curauth = get_userdata(intval($author));
endif;
?>
<h3><?php _e( 'The <span>author</span>:' ) ?> <?php echo $curauth->nickname; ?></h3>
<p><img src="<?php bloginfo('template_url'); ?>/favicon.ico" alt="The Author" class="left" /> <?php _e( 'Profile:' ) ?> <?php the_author_description(); ?><br />
<a href="mailto:<?php the_author_email(); ?>" title="<?php _e( 'Email this author' ) ?>"><?php _e( 'Email this author' ) ?></a></p>
<h3><?php _e( 'Recen<span>tly</span>' ) ?></h3>
<?php $my_query = new WP_Query('showposts=4'); ?>
<?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
<li><a title="<?php the_content_rss('', FALSE, '', 40); ?>" href="<?php the_permalink() ?>"><?php the_title() ?>   </a></li>
<?php endwhile; ?>

<?php } ?>

<?php /* If this is the frontpage */ if ( is_home()  ) { ?>

<h3><?php _e( 'The <span>Past</span>' ) ?></h3>
<ul><?php wp_get_archives('type=monthly&limit=8'); ?></ul>

<?php } ?>

<h3><?php _e( 'Best <span>Topics</span>' ) ?></h3>
<?php wp_tag_cloud('smallest=8&largest=22'); ?>

<?php endif; ?>
</div>