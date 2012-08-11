<?php

/**
 * User Topics Created
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

	<?php bbp_set_query_name( 'bbp_user_profile_topics_created' ); ?>

	<div id="bbp-author-topics-started" class="bbp-author-topics-started">

		<h2 class="entry-title"><?php _e( 'Forum Topics Created', 'bbpress' ); ?></h2>

		<?php if ( bbp_get_user_topics_started() ) :

			bbp_get_template_part( 'bbpress/pagination', 'topics' );
			bbp_get_template_part( 'bbpress/loop',       'topics' );
			bbp_get_template_part( 'bbpress/pagination', 'topics' );

		else : ?>

			<div class="bbp-template-notice"><?php bbp_is_user_home() ? _e( 'You have not created any topics.', 'bbpress' ) : _e( 'This user has not created any topics.', 'bbpress' ); ?></div>

		<?php endif; ?>

	</div><!-- #bbp-author-topics-started -->

	<?php bbp_reset_query_name(); ?>
