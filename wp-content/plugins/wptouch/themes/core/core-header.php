<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<?php if ( bnc_is_zoom_enabled() ) { ?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />
	<?php } else { ?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<?php } ?>
	<title><?php global $page, $paged; wp_title( '|', true, 'right' ); bloginfo( 'name' ); $site_description = get_bloginfo( 'description', 'display' ); if ( $site_description && ( is_home() || is_front_page() ) ) echo " | $site_description"; if ( $paged >= 2 || $page >= 2 ) echo ' | ' . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) ); ?></title>

	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
	<link <?php if (bnc_is_flat_icon_enabled()) { echo 'rel="apple-touch-icon-precomposed"'; } else { echo 'rel="apple-touch-icon"';} ?> href="<?php echo bnc_get_title_image(); ?>" />
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/style.css" type="text/css" media="screen" />
	<?php if (bnc_is_gigpress_enabled() && function_exists( 'gigpress_shows' )) { ?>
		<link rel="stylesheet" href="<?php echo compat_get_plugin_url( 'wptouch' ); ?>/themes/core/core-css/gigpress.css" type="text/css" media="screen" />
	<?php } ?>
	<?php wptouch_core_header_styles(); wptouch_core_header_enqueue(); ?>
	<?php if (!is_single()) { ?>
		<script type="text/javascript">
			// Hides the addressbar on non-post pages
			function hideURLbar() { window.scrollTo(0,1); }
			addEventListener('load', function() { setTimeout(hideURLbar, 0); }, false );
		</script>
<?php } ?>
</head>
<?php flush();