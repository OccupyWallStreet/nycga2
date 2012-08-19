<?php
/**
* Template Name: User Questio Template
*
* @package Thesis
*/

// $loopty_loop = new qa_loops;

add_action('thesis_hook_before_content', 'do_qa_single');
function do_qa_single(){
  ?>
  <div class="post_box top">
    <div class="format_text">
      <?php //get_header( 'question' ); ?>

      <div id="qa-page-wrapper">
        <div id="qa_banner"></div>

        <?php the_qa_menu(); ?>

        <div id="qa-user-box">
          <?php echo get_avatar( get_queried_object_id(), 128 ); ?>
          <?php the_qa_user_rep( get_queried_object_id() ); ?>
        </div>

        <table id="qa-user-details">
          <tr>
            <th><?php _e( 'Name', QA_TEXTDOMAIN ); ?></th>
            <td><strong><?php echo get_queried_object()->display_name; ?></strong></td>
          </tr>
          <tr>
            <th><?php _e( 'Member for', QA_TEXTDOMAIN ); ?></th>
            <td><?php echo human_time_diff( strtotime( get_queried_object()->user_registered ) ); ?></td>
          </tr>
          <tr>
            <th><?php _e( 'Website', QA_TEXTDOMAIN ); ?></th>
            <td><?php echo make_clickable( get_queried_object()->user_url ); ?></td>
          </tr>
        </table>

        <?php
        $answer_query = new WP_Query( array(
        'author' => get_queried_object_id(),
        'post_type' => 'answer',
        'posts_per_page' => 20,
        'update_post_term_cache' => false
        ) );

        $fav_query = new WP_Query( array(
        'post_type' => 'question',
        'meta_key' => '_fav',
        'meta_value' => get_queried_object_id(),
        'posts_per_page' => 20,
        ) );
        ?>

        <div id="qa-user-tabs-wrapper">
          <ul id="qa-user-tabs">
            <li><a href="#qa-user-questions">
              <span id="user-questions-total"><?php echo number_format_i18n( $wp_query->found_posts ); ?></span>
              <?php echo _n( 'Question', 'Questions', $wp_query->found_posts, QA_TEXTDOMAIN ); ?>
            </a></li>

            <li><a href="#qa-user-answers">
              <span id="user-answers-total"><?php echo number_format_i18n( $answer_query->found_posts ); ?></span>
              <?php echo _n( 'Answer', 'Answers', $answer_query->found_posts, QA_TEXTDOMAIN ); ?>
            </a></li>
          </ul>

          <div id="qa-user-questions">
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

                  <?php the_question_tags(); ?>
                  <div class="question-started">
                    <?php the_qa_time( get_the_ID() ); ?>
                  </div>
                </div>
              </div>
              <?php endwhile; ?>
            </div><!--#question-list-->
          </div><!--#qa-user-questions-->

          <div id="qa-user-answers">
            <ul>
              <?php
              while ( $answer_query->have_posts() ) : $answer_query->the_post();
              list( $up, $down ) = qa_get_votes( get_the_ID() );

              echo '<li>';
              echo "<div class='answer-score'>";
              echo number_format_i18n( $up - $down );
              echo "</div> ";
              the_answer_link( get_the_ID() );
              echo '</li>';
              endwhile;
              ?>
            </ul>
          </div><!--#qa-user-answers-->

        </div><!--#qa-user-tabs-wrapper-->

      </div><!--#qa-page-wrapper-->

      <?php //get_sidebar( 'question' ); ?>

      <?php //get_footer( 'question' ); ?>
    </div>
  </div>

  <?php

}

thesis_html_framework();
?>
