<?php get_header(); ?>
<div id="content">
<div id="contentmiddle2">

<?php
if(isset($_GET['author_name'])) :
$curauth = get_userdatabylogin($author_name);
else :
$curauth = get_userdata(intval($author));
endif;
?>
<div class="sl"></div>
<h2>About the author: <?php echo $curauth->nickname; ?></h2>
<p>Website: <a href="<?php echo $curauth->user_url; ?>"><?php echo $curauth->user_url; ?></a></p>
<p>
<?php $aOptions = Detox::initOptions(false); ?>
<img src="<?php echo($aOptions['featured111-image']); ?>" alt="<?php bloginfo('name'); ?>" class="left" /> Profile: <?php the_author_description(); ?><br />
<a href="mailto:<?php the_author_email(); ?>" title="Email this author">Email this author</a></p>

<h2>Posts by <?php echo $curauth->nickname; ?>:</h2>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div class="sl"></div>
<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>

<div class="entry">
<div class="alignright">
<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
<?php $image = get_the_post_thumbnail($post->ID, 'browse'); ?>
<?php echo $image; ?>
</a>
</div>
<?php the_excerpt(__('Read more'));?><div class="clearfix"></div>
<div class="read"><a title="Read more here" href="<?php the_permalink() ?>">Read on </a></div>
</div>
<div class="clearfix"></div>

<div class="postspace"></div>
<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts by this author.'); ?></p>
<?php endif; ?>

<div class="navigation">
<?php if(function_exists('wp_pagenavi')) { wp_pagenavi('', '', '', '', 3, false);} ?>
</div>
</div>

<?php get_template_part('sbar'); ?>
<?php get_template_part('bar'); ?>


</div>

<?php get_footer(); ?>