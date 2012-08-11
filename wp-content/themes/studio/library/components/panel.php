<?php
	$panelheader = get_option('dev_studio_panel_header');
	$panelheadertwo = get_option('dev_studio_panel_headertwo');
	$paneldescription = get_option('dev_studio_panel_description');
?>
<!-- start community panel -->
<div id="toppanel"><!-- start #toppanel -->
	<div id="panel"><!-- start #panel -->
		<div class="content clearfix">
			<div class="left">
				<h1><?php echo stripslashes($panelheader); ?></h1>
				<h2><?php echo stripslashes($panelheadertwo); ?></h2>		
				<p class="grey"><?php echo stripslashes($paneldescription); ?></p>
							<?php if ( function_exists( 'bp_message_get_notices' ) ) : ?>
								<?php bp_message_get_notices(); /* Site wide notices to all users */ ?>
							<?php endif; ?>
			</div>
			<div class="left">
				<?php locate_template( array( '/library/components/buddypress/buddypress-navigation.php' ), true ); ?>		
			</div>
			<div class="left right">
				<?php locate_template( array( '/library/components/buddypress/buddypress-panel.php' ), true ); ?>		
			</div>
		</div>
		<div class='clear'></div>
</div><!-- end #panel-->
	<div class="tab"><!-- start tabs -->
		<ul class="login">
			<li class="left">&nbsp;</li>
			<?php if (!$user_ID): ?>
			<li><?php _e( 'Hello', 'studio' ) ?></li>
			<?php else: ?>
			<li><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></li>
			<?php endif; ?>
			<li class="sep">|</li>
			<li id="toggle">
					<?php if (!$user_ID): ?>
				<a id="open" class="open" href="#"><?php _e( 'Our Community', 'studio' ) ?></a>
					<?php else: ?>
					<a id="open" class="open" href="#"><?php _e( 'Our Community', 'studio' ) ?></a>
						<?php endif; ?>
				<a id="close" style="display: none;" class="close" href="#"><?php _e( 'Close Panel', 'studio' ) ?></a>			
			</li>
			<li class="right">&nbsp;</li>
		</ul> 
	</div> <!-- end tabs -->
</div> <!-- end #toppanel -->
<!-- end community panel -->
