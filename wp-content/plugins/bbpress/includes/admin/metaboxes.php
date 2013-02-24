<?php

/**
 * bbPress Admin Metaboxes
 *
 * @package bbPress
 * @subpackage Administration
 */

/** Dashboard *****************************************************************/

/**
 * bbPress Dashboard Right Now Widget
 *
 * Adds a dashboard widget with forum statistics
 *
 * @since bbPress (r2770)
 *
 * @uses bbp_get_version() To get the current bbPress version
 * @uses bbp_get_statistics() To get the forum statistics
 * @uses current_user_can() To check if the user is capable of doing things
 * @uses bbp_get_forum_post_type() To get the forum post type
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses bbp_get_reply_post_type() To get the reply post type
 * @uses get_admin_url() To get the administration url
 * @uses add_query_arg() To add custom args to the url
 * @uses do_action() Calls 'bbp_dashboard_widget_right_now_content_table_end'
 *                    below the content table
 * @uses do_action() Calls 'bbp_dashboard_widget_right_now_table_end'
 *                    below the discussion table
 * @uses do_action() Calls 'bbp_dashboard_widget_right_now_discussion_table_end'
 *                    below the discussion table
 * @uses do_action() Calls 'bbp_dashboard_widget_right_now_end' below the widget
 */
function bbp_dashboard_widget_right_now() {

	// Get the statistics and extract them
	extract( bbp_get_statistics(), EXTR_SKIP ); ?>

	<div class="table table_content">

		<p class="sub"><?php _e( 'Discussion', 'bbpress' ); ?></p>

		<table>

			<tr class="first">

				<?php
					$num  = $forum_count;
					$text = _n( 'Forum', 'Forums', $forum_count, 'bbpress' );
					if ( current_user_can( 'publish_forums' ) ) {
						$link = add_query_arg( array( 'post_type' => bbp_get_forum_post_type() ), get_admin_url( null, 'edit.php' ) );
						$num  = '<a href="' . $link . '">' . $num  . '</a>';
						$text = '<a href="' . $link . '">' . $text . '</a>';
					}
				?>

				<td class="first b b-forums"><?php echo $num; ?></td>
				<td class="t forums"><?php echo $text; ?></td>

			</tr>

			<tr>

				<?php
					$num  = $topic_count;
					$text = _n( 'Topic', 'Topics', $topic_count, 'bbpress' );
					if ( current_user_can( 'publish_topics' ) ) {
						$link = add_query_arg( array( 'post_type' => bbp_get_topic_post_type() ), get_admin_url( null, 'edit.php' ) );
						$num  = '<a href="' . $link . '">' . $num  . '</a>';
						$text = '<a href="' . $link . '">' . $text . '</a>';
					}
				?>

				<td class="first b b-topics"><?php echo $num; ?></td>
				<td class="t topics"><?php echo $text; ?></td>

			</tr>

			<tr>

				<?php
					$num  = $reply_count;
					$text = _n( 'Reply', 'Replies', $reply_count, 'bbpress' );
					if ( current_user_can( 'publish_replies' ) ) {
						$link = add_query_arg( array( 'post_type' => bbp_get_reply_post_type() ), get_admin_url( null, 'edit.php' ) );
						$num  = '<a href="' . $link . '">' . $num  . '</a>';
						$text = '<a href="' . $link . '">' . $text . '</a>';
					}
				?>

				<td class="first b b-replies"><?php echo $num; ?></td>
				<td class="t replies"><?php echo $text; ?></td>

			</tr>

			<?php if ( bbp_allow_topic_tags() ) : ?>

				<tr>

					<?php
						$num  = $topic_tag_count;
						$text = _n( 'Topic Tag', 'Topic Tags', $topic_tag_count, 'bbpress' );
						if ( current_user_can( 'manage_topic_tags' ) ) {
							$link = add_query_arg( array( 'taxonomy' => bbp_get_topic_tag_tax_id(), 'post_type' => bbp_get_topic_post_type() ), get_admin_url( null, 'edit-tags.php' ) );
							$num  = '<a href="' . $link . '">' . $num  . '</a>';
							$text = '<a href="' . $link . '">' . $text . '</a>';
						}
					?>

					<td class="first b b-topic_tags"><span class="total-count"><?php echo $num; ?></span></td>
					<td class="t topic_tags"><?php echo $text; ?></td>

				</tr>

			<?php endif; ?>

			<?php do_action( 'bbp_dashboard_widget_right_now_content_table_end' ); ?>

		</table>

	</div>


	<div class="table table_discussion">

		<p class="sub"><?php _e( 'Users &amp; Moderation', 'bbpress' ); ?></p>

		<table>

			<tr class="first">

				<?php
					$num  = $user_count;
					$text = _n( 'User', 'Users', $user_count, 'bbpress' );
					if ( current_user_can( 'edit_users' ) ) {
						$link = get_admin_url( null, 'users.php' );
						$num  = '<a href="' . $link . '">' . $num  . '</a>';
						$text = '<a href="' . $link . '">' . $text . '</a>';
					}
				?>

				<td class="b b-users"><span class="total-count"><?php echo $num; ?></span></td>
				<td class="last t users"><?php echo $text; ?></td>

			</tr>

			<?php if ( isset( $topic_count_hidden ) ) : ?>

				<tr>

					<?php
						$num  = $topic_count_hidden;
						$text = _n( 'Hidden Topic', 'Hidden Topics', $topic_count_hidden, 'bbpress' );
						$link = add_query_arg( array( 'post_type' => bbp_get_topic_post_type() ), get_admin_url( null, 'edit.php' ) );
						if ( '0' != $num ) {
							$link = add_query_arg( array( 'post_status' => bbp_get_spam_status_id() ), $link );
						}
                        $num  = '<a href="' . $link . '" title="' . esc_attr( $hidden_topic_title ) . '">' . $num  . '</a>';
						$text = '<a class="waiting" href="' . $link . '" title="' . esc_attr( $hidden_topic_title ) . '">' . $text . '</a>';
					?>

					<td class="b b-hidden-topics"><?php echo $num; ?></td>
					<td class="last t hidden-replies"><?php echo $text; ?></td>

				</tr>

			<?php endif; ?>

			<?php if ( isset( $reply_count_hidden ) ) : ?>

				<tr>

					<?php
						$num  = $reply_count_hidden;
						$text = _n( 'Hidden Reply', 'Hidden Replies', $reply_count_hidden, 'bbpress' );
						$link = add_query_arg( array( 'post_type' => bbp_get_reply_post_type() ), get_admin_url( null, 'edit.php' ) );
						if ( '0' != $num ) {
							$link = add_query_arg( array( 'post_status' => bbp_get_spam_status_id() ), $link );
						}
                        $num  = '<a href="' . $link . '" title="' . esc_attr( $hidden_reply_title ) . '">' . $num  . '</a>';
						$text = '<a class="waiting" href="' . $link . '" title="' . esc_attr( $hidden_reply_title ) . '">' . $text . '</a>';
					?>

					<td class="b b-hidden-replies"><?php echo $num; ?></td>
					<td class="last t hidden-replies"><?php echo $text; ?></td>

				</tr>

			<?php endif; ?>

			<?php if ( bbp_allow_topic_tags() && isset( $empty_topic_tag_count ) ) : ?>

				<tr>

					<?php
						$num  = $empty_topic_tag_count;
						$text = _n( 'Empty Topic Tag', 'Empty Topic Tags', $empty_topic_tag_count, 'bbpress' );
						$link = add_query_arg( array( 'taxonomy' => bbp_get_topic_tag_tax_id(), 'post_type' => bbp_get_topic_post_type() ), get_admin_url( null, 'edit-tags.php' ) );
						$num  = '<a href="' . $link . '">' . $num  . '</a>';
						$text = '<a class="waiting" href="' . $link . '">' . $text . '</a>';
					?>

					<td class="b b-hidden-topic-tags"><?php echo $num; ?></td>
					<td class="last t hidden-topic-tags"><?php echo $text; ?></td>

				</tr>

			<?php endif; ?>

			<?php do_action( 'bbp_dashboard_widget_right_now_discussion_table_end' ); ?>

		</table>

	</div>

	<?php do_action( 'bbp_dashboard_widget_right_now_table_end' ); ?>

	<div class="versions">

		<span id="wp-version-message">
			<?php printf( __( 'You are using <span class="b">bbPress %s</span>.', 'bbpress' ), bbp_get_version() ); ?>
		</span>

	</div>

	<br class="clear" />

	<?php

	do_action( 'bbp_dashboard_widget_right_now_end' );
}

/** Forums ********************************************************************/

/**
 * Forum metabox
 *
 * The metabox that holds all of the additional forum information
 *
 * @since bbPress (r2744)
 *
 * @uses bbp_is_forum_closed() To check if a forum is closed or not
 * @uses bbp_is_forum_category() To check if a forum is a category or not
 * @uses bbp_is_forum_private() To check if a forum is private or not
 * @uses bbp_dropdown() To show a dropdown of the forums for forum parent
 * @uses do_action() Calls 'bbp_forum_metabox'
 */
function bbp_forum_metabox() {

	// Post ID
	$post_id     = get_the_ID();
	$post_parent = bbp_get_global_post_field( 'post_parent', 'raw'  );
	$menu_order  = bbp_get_global_post_field( 'menu_order',  'edit' );

	/** Type ******************************************************************/

	?>

	<p>
		<strong class="label"><?php _e( 'Type:', 'bbpress' ); ?></strong>
		<label class="screen-reader-text" for="bbp_forum_type_select"><?php _e( 'Type:', 'bbpress' ) ?></label>
		<?php bbp_form_forum_type_dropdown( $post_id ); ?>
	</p>

	<?php

	/** Status ****************************************************************/

	?>

	<p>
		<strong class="label"><?php _e( 'Status:', 'bbpress' ); ?></strong>
		<label class="screen-reader-text" for="bbp_forum_status_select"><?php _e( 'Status:', 'bbpress' ) ?></label>
		<?php bbp_form_forum_status_dropdown( $post_id ); ?>
	</p>

	<?php

	/** Visibility ************************************************************/

	?>

	<p>
		<strong class="label"><?php _e( 'Visibility:', 'bbpress' ); ?></strong>
		<label class="screen-reader-text" for="bbp_forum_visibility_select"><?php _e( 'Visibility:', 'bbpress' ) ?></label>
		<?php bbp_form_forum_visibility_dropdown( $post_id ); ?>
	</p>

	<hr />

	<?php

	/** Parent ****************************************************************/

	?>

	<p>
		<strong class="label"><?php _e( 'Parent:', 'bbpress' ); ?></strong>
		<label class="screen-reader-text" for="parent_id"><?php _e( 'Forum Parent', 'bbpress' ); ?></label>
		<?php bbp_dropdown( array(
			'post_type'          => bbp_get_forum_post_type(),
			'selected'           => $post_parent,
			'child_of'           => '0',
			'numberposts'        => -1,
			'orderby'            => 'title',
			'order'              => 'ASC',
			'walker'             => '',
			'exclude'            => $post_id,

			// Output-related
			'select_id'          => 'parent_id',
			'tab'                => bbp_get_tab_index(),
			'options_only'       => false,
			'show_none'          => __( '&mdash; No parent &mdash;', 'bbpress' ),
			'none_found'         => false,
			'disable_categories' => false,
			'disabled'           => ''
		) ); ?>
	</p>

	<p>
		<strong class="label"><?php _e( 'Order:', 'bbpress' ); ?></strong>
		<label class="screen-reader-text" for="menu_order"><?php _e( 'Forum Order', 'bbpress' ); ?></label>
		<input name="menu_order" type="number" step="1" size="4" id="menu_order" value="<?php echo esc_attr( $menu_order ); ?>" />
	</p>

	<?php
	wp_nonce_field( 'bbp_forum_metabox_save', 'bbp_forum_metabox' );
	do_action( 'bbp_forum_metabox', $post_id );
}

/** Topics ********************************************************************/

/**
 * Topic metabox
 *
 * The metabox that holds all of the additional topic information
 *
 * @since bbPress (r2464)
 *
 * @uses bbp_get_topic_forum_id() To get the topic forum id
 * @uses do_action() Calls 'bbp_topic_metabox'
 */
function bbp_topic_metabox() {

	// Post ID
	$post_id = get_the_ID(); ?>

	<p>
		<strong class="label"><?php _e( 'Type:', 'bbpress' ); ?></strong>
		<label class="screen-reader-text" for="bbp_stick_topic"><?php _e( 'Topic Type', 'bbpress' ); ?></label>
		<?php bbp_topic_type_select( array( 'topic_id' => $post_id ) ); ?>
	</p>

	<p>
		<strong class="label"><?php _e( 'Forum:', 'bbpress' ); ?></strong>
		<label class="screen-reader-text" for="parent_id"><?php _e( 'Forum', 'bbpress' ); ?></label>
		<?php bbp_dropdown( array(
			'post_type'          => bbp_get_forum_post_type(),
			'selected'           => bbp_get_topic_forum_id( $post_id ),
			'child_of'           => '0',
			'numberposts'        => -1,
			'orderby'            => 'title',
			'order'              => 'ASC',
			'walker'             => '',
			'exclude'            => '',

			// Output-related
			'select_id'          => 'parent_id',
			'tab'                => bbp_get_tab_index(),
			'options_only'       => false,
			'show_none'          => __( '&mdash; No parent &mdash;', 'bbpress' ),
			'none_found'         => false,
			'disable_categories' => current_user_can( 'edit_forums' ),
			'disabled'           => ''
		) ); ?>
	</p>

	<?php
	wp_nonce_field( 'bbp_topic_metabox_save', 'bbp_topic_metabox' );
	do_action( 'bbp_topic_metabox', $post_id );
}

/** Replies *******************************************************************/

/**
 * Reply metabox
 *
 * The metabox that holds all of the additional reply information
 *
 * @since bbPress (r2464)
 *
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses do_action() Calls 'bbp_reply_metabox'
 */
function bbp_reply_metabox() {

	// Post ID
	$post_id = get_the_ID();

	// Get some meta
	$reply_topic_id = bbp_get_reply_topic_id( $post_id );
	$reply_forum_id = bbp_get_reply_forum_id( $post_id );

	// Allow individual manipulation of reply forum
	if ( current_user_can( 'edit_others_replies' ) || current_user_can( 'moderate' ) ) : ?>

		<p>
			<strong class="label"><?php _e( 'Forum:', 'bbpress' ); ?></strong>
			<label class="screen-reader-text" for="bbp_forum_id"><?php _e( 'Forum', 'bbpress' ); ?></label>
			<?php bbp_dropdown( array(
				'post_type'          => bbp_get_forum_post_type(),
				'selected'           => $reply_forum_id,
				'child_of'           => '0',
				'numberposts'        => -1,
				'orderby'            => 'title',
				'order'              => 'ASC',
				'walker'             => '',
				'exclude'            => '',

				// Output-related
				'select_id'          => 'bbp_forum_id',
				'tab'                => bbp_get_tab_index(),
				'options_only'       => false,
				'show_none'          => __( '&mdash; No parent &mdash;', 'bbpress' ),
				'none_found'         => false,
				'disable_categories' => current_user_can( 'edit_forums' ),
				'disabled'           => ''
			) ); ?>
		</p>

	<?php endif; ?>

	<p>
		<strong class="label"><?php _e( 'Topic:', 'bbpress' ); ?></strong>
		<label class="screen-reader-text" for="parent_id"><?php _e( 'Topic', 'bbpress' ); ?></label>
		<input name="parent_id" id="bbp_topic_id" type="text" value="<?php echo esc_attr( $reply_topic_id ); ?>" />
	</p>

	<?php
	wp_nonce_field( 'bbp_reply_metabox_save', 'bbp_reply_metabox' );
	do_action( 'bbp_reply_metabox', $post_id );
}

/** Users *********************************************************************/

/**
 * Anonymous user information metabox
 *
 * @since bbPress (r2828)
 *
 * @uses bbp_is_reply_anonymous() To check if reply is anonymous
 * @uses bbp_is_topic_anonymous() To check if topic is anonymous
 * @uses get_the_ID() To get the global post ID
 * @uses get_post_meta() To get the author user information
 */
function bbp_author_metabox() {

	// Post ID
	$post_id = get_the_ID();

	// Show extra bits if topic/reply is anonymous
	if ( bbp_is_reply_anonymous( $post_id ) || bbp_is_topic_anonymous( $post_id ) ) : ?>

		<p>
			<strong class="label"><?php _e( 'Name:', 'bbpress' ); ?></strong>
			<label class="screen-reader-text" for="bbp_anonymous_name"><?php _e( 'Name', 'bbpress' ); ?></label>
			<input type="text" id="bbp_anonymous_name" name="bbp_anonymous_name" value="<?php echo esc_attr( get_post_meta( $post_id, '_bbp_anonymous_name', true ) ); ?>" />
		</p>

		<p>
			<strong class="label"><?php _e( 'Email:', 'bbpress' ); ?></strong>
			<label class="screen-reader-text" for="bbp_anonymous_email"><?php _e( 'Email', 'bbpress' ); ?></label>
			<input type="text" id="bbp_anonymous_email" name="bbp_anonymous_email" value="<?php echo esc_attr( get_post_meta( $post_id, '_bbp_anonymous_email', true ) ); ?>" />
		</p>

		<p>
			<strong class="label"><?php _e( 'Website:', 'bbpress' ); ?></strong>
			<label class="screen-reader-text" for="bbp_anonymous_website"><?php _e( 'Website', 'bbpress' ); ?></label>
			<input type="text" id="bbp_anonymous_website" name="bbp_anonymous_website" value="<?php echo esc_attr( get_post_meta( $post_id, '_bbp_anonymous_website', true ) ); ?>" />
		</p>

	<?php endif; ?>

	<p>
		<strong class="label"><?php _e( 'IP:', 'bbpress' ); ?></strong>
		<label class="screen-reader-text" for="bbp_author_ip_address"><?php _e( 'IP Address', 'bbpress' ); ?></label>
		<input type="text" id="bbp_author_ip_address" name="bbp_author_ip_address" value="<?php echo esc_attr( get_post_meta( $post_id, '_bbp_author_ip', true ) ); ?>" disabled="disabled" />
	</p>

	<?php

	do_action( 'bbp_author_metabox', $post_id );
}
