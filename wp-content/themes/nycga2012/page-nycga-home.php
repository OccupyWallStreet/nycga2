<?php
/*
Template Name: Home Page
*/
?>

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
					<?php $photoCredit = get_post_meta( $post->ID, 'photo_credit', true );?>
					<?=$photoCredit;?>
<!-- we should give attribution somehow but the featurebox isn't where, maybe a little caption -->
					<a class="imageWrapLink" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'grid14', array( 'class'=>'post-image') );?></a>
					<div class="content">
						<p class="small"><?php the_date();?> by <a href="<?php the_author_link();?>"><?php the_author();?></a> <?php if( $photoCredit ):?>photo by <?php echo $photoCredit;?><?endif;?></p>
						<h3 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
						<p>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>">
							<?php the_content();?>
							</a>
						</p>
					</div>
				</li><!-- end .post-content -->
				<?php endif;?>
				<?php endwhile; ?>	
			</ul><!-- end .gallery -->
			<ul class="nav">
		  	<?php while ( $my_query->have_posts() ) : $my_query->the_post();?>
				<li>
					<a title="<?php the_title();?>" class="tipTipActuator" href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title();?></a>
				</li>
				<?endwhile;?>
			</ul>
		</div><!-- end .carrousel -->
		<?php else : ?>
			<h2 class="center"><?php _e( 'Not Found', 'buddypress' ) ?></h2>
			<p class="center"><?php _e( 'Sorry, but you are looking for something that isn\'t here.', 'buddypress' ) ?></p>
			<?php get_search_form() ?>
		<?php endif; ?>
	</div>


			<div id="CommHub" class="clearfix">
				<ul class="clearfix">
				<?php if ( have_posts() ) : ?>
				<?php
					$query = 'category_name=commhub&posts_per_page=8';
					$my_query = new WP_Query( $query );
					$i = 0;
			  	while ( $my_query->have_posts() ) : $my_query->the_post();
					$i++;
					$postCount = $my_query->post_count;
				?>
				<!-- check if the post has a Post Thumbnail assigned to it. Otherwise skip it. -->
					<li class="post-tout grid_5 <?php if($i==1):?>alpha<?php elseif($i==$postCount):?>omega<?php endif;?>">
						<?php $photoCredit = get_post_meta( $post->ID, 'photo_credit', true );?>
	<!-- we should give attribution somehow but the featurebox isn't where, maybe a little caption -->
						<div class="toutHeader">
							<?php if ( has_post_thumbnail() ):?> 
							<a class="imageWrapLink" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'grid4', array( 'class'=>'post-image') );?></a>
							<?php endif;?>
							
							<div class="titleBlock">
								<p class="small transBg"><?php the_date();?> by <a href="<?php the_author_link();?>"><?php the_author();?></a> <?php if( $photoCredit ):?>photo by <?php echo $photoCredit;?><?endif;?></p>
								<h5 class="posttitle transBg"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h5>
							</div>
						</div>
						<p>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>">
							<?php the_excerpt();?>
							</a>
						</p>
					</li><!-- end .post-content -->
					<?php endwhile; ?>
					<?php endif;?>
				</ul><!-- end .gallery -->
			</div><!-- end .carrousel -->



	<?php do_action( 'bp_after_blog_home' ) ?>

	</div><!-- #content -->

	<?php get_sidebar(); ?>
	<?php get_footer(); ?>

