<?php 

add_action( 'bizz_twitter', 'bizz_twitter_area' );

function bizz_twitter_area() {

?>

<?php bizz_twitter_before(); ?> 

<div class="twitter-area clearfix">
<div class="container_16">
	
	<div class="grid_2">
	    <a href="http://www.twitter.com/<?php echo stripslashes($GLOBALS['opt']['bizzthemes_twitter_uname']); ?>" title="<?php echo stripslashes(__('Follow us on Twitter', 'bizzthemes')); ?>" class="twitter-logo" >
		    <img src="<?php if ( $GLOBALS['opt']['bizzthemes_twitter_ico'] <> "" ) { echo $GLOBALS['opt']['bizzthemes_twitter_ico']; } else { echo BIZZ_THEME_IMAGES .'/twittermoby-trans.png'; } ?>" alt="<?php echo stripslashes(__('Follow us on Twitter', 'bizzthemes')); ?>" />
		</a> 
	</div><!-- /.grid_2 -->
	<div class="grid_14">
	    <div class="twitter-spot-outer">
		<div class="twitter-spot-inner clearfix">
			<?php hosted_twitter_script('',$GLOBALS['opt']['bizzthemes_twitter_uname'],$GLOBALS['opt']['bizzthemes_twitter_count']); ?>
		</div><!-- /.twitter-spot-inner -->
		</div><!-- /.twitter-spot-outer -->
	</div><!-- /.grid_14 -->
	
</div><!-- /.container_16 -->
</div><!-- /.twitter-area -->

<?php bizz_twitter_after(); ?>

<?php } ?>