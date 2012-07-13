<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

get_header() ?>

	<div id="content">
		<div class="padder">

			<?php do_action( 'bpe_before_member_home_content' ) ?>

			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>
						<?php do_action( 'bp_member_options_nav' ) ?>
					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body">
				<?php do_action( 'bpe_before_member_body' ) ?>

                <div class="item-list-tabs no-ajax events-nav" id="subnav">
                    <ul>
        				<li class="feed"><a href="<?php bpe_event_user_category_feed_links() ?>" title="RSS Feed"><?php _e( 'RSS', 'events' ) ?></a></li>
                        <?php bp_get_options_nav() ?>
						<?php do_action( 'bpe_display_archive_options' ) ?>
                    </ul>
                    <div class="clear"></div>
                </div>

				<?php do_action( 'bpe_before_event_content' ) ?>
                
                <div id="events-dir-list" class="event">
                
					<?php if( bpe_is_member_map() ) : ?>
                        <?php bpe_load_template( 'events/member/map' ); ?>
                
                    <?php elseif( bpe_is_member_calendar() ) : ?>
                        <?php bpe_load_template( 'events/member/calendar' ); ?>
                
                    <?php elseif( bpe_is_member_active() ) : ?>
                        <?php bpe_load_template( 'events/member/active' ); ?>

                    <?php elseif( bpe_is_member_archive() ) : ?>
                        <?php bpe_load_template( 'events/member/archive' ); ?>

                    <?php elseif( bpe_is_member_sale_success() ) : ?>
                        <?php bpe_load_template( 'events/member/success' ); ?>

                    <?php elseif( bpe_is_member_sale_cancel() ) : ?>
                        <?php bpe_load_template( 'events/member/cancel' ); ?>

                    <?php elseif( bpe_is_member_invoices() ) : ?>
                        <?php bpe_load_template( 'events/member/invoices' ); ?>

                    <?php elseif( bpe_is_member_attending() ) : ?>
                        <?php bpe_load_template( 'events/member/attending' ); ?>

                    <?php elseif( bpe_is_member_category() ) : ?>
                        <?php bpe_load_template( 'events/member/category' ); ?>

                    <?php elseif( bpe_is_member_timezone() ) : ?>
                        <?php bpe_load_template( 'events/member/timezone' ); ?>

                    <?php elseif( bpe_is_member_venue() ) : ?>
                        <?php bpe_load_template( 'events/member/venue' ); ?>

                    <?php elseif( bpe_is_member_month() ) : ?>
                        <?php bpe_load_template( 'events/member/month' ); ?>

                    <?php elseif( bpe_is_member_day() ) : ?>
                        <?php bpe_load_template( 'events/member/day' ); ?>
                
                    <?php endif; ?>
                
                    <?php do_action( 'bpe_inside_event_content' ) ?>

                </div><!-- .event -->
                
				<?php do_action( 'bpe_after_member_body' ) ?>
			</div><!-- #item-body -->

			<?php do_action( 'bpe_after_member_home_content' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>