<?php
/**
* Template Name: Edit Answer Template
*
* @package Thesis
*/

// $loopty_loop = new qa_loops;

add_action('thesis_hook_before_content', 'do_qa_edit');
function do_qa_edit(){
  ?>
  <div class="post_box top">
    <div class="format_text">
      <?php //get_header( 'question' ); ?>
      <div id="qa-page-wrapper">
        <div id="qa_banner"></div>
        <?php the_qa_menu(); ?>

        <?php the_post(); ?>

        <div id="answer-form">
          <h2><?php printf( __( 'Answer for %s', QA_TEXTDOMAIN ), get_question_link( $post->post_parent ) ); ?></h2>
          <?php the_answer_form(); ?>
        </div>

      </div><!--#qa-page-wrapper-->
      <?php //get_sidebar( 'question' ); ?>

      <?php //get_footer( 'question' ); ?>
    </div>
  </div>
  <?php
}

thesis_html_framework();

?>

