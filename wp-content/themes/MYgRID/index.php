<?php get_header(); ?>
<?php 
	$pov_disthumb = get_option('pov_disthumb');
?>
	<div class="box">
	<ul id="latestpost">
	<?php $firstClass = 'firstpost'; ?>
	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<li class="bags <?php echo $firstClass; $firstClass = ""; ?>" id="post-<?php the_ID(); ?>">

    			<div class="thumb">
                <?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'homepage-thumb' ); } ?>
                </div>
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<div class="postinn bags">
				<div class="ger"><i><?php the_time('j M Y') ?></i> &bull; <?php the_excerpt(); ?></div>
				<span><a href="<?php the_permalink(); ?>">Read More +</a></span></div>

			</li>

		<?php endwhile; ?><?php else : ?>

		<div class="post">
		<h2>Not Found!</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php get_search_form(); ?>
        </div>

	<?php endif; ?>

	</ul>
			<div class="clear"></div>
			<div class="navigation ger">
			<div class="nav-previous fl"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts' ) ); ?></div>
			<div class="nav-next fr"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>' ) ); ?></div>
			</div>

	<div class="hrlineB"></div>


	<h1 class="title"><?php bloginfo('description'); ?></h1>

<?php get_footer(); ?>
