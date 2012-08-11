<?php
/**
* Template Name: Edit Question Template
*
* @package Thesis
*/

$loopty_loop = new qa_loops;

add_action('thesis_hook_before_content', 'do_qa_edit');
function do_qa_edit(){
  ?>
  <div class="post_box top">
    <div class="format_text">
      <?php //get_header( 'question' ); ?>
      <div id="qa-page-wrapper">
        <div id="qa_banner"></div>
        <?php the_qa_menu(); ?>

        <div id="edit-question">
          <?php the_question_form(); ?>
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

