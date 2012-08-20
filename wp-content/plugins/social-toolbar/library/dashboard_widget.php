<style type="text/css">
.wp-social-toolbar-dashboard-widget h4
{
	font-size:13px;
	line-height:15px;
}
.wp-social-toolbar-dashboard-widget h2
{
	font-size:18px;
}
.wp-social-toolbar-dashboard-widget h4 a
{
	font-size:12px;
	color:#000;
	text-decoration:none;
}
</style>
<table>
	<tr>
		<td>
		<?php
		DDST_fetch_feed('http://feeds2.feedburner.com/daddydesign',7);
		?>
		</td>
		<td align="center" style="padding:5px;" width="250px" class="wp-social-toolbar-dashboard-widget">
		<a href="http://socialtoolbarpro.com/" title="Social Toolbar Pro" target="_blank" style="margin-bottom:20px;overflow:hidden;display:block;"><img src="<?php echo DD_SOCIAL_TOOLBAR_PATH;?>images/wp_social_toolbar_logo.png" alt="Social Toolbar Pro" /></a>
		<h4><?php _e('Did you know there is an advanced PRO Version of this plug-in available?','WPSOCIALTOOLBAR');?></h4>
		<h2>WHY GO PRO?</h2>  <h4><a href="http://socialtoolbarpro.com" title="Wp Social Toolbar Pro" target="_blank"><?php _e('CLICK HERE TO FIND OUT','WPSOCIALTOOLBAR');?></a></h4>
		<a href="http://socialtoolbarpro.com/" title="Social Toolbar Pro" target="_blank"><img src="<?php echo DD_SOCIAL_TOOLBAR_PATH;?>images/go_pro.png" alt="Social Toolbar Pro" /></a>
		</td>
	</tr>
</table>