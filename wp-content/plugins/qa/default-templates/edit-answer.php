<?php get_header( 'question' ); ?>

<div id="qa-page-wrapper">
	<div id="qa-content-wrapper">
	<?php do_action( 'qa_before_content', 'edit-answer' ); ?>
	
	<?php the_qa_menu(); ?>
	
	<?php wp_reset_postdata(); ?>
	
	<div id="answer-form">
		<h2><?php printf( __( 'Answer for %s', QA_TEXTDOMAIN ), get_question_link( $post->post_parent ) ); ?></h2>
		<?php the_answer_form(); ?>
	</div>
	
	<?php do_action( 'qa_after_content', 'edit-answer' ); ?>
	</div>
</div><!--#qa-page-wrapper-->

<?php 
global $qa_general_settings;

if ( !isset( $qa_general_settings["full_width"] ) || !$qa_general_settings["full_width"] )	
	get_sidebar( 'question' ); 
?>

<?php get_footer( 'question' ); ?>

