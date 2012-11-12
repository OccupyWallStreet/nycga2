<div class="upsell-modal">
	<div class="slidedeck-header">
	    <h1><?php _e( "Upgrade to get Covers", $this->namespace ); ?></h1>
	</div>
	<div class="background">
		<div class="inner">
			<div class="copyblock">
			    <h3><?php _e( "Curate the experience with SlideDeck Covers", $this->namespace ); ?></h3>
				<p>A great story needs a beginning and an end. Covers give users context for what you're sharing. They're great for product tours, slideshows and more.</p>
				<img height="189" id="covers-modal-upsell" src="https://s3.amazonaws.com/slidedeck-pro/lite_upsell_assets/images/covers-modal-upsell.jpg" alt="SlideDeck Covers" />
			</div>
			<div class="cta">
				<a class="slidedeck-noisy-button" href="<?php echo slidedeck2_action( "/upgrades" ); ?>" class="button slidedeck-noisy-button"><span>Upgrade to Personal</span></a>
				<a class="features-link" href="http://www.slidedeck.com/features?utm_campaign=sd2_lite&utm_medium=handslap_link&utm_source=handslap_covers&utm_content=slides_slider_image<?php echo self::get_cohort_query_string('&'); ?>" target="_blank">or learn more about other SlideDeck features</a>
			</div>
		</div>
	</div>
</div>