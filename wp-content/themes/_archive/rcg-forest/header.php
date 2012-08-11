<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title>
<?php bloginfo('name');
if( is_single() ) {
	_e('&raquo; Blog Archive', 'rcg-forest');
}
wp_title(); ?>
</title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php printf(__('%s RSS Feed', 'rcg-forest'), get_bloginfo('name')); ?>" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<style type="text/css" media="screen">
<!-- Checks to see whether it needs a sidebar or not -->
<?php if (!empty($withcomments) && !is_single() ) { ?>

<?php } ?>
</style>

<?php wp_head(); ?>
</head>
<body>

<div id="top">
        <div id="topcenter">
                <h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
                        <?php if (get_option('blogdescription')!='') { ?>
                        <h1>|</h1><h2><?php bloginfo('description'); ?></h2>
                        <?php } ?>
                <div id="search">
                        <?php include (TEMPLATEPATH . '/searchform.php'); ?>
                </div>
        </div>
</div>

<div id="page" <?php if(!is_single()) {?> class="imagebg"<?php }?>>
<div id="header">
</div>
