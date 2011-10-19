<?php
/**
 * This is the plugin template that is loaded when visiting the 'blog tab' within the group.
 * To modify you can either use the provided hooks. You may also copy the whole groupblog over
 * to your active theme and completely re-theme the blog template.
 *
 * Includes: inc/pages.php, inc/posts.php, inc/activity.php
 */
?>

<?php do_action( 'bp_before_group_blog_template' ) ?>

	<?php if ( bp_group_is_visible() && bp_groupblog_is_blog_enabled ( bp_get_group_id() ) ) : ?>

		<?php switch_to_blog( get_groupblog_blog_id() ); ?>			  

		<?php do_action( 'bp_before_group_blog_content' ) ?>

		<?php /********************* Start your custom content *********************/ ?>

		<?php /* Uncomment to disable */ include( 'groupblog/inc/pages.php' ); ?>
    <?php /* Uncomment to disable */ include( 'groupblog/inc/posts.php' ); ?>		

		<?php /********************* End your custom content *********************/ ?>

		<?php do_action( 'bp_after_group_blog_content' ) ?>

    <?php	restore_current_blog(); ?>
    	
    <?php /* Uncomment to disable */ include( 'groupblog/inc/activity.php' ); ?>

	<?php elseif ( !bp_group_is_visible() ) : ?>
		<?php /* The group is not visible, show the status message */ ?>

		<?php do_action( 'bp_before_group_status_message' ) ?>

		<div id="message" class="info">
			<p><?php bp_group_status_message() ?></p>
		</div>

		<?php do_action( 'bp_after_group_status_message' ) ?>
									
	<?php endif;?>
			
<?php do_action( 'bp_after_group_blog_template' ) ?>			