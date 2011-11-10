<?php 

add_action( 'bizz_addinfo', 'bizz_addinfo_area' );

function bizz_addinfo_area() { 

?>

<?php bizz_addinfo_before(); ?>

<div class="addinfo-area clearfix">
<div class="container_12">
<div class="grid_12">
    <div class="addinfo clearfix">
		<?php if ($GLOBALS['opt']['bizzthemes_addinfo_button_url'] <> '') { ?>
		    <a href="<?php echo stripslashes($GLOBALS['opt']['bizzthemes_addinfo_button_url']); ?>">
			<?php if ($GLOBALS['opt']['bizzthemes_addinfo_button'] <> '') { ?>
			    <img src="<?php echo stripslashes($GLOBALS['opt']['bizzthemes_addinfo_button']); ?>" alt="" />
			<?php } ?>
			</a>
		<?php } ?>
		<?php echo stripslashes($GLOBALS['opt']['bizzthemes_addinfo_title']); ?>
	</div><!-- /.addinfo-->
</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.addinfo-area -->

<?php bizz_addinfo_after(); ?>
		        		
<?php } ?>