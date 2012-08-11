<?php include (get_template_directory() . '/library/options/options.php'); ?>
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_before_sidebar' ) ?>
<?php endif; ?>
<div id="sidebar">
		<div id="sidebox">
				<?php
		$adone = get_option('dev_businessblog_adone');	
		$adone_title = get_option('dev_businessblog_adone_title');	
		$adone_link = get_option('dev_businessblog_adone_link');
		$adtwo = get_option('dev_businessblog_adtwo');	
		$adtwo_title = get_option('dev_businessblog_adtwo_title');	
		$adtwo_link = get_option('dev_businessblog_adtwo_link');
		$adthree = get_option('dev_businessblog_adthree');	
		$adthree_title = get_option('dev_businessblog_adthree_title');	
		$adthree_link = get_option('dev_businessblog_adthree_link');
		$adfour = get_option('dev_businessblog_adfour');	
		$adfour_title = get_option('dev_businessblog_adfour_title');	
		$adfour_link = get_option('dev_businessblog_adfour_link');
				?>
				<?php
				if ($adone_link != ""){
				?>
		<ul id="banner">
		<li><a href="<?php echo $adone_link ?>"><img src="<?php echo $adone ?>" alt="<?php echo stripslashes($adone_title); ?>" /></a></li>
		<li><a href="<?php echo $adtwo_link ?>"><img src="<?php echo $adtwo ?>" alt="<?php echo stripslashes($adtwo_title); ?>" /></a></li>
				<li><a href="<?php echo $adthree_link ?>"><img src="<?php echo $adthree ?>" alt="<?php echo stripslashes($adthree_title); ?>" /></a></li>
						<li><a href="<?php echo $adfour_link ?>"><img src="<?php echo $adfour ?>" alt="<?php echo stripslashes($adfour_title); ?>" /></a></li>
		</ul>
					<?
				}
					?>
					
							<?php if ( !is_user_logged_in() ) : ?>
							<?php

								locate_template( array( '/library/components/signup-box.php' ), true );

							?>
							
						<?php endif; ?>
		<?php locate_template( array( '/library/components/buddypress/buddypress-panel.php' ), true ); ?>
				
					<?php if ( is_active_sidebar( 'home-sidebar' ) ) : ?>
							<?php dynamic_sidebar( 'home-sidebar' ); ?>
					<?php endif; ?>
						<?php if($bp_existed == 'true') : ?>
						<?php do_action( 'bp_inside_after_sidebar' ) ?>
						<?php endif; ?>
							</div>
</div><!-- #sidebar -->
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_after_sidebar' ) ?>
<?php endif; ?>