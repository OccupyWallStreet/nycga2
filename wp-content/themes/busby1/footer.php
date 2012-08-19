<footer>

<div id="footer">
<?php if ( ! dynamic_sidebar( 'footer' ) ) : ?>
<?php endif; ?>

<div id="copyright">
<p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Powered by WordPress. <a href="http://wplift.com">Busby Theme by WPLIft</a></p>
<?php up_footer(); ?> 
</div>

</div>
</footer>
</div> <!--! end of #container -->

  <!--[if lt IE 7 ]>
    <script src="<?php bloginfo('template_url'); ?>/js/libs/dd_belatedpng.js"></script>
    <script>DD_belatedPNG.fix("img, .png_bg"); // Fix any <img> or .png_bg bg-images. Also, please read goo.gl/mZiyb </script>
  <![endif]-->
<?php wp_footer(); ?>
</body>
</html>