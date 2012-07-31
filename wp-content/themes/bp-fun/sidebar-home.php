<?php include (TEMPLATEPATH . '/options.php'); ?>

<div id="sidebar-column" class="bpside">

<div id="login-panel" class="widget">
<h2 class="widgettitle"><?php _e('Search the site', TEMPLATE_DOMAIN); ?></h2><ul>
<li>

<div id="searchbox">

<?php if($bp_existed == 'true') { ?>
<?php locate_template ( array('lib/templates/bp-template/bp-search-panel.php'), true ); ?>
<?php } else { ?>
<?php locate_template ( array('lib/templates/wp-template/search-panel.php'), true ); ?>
<?php } ?>


<h2 class="widgettitle"><?php if (!is_user_logged_in()) { ?> <?php _e('Member login', TEMPLATE_DOMAIN); ?><?php } else { ?><?php _e('Member Profile', TEMPLATE_DOMAIN); ?><?php } ?></h2>

<?php if($bp_existed == 'true') { ?>
<?php locate_template ( array('lib/templates/bp-template/bp-login-panel.php'), true ); ?>
<?php } else { ?>
<?php locate_template ( array('lib/templates/wp-template/login-panel.php'), true ); ?>
<?php } ?>

</div>
</li>
</ul>
</div>


<?php if ( is_active_sidebar( __('right-column', TEMPLATE_DOMAIN ) ) ) : ?>
<?php dynamic_sidebar( __('right-column', TEMPLATE_DOMAIN ) ); ?>
<?php endif; ?>


</div>