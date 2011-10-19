<div id="featured">
	<?php query_posts('post_type=post&post_status=publish&showposts=1'); ?>
	<?php $featured = new WP_Query('showposts=1&category_name=featured'); ?>
	<?php $nofeature = new WP_Query('showposts=1&category_name=post'); ?>
	<?php if ( $featured->have_posts() ) : ?>
		<div class="heading"><?php _e('Featured', 'groupblog') ?></div>
		<?php while ( $featured->have_posts() ) : $featured->the_post(); ?>	
			<div class="post" id="post-<?php the_ID(); ?>">
				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<p class="date"><?php echo get_avatar( get_the_author_meta( 'user_email' ), '16' ); ?><?php the_time('F j, Y') ?> <em><?php _e( 'in', 'buddypress' ) ?> <?php the_category(', ') ?> <?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></em></p>
				<div class="entry">
					<?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
				</div>
				<?php include( locate_template( array( 'groupblog/comments.php' ), false ) ) ?>
			</div>			
		<?php endwhile; ?>
	<?php elseif ( $nofeature->have_posts() ) : ?>
		<div class="heading"><?php _e('Latest Post', 'groupblog') ?></div>
		<?php while ( $nofeature->have_posts() ) : $nofeature->the_post(); ?>
			<div class="post" id="post-<?php the_ID(); ?>">
				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<p class="date"><?php echo get_avatar( get_the_author_meta( 'user_email' ), '16' ); ?><?php the_time('F j, Y') ?> <em><?php _e( 'in', 'buddypress' ) ?> <?php the_category(', ') ?> <?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></em></p>
				<div class="entry">
					<?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
				</div>
				<?php include( locate_template( array( 'groupblog/comments.php' ), false ) ) ?>
			</div>	
		<?php endwhile; ?>
	<?php elseif ( have_posts() ) : ?>
		<div class="heading"><?php _e('Latest Post', 'groupblog') ?></div>
		<?php while ( have_posts() ) : the_post(); ?>
			<div class="post" id="post-<?php the_ID(); ?>">
				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<p class="date"><?php echo get_avatar( get_the_author_meta( 'user_email' ), '16' ); ?><?php the_time('F j, Y') ?> <em><?php _e( 'in', 'buddypress' ) ?> <?php the_category(', ') ?> <?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></em></p>
				<div class="entry">
					<?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
				</div>
				<?php include( locate_template( array( 'groupblog/comments.php' ), false ) ) ?>
			</div>	
		<?php endwhile; ?>
	<?php else : ?>	
		<h2 class="center"><?php _e( 'Not Found', 'buddypress' ) ?></h2>
		<p class="center"><?php _e( 'Sorry, but you are looking for something that isn\'t here.', 'buddypress' ) ?></p>
		<?php locate_template( array( 'searchform.php' ), true ) ?>			
	<?php endif; ?>
</div>

<div id="statuses">
	<?php $statuses = new WP_Query('showposts=1&category_name=status'); ?>
	<?php if ( $statuses->have_posts() ) : ?>
		<span class="heading"><?php _e('Status', 'groupblog') ?></span>
		<?php while ( $statuses->have_posts() ) : $statuses->the_post(); ?>
			<div class="status-content"><?php status_excerpt(); ?></div>
		<?php endwhile; ?>	
	<?php endif; ?>
</div>

<div id="latest">
	<?php $latest = new WP_Query('showposts=3&category_name=post'); ?>
	<?php if ( $latest->have_posts() ) : ?>
		<div class="heading"><?php _e('Latest Writings', 'groupblog') ?></div>
		<ul>
			<?php while ( $latest->have_posts() ) : $latest->the_post(); ?>
				<li id="latest-content"> 
					<div class="post" id="post-<?php the_ID(); ?>">
						<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>
						<?php the_excerpt(); ?>
						<p class="date"><?php echo get_avatar( get_the_author_meta( 'user_email' ), '16' ); ?><?php the_time('F j') ?> <em> <?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></em></p>
					</div>
				</li>
			<?php endwhile; ?>
		</ul>	
	<?php endif; ?>
</div>

<div id="photos">
	<?php $photos = new WP_Query('showposts=4&category_name=photo'); ?>
	<?php if ( $photos->have_posts() ) : ?>
		<div class="heading"><?php _e('Latest Photos', 'groupblog') ?></div>
		<ul>
			<?php while ( $photos->have_posts() ) : $photos->the_post(); ?>
				<li >
					<div class="post" id="post-<?php the_ID(); ?>">
    	   		<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a> <em> <?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></em></h2>
						<a href="<?php echo catch_that_image() ?>" title="<?php the_title(); ?>" class="photos thickbox">
  	      		<img src="<?php echo catch_that_image() ?>" alt="<?php the_title(); ?>" />
    	   		</a>
      		</div>
       	</li>
			<?php endwhile; ?>
     </ul>
	<?php endif; ?>
</div>

<div id="video">	
	<?php $videos = new WP_Query('showposts=1&category_name=video'); ?>
	<?php if ( $videos->have_posts() ) : ?>
		<div class="heading"><?php _e('Latest Video', 'groupblog') ?></div>
		<div id="video-content">
			<?php while ( $videos->have_posts() ) : $videos->the_post(); ?>
				<div class="post" id="post-<?php the_ID(); ?>">
					<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					<p class="date"><?php echo get_avatar( get_the_author_meta( 'user_email' ), '16' ); ?><?php the_time('F j, Y') ?> <em><?php _e( 'in', 'buddypress' ) ?> <?php the_category(', ') ?> <?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></em></p>
					<div class="entry">
						<?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
					</div>
					<?php include( locate_template( array( 'groupblog/comments.php' ), false ) ) ?>
				</div>			
			<?php endwhile; ?>
		</div>
	<?php endif; ?>
</div>	
	