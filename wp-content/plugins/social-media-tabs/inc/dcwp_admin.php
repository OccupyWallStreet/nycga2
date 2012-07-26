<?php
require_once('dcwp_plugin_admin.php');
require_once('dcwp_widget.php');

if(!class_exists('dc_jqsocialmediatabs_admin')) {
	
	class dc_jqsocialmediatabs_admin extends dcwp_plugin_admin_dcsmt {
	
		var $hook = 'social-media-tabs';
		var $longname = 'Social Media Tabs Configuration';
		var $shortname = 'Social Media Tabs';
		var $filename = 'social-media-tabs/dcwp_social_media_tabs.php';
		var $homepage = 'http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-social-media-tabs/';
		var $homeshort = 'http://bit.ly/q4SPdJ';
		var $twitter = 'designchemical';
		var $title = 'Wordpress plugin Social Media Tabs';
		var $description = 'Social media tabs allows you to add facebook, google +1, twitter, flickr, pinterest, YouTube subscription and RSS profiles and feeds to any widget area with stylish sliding tabs. Option also to have the tabs slide out from the side of the browsers.';
		var $def_tabs = 'facebook,twitter,plusone,rss,youtube,flickr,pinterest,custom';
		
		function __construct() {
		
			parent::__construct();
			
			add_action('admin_init', array(&$this,'settings_init'));
			
		}
		 
		function settings_init() {
		
			register_setting('dcsmt_options_group', 'dcsmt_options');
		}
		
		// Plugin specific side info box
		function info_box() {}
		
		function option_page() {
			
			$this->setup_admin_page('Social Media Tabs Settings','Social Media Tabs Configuration Settings');
			
		?>
		<?php if (!empty($message)) : ?>
			<div id="message" class="updated fade"><p><strong><?php echo $message ?></strong></p></div>
		<?php endif; ?>
		<p class="dcwp-intro">For instructions on how to configure this plugin check out the <a target="_blank" href="http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-social-media-tabs/"><?php echo $this->shortname; ?> project page</a>.</p>
		
		<form method="post" id="dcsmt_settings_page" class="dcwp-form" action="options.php">
			
			<?php 
				settings_fields('dcsmt_options_group'); $options = get_option('dcsmt_options'); 
				$plugin_url = dc_jqsocialmediatabs::get_plugin_directory();
				$icon_url = $plugin_url.'/css/images/';
				$skin = $options['skin'] ;
				$links = $options['links'] ? $options['links'] : 'true' ;
				$plusone_statistics = $options['plusone_statistics'];
				$twitter_replies = $options['twitter_replies'];
				$twitter_lang = $options['twitter_lang'] == '' ? 'en' : $options['twitter_lang'] ;
				$cache = $options['cache'] == '' ? '' : $options['cache'] ;
			?>
				<ul>
					<li>
					  <label for="dcsmt_skin">Disable Default Skin</label>
					  <input type="checkbox" value="true" class="checkbox" id="dcsmt_skin" name="dcsmt_options[skin]"<?php checked( $skin, 'true'); ?> />
					</li>
					<li>
					  <label for="dcsmt_links">Open Links In New Window</label>
					  <input type="checkbox" value="true" class="checkbox" id="dcsmt_links" name="dcsmt_options[links]"<?php checked( $links, 'true'); ?> />
					</li>
					<li>
						<label for="dcsmt_cache">Cache Results</label>
						<input type="text" id="dcsmt_cache" name="dcsmt_options[cache]" value="<?php echo $options['cache']; ?>" size="4" /> mins
					</li>
					<li><h4>Icons - Leave blank to use default images</h4></li>
					
					<?php
						$deftabs = explode(',',$this->def_tabs);
						foreach($deftabs as $tab){
							if($tab != ''){
								$icon = $options['icon_'.$tab] == '' ? '<img src="'.$icon_url.$tab.'.png" alt="" />' : '<img src="'.$options['icon_'.$tab].'" alt="" />';
								echo '<li class="dcsmt-icon">
						<label for="dcsmt_icon_'.$tab.'">'.$tab.'</label>
						<input type="text" id="dcsmt_icon_'.$tab.'" name="dcsmt_options[icon_'.$tab.']" value="'.$options['icon_'.$tab].'" size="30" />';
								echo $icon;
						echo '</li>';
							}
						}
					
					?>
					<li><h4>Google +1</h4></li>
					<!--
					<li>
						<label for="dcsmt_plusone_statistics">Add Statistics</label>
						<input type="checkbox" value="true" class="checkbox" id="dcsmt_plusone_statistics" name="dcsmt_options[plusone_statistics]"<?php //checked( $plusone_statistics, 'true'); ?> />
					</li>
					-->
					<li>
						<label for="dcsmt_google_api">API Key</label>
						<input type="text" id="dcsmt_google_api" name="dcsmt_options[google_api]" value="<?php echo $options['google_api']; ?>" size="30" />
					</li>
					<li><h4>Twitter</h4></li>
					<li>
						<label for="dcsmt_twitter_replies">Show Replies</label>
						<input type="checkbox" value="true" class="checkbox" id="dcsmt_twitter_replies" name="dcsmt_options[twitter_replies]"<?php checked( $twitter_replies, 'true'); ?>
					</li>
					<li>
						<label for="dcsmt_twitter_lang">Language options</label>
						<select class="select" id="dcsmt_twitter_lang" name="dcsmt_options[twitter_lang]"><option value="nl">Dutch</option>
							<option value="en"<?php echo $twitter_lang == 'en' ? ' selected="selected"' : ''; ?>>English</option>
							<option value="fr"<?php echo $twitter_lang == 'fr' ? ' selected="selected"' : ''; ?>>French</option>
							<option value="de"<?php echo $twitter_lang == 'de' ? ' selected="selected"' : ''; ?>>German</option>
							<option value="id"<?php echo $twitter_lang == 'id' ? ' selected="selected"' : ''; ?>>Indonesian</option>
							<option value="it"<?php echo $twitter_lang == 'it' ? ' selected="selected"' : ''; ?>>Italian</option>
							<option value="ja"<?php echo $twitter_lang == 'ja' ? ' selected="selected"' : ''; ?>>Japanese</option>
							<option value="ko"<?php echo $twitter_lang == 'ko' ? ' selected="selected"' : ''; ?>>Korean</option>
							<option value="pt"<?php echo $twitter_lang == 'pt' ? ' selected="selected"' : ''; ?>>Portuguese</option>
							<option value="ru"<?php echo $twitter_lang == 'ru' ? ' selected="selected"' : ''; ?>>Russian</option>
							<option value="es"<?php echo $twitter_lang == 'es' ? ' selected="selected"' : ''; ?>>Spanish</option>
							<option value="tr"<?php echo $twitter_lang == 'tr' ? ' selected="selected"' : ''; ?>>Turkish</option>
						</select>
					</li>
				</ul>

				<p class="submit">
				
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />

				</p>
				<p>Default icons <a href="http://komodomedia.com">Komodo Media, Rogie King</a></p>
		</form>			

			<?php

			$this->close_admin_page();

		}
	}
}