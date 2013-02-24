<?php get_header( 'network' ); ?>

	<div id="content">
		<div class="padder">

			<?php do_action( 'bp_before_directory_forums_content' ); ?>

				<div id="item-header" role="complementary">

					<?php locate_template( array( 'forums/single/forum-header.php' ), true ); ?>

				</div><!-- #item-header -->

				<div id="item-nav">
					<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
						<ul>

							<li>
								<a href="#post-new" class="show-hide-new"><?php _e( 'New Topic', 'network' ); ?></a>
							</li>

							<?php if ( bp_forums_has_directory() ) : ?>

								<li>
									<a href="<?php bp_forums_directory_permalink() ?>"><?php _e( 'Forum Directory', 'network'); ?></a>
								</li>

							<?php endif; ?>

							<?php do_action( 'bp_forums_directory_group_sub_types' ); ?>

							<li id="forums-order-select" class="last filter">

								<label for="forums-order-by"><?php _e( 'Order By:', 'network' ); ?></label>
								<select id="forums-order-by">
									<option value="active"><?php _e( 'Last Active', 'network' ); ?></option>
									<option value="popular"><?php _e( 'Most Posts', 'network' ); ?></option>
									<option value="unreplied"><?php _e( 'Unreplied', 'network' ); ?></option>

									<?php do_action( 'bp_forums_directory_order_options' ); ?>

								</select>
							</li>
						</ul>
					</div>
				</div><!-- #item-nav -->

				<div id="item-body">

					<div id="forums-dir-list" class="forums dir-list" role="main">

						<?php locate_template( array( 'forums/forums-loop.php' ), true ); ?>

					</div>

					<?php do_action( 'bp_directory_forums_content' ); ?>

				</div>
			</div><!-- #new-topic-post -->
		</div><!-- .padder -->

	<?php do_action( 'bp_after_directory_forums_content' ); ?>

<?php get_sidebar( 'network' ); ?>
<?php get_footer( 'network' ); ?>