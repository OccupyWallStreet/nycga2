<?php
/**
 * @package WordPress
 * @subpackage PulsePress
 */
?>
<?php get_header(); ?>

<div class="sleeve_main">

	<div id="main">
		<?php pulse_press_breadcrumbs(); ?>
		<?php if ( have_posts() ) : ?>
			
			<?php while ( have_posts() ) : the_post(); ?>
				<h1 id="page-title"><?php the_title(); ?></h1>
				<?php the_content(); ?>
				<div class="bottom_of_entry">&nbsp;</div>
				<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'pulse_press' ), 'after' => '</div>' ) ); ?>
				<?php edit_post_link( __( 'Edit', 'pulse_press' ), '<span class="edit-link">', '</span>' ); ?>
				
				
			
				<?php if ( ! pulse_press_is_ajax_request() ) : ?>
						
					<?php comments_template(); ?>
					<?php $pc = 0; ?>
					<?php if ( pulse_press_show_comment_form() && $pc == 0 && ! post_password_required() ) : ?>
						<?php $pc++; ?>
						<div class="respond-wrap"<?php if ( ! is_singular() ): ?> style="display: none; "<?php endif; ?>>
							<?php
							
								
								$pulse_press_comment_args = array(
									'title_reply' => __( 'Reply', 'pulse_press' ),
									'comment_field' => '<div class="form"><textarea id="comment" class="expand50-100" name="comment" cols="45" rows="3"></textarea></div> <label class="post-error" for="comment" id="commenttext_error"></label>',
									'comment_notes_before' => '<p class="comment-notes">' . ( get_option( 'require_name_email' ) ? sprintf( ' ' . __('Required fields are marked %s','pulse_press'), '<span class="required">*</span>' ) : '' ) . '</p>',
									'comment_notes_after' => sprintf(
										'<span class="progress"><img src="%1$s" alt="%2$s" title="%2$s" /></span>',
										str_replace( WP_CONTENT_DIR, content_url(), locate_template( array( "i/indicator.gif" ) ) ),
										esc_attr( 'Loading...', 'pulse_press' )
									),
									'label_submit' => __( 'Reply', 'pulse_press' ),
									'id_submit' => 'comment-submit',
								);
								if( pulse_press_get_option( 'limit_comments' ) )
								$pulse_press_comment_args['comment_field'] = '<div class="form"><textarea id="comment" class="expand50-100" name="comment" maxlength="140" cols="45" rows="3"></textarea></div> <label class="post-error" for="comment" id="commenttext_error"></label>';
								comment_form( $pulse_press_comment_args );
							?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			<?php endwhile; ?>
		
		<?php endif; ?>
		
		
	</div> <!-- main -->

</div> <!-- sleeve -->

<?php get_footer(); ?>