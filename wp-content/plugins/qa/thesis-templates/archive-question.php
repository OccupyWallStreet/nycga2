<?php
/**
* Template Name: Archive Question Template
*
* @package Thesis
*/

$loopty_loop = new qa_loops;

add_action('thesis_hook_before_content', 'do_qa_archive');
function do_qa_archive(){
  ?>
  <div class="post_box top">
    <div class="format_text">
      <?php //get_header( 'question' ); ?>

      <div id="qa-page-wrapper">
        <div id="qa_banner"></div>
        <?php the_qa_error_notice(); ?>
        <?php the_qa_menu(); ?>

        <?php if ( !have_posts() ) : ?>

        <p><?php $question_ptype = get_post_type_object( 'question' ); echo $question_ptype->labels->not_found; ?></p>

        <?php else: ?>

        <div id="question-list">
          <?php while ( have_posts() ) : the_post(); ?>
          <div class="question">
            <div class="question-stats">
              <?php the_question_score(); ?>
              <?php the_question_status(); ?>
            </div>
            <div class="question-summary">
              <h3><?php the_question_link(); ?></h3>
              <div class="question-excerpt">
                <?php the_excerpt(); ?>
              </div>
              <?php the_question_tags( '<div class="question-tags">', ' ', '</div>' ); ?>
              <div class="question-started">
                <?php the_qa_time( get_the_ID() ); ?>
                <?php //the_qa_user_link( $post->post_author ); ?>
              </div>
              <?php the_qa_author_box( get_the_ID() ); ?>
            </div>
          </div>
          <?php endwhile; ?>
        </div><!--#question-list-->

        <?php the_qa_pagination(); ?>

        <?php endif; ?>

      </div><!--#qa-page-wrapper-->

      <?php //get_sidebar( 'question' ); ?>

      <?php //get_footer( 'question' ); ?>
    </div>
  </div>
  <?php
}

thesis_html_framework();
?>