<?php

/**
 * Forums Loop - Single Forum
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

					<div id="bbp-forum-<?php bbp_forum_id(); ?>" <?php bbp_forum_class(); ?>>

						<?php do_atomic( 'open_entry' ); // fanwood_open_entry ?>
						
						<?php do_action( 'bbp_theme_before_forum_title' ); ?>
						
						<h2 class="entry-title"><a class="bbp-forum-title" href="<?php bbp_forum_permalink(); ?>" title="<?php bbp_forum_title(); ?>"><?php bbp_forum_title(); ?></a></h2>
						
						<?php do_action( 'bbp_theme_after_forum_title' ); ?>
							
						<div class="byline">
							<span class="bbp-forum-topic-count">
								<?php _e( 'Topics:', 'bbpress' ); ?> <?php bbp_forum_topic_count(); ?>
							</span><!-- .bbp-forum-topic-count -->
							<span class="bbp-forum-reply-count">
								<?php _e( 'Replies:', 'bbpress' ); ?> <?php bbp_show_lead_topic() ? bbp_forum_reply_count() : bbp_forum_post_count(); ?>
							</span><!-- .bbp-forum-reply-count -->
							<span class="bbp-forum-freshness">
								<?php do_action( 'bbp_theme_before_forum_freshness_link' ); ?>
								<?php _e( 'Freshness:', 'bbpress' ); ?> <?php bbp_forum_freshness_link(); ?>
								<?php do_action( 'bbp_theme_after_forum_freshness_link' ); ?>
							</span><!-- .bbp-forum-freshness -->
							<span class="bbp-topic-freshness-author">
								<?php do_action( 'bbp_theme_before_topic_author' ); ?>
								<?php _e( 'Latest by:', 'bbpress' ); ?><?php bbp_author_link( array( 'post_id' => bbp_get_forum_last_active_id(), 'size' => 14 ) ); ?>
								<?php do_action( 'bbp_theme_after_topic_author' ); ?>
							</span>
						</div><!-- .byline -->

						<div class="entry-content">
							<?php do_action( 'bbp_theme_before_forum_sub_forums' ); ?>
							<?php bbp_list_forums(); ?>
							<?php do_action( 'bbp_theme_after_forum_sub_forums' ); ?>
								
							<?php do_action( 'bbp_theme_before_forum_description' ); ?>
							<?php the_content(); ?>
							<?php do_action( 'bbp_theme_after_forum_description' ); ?>
						</div><!-- .entry--content -->

						<?php do_atomic( 'close_entry' ); // fanwood_close_entry ?>

					</div><!-- .hentry -->
