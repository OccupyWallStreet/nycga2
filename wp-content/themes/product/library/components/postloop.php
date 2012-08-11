<?php if (is_single()) { ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="post-content-wp">
				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'product' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<p class="date"><span class="byline"><?php the_time('M j Y') ?> <?php _e( 'in', 'product' ) ?> <?php the_category(', ') ?> <em><?php _e( 'by ', 'product' ) ?><?php the_author_link();  ?></em></span></p>
				<div class="entry">
					<?php the_content(); ?>
				</div>
				<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'product' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'product' ), __( '1 Comment &#187;', 'product' ), __( '% Comments &#187;', 'product' ) ); ?></span></p>
			</div>
		</div>
<?php } else if (is_page()) { ?>
			<h2 class="pagetitle"><?php the_title(); ?></h2>
			<div class="post" id="post-<?php the_ID(); ?>">
				<div class="entry">
					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => __( '<p><strong>Pages:</strong> ', 'product' ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<?php edit_post_link( __( 'Edit this entry.', 'product' ), '<p>', '</p>'); ?>
				</div>
			</div>
<?php } else if (is_tag()) { ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="post-content-wp">
				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'product' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					<p class="date"><span class="byline"><?php the_time('M j Y') ?> <?php _e( 'in', 'product' ) ?> <?php the_category(', ') ?> <em><?php _e( 'by ', 'product' ) ?><?php the_author_link();  ?></em></span></p>
						<div class="entry">
							
										<a href="<?php the_permalink() ?>">
											<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
											<?php the_post_thumbnail(); ?></div><?php } } ?>
										</a>
							<?php the_excerpt(); ?>
						</div>
				<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'product' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'product' ), __( '1 Comment &#187;', 'product' ), __( '% Comments &#187;', 'product' ) ); ?></span></p>
			</div>
		</div>
<?php } else if ((is_category()) || (is_archive())) { ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="post-content-wp">
				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'product' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					<p class="date"><span class="byline"><?php the_time('M j Y') ?> <?php _e( 'in', 'product' ) ?> <?php the_category(', ') ?> <em><?php _e( 'by ', 'product' ) ?><?php the_author_link();  ?></em></span></p>
						<div class="entry">
							
										<a href="<?php the_permalink() ?>">
											<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
											<?php the_post_thumbnail(); ?></div><?php } } ?>
										</a>
							<?php the_excerpt(); ?>
						</div>
				<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'product' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'product' ), __( '1 Comment &#187;', 'product' ), __( '% Comments &#187;', 'product' ) ); ?></span></p>
			</div>
		</div>
<?php } else { ?>
	<div class="post" id="post-<?php the_ID(); ?>">
		<div class="post-content-wp">
			<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'product' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<p class="date"><span class="byline"><?php the_time('M j Y') ?> <?php _e( 'in', 'product' ) ?> <?php the_category(', ') ?> <em><?php _e( 'by ', 'product' ) ?><?php the_author_link();  ?></em></span></p>
					<div class="entry">
						
									<a href="<?php the_permalink() ?>">
										<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
										<?php the_post_thumbnail(); ?></div><?php } } ?>
									</a>
						<?php the_excerpt(); ?>
					</div>
			<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'product' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'product' ), __( '1 Comment &#187;', 'product' ), __( '% Comments &#187;', 'product' ) ); ?></span></p>
		</div>
	</div>
<?php } ?>