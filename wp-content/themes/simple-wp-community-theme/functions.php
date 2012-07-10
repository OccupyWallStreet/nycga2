<?php
global $content_width;
$content_width  = "432";

register_sidebars( 1,
	array(
		'name' => 'left sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	)
	
);

// This theme uses wp_nav_menu() in one location.
register_nav_menus( array(
	'primary' => __( 'Primary Navigation', 'buddypress' ),
) );

function widget_community_nav() {
?>
  <?php if ( bp_is_page( BP_ACTIVITY_SLUG ) || (bp_is_page( BP_MEMBERS_SLUG ) || bp_is_member()) || (bp_is_page( BP_GROUPS_SLUG ) || bp_is_group()) || bp_is_page( BP_FORUMS_SLUG ) || bp_is_page( BP_BLOGS_SLUG ) ) { ?>
  		<div id="community-nav" class="widget-title" >
  					<ul class="item-list">
          <h3 class="widgettitle"><?php _e( 'Community', 'buddypress' ) ?></h3>
					
  					<?php if ( 'activity' != bp_dtheme_page_on_front() && bp_is_active( 'activity' ) ) : ?>
					<li<?php if ( bp_is_page( BP_ACTIVITY_SLUG ) ) : ?> class="selected"<?php endif; ?>>
						<a href="<?php echo site_url() ?>/<?php echo BP_ACTIVITY_SLUG ?>/" title="<?php _e( 'Activity', 'buddypress' ) ?>"><?php _e( 'Activity', 'buddypress' ) ?></a>
					</li>
				<?php endif; ?>

				<li<?php if ( bp_is_page( BP_MEMBERS_SLUG ) || bp_is_member() ) : ?> class="selected"<?php endif; ?>>
					<a href="<?php echo site_url() ?>/<?php echo BP_MEMBERS_SLUG ?>/" title="<?php _e( 'Members', 'buddypress' ) ?>"><?php _e( 'Members', 'buddypress' ) ?></a>
				</li>

				<?php if ( bp_is_active( 'groups' ) ) : ?>
					<li<?php if ( bp_is_page( BP_GROUPS_SLUG ) || bp_is_group() ) : ?> class="selected"<?php endif; ?>>
						<a href="<?php echo site_url() ?>/<?php echo BP_GROUPS_SLUG ?>/" title="<?php _e( 'Groups', 'buddypress' ) ?>"><?php _e( 'Groups', 'buddypress' ) ?></a>
					</li>

					<?php if ( bp_is_active( 'forums' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && !(int) bp_get_option( 'bp-disable-forum-directory' ) ) && bp_forums_is_installed_correctly() ) : ?>
						<li<?php if ( bp_is_page( BP_FORUMS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
							<a href="<?php echo site_url() ?>/<?php echo BP_FORUMS_SLUG ?>/" title="<?php _e( 'Forums', 'buddypress' ) ?>"><?php _e( 'Forums', 'buddypress' ) ?></a>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ( bp_is_active( 'blogs' ) && bp_core_is_multisite() ) : ?>
					<li<?php if ( bp_is_page( BP_BLOGS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
						<a href="<?php echo site_url() ?>/<?php echo BP_BLOGS_SLUG ?>/" title="<?php _e( 'Blogs', 'buddypress' ) ?>"><?php _e( 'Blogs', 'buddypress' ) ?></a>
					</li>
				<?php endif; ?>
  			   </ul>
    </div>				
   <?php } if( bp_is_single_item() || bp_is_member() ) {
   include("sidebar-community.inc.php");   
   } ?>
 <br /> 
<?php
}
if ( function_exists('widget_community_nav') )
    wp_register_sidebar_widget( 'widget_community_nav', 'Simple WP Community Navigation', 'widget_community_nav', '' );


?>