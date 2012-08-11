<!-- start footer widgets -->
<div id="widget-wrapper"><!-- start #widget-wrapper -->
	<div id="widgets"><!-- start #widgets -->
		<div id="content-blocks-widget">
			<div class="content-block"><!-- first widget area -->
				<?php if ( !function_exists('dynamic_sidebar')
				|| !dynamic_sidebar('widgetone-area') ) : ?>
				<?php endif; ?>
			</div>
			<div class="content-block"><!-- second widget area -->
				<?php if ( !function_exists('dynamic_sidebar')
				|| !dynamic_sidebar('widgettwo-area') ) : ?>
				<?php endif; ?>
			</div>
			<div class="content-block-end"><!-- third widget area -->
				<?php if ( !function_exists('dynamic_sidebar')
				|| !dynamic_sidebar('widgetthree-area') ) : ?>
				<?php endif; ?>
			</div>
			<div class="clear"></div>
		</div>
	</div>
</div>
<!-- end #footer-widgets -->