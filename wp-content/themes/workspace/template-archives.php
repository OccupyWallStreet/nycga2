<?php
/*
Template Name: Archives
*/
?>

<?php get_header(); ?>

	<h1 class="page-title"><?php the_title(); ?></h1>
	<div id="content">
		<div class="entry-content">
			<h3><?php _e('The Latest 30 Posts', 'themejunkie'); ?></h3>
			<ul>
				<?php query_posts( 'showpost=30' ); ?>
				<?php while ( have_posts() ) { the_post(); ?>
					<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> - <?php the_time('Y.m.d'); ?> - <?php _e( 'Comments', 'themejunkie' ); ?> (<?php echo $post->comment_count; ?>)</li>
				<?php } ?>            
			</ul><!-- Latest Posts -->
			<div class="clear"></div>
			<div class="left" style="width:50%;">
				<h3><?php _e('Categories', 'themejunkie'); ?></h3>
				<ul>
					<?php wp_list_categories('title_li=&show_count=true'); ?>
				</ul>
			</div><!-- Categories -->
			<div class="right" style="width:50%;">
				<h3><?php _e('Monthly Archives', 'themejunkie'); ?></h3>
				<ul>
					<?php wp_get_archives('type=monthly&show_post_count=true'); ?>
				</ul>
			</div><!-- Monthly Archives -->
			<div class="clear"></div>
		</div><!-- .entry-content -->
	</div><!-- #content -->
	
<?php get_sidebar(); ?>
<?php get_footer(); ?>
