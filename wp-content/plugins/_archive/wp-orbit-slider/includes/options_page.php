<div class="wrap">
<div id="icon-themes" class="icon32"></div>	
	<h2><?php _e('WP Orbit Slider Options', 'wp-orbit-slider'); ?></h2>
	
	<div class="postbox-container" style="width:50%;">
	
		<div class="metabox-holder">
		
			<div class="meta-box-sortables ui-sortable">
			
				<form method="post" action="options.php">
					<?php settings_fields( 'slider-settings-group' ); ?>
					<?php $options = get_option('orbit_slider_options'); ?>


					<div id="general-settings" class="postbox">						
						<h3 class="hndle"><span><?php _e('General Settings', 'wp-orbit-slider'); ?></span></h3>						
						<div class="inside">
						 
                            <!-- animationSpeed -->
							<p>
								<strong><?php _e('Animation Speed:', 'wp-orbit-slider'); ?> </strong>
								<select name="orbit_slider_options[animationSpeed]">
									<?php for($i=100; $i<=3000; $i+= 100): ?>
										<option value="<?php echo $i; ?>"<?php if( $options['animationSpeed'] == $i ) echo 'selected="selected"'; ?>>
										<?php echo $i; ?> <?php _e('ms', 'wp-orbit-slider'); ?>
										</option>
									<?php endfor; ?>
								</select>
							</p>  
                                                                                 
                            <!-- animation -->
							<p>
								<strong><?php _e('Transition Style:', 'wp-orbit-slider'); ?></strong><br />
								<input type="radio" name="orbit_slider_options[animation]" value="fade" <?php if( $options['animation'] == 'fade' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Fade', 'wp-orbit-slider'); ?></label><br />
								
								<input type="radio" name="orbit_slider_options[animation]" value="horizontal-slide" <?php if( $options['animation'] == 'horizontal-slide' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Horizontal Slide', 'wp-orbit-slider'); ?></label><br />
								
								<input type="radio" name="orbit_slider_options[animation]" value="vertical-slide" <?php if( $options['animation'] == 'vertical-slide' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Vertical Slide', 'wp-orbit-slider'); ?></label><br />
								
								<input type="radio" name="orbit_slider_options[animation]" value="horizontal-push" <?php if( $options['animation'] == 'horizontal-push' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Horizontal Push', 'wp-orbit-slider'); ?></label><br />
							</p>

							<!-- directionalNav -->
							<p>
								<strong><?php _e('Show Nav Arrows', 'wp-orbit-slider'); ?></strong><br />
								<input type="radio" name="orbit_slider_options[directionalNav]" value="true" <?php if( $options['directionalNav'] == 'true' ) echo 'checked="checked"'; ?> />
								<label><?php _e('On', 'wp-orbit-slider'); ?></label><br />
								
								<input type="radio" name="orbit_slider_options[directionalNav]" value="false" <?php if( $options['directionalNav'] == 'false' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Off', 'wp-orbit-slider'); ?></label>
							</p>
	
						</div>						
					</div><!-- #general-settings -->


					<div id="caption-settings" class="postbox">						
						<h3 class="hndle"><span><?php _e('Caption Settings', 'wp-orbit-slider'); ?></span></h3>						
						<div class="inside">
 
                            <!-- captionAnimationSpeed -->
							<p>
								<strong><?php _e('Caption Animation Speed:', 'wp-orbit-slider'); ?> </strong>
								<select name="orbit_slider_options[captionAnimationSpeed]">
									<?php for($i=100; $i<=3000; $i+= 100): ?>
										<option value="<?php echo $i; ?>"<?php if( $options['captionAnimationSpeed'] == $i ) echo 'selected="selected"'; ?>>
										<?php echo $i; ?> <?php _e('ms', 'wp-orbit-slider'); ?>
										</option>
									<?php endfor; ?>
								</select>
							</p>                                                        
							
                            <!-- captions -->
							<p>
								<strong><?php _e('Slide Captions:', 'wp-orbit-slider'); ?></strong><br />
								<input type="radio" name="orbit_slider_options[captions]" value="true" <?php if( $options['captions'] == 'true' ) echo 'checked="checked"'; ?> />
								<label><?php _e('On', 'wp-orbit-slider'); ?></label><br />
								
								<input type="radio" name="orbit_slider_options[captions]" value="false" <?php if( $options['captions'] == 'false' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Off', 'wp-orbit-slider'); ?></label>
							</p>
							
                            <!-- captionAnimation -->	
							<p>
								<strong><?php _e('Caption Animation Style:', 'wp-orbit-slider'); ?></strong><br />
								<input type="radio" name="orbit_slider_options[captionAnimation]" value="fade" <?php if( $options['captionAnimation'] == 'fade' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Fade', 'wp-orbit-slider'); ?></label><br />
								
								<input type="radio" name="orbit_slider_options[captionAnimation]" value="slideOpen" <?php if( $options['captionAnimation'] == 'slideOpen' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Slide Open', 'wp-orbit-slider'); ?></label><br />
								
								<input type="radio" name="orbit_slider_options[captionAnimation]" value="none" <?php if( $options['captionAnimation'] == 'none' ) echo 'checked="checked"'; ?> />
								<label><?php _e('None', 'wp-orbit-slider'); ?></label>
							</p>
	
						</div>						
					</div><!-- #caption-settings -->


					<div id="bullet-settings" class="postbox">	
						<h3 class="hndle"><span><?php _e('Bullet Navigation', 'wp-orbit-slider'); ?></span></h3>
						<div class="inside">

                        	<!-- bullets -->
							<p>
								<strong><?php _e('Bullet Navigation:', 'wp-orbit-slider'); ?></strong><br />
								<input type="radio" name="orbit_slider_options[bullets]" value="true" <?php if( $options['bullets'] == 'true' ) echo 'checked="checked"'; ?> />
								<label><?php _e('On', 'wp-orbit-slider'); ?></label><br />
								
								<input type="radio" name="orbit_slider_options[bullets]" value="false" <?php if( 	$options['bullets'] == 'false' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Off', 'wp-orbit-slider'); ?></label>
							</p>
                            
                            <!-- centerBullets -->
                            <p>
								<strong><?php _e('Center Bullets:', 'wp-orbit-slider'); ?></strong><br />
								<input type="radio" name="orbit_slider_options[centerBullets]" value="true" <?php if( $options['centerBullets'] == 'true' ) echo 'checked="checked"'; ?> />
								<label><?php _e('True', 'wp-orbit-slider'); ?></label><br />
								
								<input type="radio" name="orbit_slider_options[centerBullets]" value="false" <?php if( 	$options['centerBullets'] == 'false' ) echo 'checked="checked"'; ?> />
								<label><?php _e('False', 'wp-orbit-slider'); ?></label>
							</p>
                            
                            <!-- bulletThumbs -->
                            <p>
								<strong><?php _e('Bullet Thumbs:', 'wp-orbit-slider'); ?></strong><br />
								<input type="radio" name="orbit_slider_options[bulletThumbs]" value="true" <?php if( $options['bulletThumbs'] == 'true' ) echo 'checked="checked"'; ?> />
								<label><?php _e('True', 'wp-orbit-slider'); ?></label><br />
								
								<input type="radio" name="orbit_slider_options[bulletThumbs]" value="false" <?php if( 	$options['bulletThumbs'] == 'false' ) echo 'checked="checked"'; ?> />
								<label><?php _e('False', 'wp-orbit-slider'); ?></label>
							</p>
	
						</div>	
					</div><!-- #bullet-settings -->


					<div id="autoplay-settings" class="postbox">	
						<h3 class="hndle"><span><?php _e('Autoplay Timer Settings', 'wp-orbit-slider'); ?></span></h3>
						<div class="inside">
						
                        	<!-- pauseOnHover -->
							<p>
								<strong><?php _e('Pause on Hover:', 'wp-orbit-slider'); ?></strong><br />
								<input type="radio" name="orbit_slider_options[pauseOnHover]" value="true" <?php if( $options['pauseOnHover'] == 'true' ) echo 'checked="checked"'; ?> />
								<label><?php _e('True', 'wp-orbit-slider'); ?></label><br />
								
								<input type="radio" name="orbit_slider_options[pauseOnHover]" value="false" <?php if( $options['pauseOnHover'] == 'false' ) echo 'checked="checked"'; ?> />
								<label><?php _e('False', 'wp-orbit-slider'); ?></label>
							</p>

                        	<!-- advanceSpeed -->
							<p>
								<strong><?php _e('Set Slider Time Delay:', 'wp-orbit-slider'); ?> </strong>
								<select name="orbit_slider_options[advanceSpeed]">
									<?php for($i=100; $i<=5000; $i+= 100): ?>
										<option value="<?php echo $i; ?>"<?php if( $options['advanceSpeed'] == $i ) echo 'selected="selected"'; ?>>
										<?php echo $i; ?> <?php _e('ms', 'wp-orbit-slider'); ?>
										</option>
									<?php endfor; ?>
								</select>
							</p>

                        	<!-- timer -->
							<p>
								<strong><?php _e('Slide Timer Animation', 'wp-orbit-slider'); ?></strong><br />
								<input type="radio" name="orbit_slider_options[timer]" value="true" <?php if( $options['timer'] == 'true' ) echo 'checked="checked"'; ?> />
								<label><?php _e('On', 'wp-orbit-slider'); ?></label><br />
								
								<input type="radio" name="orbit_slider_options[timer]" value="false" <?php if( $options['timer'] == 'false' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Off', 'wp-orbit-slider'); ?></label>
							</p>
	
						</div>
					</div><!-- #autplay-settings -->
						
					<div id="advanced-settings" class="postbox">						
						<h3 class="hndle"><span><?php _e('Advanced Settings', 'wp-orbit-slider'); ?></span></h3>						
						<div class="inside">		


                        	<!-- setImgSize -->
							<p>
								<strong><?php _e('As default, the slides are 540x450 but are responsive. If your slide needs to be larger or a different ratio, add_image_size in your functions.php - name the size: orbit-custom and select it from below. In all cases, the slider will fill stretch to the div that surrounds it.', 'wp-orbit-slider'); ?></strong><br />
								<input type="radio" name="orbit_slider_options[imgSize]" value="orbit-slide" <?php if( $options['imgSize'] == 'orbit-slide' ) echo 'checked="checked"'; ?> />
								<label><?php _e('540, 450 hard crop', 'wp-orbit-slider'); ?></label><br />
								
								<input type="radio" name="orbit_slider_options[imgSize]" value="orbit-custom" <?php if( $options['imgSize'] == 'orbit-custom' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Custom Size', 'wp-orbit-slider'); ?></label>
							</p>
						
                        	<!-- loadJs -->
							<p>
								<strong><?php _e('JS in...', 'wp-orbit-slider'); ?></strong><br />
								<input type="radio" name="orbit_slider_options[loadJs]" value="header" <?php if( $options['loadJs'] == 'header' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Header', 'wp-orbit-slider'); ?></label><br />
								
								<input type="radio" name="orbit_slider_options[loadJs]" value="footer" <?php if( $options['loadJs'] == 'footer' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Footer', 'wp-orbit-slider'); ?></label>
							</p>
                            
                            <!-- readyLoad -->
                            <p>
								<strong><?php _e('Load JS when DOC ready or WINDOW load', 'wp-orbit-slider'); ?></strong><br />
								<input type="radio" name="orbit_slider_options[readyLoad]" value="ready" <?php if( $options['readyLoad'] == 'ready' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Document Ready', 'wp-orbit-slider'); ?></label><br />
								
								<input type="radio" name="orbit_slider_options[readyLoad]" value="load" <?php if( $options['readyLoad'] == 'load' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Window Load', 'wp-orbit-slider'); ?></label>
     
							</p>
                            
                            <!-- sliderTheme -->
                            <p>
                            	<strong><?php _e('CSS Slider Theme Settings', 'wp-orbit-slider'); ?></strong><br />
								<input type="radio" name="orbit_slider_options[sliderTheme]" value="default" <?php if( $options['sliderTheme'] == 'default' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Default', 'wp-orbit-slider'); ?></label><br />
								
								<input type="radio" name="orbit_slider_options[sliderTheme]" value="custom" <?php if( $options['sliderTheme'] == 'custom' ) echo 'checked="checked"'; ?> />
								<label><?php _e('Custom', 'wp-orbit-slider'); ?></label>
							</p>
					
						</div>	
					</div><!-- #advanced-settings -->
                    
			
					<div class="submit">
						<input type="submit" class="button-primary" value="<?php _e('Save Settings', 'wp-orbit-slider') ?>" />
					</div>
			  
				</form>
			
			</div> 
		
		</div>
  
	</div>
	
	<div class="postbox-container" style="width:20%;">
	
		<div class="metabox-holder">
		
			<div class="meta-box-sortables ui-sortable">
				
				<div id="donate" class="postbox">
					<div class="inside">
						<p><?php _e('Have you found this plugin useful? Consider a donation. Chuck us a buck!', 'wp-orbit-slider'); ?></p>
                      <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                      <input type="hidden" name="cmd" value="_s-xclick">
                      <input type="hidden" name="hosted_button_id" value="ZY92BLQVS5BSS">
                      <input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
                      <img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
                      </form>
					</div> 
				</div>
<?php /*				
				<div id="extra" class="postbox">
					<h3></span></h3>
					<div class="inside">

					</div> 
				</div>
*/ ?>
					
			</div>  
		</div>	
	</div>	
</div>