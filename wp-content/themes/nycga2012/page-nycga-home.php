<!-- index.php -->

	<?php get_header() ?>

	<?php locate_template( array( 'leftsidebar.php' ), true ); ?>	

	<div id="content" class="grid_14">

	<?php do_action( 'template_notices' ) ?>

	<div class="page" id="blog-latest" role="main">
		
		<div id="homapgeFeatures" class="carrousel clearfix">
			<ul class="gallery clearfix">
			<?php if ( have_posts() ) : ?>
			<?php bp_dtheme_content_nav( 'nav-above' ); ?>
			<?php
				$query = 'category_name=homepage-featured&posts_per_page=5';
				$my_query = new WP_Query( $query );
		  	while ( $my_query->have_posts() ) : $my_query->the_post();
			?>
			<!-- check if the post has a Post Thumbnail assigned to it. Otherwise skip it. -->
				<?php if ( has_post_thumbnail() ):?> 
				<li class='post-content'>
					<?php do_action( 'bp_before_blog_post' ) ?>
<!-- we should give attribution somehow but the featurebox isn't where, maybe a little caption -->
					<a class="imageWrapLink" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'grid14', array( 'class'=>'post-image') );?></a>
					<div class="content">
						<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
						<p><?php the_content();?></p>
						<p class="date"><?php the_date();?></p>
					</div>
				</li><!-- end .post-content -->
				<?php endif;?>
				<?php endwhile; ?>	
			</ul><!-- end .gallery -->
			<ul class="nav">
		  	<?php while ( $my_query->have_posts() ) : $my_query->the_post();?>
				<li>
					<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
				</li>
				<?endwhile;?>
			</ul>
		</div><!-- end .carrousel -->

		<?php bp_dtheme_content_nav( 'nav-below' ); ?>
		<?php else : ?>
			<h2 class="center"><?php _e( 'Not Found', 'buddypress' ) ?></h2>
			<p class="center"><?php _e( 'Sorry, but you are looking for something that isn\'t here.', 'buddypress' ) ?></p>
			<?php get_search_form() ?>
		<?php endif; ?>
	</div>

	<?php do_action( 'bp_after_blog_home' ) ?>

	</div><!-- #content -->

	<?php get_sidebar() ?>

<?php get_footer() ?>
