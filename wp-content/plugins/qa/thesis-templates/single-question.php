<?php
/**
* Template Name: Single Question Template
*
* @package Thesis
*/

$loopty_loop = new qa_loops;

add_action('thesis_hook_before_content', 'do_qa_single');
function do_qa_single(){
  ?>
  <div class="post_box top">
    <div class="format_text">
      <?php //get_header( 'question' ); ?>

      <div id="qa-page-wrapper">
        <div id="qa_banner"></div>
        <?php the_qa_menu(); ?>

        <?php the_post(); ?>

        <?php if ( $user_ID == 0 || current_user_can( 'read_questions', 0 ) ) { ?>
        <div id="single-question">
          <h1><?php the_title(); ?></h1>
          <div id="single-question-container">
            <?php the_question_voting(); ?>
            <div id="question-body">
              <div id="question-content"><?php the_content(); ?></div>
              <?php the_question_category(  __( 'Category:', QA_TEXTDOMAIN ) . ' <span class="question-category">', '', '</span>' ); ?>
              <?php the_question_tags( __( 'Tags:', QA_TEXTDOMAIN ) . ' <span class="question-tags">', ' ', '</span>' ); ?>
              <span id="qa-lastaction"><?php _e( 'asked', QA_TEXTDOMAIN ); ?> <?php the_qa_time( get_the_ID() ); ?></span>

              <div class="question-meta">
                <?php the_qa_action_links( get_the_ID() ); ?>
                <?php the_qa_author_box( get_the_ID() ); ?>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>

        <?php if ( ($user_ID == 0 || current_user_can( 'read_answers', 0 )) && is_question_answered() ) { ?>
        <div id="answer-list">
          <h2><?php the_answer_count(); ?></h2>
          <?php the_answer_list(); ?>
        </div>
        <?php } ?>
        <?php if ( $user_ID == 0 || current_user_can( 'publish_answers', 0 ) ) { ?>
        <div id="edit-answer">
          <h2><?php _e( 'Your Answer', QA_TEXTDOMAIN ); ?></h2>
          <?php the_answer_form(); ?>
        </div>
        <?php } ?>

        <p><?php the_question_subscription(); ?></p>

      </div><!--#qa-page-wrapper-->

      <?php //get_sidebar( 'question' ); ?>

      <?php //get_footer( 'question' ); ?>
    </div>
  </div>

  <?php
}

thesis_html_framework();
?>
