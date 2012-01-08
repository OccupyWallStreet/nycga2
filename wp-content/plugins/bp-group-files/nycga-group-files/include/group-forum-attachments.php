<?php

// Add group document uploading to new forum posts
add_action( 'bp_after_group_forum_post_new', 'nycga_group_files_forum_attachments_upload_attachment' );
add_action( 'groups_forum_new_reply_after', 'nycga_group_files_forum_attachments_upload_attachment' );
function nycga_group_files_forum_attachments_upload_attachment() { ?>
	<div>
		<a id="nycga_group_files_forum_upload_toggle" href="#">Upload File (+)</a>
	</div>
	<div id="nycga_group_files_forum_upload">
		<label><?php _e('Choose File:','nycga-group-files'); ?></label>
		<input type="file" name="nycga_group_files_file" class="bp-group-files-file" />

		<div id="document-detail-clear" class="clear"></div>
		<div class="document-info">
		<label><?php _e('Display Name:','nycga-group-files'); ?></label>
		<input type="text" name="nycga_group_files_name" id="nycga-group-files-name" />

		<?php if( nycga_group_files_SHOW_DESCRIPTIONS ) { ?>
		<label><?php _e('Description:', 'nycga-group-files'); ?></label>
		<textarea name="nycga_group_files_description" id="nycga-group-files-description"></textarea>
		<?php } ?>
		</div>
	</div>
<?php
}

// Save group documents and append link to forum topic text
add_filter( 'group_forum_topic_text_before_save', 'nycga_group_files_forum_attachments_topic_text', 10, 1 );
add_filter( 'group_forum_post_text_before_save', 'nycga_group_files_forum_attachments_topic_text', 10, 1 );
function nycga_group_files_forum_attachments_topic_text( $topic_text ) {
	global $bp;

	if ( ! empty($_FILES) ) {
		$document = new NYCGA_Group_Files();
		$document->user_id = get_current_user_id();
		$document->group_id = $bp->groups->current_group->id;
		$document->name = $_POST['nycga_group_files_name'];
		$document->description = $_POST['nycga_group_files_description'];
		if( $document->save() ) {
			do_action('nycga_group_files_add_success',$document);
			bp_core_add_message( __('File successfully uploaded','nycga-group-files') );
			return $topic_text . nycga_group_files_forum_attachments_document_link( $document );
		}
	}
	return $topic_text;
}

// Returns html that links to a group document
function nycga_group_files_forum_attachments_document_link( $document ) {
	$html = "<br /><a class='group-files-title' id='group-document-link-{$document->id}' href='{$document->get_url()}' target='_blank'>{$document->name}";
	if ( get_option( 'nycga_group_files_display_file_size' ) )
		$html .= " <span class='group-documents-filesize'>(" . get_file_size( $document ) . ")</span>";
	$html .= "</a>";

	if ( NYCGA_GROUP_FILES_SHOW_DESCRIPTIONS && $document->description ) {
		$html .= "<br /><span class='group-documents-description'>" . nl2br($document->description) . "</span>";
	}

	return apply_filters( 'nycga_group_files_forum_document_link', $html, $document );
}

// Allow the id attribute in <a> tags so download counting works.
add_filter( 'bp_forums_allowed_tags', 'nycga_group_files_forum_attachments_allowed_tags', 10, 1 );
function nycga_group_files_forum_attachments_allowed_tags( $forums_allowedtags ) {
	$forums_allowedtags['a']['id'] = array();

	return $forums_allowedtags;
}
