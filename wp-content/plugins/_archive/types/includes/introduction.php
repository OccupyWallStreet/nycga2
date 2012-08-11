<?php
/*
 * Introduction page.
 */

echo wpcf_add_admin_header(__('Help', 'wpcf'));

?>
<h3><?php _e('Start using Types', 'wpcf'); ?></h3>
<p><a class="button-primary" href="<?php echo admin_url('admin.php?page=wpcf-edit-type'); ?>"><?php _e('New custom post types and taxonomy', 'wpcf'); ?></a> &nbsp; <a class="button-primary" href="<?php echo admin_url('admin.php?page=wpcf-edit'); ?>"><?php _e('New custom fields', 'wpcf'); ?></a></p>

<h3 style="margin-top:3em;"><?php _e('Documentation and Support', 'wpcf'); ?></h3>
<ul>
    <li><?php printf(__('%sUser Guides%s  - everything you need to know about using Types', 'wpcf'), '<a target="_blank" href="http://wp-types.com/documentation/user-guides/#1?utm_source=typesplugin&utm_medium=intro&utm_campaign=types"><strong>', ' &raquo;</strong></a>'); ?></li>
    <li><?php printf(__('%sTypes fields API%s (for developers) - Learn how to insert Types fields to PHP templates', 'wpcf'), '<a target="_blank" href="http://wp-types.com/documentation/functions/?utm_source=typesplugin&utm_medium=intro&utm_campaign=types"><strong>', ' &raquo;</strong></a>'); ?></li>
    <li><?php printf(__('%sSupport forum%s - register in our forum to receive support', 'wpcf'), '<a target="_blank" href="http://wp-types.com/support/register/?utm_source=typesplugin&utm_medium=intro&utm_campaign=types"><strong>', ' &raquo;</strong></a>'); ?></li>
</ul>

<h3 style="margin-top:3em;"><?php _e('Want to help Types?', 'wpcf'); ?></h3>
<ul>
    <li><?php printf(__('%sRate Types on WordPress.org%s', 'wpcf'), '<a target="_blank" href="http://wordpress.org/extend/plugins/types/"><strong>', ' &raquo;</strong></a>'); ?></li>
    <li><?php printf(__('%sTypes press resources%s  - write about Types', 'wpcf'), '<a target="_blank" href="http://wp-types.com/home/press-resources/?utm_source=typesplugin&utm_medium=intro&utm_campaign=types"><strong>', ' &raquo;</strong></a>'); ?></li>
	<li><?php printf(__('%sViews%s - the commercial complement of Types, which makes content display a breeze.', 'wpcf'), '<a target="_blank" href="http://wp-types.com/home/views-create-elegant-displays-for-your-content/?utm_source=typesplugin&utm_medium=intro&utm_campaign=types"><strong>', ' &raquo;</strong></a>'); ?></li>
</ul>

<?php
echo wpcf_add_admin_footer();
