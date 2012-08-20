<?php
//**
//Featured Events Template
//**
?>

<?php

global $post;

$myquery = new WP_Query('post_type=incsub_event&post_status=publish&count=1'); 

?>

<?php if ($myquery->have_posts()) : ?>


<div id="feat-content">
<div class="feat-articles">
<div class="feat-post">

<h4>Featured Events</h4>
<?php while (have_posts()) : the_post(); ?>
<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
<div class="feat-post-content">
<?php the_post_thumbnail(array(432,999), array('class' => 'feat-post-thumbnail')); ?>
<?php echo Eab_Template::get_event_dates ($post); ?>
<?php echo custom_the_content(125); ?>
</div>
<?php endwhile; ?>

<h2><?php _e( 'More Article &raquo;', TEMPLATE_DOMAIN) ?></h2>
<ul class="more-article">


<li><div class="alignleft"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></div><div class="alignright"><?php echo Eab_Template::get_event_dates ($post); ?></div></li>

</div>
</div>
</div>

