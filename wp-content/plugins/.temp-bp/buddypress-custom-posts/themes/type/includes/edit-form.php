<?php

global $post_ID, $post, $post_type, $post_type_object, $current_screen, $bp, $current_user, $user_ID;

$type = $bp->active_components[ $bp->current_component ];
$type_object = get_post_type_object( $type );

$user_ID = $current_user->ID;

if ( $post == '' ) {
	$post = get_default_post_to_edit( $post_type, true );
	$post_ID = $post->ID;
} else {
	$post = get_post_to_edit( $post_ID );
}
$post_id = $post_ID;

if ( $bp->current_action == 'create' )
	$action = bp_get_root_domain() . "/" . $bp->current_component . "/create/save/"; 
else 
	$action = get_permalink() . 'edit/save/';

?>
	<form name="post" class = 'edit-form' action="<?php echo $action; ?>" method="post" id="post">

		<!-- Hidden fields for data -->
		<?php wp_nonce_field( 'update-' . $post->post_type . '_' . $post->ID ); ?>
		<input type="hidden" id="post-id" name="post_ID" value="<?php echo (int) $post->ID ?>" />
		<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />
		<input type="hidden" id="post_author" name="post_author" value="<?php echo esc_attr( $post->post_author ); ?>" />
		<input type="hidden" id="post_type" name="post_type" value="<?php echo esc_attr($post->post_type) ?>" />
		<input type="hidden" id="referredby" name="referredby" value="<?php echo esc_url(stripslashes(wp_get_referer())); ?>" />

		<div id = 'post-body' >
			<div id = 'post-body-content' >
			<?php if ( post_type_supports($type, 'title') ) { ?>
				<div id = 'titlediv' >
					<div id = 'titlewrap' >
						<label id = 'post-title' for = 'post_title' ><?php _e('Enter title here') ?></label>
						<input type = 'text' id = 'post_title' name = 'post_title' size = '20' tabindex = '1' value = '<?php echo esc_attr( htmlspecialchars( $post->post_title ) ); ?>' autocomplete = 'off' />
					</div><!-- /#titlewrap -->
				</div><!-- /#titlediv -->

				<div class="inside">
				<?php
					$sample_permalink_html = get_sample_permalink_html($post->ID);
					$shortlink = wp_get_shortlink($post->ID, 'post');

					if ( !empty($shortlink) )
					    $sample_permalink_html .= '<input id="shortlink" type="hidden" value="' . esc_attr($shortlink) . '" /><a href="#" class="button" onclick="prompt(&#39;URL:&#39;, jQuery(\'#shortlink\').val()); return false;">' . __('Get Shortlink') . '</a>';
				?>
				</div><!-- /.inside -->
			<?php } ?>

			<?php if ( post_type_supports($type, 'editor') ) { ?>
				<div id = 'postdiv' class = 'postdiv'>
					<?php the_editor( $post->post_content, 'post-content', 'switch-content', true, 2 ); ?>

				<table id="post-status-info" cellspacing="0"><tbody><tr>
					<td id="wp-word-count"></td>
					<td class="autosave-info">
						<span id="autosave">&nbsp;</span>
						<?php
						if ( 'auto-draft' != $post->post_status ) {
							echo '<span id="last-edit">';
							if ( $last_id = get_post_meta($post_ID, '_edit_last', true) ) {
								$last_user = get_userdata($last_id);
								printf(__('Last edited by %1$s on %2$s at %3$s'), esc_html( $last_user->display_name ), mysql2date(get_option('date_format'), $post->post_modified), mysql2date(get_option('time_format'), $post->post_modified));
							} else {
								printf(__('Last edited on %1$s at %2$s'), mysql2date(get_option('date_format'), $post->post_modified), mysql2date(get_option('time_format'), $post->post_modified));
							}
							echo '</span>';
						} ?>
					</td>
				</tr></tbody></table>

				</div><!-- /#postdiv -->
			<?php } ?>

		<?php
			do_meta_boxes($type, 'normal', $post);
			do_meta_boxes($type, 'advanced', $post);
		?>

			</div><!-- /#post-body-content -->
			<div id = 'bpcp-sidebar'>
				<?php do_meta_boxes($type, 'side', $post); ?>
			</div><!-- /#sidebar -->
		</div><!-- /#post-body -->
	</form><!-- /post_edit_form -->
</div>
