<?php if (is_single()) { ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="author-box">
				<?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
				<p><?php printf( __( 'by %s', 'bp-daily' ), bp_core_get_userlink( $post->post_author ) ) ?></p>
			</div>
			<div class="post-content">
				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'bp-daily' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<p class="date"><?php the_time('F j, Y') ?> <em><?php _e( 'in', 'bp-daily' ) ?> <?php the_category(', ') ?> <?php printf( __( 'by %s', 'bp-daily' ), bp_core_get_userlink( $post->post_author ) ) ?></em></p>
				<div class="entry">
					<?php the_content( __( 'Read the rest of this entry &rarr;', 'bp-daily' ) ); ?>
				</div>
				<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'bp-daily' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'bp-daily' ), __( '1 Comment &#187;', 'bp-daily' ), __( '% Comments &#187;', 'bp-daily' ) ); ?></span></p>
			</div>
		</div>
<?php } else if (is_page()) { ?>
			<h2 class="pagetitle"><?php the_title(); ?></h2>
			<div class="post" id="post-<?php the_ID(); ?>">
				<div class="entry">
					<?php the_content( __( '<p class="serif">Read the rest of this page &rarr;</p>', 'bp-daily' ) ); ?>
					<?php wp_link_pages( array( 'before' => __( '<p><strong>Pages:</strong> ', 'bp-daily' ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<?php edit_post_link( __( 'Edit this entry.', 'bp-daily' ), '<p>', '</p>'); ?>
				</div>
			</div>
<?php } else if (is_tag()) { ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="author-box">
				<?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
				<p><?php printf( __( 'by %s', 'bp-daily' ), bp_core_get_userlink( $post->post_author ) ) ?></p>
			</div>
			<div class="post-content">
				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'bp-daily' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<p class="date"><?php the_time('F j, Y') ?> <em><?php _e( 'in', 'bp-daily' ) ?> <?php the_category(', ') ?> <?php printf( __( 'by %s', 'bp-daily' ), bp_core_get_userlink( $post->post_author ) ) ?></em></p>
				<div class="entry">
								<a href="<?php the_permalink() ?>">
									<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
									<?php the_post_thumbnail(); ?></div><?php } } ?>
								</a>
							<?php the_excerpt(); ?>
				</div>
				<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'bp-daily' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'bp-daily' ), __( '1 Comment &#187;', 'bp-daily' ), __( '% Comments &#187;', 'bp-daily' ) ); ?></span></p>
			</div>
		</div>
<?php } else if ((is_category()) || (is_archive())) { ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="author-box">
				<?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
				<p><?php printf( __( 'by %s', 'bp-daily' ), bp_core_get_userlink( $post->post_author ) ) ?></p>
			</div>
			<div class="post-content">
				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'bp-daily' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<p class="date"><?php the_time('F j, Y') ?> <em><?php _e( 'in', 'bp-daily' ) ?> <?php the_category(', ') ?> <?php printf( __( 'by %s', 'bp-daily' ), bp_core_get_userlink( $post->post_author ) ) ?></em></p>
				<div class="entry">
							<a href="<?php the_permalink() ?>">
								<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
								<?php the_post_thumbnail(); ?></div><?php } } ?>
							</a>
						<?php the_excerpt(); ?>
				</div>
				<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'bp-daily' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'bp-daily' ), __( '1 Comment &#187;', 'bp-daily' ), __( '% Comments &#187;', 'bp-daily' ) ); ?></span></p>
			</div>
		</div>
<?php } else { ?>
	<div class="post" id="post-<?php the_ID(); ?>">
		<div class="author-box">
			<?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
			<p><?php printf( __( 'by %s', 'bp-daily' ), bp_core_get_userlink( $post->post_author ) ) ?></p>
		</div>
		<div class="post-content">
			<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'bp-daily' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
			<p class="date"><?php the_time('F j, Y') ?> <em><?php _e( 'in', 'bp-daily' ) ?> <?php the_category(', ') ?> <?php printf( __( 'by %s', 'bp-daily' ), bp_core_get_userlink( $post->post_author ) ) ?></em></p>
			<div class="entry">
						<a href="<?php the_permalink() ?>">
							<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
							<?php the_post_thumbnail(); ?></div><?php } } ?>
						</a>
				<?php the_excerpt(); ?>
			</div>
			<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'bp-daily' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'bp-daily' ), __( '1 Comment &#187;', 'bp-daily' ), __( '% Comments &#187;', 'bp-daily' ) ); ?></span></p>
		</div>
	</div>
<?php } ?>