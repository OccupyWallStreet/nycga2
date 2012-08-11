<div style="clear:both"> </div>
</div><?php //closing for #main ?>
<div id="footer">
  <?php // It's completely optional, but if you like the theme I would appreciate it if you keep the credit link at the bottom. ?>
	<p>
		<span>
		<a href="<?php echo admin_url(); ?>" title="Site Admin">Site Admin</a> | Theme by <a href="http://www.diovo.com/links/voidy/" title="Diovo">Niyaz</a>
		</span>
		<strong><?php bloginfo('name');?></strong> <?php _e("Copyright", "voidy" ); ?> &copy; <?php echo date('Y');?> <?php _e("All Rights Reserved", "voidy" ); ?>
	</p>
</div>
<?php wp_footer();?>
</body>
</html>