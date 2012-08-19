<?php get_header( 'question' ); ?>
<div id="qa-page-wrapper">
	<div id="qa-content-wrapper">
		<?php do_action( 'qa_before_content', 'archive-question' ); ?>
			
		<?php the_qa_error_notice(); ?>
		<?php the_qa_menu(); ?>
			
		<?php if ( !have_posts() ) : ?>
			
			<p><?php $question_ptype = get_post_type_object( 'question' ); echo $question_ptype->labels->not_found; ?></p>
		
		<?php else: ?>
		
			<div id="question-list">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php do_action( 'qa_before_question_loop' ); ?>
				<div class="question">
					<?php do_action( 'qa_before_question' ); ?>
					<div class="question-stats">
						<?php do_action( 'qa_before_question_stats' ); ?>
						<div class="qa-status-icon <?php echo (is_question_answered())?'qa-answered-icon':'qa-unanswered-icon'; ?>"></div>
						<?php the_question_score(); ?>
						<?php the_question_status(); ?>
						<?php do_action( 'qa_after_question_stats' ); ?>
					</div>
					<div class="question-summary">
						<?php do_action( 'qa_before_question_summary' ); ?>
						<h3><?php the_question_link(); ?></h3>
						<?php the_question_tags( '<div class="question-tags">', ' ', '</div>' ); ?>
						<div class="question-started">
							<?php the_qa_time( get_the_ID() ); ?>
							<?php the_qa_user_link( $post->post_author ); ?>
						</div>
						<?php do_action( 'qa_after_question_summary' ); ?>
					</div>
					<?php do_action( 'qa_after_question' ); ?>
				</div>
				<?php do_action( 'qa_after_question_loop' ); ?>
			<?php endwhile; $wp_query->set('posts_per_page', 6); ?>
			</div><!--#question-list-->
			
			<?php the_qa_pagination( ); ?>
			
			<?php do_action( 'qa_after_content', 'archive-question' ); ?>
		
		<?php endif;?>
	</div>
</div><!--#qa-page-wrapper-->
<?php
global $qa_general_settings;

if ( !isset( $qa_general_settings["full_width"] ) || !$qa_general_settings["full_width"] )	
	get_sidebar( 'question' ); 
?>
	
<?php get_footer( 'question' ); ?>
