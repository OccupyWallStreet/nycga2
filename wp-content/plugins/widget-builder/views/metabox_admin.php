<?php
/**
 * Widget template. This template can be overriden using the "tribe_widget_builder_metabox_admin.php" filter.
 * See the readme.txt file for more info.
 */

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

$saved_value = get_post_meta( $post_id, '_' . $field, true );
$html = '<label for="' . $field . '">' . __('Widget Description', 'widget-builder' ) . '</label><br />';
$html .= '<textarea id="' . $field . '" name="' . $field . '" style="width:100%;" />' . $saved_value . '</textarea>';
echo $html;