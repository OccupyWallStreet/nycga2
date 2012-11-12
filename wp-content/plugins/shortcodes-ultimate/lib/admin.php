<?php

	/**
	 * Share buttons for admin page
	 */
	function su_share() {

		// Share data
		$title = str_replace( '+', '%20', urlencode( __( 'Shortcodes Ultimate', 'shortcodes-ultimate' ) ) );
		$text = str_replace( '+', '%20', urlencode( __( 'Must have WordPress plugin - Shortcodes Ultimate', 'shortcodes-ultimate' ) ) );
		$url = urlencode( 'http://shortcodes-ultimate.com/' );
		?>
		<div id="su-share">

			<!-- PlusOne -->
			<iframe src="https://plusone.google.com/_/+1/fastbutton?url=<?php echo $url; ?>&amp;size=medium&amp;count=true&amp;annotation=&amp;hl=en-US" style="width:80px;height:21px;" scrolling="no"></iframe>

			<!-- Twitter -->
			<iframe src="http://platform.twitter.com/widgets/tweet_button.html?url=<?php echo $url; ?>&amp;via=gn_themes&amp;text=<?php echo $text; ?>&amp;lang=en" style="width:105px;height:21px;" scrolling="no"></iframe>

			<!-- Facebook -->
			<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo $url; ?>&amp;send=false&amp;layout=button_count&amp;width=80&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;height=21&amp;locale=en_US" style="width:80px;height:21px;" scrolling="no"></iframe>

			<div id="su-donate-button">
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="LR3TK37ZCK5XG">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/ru_RU/i/scr/pixel.gif" width="1" height="1">
				</form>
			</div>
			<div id="su-donate-label"><?php _e( 'Support project', 'shortcodes-ultimate' ); ?></div>
		</div>
		<?php
	}

	/**
	 * Banners from partners
	 */
	function su_show_partners() {
		?>
		<h3><?php _e( 'Partners', 'shortcodes-ultimate' ); ?></h3>
		<p><a href="http://themefuse.com/wp-themes-shop/?plugin=shortcodes-ultimate" target="_blank"><img src="<?php echo su_plugin_url(); ?>/images/banners/themefuse.jpg" alt="ThemeFuse" width="445" height="220" /></a></p>
		<?php
	}

	/**
	 * Register administration page
	 */
	function shortcodes_ultimate_add_admin() {
		add_options_page( __( 'Shortcodes', 'shortcodes-ultimate' ), __( 'Shortcodes', 'shortcodes-ultimate' ), 'manage_options', 'shortcodes-ultimate', 'shortcodes_ultimate_admin_page' );
	}

	/**
	 * Administration page
	 */
	function shortcodes_ultimate_admin_page() {

		$checked = ' checked="checked"';
		$disabled_formatting = ( get_option( 'su_disable_custom_formatting' ) == 'on' ) ? $checked : '';
		$disabled_compatibility = ( get_option( 'su_compatibility_mode' ) == 'on' ) ? $checked : '';
		$disabled_scripts = get_option( 'su_disabled_scripts' );
		$disabled_styles = get_option( 'su_disabled_styles' );
		?>

		<!-- .wrap -->
		<div class="wrap">

			<div id="icon-options-general" class="icon32"><br /></div>
			<h2><?php _e( 'Shortcodes Ultimate', 'shortcodes-ultimate' ); ?></h2>

			<!-- #su-wrapper -->
			<div id="su-wrapper">

				<?php su_save_notification(); ?>

				<div id="su-tabs">
					<a class="su-current"><span><?php _e( 'About', 'shortcodes-ultimate' ); ?></span></a>
					<a><span><?php _e( 'Settings', 'shortcodes-ultimate' ); ?></span></a>
					<a><span><?php _e( 'Custom CSS', 'shortcodes-ultimate' ); ?></span></a>
					<a><span><?php _e( 'Shortcodes', 'shortcodes-ultimate' ); ?></span></a>
					<a><span><?php _e( 'Demo', 'shortcodes-ultimate' ); ?></span></a>
				</div>
				<div class="su-pane">
					<p class="su-message su-message-error"><?php _e( 'For full functionality of this page it is recommended to enable JavaScript.', 'shortcodes-ultimate' ); ?> <a href="http://www.enable-javascript.com/" target="_blank"><?php _e( 'Instructions', 'shortcodes-ultimate' ); ?></a></p>
					<div class="su-onethird-column">
						<h3><?php _e( 'FREE Support', 'shortcodes-ultimate' ); ?></h3>
						<p><a href="http://wordpress.org/support/plugin/shortcodes-ultimate" target="_blank"><?php _e( 'Support forum', 'shortcodes-ultimate' ); ?></a></p>
						<p><a href="http://twitter.com/gn_themes" target="_blank"><?php _e( 'Twitter', 'shortcodes-ultimate' ); ?></a></p>
					</div>

					<div class="su-twothird-column">
						<h3><?php _e( 'Do you love this plugin?', 'shortcodes-ultimate' ); ?></h3>
						<p><a href="http://wordpress.org/extend/plugins/shortcodes-ultimate/" target="_blank"><?php _e( 'Rate this plugin at wordpress.org', 'shortcodes-ultimate' ); ?></a> (<?php _e( '5 stars', 'shortcodes-ultimate' ); ?>)</p>
						<p><?php _e( 'Review this plugin in your blog', 'shortcodes-ultimate' ); ?></p>
						<p><strong><a href="http://shortcodes-ultimate.com/" target="_blank"><?php _e( 'Check Premium Addons', 'shortcodes-ultimate' ); ?></a> <?php _e( '(coming soon)', 'shortcodes-ultimate' ); ?></strong></p>
					</div>

					<div class="su-clear"></div>
					<?php
					// Show partners banners
					su_show_partners();
					?>
					<div class="su-clear"></div>
					<?php
					// Share buttons
					su_share();
					?>

				</div>
				<div class="su-pane">
					<form action="" method="post" id="su-form-save-settings">
						<p class="su-message su-message-loading"><?php _e( 'Saving...', 'shortcodes-ultimate' ); ?></p>
						<p class="su-message su-message-success"><?php _e( 'Settings saved', 'shortcodes-ultimate' ); ?></p>
						<table class="fixed">
							<tr>
								<td width="250">
									<p><label><input type="checkbox" name="su_disable_custom_formatting" <?php echo $disabled_formatting; ?> /> <?php _e( 'Disable custom formatting', 'shortcodes-ultimate' ); ?></label></p>
								</td>
								<td>
									<p><small><?php _e( 'Enable this option if you have some problems with other plugins or content formatting', 'shortcodes-ultimate' ); ?></small></p>
								</td>
							</tr>
							<tr>
								<td>
									<p><label><input type="checkbox" name="su_compatibility_mode" <?php echo $disabled_compatibility; ?> /> <?php _e( 'Compatibility mode', 'shortcodes-ultimate' ); ?></label></p>
								</td>
								<td>
									<p><small><?php _e( 'Enable this option if you have some problems with other plugins that uses similar shortcode names', 'shortcodes-ultimate' ); ?><br/><code>[button] => [gn_button]</code><br/><a href="http://wordpress.org/support/topic/plugin-shortcodes-ultimate-shortcode-within-a-shortcode?replies=8#post-2081166" target="_blank"><?php _e( 'Forum topic', 'shortcodes-ultimate' ); ?></a></small></p>
								</td>
							</tr>
							<tr>
								<td colspan="2"><h4><?php _e( 'Disable scripts', 'shortcodes-ultimate' ); ?></h4></td>
							</tr>
							<tr>
								<td>
									<p><label><input type="checkbox" name="su_disabled_scripts[jquery]" <?php echo ( isset( $disabled_scripts['jquery'] ) ) ? $checked : ''; ?> /> <?php _e( 'Disable', 'shortcodes-ultimate' ); ?> jQuery</label></p>
									<p><label><input type="checkbox" name="su_disabled_scripts[nivo-slider]" <?php echo ( isset( $disabled_scripts['nivo-slider'] ) ) ? $checked : ''; ?> /> <?php _e( 'Disable', 'shortcodes-ultimate' ); ?> Nivo Slider</label></p>
									<p><label><input type="checkbox" name="su_disabled_scripts[jcarousel]" <?php echo ( isset( $disabled_scripts['jcarousel'] ) ) ? $checked : ''; ?> /> <?php _e( 'Disable', 'shortcodes-ultimate' ); ?> jCarousel</label></p>
									<p><label><input type="checkbox" name="su_disabled_scripts[jwplayer]" <?php echo ( isset( $disabled_scripts['jwplayer'] ) ) ? $checked : ''; ?> /> <?php _e( 'Disable', 'shortcodes-ultimate' ); ?> JW Player</label></p>
									<p><label><input type="checkbox" name="su_disabled_scripts[init]" <?php echo ( isset( $disabled_scripts['init'] ) ) ? $checked : ''; ?> /> <?php _e( 'Disable', 'shortcodes-ultimate' ); ?> Init</label></p>
								</td>
								<td>
									<p><small><?php _e( 'Check scripts, that you want to exclude form wp_head section', 'shortcodes-ultimate' ); ?><br/><span class="su-warning"><?php _e( 'Be careful with this settings!', 'shortcodes-ultimate' ); ?></span></small></p>
								</td>
							</tr>
							<tr>
								<td colspan="2"><h4><?php _e( 'Disable styles', 'shortcodes-ultimate' ); ?></h4></td>
							</tr>
							<tr>
								<td>
									<p><label><input type="checkbox" name="su_disabled_styles[nivo-slider]" <?php echo ( isset( $disabled_styles['nivo-slider'] ) ) ? $checked : ''; ?> /> <?php _e( 'Disable', 'shortcodes-ultimate' ); ?> <code>nivo-slider.css</code></label></p>
									<p><label><input type="checkbox" name="su_disabled_styles[jcarousel]" <?php echo ( isset( $disabled_styles['jcarousel'] ) ) ? $checked : ''; ?> /> <?php _e( 'Disable', 'shortcodes-ultimate' ); ?> <code>jcarousel.css</code></label></p>
									<p><label><input type="checkbox" name="su_disabled_styles[style]" <?php echo ( isset( $disabled_styles['style'] ) ) ? $checked : ''; ?> /> <?php _e( 'Disable', 'shortcodes-ultimate' ); ?> <code>style.css</code></label></p>
								</td>
								<td>
									<p><small><?php _e( 'Check stylesheets, that you want to exclude form wp_head section', 'shortcodes-ultimate' ); ?><br/><span class="su-warning"><?php _e( 'Be careful with this settings!', 'shortcodes-ultimate' ); ?></span></small></p>
								</td>
							</tr>
							<tr><td><br/></td><td><br/></td></tr>
							<tr>
								<td colspan="2">
									<input type="submit" value="<?php _e( 'Save settings', 'shortcodes-ultimate' ); ?>" class="button-primary su-submit" />
									<span class="su-spin"><img src="<?php echo su_plugin_url(); ?>/images/admin/spin.gif" alt="" /></span>
									<div class="su-clear"></div>
									<input type="hidden" name="save" value="true" />
								</td>
							</tr>
						</table>
					</form>
				</div>
				<div class="su-pane">
					<form action="" method="post" id="su-form-save-custom-css">
						<p class="su-message su-message-loading"><?php _e( 'Saving...', 'shortcodes-ultimate' ); ?></p>
						<p class="su-message su-message-success"><?php _e( 'Custom CSS saved', 'shortcodes-ultimate' ); ?></p>
						<p><?php _e( 'You can add custom styles, that will override defaults', 'shortcodes-ultimate' ); ?></p>
						<p><a href="<?php echo su_plugin_url(); ?>/css/style.css" target="_blank"><?php _e( 'See original styles', 'shortcodes-ultimate' ); ?></a> ( <a href="<?php echo su_plugin_url(); ?>/css/nivoslider.css" target="_blank">nivoslider.css</a>, <a href="<?php echo su_plugin_url(); ?>/css/jcarousel.css" target="_blank">jcarousel.css</a> )</p>
						<p><textarea id="su-custom-css" name="su_custom_css" rows="14" cols="90"><?php echo get_option( 'su_custom_css' ); ?></textarea></p>
						<p>
							<input type="submit" value="<?php _e( 'Save styles', 'shortcodes-ultimate' ); ?>" class="button-primary su-submit" />
							<span class="su-spin"><img src="<?php echo su_plugin_url(); ?>/images/admin/spin.gif" alt="" /></span>
							<span class="su-clear"></span>
							<input type="hidden" name="save-css" value="true" />
						</p>
					</form>
				</div>
				<div class="su-pane">
					<table class="widefat fixed su-table-shortcodes">
						<tr>
							<th width="150"><?php _e( 'Shortcode', 'shortcodes-ultimate' ); ?></th>
							<th width="200"><?php _e( 'Parameters', 'shortcodes-ultimate' ); ?></th>
							<th><?php _e( 'Usage', 'shortcodes-ultimate' ); ?></th>
						</tr>
						<?php
						foreach ( su_shortcodes() as $id => $shortcode ) {
							if ( $shortcode['type'] != 'opengroup' && $shortcode['type'] != 'closegroup' ) {
								?>
								<tr>
									<td>
										<strong><?php echo $id; ?></strong><br/>
										<small><?php echo $shortcode['desc']; ?></small>
									</td>
									<td>
										<?php
										foreach ( $shortcode['atts'] as $attr_name => $attr ) {
											echo '<strong>' . $attr['desc'] . '</strong><br/>';
											echo $attr_name;
											if ( $attr['values'] ) {
												echo '="' . implode( '|', $attr['values'] ) . '"';
											} elseif ( $attr['default'] ) {
												echo '="' . $attr['default'] . '"';
											}
											echo '<br/>';
										}
										?>
									</td>
									<td><?php echo str_replace( '&lt;br/&gt;', '<br/>', htmlspecialchars( $shortcode['usage'] ) ); ?></td>
								</tr>
								<?php
							}
						}
						?>
					</table>
				</div>
				<div class="su-pane">
					<table class="widefat fixed su-table-demos">
						<tr>
							<th width="100"><?php _e( 'Shortcode', 'shortcodes-ultimate' ); ?></th>
							<th><?php _e( 'Demo', 'shortcodes-ultimate' ); ?></th>
						</tr>
						<?php
						foreach ( su_shortcodes() as $id => $shortcode ) {
							if ( $shortcode['type'] != 'opengroup' && $shortcode['type'] != 'closegroup' ) {
								?>
								<tr>
									<td>
										<strong><?php echo $shortcode['name']; ?></strong><br/>
										<small><?php echo $shortcode['desc']; ?></small>
									</td>
									<td><img src="<?php echo su_plugin_url(); ?>/images/demo/<?php echo $id; ?>.png" width="530" alt="<?php echo $shortcode['name']; ?>" /></td>
								</tr>
								<?php
							}
						}
						?>
					</table>
				</div>
			</div>
			<!-- /#su-wrapper -->

		</div>
		<!-- /.wrap -->
		<?php
	}

	add_action( 'admin_menu', 'shortcodes_ultimate_add_admin' );
?>