<?php
/**
 * Loop Error Template
 *
 * Displays an error message when no posts are found.
 *
 * @package Fanwood
 * @subpackage Template
 */
?>
	<li id="post-0" class="<?php hybrid_entry_class(); ?>">

		<div class="entry-summary">

			<p><?php _e( 'Apologies, but no entries were found.', 'fanwood' ); ?></p>

		</div><!-- .entry-summary -->

	</li><!-- .hentry .error -->