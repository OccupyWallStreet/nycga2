<?php
global $wpmudev_video_pages;

$wpmudev_video_pages = array(
	'dashboard' => array('dashboard', 'admin-bar', 'quickpress', 'change-password'),
	'post' => array('add-new-post', 'the-toolbar', 'edit-text', 'add-paragraph', 'add-heading', 'hyperlinks', 'lists', 'paste-from-word', 'oEmbed', 'excerpt', 'add-image-from-pc', 'add-image-from-media-library', 'add-image-from-url', 'edit-image', 'replace-image', 'delete-image', 'image-editor', 'image-editor-crop-and-scale', 'image-editor-rotate-flip-undo-redo', 'featured-image'),
	'edit-post' => array('add-new-post', 'trash-post', 'restore-post'),
	'page' => array('add-new-page', 'the-toolbar', 'edit-text', 'add-paragraph', 'add-heading', 'hyperlinks', 'lists', 'paste-from-word', 'oEmbed', 'add-image-from-pc', 'add-image-from-media-library', 'add-image-from-url', 'edit-image', 'replace-image', 'delete-image', 'image-editor', 'image-editor-crop-and-scale', 'image-editor-rotate-flip-undo-redo'),
	'edit-page' => array('add-new-page', 'trash-page', 'restore-page'),
	'widgets' => array('widgets'),
	'nav-menus' => array('menus'),
	'themes' => array('change-theme'),
	'profile' => array('change-password'),
	'link-manager' => array('add-links'),
	'link' => array('add-links'),
	'edit-link_category' => array('link-categories'),
	'edit-post_tag' => array('tags'),
	'edit-category' => array('categories'),
	'upload' => array('media-library', 'image-editor', 'image-editor-crop-and-scale', 'image-editor-rotate-flip-undo-redo'),
	'media' => array('add-media'),
	'edit-comments' => array('comments')
);

add_filter( 'contextual_help', 'wpmudev_vids_help', 10, 3 );

function wpmudev_vids_help($old_help, $screen_id, $screen) {
	global $wpmudev_video_pages, $wpmudev_vids, $wp_version;
	
	if ( isset($wpmudev_video_pages[$screen_id]) ) {
		$hidden = $wpmudev_vids->get_setting('hide');
		$contextual_help = '<div id="poststuff" class="metabox-holder">';
		foreach ($wpmudev_video_pages[$screen_id] as $video) {
			//remove any hidden videos from the list
			if (isset($hidden[$video]))
				continue;
				
			$contextual_help .= '<div class="postbox" style="width: 500px;float: left;margin-right: 10px;">
				<h3 class="hndle"><span>' . esc_attr($wpmudev_vids->video_list[$video]) . '</span></h3>
				<div class="inside">
					<iframe src="' .(is_ssl() ? 'https': 'http'). '://premium.wpmudev.org/video/'.$video.'" frameborder="0" height="325" width="480"></iframe>
				</div>
			</div>';
		}
		$contextual_help .= '</div><div class="clear"></div>';
		
		$screen->add_help_tab( array(
			 'id'	=> 'wpmudev_vids',
			 'title'	=> $wpmudev_vids->get_setting('menu_title'),
			 'content'	=> $contextual_help ) );
	}
	
	return $old_help;	
}
?>