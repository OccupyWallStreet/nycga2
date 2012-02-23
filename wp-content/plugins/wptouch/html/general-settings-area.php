<?php global $wptouch_settings; ?>

<div class="metabox-holder">
	<div class="postbox">
		<h3><span class="global-settings">&nbsp;</span><?php _e( "General Settings", "wptouch" ); ?></h3>

			<div class="left-content">
				<h4><?php _e( "Regionalization Settings", "wptouch" ); ?></h4>
				<p><?php _e( "Select the language for WPtouch.  Custom .mo files should be placed in wp-content/wptouch/lang.", "wptouch" ); ?></p>
				<br /><br />

				<h4><?php _e( "Home Page Re-Direction", "wptouch" ); ?></h4>
				<p><?php echo sprintf( __( "WPtouch by default follows your %sWordPress &raquo; Reading Options%s.", "wptouch"), '<a href="options-reading.php">', '</a>' ); ?></p>

				<br /><br />
				
				<h4><?php _e( "Site Title", "wptouch" ); ?></h4>
				<p><?php _e( "You can change your site title (if needed) in WPtouch.", "wptouch" ); ?></p>

				<br /><br />

				<h4><?php _e( "Excluded Categories", "wptouch" ); ?></h4>
				<p><?php _e( "Categories by ID you want excluded everywhere in WPtouch.", "wptouch" ); ?></p>

				<h4><?php _e( "Excluded Tags", "wptouch" ); ?></h4>
				<p><?php _e( "Tags by ID you want excluded everywhere in WPtouch.", "wptouch" ); ?></p>

				<br /><br />

				<h4><?php _e( "Text Justification Options", "wptouch" ); ?></h4>
				<p><?php _e( "Set the alignment for text.", "wptouch" ); ?></p>

				<br /><br />
				
				<h4><?php _e( "Post Listings Options", "wptouch" ); ?></h4>
				<p><?php _e( "Choose between calendar Icons, post thumbnails (WP 2.9) or none for your post listings.", "wptouch" ); ?></p>
				<p><?php _e( "Select which meta items are shown below titles on main, search, &amp; archives pages.", "wptouch" ); ?></p>

				<br /><br /><br /><br /><br /><br /><br /><br /><br />

				<h4><?php _e( "Footer Message", "wptouch" ); ?></h4>
				<p><?php _e( "Customize the default footer message shown in WPtouch here.", "wptouch" ); ?></p>
			</div>

			<div class="right-content">
				<p><label for="home-page"><strong><?php _e( "WPtouch Language", "wptouch" ); ?></strong></label></p>
				<ul class="wptouch-make-li-italic">
					<li>
						<select name="wptouch-language">
							<option value="auto"<?php if ( $wptouch_settings['wptouch-language'] == "auto" ) echo " selected"; ?>><?php _e( "Automatically detected", "wptouch" ); ?></option>
							<option value="fr_FR"<?php if ( $wptouch_settings['wptouch-language'] == "fr_FR" ) echo " selected"; ?>>Français</option>
							<option value="es_ES"<?php if ( $wptouch_settings['wptouch-language'] == "es_ES" ) echo " selected"; ?>>Español</option>
							<option value="eu_EU"<?php if ( $wptouch_settings['wptouch-language'] == "eu_EU" ) echo " selected"; ?>>Basque</option>
							<!-- <option value="de_DE"<?php if ( $wptouch_settings['wptouch-language'] == "de_DE" ) echo " selected"; ?>>Deutsch</option> -->
							<option value="ja_JP"<?php if ( $wptouch_settings['wptouch-language'] == "ja_JP" ) echo " selected"; ?>>Japanese</option>
							
							<?php $custom_lang_files = bnc_get_wptouch_custom_lang_files(); ?>
							<?php if ( count( $custom_lang_files ) ) { ?>
								<?php foreach( $custom_lang_files as $lang_file ) { ?>
									<option value="<?php echo $lang_file->prefix; ?>"<?php if ( $wptouch_settings['wptouch-language'] == $lang_file->prefix ) echo " selected"; ?>><?php echo $lang_file->name; ?></option>
								<?php } ?>	
							<?php } ?>
						</select>
					</li>
				</ul>
				<br /><br />

				<p><label for="home-page"><strong><?php _e( "WPtouch Home Page", "wptouch" ); ?></strong></label></p>
				<?php $pages = bnc_get_pages_for_icons(); ?>
				<?php if ( count( $pages ) ) { ?>
					<?php wp_dropdown_pages( 'show_option_none=WordPress Settings&name=home-page&selected=' . bnc_get_selected_home_page()); ?>
				<?php } else {?>
					<strong class="no-pages"><?php _e( "You have no pages yet. Create some first!", "wptouch" ); ?></strong>
				<?php } ?>

				<br /><br /><br />

				<ul class="wptouch-make-li-italic">
					<li><input type="text" class="no-right-margin" name="header-title" value="<?php $str = $wptouch_settings['header-title']; echo stripslashes($str); ?>" /><?php _e( "Site title text", "wptouch" ); ?></li>
				</ul>

				<br /><br />

				<ul class="wptouch-make-li-italic">			
				<li><input name="excluded-cat-ids" class="no-right-margin" type="text" value="<?php $str = $wptouch_settings['excluded-cat-ids']; echo stripslashes($str); ?>" /><?php _e( "Comma list of Category IDs, eg: 1,2,3", "wptouch" ); ?></li>
				<li><input name="excluded-tag-ids" class="no-right-margin" type="text" value="<?php $str = $wptouch_settings['excluded-tag-ids']; echo stripslashes($str); ?>" /><?php _e( "Comma list of Tag IDs, eg: 1,2,3", "wptouch" ); ?></li>
				</ul>

				<br /><br />

				<ul class="wptouch-make-li-italic">

					<li><select name="style-text-justify">
							<option <?php if ($wptouch_settings['style-text-justify'] == "left-justified") echo " selected"; ?> value="left-justified"><?php _e( "Left", "wptouch" ); ?></option>
							<option <?php if ($wptouch_settings['style-text-justify'] == "full-justified") echo " selected"; ?> value="full-justified"><?php _e( "Full", "wptouch" ); ?></option>
						</select>
						<?php _e( "Font justification", "wptouch" ); ?>
					</li>
				</ul>
				<br />
				<ul>
					<li>
						<ul class="wptouch-make-li-italic">		
							<li><select name="post-cal-thumb">
									<option <?php if ($wptouch_settings['post-cal-thumb'] == "calendar-icons") echo " selected"; ?> value="calendar-icons"><?php _e( "Calendar Icons", "wptouch" ); ?></option>
									<option <?php $version = bnc_get_wp_version(); if ($version <= 2.89) : ?>disabled="true"<?php endif; ?> <?php if ($wptouch_settings['post-cal-thumb'] == "post-thumbnails") echo " selected"; ?> value="post-thumbnails"><?php _e( "Post Thumbnails / Featured Images", "wptouch" ); ?></option>
									<option <?php $version = bnc_get_wp_version(); if ($version <= 2.89) : ?>disabled="true"<?php endif; ?> <?php if ($wptouch_settings['post-cal-thumb'] == "post-thumbnails-random") echo " selected"; ?> value="post-thumbnails-random"><?php _e( "Post Thumbnails / Featured Images (Random)", "wptouch" ); ?></option>
									<option <?php if ($wptouch_settings['post-cal-thumb'] == "nothing-shown") echo " selected"; ?> value="nothing-shown"><?php _e( "No Icon or Thumbnail", "wptouch" ); ?></option>
								</select>
								<?php _e( "Post Listings Display", "wptouch" ); ?> <small>(<?php _e( "Thumbnails Requires WordPress 2.9+", "wptouch" ); ?>)</small> <a href="#thumbs-info" class="fancylink">?</a>
				<div id="thumbs-info" style="display:none">
					<h2><?php _e( "More Info", "wptouch" ); ?></h2>
					<p><?php _e( "This will change the display of blog and post listings between Calendar Icons view and Post Thumbnails view.", "wptouch" ); ?></p>
					<p><?php _e( "The <em>Post Thumbnails w/ Random</em> option will fill missing post thumbnails with random abstract images. (WP 2.9+)", "wptouch" ); ?></p>
				</div>
							</li>
						</ul>	
					</li>
					<li>
						<input type="checkbox" class="checkbox" name="enable-truncated-titles" <?php if (isset($wptouch_settings['enable-truncated-titles']) && $wptouch_settings['enable-truncated-titles'] == 1) echo('checked'); ?> />
						<label for="enable-truncated-titles"><?php _e( "Enable Truncated Titles", "wptouch" ); ?> <small>(<?php _e( "Will use ellipses when titles are too long instead of wrapping them", "wptouch" ); ?>)</small></label>
					</li>					
					<li>
						<input type="checkbox" class="checkbox" name="enable-main-name" <?php if (isset($wptouch_settings['enable-main-name']) && $wptouch_settings['enable-main-name'] == 1) echo('checked'); ?> />
						<label for="enable-authorname"> <?php _e( "Show Author's Name", "wptouch" ); ?></label>
					</li>			
					<li>
						<input type="checkbox" class="checkbox" name="enable-main-categories" <?php if (isset($wptouch_settings['enable-main-categories']) && $wptouch_settings['enable-main-categories'] == 1) echo('checked'); ?> />
						<label for="enable-categories"> <?php _e( "Show Categories", "wptouch" ); ?></label>
					</li>			
					<li>
						<input type="checkbox" class="checkbox" name="enable-main-tags" <?php if (isset($wptouch_settings['enable-main-tags']) && $wptouch_settings['enable-main-tags'] == 1) echo('checked'); ?> />
						<label for="enable-tags"> <?php _e( "Show Tags", "wptouch" ); ?></label>
					</li>			
					<li>
						<input type="checkbox" class="checkbox" name="enable-post-excerpts" <?php if (isset($wptouch_settings['enable-post-excerpts']) && $wptouch_settings['enable-post-excerpts'] == 1) echo('checked'); ?> />
						<label for="enable-excerpts"><?php _e( "Hide Excerpts", "wptouch" ); ?></label>
					</li>
				</ul>
				<br /><br />
				<ul class="wptouch-make-li-italic">
					<li><input type="text" class="no-right-margin footer-msg" name="custom-footer-msg" value="<?php $str = $wptouch_settings['custom-footer-msg']; echo stripslashes($str); ?>" /><?php _e( "Footer message", "wptouch" ); ?></li>
				</ul>
			</div>
			
	<div class="bnc-clearer"></div>
	</div><!-- postbox -->
</div><!-- metabox -->