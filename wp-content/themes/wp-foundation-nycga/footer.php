			<footer role="contentinfo">

			
					<div class="twelve columns">

						<div class="row">

							<nav class="ten columns clearfix">
								<?php bones_footer_links(); ?>
							</nav>

						</div>

						<div class="footer four columns">
							<?php dynamic_sidebar( 'footer1' ); ?>
						</div>

						<div class="footer four columns">
							<?php dynamic_sidebar( 'footer2' ); ?>
						</div>

						<div class="footer four columns">
							<?php dynamic_sidebar( 'footer3' ); ?>
						</div>		

					</div>

					<div class="twelve columns">
						<a rel="license" href="http://creativecommons.org/licenses/by/3.0/deed.en_US"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/80x15.png" /></a> This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/3.0/deed.en_US">Creative Commons Attribution 3.0 Unported License</a>. [<a href="https://github.com/OccupyWallStreet/nycga2" target="_blank">Source Code</a>]
						<p></p>
					</div>
					
			</footer> <!-- end footer -->
		
		</div> <!-- end #container -->
		
		<!--[if lt IE 7 ]>
  			<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
  			<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
		<![endif]-->
		
		<?php wp_footer(); // js scripts are inserted using this function ?>

	</body>

</html>