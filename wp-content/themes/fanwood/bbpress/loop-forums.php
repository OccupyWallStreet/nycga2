<?php

/**
 * Forums Loop
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

				<?php do_action( 'bbp_template_before_forums_loop' ); ?>
				
				<?php while ( bbp_forums() ) : bbp_the_forum(); ?>
				
				<?php bbp_get_template_part( 'bbpress/loop', 'single-forum' ); ?>
						
				<?php endwhile; ?>
				
				<?php do_action( 'bbp_template_after_forums_loop' ); ?>
