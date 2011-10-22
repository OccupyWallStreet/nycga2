<?php
/**
 * @package WordPress
 * @subpackage P2
 */
?>

<?php if ( post_password_required() )
	return;
?>

<?php if ( get_comments_number() > 0 ) : ?>
<ul class="commentlist inlinecomments">
	<?php wp_list_comments( array( 'callback' => 'p2_comments' ) ); ?>
</ul>
<?php endif; ?>