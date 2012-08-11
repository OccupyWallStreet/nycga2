<div class="wrap">

    <div id="icon-views" class="icon32"><br /></div>
    <h2><?php _e('Views Help', 'wpv-views') ?></h2>

<br /><br />
<?php
	$table = '<table>';
	$table .= '<thead><tr><th style="font-size:115%;">View</th><th style="font-size:115%;">View Template</th></tr></thead>';
	$table .= '<tbody><tr>';
	$table .= '<td style="width: 220px; padding:5px 10px;"><a href="' . admin_url('edit.php?post_type=view') . '"><img style="border:1px solid #DFDFDF; padding: 5px;" src="' . WPV_URL . '/res/img/view-sample.png" width="200" height="129"></a></td>';
	$table .= '<td style="width: 220px; padding:5px 10px;"><a href="' . admin_url('edit.php?post_type=view-template') . '"><img style="border:1px solid #DFDFDF; padding: 5px;" src="' . WPV_URL . '/res/img/view-template-sample.png" width="200" height="129"></a></td>';
	$table .= '<tr><td style="width: 220px; padding:5px 10px;">' . sprintf(__('%sViews%s load content from the database and display it.','wpv-views'),'<strong>','</strong>') . ' <a target="_blank" href="http://wp-types.com/documentation/user-guides/views/">' . __('Views help','wpv-views') . '  &raquo;</a></td>';
	$table .= '<td style="width: 220px; padding:5px 10px;">' . sprintf(__('%sView Templates%s style single pages.','wpv-views'),'<strong>','</strong>') . ' <a target="_blank" href="http://wp-types.com/documentation/user-guides/view-templates/">' . __('View Template help','wpv-views') . ' &raquo;</a></td></tr>';
	$table .= '<tr>';
	$table .= '<td style="width: 220px; padding:5px 10px;"><a class="button-primary" href="' . admin_url('edit.php?post_type=view') . '">' . __('New View', 'wpv-views') . '</a></td>';
	$table .= '<td style="width: 220px; padding:5px 10px;"><a class="button-primary" href="' . admin_url('edit.php?post_type=view-template') . '">' . __('New View Template', 'wpv-views') . '</a></td></tr>';
	$table .= '</tbody></table>';
 	echo($table);
?>


<h3 style="margin-top:3em;"><?php _e('Documentation and Support', 'wpv-views'); ?></h3>
<ul>
    <li><?php printf(__('%sUser Guides%s  - everything you need to know about using Views', 'wpv-views'), '<a target="_blank" href="http://wp-types.com/documentation/user-guides/#2"><strong>', ' &raquo;</strong></a>'); ?></li>
    <li><?php printf(__('%sExamples%s - learn from examples and see what you can build with Views', 'wpv-views'), '<a target="_blank" href="http://wp-types.com/learn/"><strong>', ' &raquo;</strong></a>'); ?></li>
    <li><?php printf(__('%sSupport forum%s - get help', 'wpv-views'), '<a target="_blank" href="http://wp-types.com/forums/forum/support-2/"><strong>', ' &raquo;</strong></a>'); ?></li>
</ul>

</div>
