<?php get_header() ?>

	<div id="content">
		<?php do_action( 'bp_before_blog_search' ) ?>

		<div class="content-page" id="blog-search">

			<?php if (have_posts()) : ?>
						<div class="content-box-outer">
				<div class="h3-background">
		
				<?php locate_template( array( '/messages/blog-headers.php' ), true ); ?>
					</div>
					</div>
				<?php while (have_posts()) : the_post(); ?>

					<?php do_action( 'bp_before_blog_post' ) ?>

					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

							<div class="content-box-outer">
									<div class="h3-background">
								<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'bp-scholar' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
									</div></div>

							<div class="content-box-inner">
									<div class="post-meta-data">
												<div class="meta-date"><?php the_time('M j Y') ?> </div>
													<div class="meta-category">	<?php _e( 'in', 'bp-scholar' ) ?> <?php the_category(', ') ?></div>
															<div class="meta-comments"><?php comments_popup_link( __( 'No Comments &#187;', 'bp-scholar' ), __( '1 Comment &#187;', 'bp-scholar' ), __( '% Comments &#187;', 'bp-scholar' ) ); ?></div>
																	<?php if( $bp_existed == 'true' ) { //check if bp existed ?>		
																		<div class="meta-author"><?php echo get_avatar( get_the_author_meta('email') , '17' ); ?><?php printf( __( 'by %s', 'bp-scholar' ), bp_core_get_userlink( $post->post_author ) ) ?></div>
														<?php } else { // if not bp detected..let go normal ?>					
																		<div class="meta-author">	
																		<?php the_author_link(); ?>
																		</div>
														<?php } ?>
									<div class="meta-tag"><?php the_tags( __( 'Tags: ', 'bp-scholar' ), ', ', '<br />'); ?></div>
								
										<div class="clear"></div>
										</div>

							<div class="post-content">

								<div class="entry">
									
												<a href="<?php the_permalink() ?>">
													<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
													<?php the_post_thumbnail(); ?></div><?php } } ?>
												</a>
										<?php the_excerpt(); ?>
											<div class="clear"></div>
								</div>

							</div>

						</div>
	</div>
				

					<?php do_action( 'bp_after_blog_post' ) ?>

				<?php endwhile; ?>

			
				<?php locate_template( array( '/messages/pagination.php' ), true ); ?>

			<?php else : ?>

					<div class="content-box-outer">
			<div class="h3-background">
			
				<?php locate_template( array( '/messages/messages.php' ), true ); ?>
				</div></div>
			<?php endif; ?>

		</div>

		<?php do_action( 'bp_after_blog_search' ) ?>

	</div><!-- #content -->

	<?php get_sidebar(); ?>

<?php get_footer() ?>
