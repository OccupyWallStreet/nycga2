<?php
/**
 * Product reviews template
 *
 * DISCLAIMER
 *
 * Do not edit or add directly to this file if you wish to upgrade Jigoshop to newer
 * versions in the future. If you wish to customise Jigoshop core for your needs,
 * please use our GitHub repository to publish essential changes for consideration.
 *
 * @package		Jigoshop
 * @category	Catalog
 * @author		Jigowatt
 * @copyright	Copyright (c) 2011-2012 Jigowatt Ltd.
 * @license		http://jigoshop.com/license/commercial-edition
 */
 ?>

<?php if ( comments_open() ) : ?>

	<div id="reviews">
	
	<?php

	echo '<div id="comments">';

	$count = $wpdb->get_var("
		SELECT COUNT(meta_value) FROM $wpdb->commentmeta
		LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
		WHERE meta_key = 'rating'
		AND comment_post_ID = $post->ID
		AND comment_approved = '1'
		AND meta_value > 0
	");

	$rating = $wpdb->get_var("
		SELECT SUM(meta_value) FROM $wpdb->commentmeta
		LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
		WHERE meta_key = 'rating'
		AND comment_post_ID = $post->ID
		AND comment_approved = '1'
	");

	if ( $count>0 ) :

		$average = number_format($rating / $count, 2);

		echo '<div class="hreview-aggregate">';

		echo '<div class="star-rating" title="'.sprintf(__('Rated %s out of 5', 'jigoshop'),$average).'"><span style="width:'.($average*16).'px"><span class="rating">'.$average.'</span> '.__('out of 5', 'jigoshop').'</span></div>';

		echo '<h2>'.sprintf( _n('%s Review', '%s reviews', $count, 'jigoshop'), '<span class="count">'.$count.'</span>' ).'</h2>';

		echo '</div>';

	else :
		echo '<h2>'.__('Reviews', 'jigoshop').'</h2>';
	endif;

	$title_reply = '';

	if ( have_comments() ) :

		echo '<ol class="commentlist">';

		wp_list_comments( array( 'callback' => 'jigoshop_comments' ) );

		echo '</ol>';

		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			<div class="navigation comment-pagination">
				<?php paginate_comments_links(); ?>
			</div>
		<?php endif;

		echo '<p class="add_review"><a href="#review_form" class="inline show_review_form button">'.__('Add Review', 'jigoshop').'</a></p>';

		$title_reply = __('Add a review', 'jigoshop');

	else :

		$title_reply = __('Be the first to review ', 'jigoshop').'&ldquo;'.$post->post_title.'&rdquo;';

		echo '<p>'.__('There are no reviews yet, would you like to <a href="#review_form" class="inline show_review_form">submit yours</a>?', 'jigoshop').'</p>';

	endif;

	$commenter = wp_get_current_commenter();

	echo '</div><div id="review_form_wrapper"><div id="review_form">';

	comment_form(array(
		'title_reply' => $title_reply,
		'comment_notes_before' => '',
		'comment_notes_after' => '',
		'fields' => array(
			'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'jigoshop' ) . '</label> ' . '<span class="required">*</span>' .
			            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></p>',
			'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'jigoshop' ) . '</label> ' . '<span class="required">*</span>' .
			            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></p>',
		),
		'label_submit' => __('Submit Review', 'jigoshop'),
		'logged_in_as' => '',
		'comment_field' => '
			<p class="comment-form-rating"><label for="rating">' . __('Rating', 'jigoshop') .'</label><select name="rating" id="rating">
				<option value="">'.__('Rate...','jigoshop').'</option>
				<option value="5">'.__('Perfect','jigoshop').'</option>
				<option value="4">'.__('Good','jigoshop').'</option>
				<option value="3">'.__('Average','jigoshop').'</option>
				<option value="2">'.__('Not that bad','jigoshop').'</option>
				<option value="1">'.__('Very Poor','jigoshop').'</option>
			</select></p>
			<p class="comment-form-comment"><label for="comment">' . _x( 'Your Review', 'noun', 'jigoshop' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>'
			. jigoshop::nonce_field('comment_rating', true, false)
	));

	echo '</div></div>';

?>

	</div><!-- #reviews -->
	
	<script type="text/javascript">
	/* <![CDATA[ */
		jQuery(function(){
			jQuery('#review_form_wrapper').hide();
			if (params.load_fancybox) {
				jQuery('a.show_review_form').fancybox({
					'transitionIn'	:	'fade',
					'transitionOut'	:	'fade',
					'speedIn'		:	600,
					'speedOut'		:	200,
					'overlayShow'	:	true
				});
			}
		});
	/* ]]> */
	</script>

<?php endif; ?>