<?php include (TEMPLATEPATH . '/options.php'); ?>

<div id="front-left">
<div id="services">

<h4><?php if(!$tn_edus_feat_header_title) { echo __('Change this in theme options', TEMPLATE_DOMAIN); } else { echo stripslashes( $tn_edus_feat_header_title ); } ?></h4>
<?php if( $tn_edus_feat_style != 'post mode') { ?>
<?php locate_template( array('lib/templates/wp-template/service-mode.php'), true ); ?>
<?php } else { // end feat style check ?>
<?php locate_template( array('lib/templates/wp-template/post-mode.php'), true ); ?>
<?php } // end feat style check ?>
</div>

<?php if ( is_active_sidebar( __('home-side-left', TEMPLATE_DOMAIN ) ) ) : ?>
<ul class="sidebar_list">
<?php dynamic_sidebar( __('home-side-left', TEMPLATE_DOMAIN ) ); ?>
</ul>
<?php endif; ?>

<?php locate_template ( array('_inc/functions/rss-network.php'), true ); ?>

</div>