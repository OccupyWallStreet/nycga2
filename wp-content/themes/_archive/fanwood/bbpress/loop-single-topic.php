<?php

/**
 * Topics Loop - Single
 *
 * @package bbPress
 * @subpackage Theme
 */

?>
					<li id="topic-<?php bbp_topic_id(); ?>" <?php bbp_topic_class(); ?>>

						<?php do_atomic( 'open_entry' ); // fanwood_open_entry ?>
							
						<?php do_action( 'bbp_theme_before_topic_title' ); ?>

						<h2 class="entry-title">
							<?php if ( bbp_is_user_home() ) : ?>

								<?php if ( bbp_is_favorites() ) : ?>

										<?php do_action( 'bbp_theme_before_topic_favorites_action' ); ?>

										<?php bbp_user_favorites_link( array( 'mid' => '+', 'post' => '' ), array( 'pre' => '', 'mid' => '&times;', 'post' => '' ) ); ?>

										<?php do_action( 'bbp_theme_after_topic_favorites_action' ); ?>

								<?php elseif ( bbp_is_subscriptions() ) : ?>

										<?php do_action( 'bbp_theme_before_topic_subscription_action' ); ?>

										<?php bbp_user_subscribe_link( array( 'before' => '', 'subscribe' => '+', 'unsubscribe' => '&times;' ) ); ?>

										<?php do_action( 'bbp_theme_after_topic_subscription_action' ); ?>

								<?php endif; ?>

							<?php endif; ?>
							<a href="<?php bbp_topic_permalink(); ?>" title="<?php bbp_topic_title(); ?>"><?php bbp_topic_title(); ?></a>
						</h2>

						<?php do_action( 'bbp_theme_after_topic_title' ); ?>

						<div class="byline">
							<span class="bbp-topic-reply-count">
								<?php _e( 'Replies:', 'bbpress' ); ?> <?php bbp_show_lead_topic() ? bbp_topic_reply_count() : bbp_topic_post_count(); ?>
							</span><!-- .bbp-topic-reply-count -->
							<span class="bbp-topic-freshness">
								<?php do_action( 'bbp_theme_before_topic_freshness_link' ); ?>
								<?php _e( 'Freshness:', 'bbpress' ); ?> <?php bbp_topic_freshness_link(); ?>
								<?php do_action( 'bbp_theme_after_topic_freshness_link' ); ?>
							</span><!-- .bbp-topic-freshness -->
							<span class="bbp-topic-freshness-author">
								<?php do_action( 'bbp_theme_before_topic_freshness_author' ); ?>
								<?php _e( 'Latest by:', 'bbpress' ); ?> <?php bbp_author_link( array( 'post_id' => bbp_get_topic_last_active_id(), 'type' => 'name' ) ); ?>
								<?php do_action( 'bbp_theme_after_topic_freshness_author' ); ?>
							</span>
							
							<?php if ( !bbp_is_single_forum() || ( bbp_get_topic_forum_id() != bbp_get_forum_id() ) ) : ?>

								<?php do_action( 'bbp_theme_before_topic_started_in' ); ?>

								<span class="bbp-topic-started-in"><?php printf( __( '<a href="%1$s">%2$s</a>', 'bbpress' ), bbp_get_forum_permalink( bbp_get_topic_forum_id() ), bbp_get_forum_title( bbp_get_topic_forum_id() ) ); ?></span>

								<?php do_action( 'bbp_theme_after_topic_started_in' ); ?>

							<?php endif; ?>
						</div><!-- .byline -->
						
						<?php bbp_topic_pagination(); ?>

						<?php do_atomic( 'close_entry' ); // fanwood_close_entry ?>

					</li><!-- .hentry -->