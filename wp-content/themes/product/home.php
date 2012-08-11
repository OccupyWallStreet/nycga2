<?php get_header() ?>
	<?php 
		$slideshow = get_option('dev_product_slideshow');{
			if ($slideshow == "yes"){
				locate_template( array( '/library/components/featured-slider.php' ), true );					
			}
			else{	
			}
		}		
	?>
		<?php 
			$contentshow = get_option('dev_product_contentshow');{
				if ($contentshow == "yes"){
						?>
					
						<div class="shadow-spacer"></div> 
						<div id="content">
							<div class="padder">
<?php 					locate_template( array( '/library/components/content-blocks.php' ), true );	?>
							</div><!-- .padder -->
						</div><!-- #content -->
						<?php				
				}
				else if ($contentshow == "no"){
				}
				else{
					?>
					
					<div class="shadow-spacer"></div>
					<div id="content">
						<div class="padder">
<?php 	locate_template( array( '/library/components/content-none.php' ), true );?>
						</div><!-- .padder -->
					</div><!-- #content -->
					<?php			
				}
			}		
		?>
	
		<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
			<?php get_sidebar('bphome'); ?>
		<?php } else { // if not bp detected..let go normal ?>
			<?php get_sidebar('home'); ?>
		<?php } ?>
	
<?php get_footer() ?>
