<div class="upsell-modal">
	<div class="slidedeck-header">
	    <h1><?php _e( "Need more slides in your decks?", $this->namespace ); ?></h1>
	</div>
	<div class="background">
		<div class="inner">
			<div class="copyblock">
			    <h3><?php _e( "As many slides as you like with SlideDeck Personal", $this->namespace ); ?></h3>
				<p>You've got a lot of web content sources and slides fill up fast when they're coming in dynamically. Display all your web content across any number of slides.</p>
				<img height="114" id="covers-modal-upsell" src="https://s3.amazonaws.com/slidedeck-pro/lite_upsell_assets/images/slide-count-modal-upsell.jpg" alt="More Slides" />
			</div>
			<div class="cta">
				<a class="slidedeck-noisy-button" href="<?php echo slidedeck2_action( "/upgrades" ); ?>" class="button slidedeck-noisy-button"><span>Upgrade to Personal</span></a>
				<a class="features-link" href="http://www.slidedeck.com/features?utm_campaign=sd2_lite&utm_medium=handslap_link&utm_source=handslap_slide_count&utm_content=summer_vacation_three_covers<?php echo self::get_cohort_query_string('&'); ?>" target="_blank">or learn more about other SlideDeck features</a>
			</div>
		</div>
	</div>
</div>