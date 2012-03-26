
</div>
</div>
<div class="hrline" style="width:100%;"></div>

<div id="footer" class="inset">
<div class="box">
<!-- credit links are not required to remain intact, but is appreciated. Thanks! -->
	<p class="fl ger">&copy; <?php echo date("Y"); ?> <?php bloginfo('name'); ?> | Powered by <a href="http://wordpress.org/">WordPress</a>
	</p>
	

    <p class="fr"><a href="http://beatheme.com/" title="Professional WordPress Themes!"><img src="<?php echo get_template_directory_uri() ?>/images/beafoo-bla.png" border="0" alt="beatheme.com"/></a></p>
	
	
</div>
</div>

<?php 
$pov_google_analytics = get_option('pov_google_analytics');
if ($pov_google_analytics != '') { echo stripslashes($pov_google_analytics); }
?>
<?php wp_footer(); ?>
</body>
</html>
