<?php $navigation_on = get_option('dev_gallery_navigation'); 
	if (($navigation_on == "yes") || ($navigation_on == "")){
		?>
		<div class="navigation"><!-- start .navigation -->
			<?php if ( has_nav_menu( 'main' ) ) { ?>
				<?php wp_nav_menu( array('theme_location' => 'main', 'menu_class' => 'nav', 'container' => '', )); ?>			
			<?php } else {?>
				<ul class="nav">
				<?php wp_list_pages('title_li=&include='.wt_get_ID_by_page_name('Contact') ); ?>
				</ul>
			<?php } ?>
				<div class="clear"></div>
		</div><!-- end .navigation -->		
		<?php
	}
?>