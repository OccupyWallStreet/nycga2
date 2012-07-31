
<?php get_header() ?>
	<div id="content">
		<div class="padder">
			<h3 class="pagetitle"><?php wp_title(); ?></h3>

		<?php do_action( 'bp_before_blog_home' ) ?>

		<?php do_action( 'template_notices' ) ?>

		<div class="page" id="blog-latest" role="main">

				<!-- BEGIN: left sidebar -->
				<div class="left-sidebar">
					<?php locate_template( array( 'sidebar-home-1.php' ), true ) ?>
				</div>
				<!-- //END: left sidebar -->
			
				<!-- BEGIN: main content column -->
				<div class="main-content-column">
				
					
					<?php query_posts(array('post__in'=>get_option('sticky_posts'))); ?>

				
					<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
					<div <?php post_class(); ?>>
						<?php the_excerpt(); ?>
					</div>
					<?php endwhile; endif; ?>

					<?php locate_template( array( 'sidebar-home-2.php' ), true ) ?>
				
					<div>
					<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
						<div <?php post_class(); ?>>
							<?php the_excerpt(); ?>
						</div>
					<?php endwhile; endif; ?>
					</div>
				
					<!-- BEGIN: sidebar -->
					<?php locate_template( array( 'sidebar-home-3.php' ), true ) ?>
					
				</div>
				<!-- //END: main content column -->

		</div>

		<?php do_action( 'bp_after_blog_home' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php get_sidebar() ?>

<?php get_footer() ?>
