<div id="member-left">

<?php global $bp;
if($bp->current_component == BP_GROUPS_SLUG && bp_is_single_item() ) { ?>

<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

<?php if ( bp_group_is_visible() ) : ?>

<?php do_action( 'bp_before_group_member_widget' ) ?>

<div class="bp-widget"><div class="avatar-block">
<h4><?php printf( __( 'Members (%d)', TEMPLATE_DOMAIN ), bp_get_group_total_members() ); ?> <span><a href="<?php bp_group_all_members_permalink() ?>"><?php _e( 'See All', TEMPLATE_DOMAIN ) ?> &rarr;</a></span></h4>
<?php if ( bp_group_has_members( 'max=25&exclude_admins_mods=0' ) ) : ?>
<?php while ( bp_group_members() ) : bp_group_the_member(); ?>
<div class="item-avatar">
<a title="<?php bp_group_member_name() ?>" href="<?php bp_group_member_url() ?>"><?php bp_group_member_avatar_thumb() ?></a>
</div>
<?php endwhile; ?>
<?php endif; ?>
</div>
</div>


<?php endif; ?>

<?php do_action( 'bp_after_group_menu_content' ); /* Deprecated -> */ do_action( 'groups_sidebar_after' ); ?>

<?php endwhile; else: ?><?php endif;?>

<div id="left-group-widget" class="sidebar_list">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('group-left', TEMPLATE_DOMAIN)) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Group Left Widget', TEMPLATE_DOMAIN ) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-8"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</div>
<?php endif; ?>
</div>

<?php do_action( 'bp_after_group_member_widget' ) ?>


<?php } else  { ?>


<div class="sidebar_list">
<?php /*global $bp;
if ( $bp->is_item_admin || $bp->is_item_mod  ) { ?>
<div class="widget">
<ul>
<li class="profile-youtube"><a title="add a videos" href="<?php echo $bp->displayed_user->domain . $bp->settings->slug . '/flickr-youtube/'; ?>">Add Youtube</a></li>
<li class="profile-flickr"><a title="add a flickr" href="<?php echo $bp->displayed_user->domain . $bp->settings->slug . '/flickr-youtube/'; ?>">Add Flickr</a></li>
</ul>
</div>
<?php }*/ ?>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('member-left', TEMPLATE_DOMAIN)) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Member Left Widget', TEMPLATE_DOMAIN ) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-6"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</div>
<?php endif; ?>
</div>

<?php } ?>


</div>
