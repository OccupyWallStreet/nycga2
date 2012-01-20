<?php
/**
 * Meta Box ACF
 *
 * This file creates the extra HTML for the main ACF admin page (Field Groups)
 */
?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->dir ?>/css/global.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->dir ?>/css/acf.css" />
<div id="acf-col-right" class="hidden">

	<div class="wp-box">
		<div class="inner">
			<h3 class="h2"><?php _e("Advanced Custom Fields",'acf'); ?> <span>v<?php echo $this->version; ?></span></h3>

			<h3><?php _e("Changelog",'acf'); ?></h3>
			<p><?php _e("See what's new in",'acf'); ?> <a class="thickbox" href="<?php bloginfo('url'); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=advanced-custom-fields&section=changelog&TB_iframe=true&width=640&height=559">v<?php echo $this->version; ?></a>
			
			<h3><?php _e("Resources",'acf'); ?></h3>
			<p><?php _e("Read documentation, learn the functions and find some tips &amp; tricks for your next web project.",'acf'); ?><br />
			<a href="http://plugins.elliotcondon.com/advanced-custom-fields/"><?php _e("View the plugins website",'acf'); ?></a></p>

		</div>
		<div class="footer">
			<ul class="left hl">
				<li><?php _e("Created by",'acf'); ?> Elliot Condon</li>
			</ul>
			<ul class="right hl">
				<li><a href="http://wordpress.org/extend/plugins/advanced-custom-fields/"><?php _e("Vote",'acf'); ?></a></li>
				<li><a href="http://twitter.com/elliotcondon"><?php _e("Follow",'acf'); ?></a></li>
			</ul>
		</div>
	</div>
</div>
<script type="text/javascript">
(function($){
	
	$('#screen-meta-links').remove();
	$('#wpbody .wrap').wrapInner('<div id="acf-col-left" />');
	$('#wpbody .wrap').wrapInner('<div id="acf-cols" />');
	$('#acf-col-right').removeClass('hidden').prependTo('#acf-cols');
	
})(jQuery);
</script>