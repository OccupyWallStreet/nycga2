<?php 

add_action( 'bizz_navigation', 'bizz_navigation_area' );

add_action( 'bizz_feed_spot_inside', 'bizz_feed_spot' );

function bizz_navigation_area() { 

?>

<?php bizz_navigation_before(); ?>

<div class="navigation-area clearfix">	
<div class="container_16">

    <div class="grid_15">
	<?php if ( function_exists('wp_nav_menu') ) { ?>
	    <?php wp_nav_menu( array( 'theme_location' => 'primary-menu', 'menu_class' => 'sf-menu' ) ); ?>
	<?php } else { ?>
	    <ul class="sf-menu">
		    <?php if ($GLOBALS['opt']['bizzthemes_nofollow_home'] == 'true') { $nofollow = 'rel="nofollow"'; } ?>
		    <?php if ($GLOBALS['opt']['bizzthemes_home_link2'] == 'true') { ?>
		        <li <?php if ( is_home() ) { ?> class="current_page_item" <?php } ?>><a href="<?php echo get_option('home'); ?>/" <?php echo $nofollow; ?>><?php echo stripslashes(__('Home', 'bizzthemes')); ?></a></li>
		    <?php } ?>
	        <?php if (get_inc_pages("pag_exclude2_") == '') { $exclude = '9999999'; } else { $exclude = get_inc_pages("pag_exclude2_"); } ?>
		    <?php wp_list_pages('title_li=&depth=0&include=' . $exclude .'&sort_column=menu_order'); ?>
	    </ul><!-- /.sf-menu -->
	<?php } ?>
	</div><!-- /.grid_15 -->
	
	<div class="grid_1">
	    <?php bizz_feed_spot_inside(); ?>
	</div><!-- /.grid_1 -->

</div><!-- /.container_16 -->
</div><!-- /.navigation-area -->

<?php bizz_navigation_after(); ?>
		
<?php } ?>