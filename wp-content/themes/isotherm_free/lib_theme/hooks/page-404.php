<?php 

add_action( 'bizz_page_404', 'bizz_page_404_area' );

add_action( 'bizz_404_error_inside', 'bizz_404_error' );

function bizz_page_404_area() { 

?>

<?php bizz_page_404_before(); ?>

<div class="title-area clearfix">
<div class="container_12">
<div class="grid_12">
    <?php bizz_headline(); ?>
</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.title-area -->

<div class="cbox-area clearfix">
<div class="container_12 containerbar">

    <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignleft' ) { bizz_widgets(); } ?>

    <div class="grid_8 mainbar <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignleft' ) { ?>mainbar_right<?php } ?> equalh">
	<div class="cbox">
				
		<div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
	
	</div><!-- /.cbox -->	
	</div><!-- /.grid_8 -->

    <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignright' ) { bizz_widgets(); } ?>

</div><!-- /.container_12 -->
</div><!-- /.cbox-area -->

<?php bizz_page_404_after(); ?>
		
<?php } ?>