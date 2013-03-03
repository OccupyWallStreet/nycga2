<?php
/**
 * @package WordPress
 * @subpackage PulsePress
 */
 
 global $post,$pulse_press_options;
?>
<li id="pulse_press-<?php the_ID(); ?>" <?php post_class( get_the_author_meta( 'ID' ) ); ?> >
		<?php if ( !is_page() ) : ?>
			<?php
			
				if( pulse_press_get_option( 'show_anonymous' ) && get_post_custom_values('anonymous') ):
					echo '<img class="avatar avatar-48 photo" src="'.get_template_directory_uri().'/i/anonymous.png"  alt="anonymous" />';
				else:
				printf(
					'<a href="%1$s" title="%2$s">%3$s</a>',
					get_author_posts_url( pulse_press_get_author_id() ),
					sprintf( __( 'Posts by %s', 'pulse_press' ), esc_attr( pulse_press_get_author_name() ) ),
					pulse_press_get_user_avatar( array( 'user_id' => pulse_press_get_author_id(), 'email' => '', 'size' => 48 ) )
				);
				endif;
			?>
		<?php endif; ?>
			<h4 class="post-title">
			<?php if ( !is_page() ) : 
				if( pulse_press_get_option( 'show_anonymous' ) && get_post_custom_values('anonymous') ):
					echo __( 'Anonymous', 'pulse_press' );
				else:
					printf(
					'<a href="%1$s" title="%2$s">%3$s</a>',
					get_author_posts_url( pulse_press_get_author_id() ),
					sprintf( __( 'Posts by %s', 'pulse_press' ), esc_attr( pulse_press_get_author_name() ) ),
					get_the_author()
				);
				endif;
			 endif; // end of not page ?>
			
			<span class="meta">
				<?php if ( !is_page() ) : ?>
					<?php echo pulse_press_date_time_with_microformat(); ?>
				<?php endif; ?>
				
				<span class="actions">
					<?php 
						pulse_press_vote_on_post();
						pulse_press_star_a_post(); 
					if ( ! is_single() ) : 
												 
						 if ( ! post_password_required() && pulse_press_get_option( 'show_reply' )) : ?>
							<?php echo post_reply_link( array( 'before' => '', 'after' => ' | ',  'reply_text' => __( 'Reply', 'pulse_press' ), 'add_below' => 'pulse_press' ), get_the_id() ); ?>
						<?php endif; ?>
						<a href="<?php the_permalink(); ?>" class="permalink" title="go to: <?php echo the_title_attribute(); ?>"><?php _e( 'Permalink', 'pulse_press' ); ?></a> 
					<?php else : 
						 if ( comments_open() && ! post_password_required() && pulse_press_get_option( 'show_reply' ) ) :
							echo post_reply_link( array( 'before' => '', 'after' => '',  'reply_text' => __( 'Reply', 'pulse_press' ), 'add_below' => 'pulse_press' ), get_the_id() ); ?>
						<?php endif; ?>
					<?php endif;?>
					
					<?php if ( current_user_can( 'edit_post', get_the_id() ) ) : ?>
						| <a href="<?php echo ( get_edit_post_link( get_the_id() ) ); ?>" class="edit-post-link" title="<?php _e('edit post', 'pulse_press'); ?>" data-postid="<?php the_ID(); ?>"><?php _e( 'Edit', 'pulse_press' ); ?></a>
					<?php endif; ?> 
					
					
				</span> <!-- end of actions -->
				
			<?php if ( !is_page() ) : ?>
				<span class="tags">
					<?php pulse_press_tags_with_count( '', __( '<br />Tags:' , 'pulse_press' ) .' ', ', ', ' &nbsp;' ); ?>&nbsp;
				</span>
			<?php endif; ?>
			</span>
		</h4>

	<div class="postcontent<?php if ( current_user_can( 'edit_post', get_the_id() ) ) : ?> editarea<?php endif ?>" id="content-<?php the_ID(); ?>">
		
			<?php pulse_press_title(); ?>
			<?php 
				if(in_category('post',$post) || is_single()): 
					the_content( __( "Continue reading ", 'pulse_press' ) . the_title('', '', false)  );
				else:
					the_excerpt(); 
					if(get_the_excerpt() != get_the_content()):
					?>
					<span class="read-more"><a href="<?php the_permalink(); ?>"><?php _e("Continue reading ", 'pulse_press'); ?> <?php the_title(); ?> </a></span>
				<?php
					endif;
				endif;
				
				if( (pulse_press_get_option( 'show_categories') || is_single()) && !in_category('post') ): ?>
						<span class="categories-list"> <?php _e("Posted in", 'pulse_press'); ?>: <?php the_category(', '); ?> </span>
				<?php 
				endif;
				
				?>
				
	</div>

	<?php if ( get_comments_number() > 0 && ! post_password_required() ) : ?>
		<div class="discussion" style="display: none">
			<p>
				<?php pulse_press_discussion_links(); ?>
				<a href="#" class="show_comments" id="pulse_press-toggle-<?php the_ID(); ?>"><?php _e( 'Toggle Comments', 'pulse_press' ); ?></a>
			</p>
		</div>
	<?php endif; ?>
	
	<div class="bottom_of_entry">&nbsp;</div>
	<?php wp_link_pages(); ?>
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
</li>