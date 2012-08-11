<?php get_header() ?>
	<div id="content">
		<div class="padder">
			<?php 
			$featuretype = get_option('dev_buddydaily_featuretype');
			
			if($featuretype == "video"){
				locate_template( array( '/library/components/featured-video.php' ), true );
			}
			elseif($featuretype == "tabbed"){	
				locate_template( array( '/library/components/featured-tabbed.php' ), true );
			}
			elseif($featuretype == "slideshow"){
				locate_template( array( '/library/components/featured-slideshow.php' ), true );
			}
			else{
				locate_template( array( '/library/components/featured-none.php' ), true );
			}
			?>
				<?php 
					$contenttype = get_option('dev_buddydaily_latesttype');
				if($contenttype == "rows"){
					locate_template( array( '/library/components/content-rows.php' ), true );
				}
				elseif($contenttype == "column"){	
					locate_template( array( '/library/components/content-columns.php' ), true );
				}
				elseif($contenttype == "wall"){	
					locate_template( array( '/library/components/content-thewall.php' ), true );
				}
				else{	
					locate_template( array( '/library/components/content-none.php' ), true );
				}
				?>	
		</div><!-- .padder -->
	</div><!-- #content -->
			<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
				<?php get_sidebar('bphome'); ?>	
			<?php } else { // if not bp detected..let go normal ?>
					<?php get_sidebar('home'); ?>
			<?php } ?>
<?php get_footer() ?>
