<div class="upsell-modal">
	<div class="slidedeck-header">
	    <h1><?php _e( "Upgrade to Get More Lenses", $this->namespace ); ?></h1>
	</div>
	<div class="background">
		<div class="inner">
			<div class="copyblock">
			    <h3><?php _e( "More lenses? Yep, we've got those ready for you", $this->namespace ); ?></h3>
				<p><?php _e("We have 7 additional highly-crafted SlideDeck lenses available to all our Personal tier customers and up."); ?></p>
				<?php include( SLIDEDECK2_DIRNAME . '/views/upsells/_upsell-additional-lenses.php' ); ?>
			</div>
			<div class="cta">
				<a class="slidedeck-noisy-button" href="<?php echo slidedeck2_action( "/upgrades" ); ?>" class="button slidedeck-noisy-button"><span>Upgrade to Personal</span></a>
				<a class="features-link" href="http://demo.slidedeck.com/wp-login.php?utm_campaign=sd2_lite&utm_medium=handslap_link&utm_source=handslap_lenses&utm_content=more_lenses_list<?php echo self::get_cohort_query_string('&'); ?>" target="_blank">or check out all the lenses in the live demo</a>
			</div>
		</div>
	</div>
</div>