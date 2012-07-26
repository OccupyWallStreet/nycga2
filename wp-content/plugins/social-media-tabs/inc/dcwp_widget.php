<?php
class dc_jqsocialmediatabs_widget extends WP_Widget {
	
	/** constructor */
    function dc_jqsocialmediatabs_widget() {
	
		$name =			'Social Media Tabs';
		$desc = 		'Sliding social media profile tabs - Facebook, Google, Twitter, YouTube & RSS Feeds';
		$id_base = 		'dc_jqsocialmediatabs_widget';
		$css_class = 	'dcsmt_widget';
		$alt_option = 	'widget_dcjq_social_media_tabs'; 
		$def_tabs = 'facebook,twitter,plusone,rss,youtube,flickr,pinterest';

		$widget_ops = array(
			'classname' => $css_class,
			'description' => __( $desc, 'dcjq-social-tabs' ),
		);
		
		$this->WP_Widget($id_base, __($name, 'dcjqsocialtabs'), $widget_ops);
		$this->alt_option_name = $alt_option;
		
		add_action( 'wp_head', array(&$this, 'styles'), 10, 1 );
		add_action( 'wp_footer', array(&$this, 'footer'), 10, 1 );
		add_action('init', array(&$this, 'my_init_method'));
		
		$options = get_option('dcsmt_options');
		$this->defaults = array(
			'method' => 'slide',
			'direction' => 'horizontal',
			'width' => 260,
			'height' => 290,
			'speedMenu' => 600,
			'position' => 'right',
			'offset' => 50,
			'autoClose' => '1',
			'start' => 0,
			'facebookId' => '',
			'facebook_width' => 240,
			'facebook_height' => 270,
			'facebook_connections' => 8,
			'googleId' => '',
			'nBuzz' => 5,
			'rssId' => '',
			'nRss' => 5,
			'nTweets' => 5,
			'nPinterest' => 5,
			'nFlickr' => 12,
			'twitter_follow' => 'none',
			'pinterest_follow' => '',
			'youtubeId' => '',
			'videoId' => '',
			'pinterestId' => '',
			'flickrId' => '',
			'custom' => '',
			'tab1' => 'facebook',
			'tab2' => 'twitter',
			'tab3' => 'plusone',
			'tab4' => 'rss',
			'tab5' => 'youtube',
			'tab6' => 'none',
			'tab7' => 'none',
			'tab8' => 'none'
		);
    }

	function my_init_method(){
			
		if ( version_compare( get_bloginfo( 'version' ) , '3.0' , '<' ) && is_ssl() ) {
			$wp_content_url = str_replace( 'http://' , 'https://' , get_option( 'siteurl' ) );
		} else {
			$wp_content_url = get_option( 'siteurl' );
		}
		$wp_content_url .= '/wp-content';
		$wp_plugin_url = $wp_content_url . '/plugins';

		wp_register_style('dcwp_plugin_admin_dcsmt_css', $wp_plugin_url .'/social-media-tabs/css/admin.css');
		wp_enqueue_style('dcwp_plugin_admin_dcsmt_css');
	}    

	function widget($args, $instance){
		extract( $args );
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );
		$options = get_option('dcsmt_options');
		$cache = $options['cache'] != '' ? $options['cache'] * 60 : '' ;
		$plugin_url = dc_jqsocialmediatabs::get_plugin_directory();
		$icon_url = $plugin_url.'/css/images/';
		
		$check = '';
		$dcsmt_nav = '';
		$dcsmt_tabs = '';
		$i = 0;
		$deftabs = explode(',','facebook,twitter,plusone,rss,youtube,flickr,pinterest,extra,custom');
		foreach($deftabs as $k=>$tabv){
			if($tabv != ''){
				$k = $k+1;
				$tab = $widget_options['tab'.$k];
				if($tab != 'none' && $tab != ''){
					$checkTab = strlen(strstr($check,$tab)) > 0 ? 1 : 0 ;
					if($checkTab == 0){
						$class = $i == 0 ? 'first ' : '' ;
						$icon = $options['icon_'.$tab] == '' ? '<img src="'.$icon_url.$tab.'.png" alt="" />' : '<img src="'.$options['icon_'.$tab].'" alt="" />';
						$f_tab = 'dcsmt_inc_'.$tab;
						
						if($cache != '')
						{
							$x1 = implode('-',$args);
							$x2 = implode('-',$instance);
							$cache_key = $f_tab . '_' . md5($x1.$x2);
							$tabContent = get_transient( $cache_key );
							
							// if no cached results
							if ( false === ( $tabContent = get_transient( $cache_key ) ) ) {
								$tabContent = $this->$f_tab($args, $instance);
								set_transient( $cache_key, $tabContent, $cache );
							}
						}
						else 
						{
							$tabContent = $this->$f_tab($args, $instance);
						}
						$dcsmt_nav .= '<li class="'.$class.'dcsmt-'.$tab.'"><a href="#" rel="'.$i.'">'.$icon.'</a></li>';
						$dcsmt_tabs .= '<li class="tab-content '.$this->id.'-tab">'.$tabContent.'</li>';
						$i++;
						$check .= ','.$tab;
					}
				}
			}
		}

		?>
		<div id="<?php echo $this->id.'-item'; ?>">
			<ul class="social-tabs">
				<?php echo $dcsmt_nav; ?>
			</ul>
			<ul class="dcsmt <?php echo $this->id.'-slide'; ?>">
				<?php echo $dcsmt_tabs; ?>
			</ul>
		</div>
	
		<?php
	}

    /** @see WP_Widget::update */
    function update( $new_instance, $old_instance ) {
	
		$instance['method'] = $new_instance['method'];
		$instance['direction'] = $new_instance['direction'];
		$instance['width'] = (int) strip_tags( stripslashes($new_instance['width']) );
		$instance['height'] = (int) strip_tags( stripslashes($new_instance['height']) );
		$instance['speedMenu'] = (int) strip_tags( stripslashes($new_instance['speedMenu']) );
		$instance['position'] = $new_instance['position'];
		$instance['offset'] = (int) strip_tags( stripslashes($new_instance['offset']) );
		$instance['skin'] = $new_instance['skin'];
		$instance['autoClose'] = $new_instance['autoClose'];
		$instance['loadOpen'] = $new_instance['loadOpen'];
		$instance['facebookId'] = strip_tags( stripslashes($new_instance['facebookId']) );
		$instance['facebook_width'] = (int) strip_tags( stripslashes($new_instance['facebook_width']) );
		$instance['facebook_height'] = (int) strip_tags( stripslashes($new_instance['facebook_height']) );
		$instance['facebook_connections'] = (int) strip_tags( stripslashes($new_instance['facebook_connections']) );
		
		$instance['googleId'] = strip_tags( stripslashes($new_instance['googleId']) );
		$instance['nBuzz'] = (int) strip_tags( stripslashes($new_instance['nBuzz']) );
		$instance['rssId'] = strip_tags( stripslashes($new_instance['rssId']) );
		$instance['nRss'] = (int) strip_tags( stripslashes($new_instance['nRss']) );
		$instance['rssTitle'] = strip_tags( stripslashes($new_instance['rssTitle']) );
		$instance['twitterUrl'] = strip_tags( stripslashes($new_instance['twitterUrl']) );
		$instance['twitterTitle'] = strip_tags( stripslashes($new_instance['twitterTitle']) );
		$instance['twitter_follow'] = strip_tags( stripslashes($new_instance['twitter_follow']) );
		$instance['nTweets'] = (int) strip_tags( stripslashes($new_instance['nTweets']) );
		$instance['youtubeId'] = strip_tags( stripslashes($new_instance['youtubeId']) );
		$instance['videoId'] = strip_tags( stripslashes($new_instance['videoId']) );
		$instance['pinterestId'] = strip_tags( stripslashes($new_instance['pinterestId']) );
		$instance['pinterestTitle'] = strip_tags( stripslashes($new_instance['pinterestTitle']) );
		$instance['nPinterest'] = strip_tags( stripslashes($new_instance['nPinterest']) );
		$instance['pinterest_follow'] = strip_tags( stripslashes($new_instance['pinterest_follow']) );
		
		$instance['flickrId'] = strip_tags( stripslashes($new_instance['flickrId']) );
		$instance['flickrTitle'] = strip_tags( stripslashes($new_instance['flickrTitle']) );
		$instance['nFlickr'] = strip_tags( stripslashes($new_instance['nFlickr']) );
		$instance['custom'] = strip_tags( stripslashes($new_instance['custom']) );
		$instance['customTitle'] = strip_tags( stripslashes($new_instance['customTitle']) );
		
		$deftabs = explode(',','facebook,twitter,plusone,rss,youtube,flickr,pinterest,custom');
		foreach($deftabs as $k=>$tabv){
			if($tabv != ''){
				$k = $k+1;
				$instance['tab'.$k] = strip_tags( stripslashes($new_instance['tab'.$k]) );
			}
		}
		$instance['start'] = strip_tags( stripslashes($new_instance['start']) );

		return $instance;
	}

    /** @see WP_Widget::form */
    function form($instance) {
	
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );
		
		// Get default values
		settings_fields('dcsmt_options_group'); $options = get_option('dcsmt_options');
		$nRss = isset( $instance['nRss'] ) ? $instance['nRss'] : '5';
		$twitterUrl = isset( $instance['twitterUrl'] ) ? $instance['twitterUrl'] : '';
		$twitter_follow = isset( $instance['twitter_follow'] ) ? $instance['twitter_follow'] : 'none';
		$nTweets = isset( $instance['nTweets'] ) ? $instance['nTweets'] : '5';
		$nBuzz = isset( $instance['nBuzz'] ) ? $instance['nBuzz'] : '5';
		$nPinterest = isset( $instance['nPinterest'] ) ? $instance['nPinterest'] : '5';
		$pinterest_follow = isset( $instance['pinterest_follow'] ) ? $instance['pinterest_follow'] : '';
		
		$nFlickr = isset( $instance['nFlickr'] ) ? $instance['nFlickr'] : '12';
		$method = isset( $instance['method'] ) ? $instance['method'] : 'static';
		$direction = isset( $instance['direction'] ) ? $instance['direction'] : 'horizontal';
		$start = isset( $instance['start'] ) ? $instance['start'] : '0';
		
		?>
	<p>
		<label for="<?php echo $this->get_field_id('method1'); ?>"><?php _e('Tabs:') ?></label>
		<input type="radio" id="<?php echo $this->get_field_id('method1'); ?>" name="<?php echo $this->get_field_name('method'); ?>" value="slide"<?php checked( $method, 'slide' ); ?> class="method-slide" /> 
		<label for="<?php echo $this->get_field_id('method1'); ?>"><?php _e( 'Slide Out' , 'dcjq-social-tabs' ); ?></label>
		<input type="radio" id="<?php echo $this->get_field_id('method2'); ?>" name="<?php echo $this->get_field_name('method'); ?>" value="static"<?php checked( $method, 'static' ); ?> class="method-static" /> 
		<label for="<?php echo $this->get_field_id('method2'); ?>"><?php _e( 'Static' , 'dcjq-social-tabs' ); ?></label>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('direction1'); ?>"><?php _e('Slider:') ?></label>
		<input type="radio" id="<?php echo $this->get_field_id('direction1'); ?>" name="<?php echo $this->get_field_name('direction'); ?>" value="horizontal"<?php checked( $direction, 'horizontal' ); ?> /> 
		<label for="<?php echo $this->get_field_id('direction1'); ?>"><?php _e( 'Horizontal' , 'dcjq-social-tabs' ); ?></label>
		<input type="radio" id="<?php echo $this->get_field_id('direction2'); ?>" name="<?php echo $this->get_field_name('direction'); ?>" value="vertical"<?php checked( $direction, 'vertical' ); ?> /> 
		<label for="<?php echo $this->get_field_id('direction2'); ?>"><?php _e( 'Vertical' , 'dcjq-social-tabs' ); ?></label>
	</p>
	<p class="dcwp-row">
		Width: 
		<input type="text" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $widget_options['width']; ?>" size="4" /> 
	
		Height:
		<input type="text" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo $widget_options['height']; ?>" size="4" />
	</p>
	
	<p class="dcwp-row">
	  <label for="<?php echo $this->get_field_id('position'); ?>"><?php _e( 'Location' , 'dcjq-social-tabs' ); ?></label>
		<select name="<?php echo $this->get_field_name('position'); ?>" id="<?php echo $this->get_field_id('position'); ?>" >
			<option value='top-left' <?php selected( $widget_options['position'], 'top-left'); ?> >Top Left</option>
			<option value='top-right' <?php selected( $widget_options['position'], 'top-right'); ?> >Top Right</option>
			<option value='bottom-left' <?php selected( $widget_options['position'], 'bottom-left'); ?> >Bottom Left</option>
			<option value='bottom-right' <?php selected( $widget_options['position'], 'bottom-right'); ?> >Bottom Right</option>
			<option value='left' <?php selected( $widget_options['position'], 'left'); ?> >Left</option>
			<option value='right' <?php selected( $widget_options['position'], 'right'); ?> >Right</option>
		</select>
	</p>
	
	<p class="dcwp-row">

	<label for="<?php echo $this->get_field_id('offset'); ?>"><?php _e( 'Offset:' , 'dcjq-social-tabs' ); ?></label>
		<input type="text" id="<?php echo $this->get_field_id('offset'); ?>" name="<?php echo $this->get_field_name('offset'); ?>" value="<?php echo $widget_options['offset']; ?>" size="4" /> 
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('speedMenu'); ?>"><?php _e('Slide Speed:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('speedMenu'); ?>" name="<?php echo $this->get_field_name('speedMenu'); ?>" value="<?php echo $widget_options['speedMenu']; ?>" size="5" /> (ms)
	</p>
	
	<p>
		<label for="<?php echo $this->get_field_id('autoClose'); ?>"><?php _e( 'Auto-Close' , 'dcjq-social-tabs' ); ?></label> 
		<input type="checkbox" value="true" class="checkbox" id="<?php echo $this->get_field_id('autoClose'); ?>" name="<?php echo $this->get_field_name('autoClose'); ?>"<?php checked( $widget_options['autoClose'], 'true'); ?> style="margin-right: 5px;" /> 
	
		<label for="<?php echo $this->get_field_id('loadOpen'); ?>"><?php _e( 'Load Open' , 'dcjq-social-tabs' ); ?></label> 
		<input type="checkbox" value="true" class="checkbox" id="<?php echo $this->get_field_id('loadOpen'); ?>" name="<?php echo $this->get_field_name('loadOpen'); ?>"<?php checked( $widget_options['loadOpen'], 'true'); ?> />
	</p>
	<p class="dcwp-row"><strong>Tabs</strong></p>
	<?php
		$deftabs = explode(',','facebook,twitter,plusone,rss,youtube,flickr,pinterest');
		foreach($deftabs as $k=>$tabv){
			if($tabv != ''){
				$x = $k;
				$k = $k+1;
				$tab = isset( $instance['tab'.$k] ) ? $instance['tab'.$k] : $tabv;
				
				echo  $odd = $k%2 ? '<p class="dcwp-row">': '';
				echo $k.' '.$this->dcsmt_tab_options('tab'.$k, 'Tab '.$k, $tab);
				echo  $odd = $k%2 ? '': '</p>';
				
				$selected = $widget_options['start'] == $x ? ' selected="selected"':'';
				$open .= '<option value="'.$x.'"'.$selected.'>Tab '.$k.'</option>';
			}
		}
	
	?>
	<p class="dcwp-row">
	  <label for="<?php echo $this->get_field_id('start'); ?>"><?php _e( 'Open Tab' , 'dcjq-social-tabs' ); ?></label>
		<select name="<?php echo $this->get_field_name('start'); ?>" id="<?php echo $this->get_field_id('start'); ?>" >
			<?php echo $open; ?>
		</select>
	</p>
	<p class="dcwp-row">
		<strong>Facebook</strong>
	</p>
	<p class="dcwp-row">
		<label for="<?php echo $this->get_field_id('facebookId'); ?>"><?php _e('ID:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('facebookId'); ?>" name="<?php echo $this->get_field_name('facebookId'); ?>" value="<?php echo $facebookId; ?>" />
	</p>
	<p class="dcwp-row">
		<label for="<?php echo $this->get_field_id('facebook_width'); ?>"><?php _e( 'Size:' , 'dcjq-social-tabs' ); ?></label> 
		 
		<input type="text" id="<?php echo $this->get_field_id('facebook_width'); ?>" name="<?php echo $this->get_field_name('facebook_width'); ?>" value="<?php echo $widget_options['facebook_width']; ?>" size="4" /> x 
		<input type="text" id="<?php echo $this->get_field_id('facebook_height'); ?>" name="<?php echo $this->get_field_name('facebook_height'); ?>" value="<?php echo $widget_options['facebook_height']; ?>" size="4" />
	</p>
	<p class="dcwp-row">
		Connections: 
		<input type="text" id="<?php echo $this->get_field_id('facebook_connections'); ?>" name="<?php echo $this->get_field_name('facebook_connections'); ?>" value="<?php echo $widget_options['facebook_connections']; ?>" size="4" /> 
	</p>
	<p class="dcwp-row">
		<strong>Google</strong>
	</p>
	<p class="dcwp-row">
		<label for="<?php echo $this->get_field_id('googleId'); ?>"><?php _e('ID:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('googleId'); ?>" name="<?php echo $this->get_field_name('googleId'); ?>" value="<?php echo $googleId; ?>" />
	</p>
	<p class="dcwp-row">
	  <label for="<?php echo $this->get_field_id('nBuzz'); ?>"><?php _e( 'Results' , 'dcjq-social-tabs' ); ?></label>
	  <input type="text" id="<?php echo $this->get_field_id('nBuzz'); ?>" name="<?php echo $this->get_field_name('nBuzz'); ?>" value="<?php echo $widget_options['nBuzz']; ?>" size="4" />
	</p>
	<p class="dcwp-row">
		<strong>Twitter</strong>
	</p>
	<p class="dcwp-row">
		<label for="<?php echo $this->get_field_id('twitterUrl'); ?>"><?php _e('Username:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('twitterUrl'); ?>" name="<?php echo $this->get_field_name('twitterUrl'); ?>" value="<?php echo $twitterUrl; ?>" />
	</p>
	<p class="dcwp-row">
		<label for="<?php echo $this->get_field_id('twitterTitle'); ?>"><?php _e('Title:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('twitterTitle'); ?>" name="<?php echo $this->get_field_name('twitterTitle'); ?>" value="<?php echo $twitterTitle; ?>" />
	</p>
	<p class="dcwp-row">
	  <label for="<?php echo $this->get_field_id('nTweets'); ?>"><?php _e( 'Tweets' , 'dcjq-social-tabs' ); ?></label>
		<input type="text" id="<?php echo $this->get_field_id('nTweets'); ?>" name="<?php echo $this->get_field_name('nTweets'); ?>" value="<?php echo $nTweets; ?>" size="4" />
	</p>
	<p class="dcwp-row">
	  <label for="<?php echo $this->get_field_id('twitter_follow'); ?>"><?php _e( 'Follow Button' , 'dcjq-social-tabs' ); ?></label>
		<select name="<?php echo $this->get_field_name('twitter_follow'); ?>" id="<?php echo $this->get_field_id('twitter_follow'); ?>">
			<option value='none' <?php selected( $widget_options['twitter_follow'], 'none'); ?> >None</option>
			<option value='light' <?php selected( $widget_options['twitter_follow'], 'light'); ?> >Button only</option>
			<option value='light_count' <?php selected( $widget_options['twitter_follow'], 'light_count'); ?> >Button + count</option>
		</select>
	</p>
	<p class="dcwp-row">
		<strong>RSS Feed</strong>
	</p>
	<p class="dcwp-row">
		<label for="<?php echo $this->get_field_id('rssId'); ?>"><?php _e('URL:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('rssId'); ?>" name="<?php echo $this->get_field_name('rssId'); ?>" value="<?php echo $widget_options['rssId']; ?>" />
	</p>
	<p class="dcwp-row">
		<label for="<?php echo $this->get_field_id('rssTitle'); ?>"><?php _e('Title:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('rssTitle'); ?>" name="<?php echo $this->get_field_name('rssTitle'); ?>" value="<?php echo $rssTitle; ?>" />
	</p>
	<p class="dcwp-row">
	  <label for="<?php echo $this->get_field_id('nRss'); ?>"><?php _e( 'Results:' , 'dcjq-social-tabs' ); ?></label>
		<input type="text" id="<?php echo $this->get_field_id('nRss'); ?>" name="<?php echo $this->get_field_name('nRss'); ?>" value="<?php echo $nRss; ?>" size="4" />
	</p>
	<p class="dcwp-row">
		<strong>YouTube</strong>
	</p>
	<p class="dcwp-row">
		<label for="<?php echo $this->get_field_id('youtubeId'); ?>"><?php _e('Username:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('youtubeId'); ?>" name="<?php echo $this->get_field_name('youtubeId'); ?>" value="<?php echo $youtubeId; ?>" />
	</p>
	<p class="dcwp-row">
		<label for="<?php echo $this->get_field_id('videoId'); ?>"><?php _e('Video ID:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('videoId'); ?>" name="<?php echo $this->get_field_name('videoId'); ?>" value="<?php echo $videoId; ?>" />
	</p>
	<p class="dcwp-row">
		<strong>Flickr</strong>
	</p>
	<p class="dcwp-row">
		<label for="<?php echo $this->get_field_id('flickrId'); ?>"><?php _e('User ID:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('flickrId'); ?>" name="<?php echo $this->get_field_name('flickrId'); ?>" value="<?php echo $flickrId; ?>" />
	</p>
	<p class="dcwp-row">
		<label for="<?php echo $this->get_field_id('flickrTitle'); ?>"><?php _e('Title:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('flickrTitle'); ?>" name="<?php echo $this->get_field_name('flickrTitle'); ?>" value="<?php echo $flickrTitle; ?>" />
	</p>
	<p class="dcwp-row">
	  <label for="<?php echo $this->get_field_id('nFlickr'); ?>"><?php _e( 'Results:' , 'dcjq-social-tabs' ); ?></label>
		<input type="text" id="<?php echo $this->get_field_id('nFlickr'); ?>" name="<?php echo $this->get_field_name('nFlickr'); ?>" value="<?php echo $nFlickr; ?>" size="4" />
	</p>
	<p class="dcwp-row">
		<strong>Pinterest</strong>
	</p>
	<p class="dcwp-row">
		<label for="<?php echo $this->get_field_id('pinterestId'); ?>"><?php _e('Username:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('pinterestId'); ?>" name="<?php echo $this->get_field_name('pinterestId'); ?>" value="<?php echo $pinterestId; ?>" />
	</p>
	<p class="dcwp-row">
		<label for="<?php echo $this->get_field_id('pinterestTitle'); ?>"><?php _e('Title:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('pinterestTitle'); ?>" name="<?php echo $this->get_field_name('pinterestTitle'); ?>" value="<?php echo $pinterestTitle; ?>" />
	</p>
	<p class="dcwp-row">
	  <label for="<?php echo $this->get_field_id('nPinterest'); ?>"><?php _e( 'Results:' , 'dcjq-social-tabs' ); ?></label>
		<input type="text" id="<?php echo $this->get_field_id('nPinterest'); ?>" name="<?php echo $this->get_field_name('nPinterest'); ?>" value="<?php echo $nPinterest; ?>" size="4" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('pinterest_follow'); ?>"><?php _e( 'Follow Button' , 'dcjq-social-tabs' ); ?></label> 
		<input type="checkbox" value="true" class="checkbox" id="<?php echo $this->get_field_id('pinterest_follow'); ?>" name="<?php echo $this->get_field_name('pinterest_follow'); ?>"<?php checked( $widget_options['pinterest_follow'], 'true'); ?> style="margin-right: 5px;" /> 
	</p>
	<p class="dcwp-row">
		<strong>Custom</strong>
	</p>
	<p class="dcwp-row">
		<label for="<?php echo $this->get_field_id('customTitle'); ?>"><?php _e('Title:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('customTitle'); ?>" name="<?php echo $this->get_field_name('customTitle'); ?>" value="<?php echo $customTitle; ?>" />
	</p>
	<p class="dcwp-row">
		<label for="<?php echo $this->get_field_id('custom'); ?>"><?php _e('Shortcode:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('custom'); ?>" name="<?php echo $this->get_field_name('custom'); ?>" value="<?php echo $custom; ?>" />
	</p>
	
	<div class="widget-control-actions alignright">
		<p><a href="http://www.designchemical.com/blog/index.php/wordpress-plugin-social-media-tabs/"><?php esc_attr_e('Visit plugin site', 'dcjq-social-tabs'); ?></a></p>
	</div>
	
	<?php 
	}
	
	/** Creates tab options. */
	function dcsmt_tab_options($id, $label, $value){
		
		$facebook = $value == 'facebook' ? '<option value="facebook" selected="selected">':'<option value="facebook">';
		$twitter = $value == 'twitter' ? '<option value="twitter" selected="selected">':'<option value="twitter">';
		$plusone = $value == 'plusone' ? '<option value="plusone" selected="selected">':'<option value="plusone">';
		$rss = $value == 'rss' ? '<option value="rss" selected="selected">':'<option value="rss">';
		$youtube = $value == 'youtube' ? '<option value="youtube" selected="selected">':'<option value="youtube">';
		$flickr = $value == 'flickr' ? '<option value="flickr" selected="selected">':'<option value="flickr">';
		$pinterest = $value == 'pinterest' ? '<option value="pinterest" selected="selected">':'<option value="pinterest">';
		$custom = $value == 'custom' ? '<option value="custom" selected="selected">':'<option value="custom">';
		$none = $value == 'none' ? '<option value="none" selected="selected">':'<option value="none">';
		
		$select = '<select name="'.$this->get_field_name($id).'" id="'.$this->get_field_id($id).'" class="dcsmt-select" style="margin-right: 5px;">';
		$select .= $facebook.'Facebook</option>';
		$select .= $twitter.'Twitter</option>';
		$select .= $plusone.'Google +1</option>';
		$select .= $rss.'RSS Feed</option>';
		$select .= $youtube.'YouTube</option>';
		$select .= $flickr.'Flickr</option>';
		$select .= $pinterest.'Pinterest</option>';
		$select .= $custom.'Custom</option>';
		$select .= $none.'None</option>';
		$select .= '</select>';
		
		return $select;
	}
	
	/** Adds ID based slick skin to the header. */
	function styles(){
		
		if(!is_admin()){

			$options = get_option('dcsmt_options');
			$skin = $options['skin'];
			if($skin != 'true'){
				echo "\n\t<link rel=\"stylesheet\" href=\"".dc_jqsocialmediatabs::get_plugin_directory()."/css/dcsmt.css\" type=\"text/css\" media=\"screen\"  />";
			}
		}
	}

	/** Adds ID based activation script to the footer */
	function footer(){
		
		if(!is_admin()){
		
		$all_widgets = $this->get_settings();
		
		foreach ($all_widgets as $key => $wpdcjqsocialtabs){
		
			$widget_id = $this->id_base . '-' . $key;
		
			if(is_active_widget(false, $widget_id, $this->id_base)){
			
				$method = $wpdcjqsocialtabs['method'] == '' ? 'static' : $wpdcjqsocialtabs['method'];
				$direction = $wpdcjqsocialtabs['direction'] == '' ? 'horizontal' : $wpdcjqsocialtabs['direction'];
				$position = $wpdcjqsocialtabs['position'];
				if($position == 'top-left'){
					$location = 'top';
					$align = 'left';
				}
				if($position == 'top-right'){
					$location = 'top';
					$align = 'right';
				}
				if($position == 'bottom-left'){
					$location = 'bottom';
					$align = 'left';
				}
				if($position == 'bottom-right'){
					$location = 'bottom';
					$align = 'right';
				}
				
				if($position == 'left'){
					if($method == 'float'){
						$location = 'top';
						$align = 'left';
					} else {
						$location = 'left';
						$align = 'top';
					}
				}
				
				if($position == 'right'){
					if($method == 'float'){
						$location = 'top';
						$align = 'right';
					} else {
						$location = 'right';
						$align = 'top';
					}
				}
				
				$width = $wpdcjqsocialtabs['width'] == '' ? 260 : $wpdcjqsocialtabs['width'];
				$height = $wpdcjqsocialtabs['height'] == '' ? 260 : $wpdcjqsocialtabs['height'];
				$speedMenu = $wpdcjqsocialtabs['speedMenu'] == '' ? 600 : $wpdcjqsocialtabs['speedMenu'];
				$offset = $wpdcjqsocialtabs['offset'] == '' ? 0 : $wpdcjqsocialtabs['offset'];
				$autoClose = $wpdcjqsocialtabs['autoClose'] == '' ? 'false' : $wpdcjqsocialtabs['autoClose'];
				$loadOpen = $wpdcjqsocialtabs['loadOpen'] == '' ? 'false' : $wpdcjqsocialtabs['loadOpen'];
				$start = $wpdcjqsocialtabs['start'] == '' ? 0 : $wpdcjqsocialtabs['start'];
				
			?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$('#<?php echo $widget_id.'-item'; ?>').dcSlickTabs({
						location: '<?php echo $location; ?>',
						align: '<?php echo $align; ?>',
						offset: <?php echo $offset; ?>,
						speed: <?php echo $speedMenu; ?>,
						width: <?php echo $width; ?>,
						height: <?php echo $height; ?>,
						slider: '<?php echo $widget_id.'-slide'; ?>',
						slides: '<?php echo $widget_id.'-tab'; ?>',
						tabs: 'social-tabs',
						slideWrap: '<?php echo $widget_id.'-wrap'; ?>',
						direction: '<?php echo $direction; ?>',
						autoClose: <?php echo $autoClose; ?>,
						method: '<?php echo $method; ?>',
						start: <?php echo $start; ?>
					});
					<?php
						if($this->get_dcsmt_default('links') == 'true') { 
					?>
					$('.dc-social .tab-content a').click(function(){
						this.target = "_blank";
					});
					<?php } ?>
				});
			</script>
		
			<?php
			
			}		
		}
		}
	}
	
	function get_dcsmt_default($option){

		$options = get_option('dcsmt_options');
		$default = $options[$option];
		return $default;
	}
	
		/* Twitter */
	function dcsmt_inc_twitter($args, $instance){
	
		extract( $args );

		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );
		$options = get_option('dcsmt_options');
		$twitterUrl = $widget_options['twitterUrl'];
		$nTweets = $widget_options['nTweets'];
		$twitter_follow = $widget_options['twitter_follow'];
		$replies = $options['twitter_replies'];
		$lang = $options['twitter_lang'] == '' ? 'en' : $options['twitter_lang'];
		$icon_close = '<a href="#" class="dcsmt-close dcsmt-close-tab"></a>';
		$title = $widget_options['twitterTitle'] == '' ? '' : '<h3><a href="'.esc_url( "http://twitter.com/{$twitterUrl}" ).'">'.$widget_options['twitterTitle'].'</a>'.$icon_close.'</h3>';
		
		$follow = '';
		if($twitter_follow != 'none'){
			$count = $twitter_follow == 'light_count' ? 'data-show-count="true"' : 'data-show-count="false"' ;
			$follow = '<div class="dcsmt-twitter-follow"><a href="https://twitter.com/'.$twitterUrl.'" class="twitter-follow-button" '.$count.' data-lang="'.$lang.'">Follow @'.$twitterUrl.'</a><script src="//platform.twitter.com/widgets.js" type="text/javascript"></script></div>';
		}
	
		// Chec for json class
		if ( version_compare( $wp_version, '2.9', '<' ) && !class_exists( 'Services_JSON' ) ) {
			
		}
		
		$params = array(
				'screen_name'=>$twitterUrl,
				'trim_user'=>true,
				'include_entities'=>false
			);

			/**
			 * The exclude_replies parameter filters out replies on the server. If combined with count it only filters that number of tweets (not all tweets up to the requested count)
			 * If we are not filtering out replies then we should specify our requested tweet count
			 */
			if (!$replies) {
				$params['exclude_replies'] = true;
			} else {
				$params['count'] = $nTweets;
			}
			
			if ($retweets){
				$params['include_rts'] = true;
			}
			$url_json = esc_url_raw('http://api.twitter.com/1/statuses/user_timeline.json?' . http_build_query($params), array('http', 'https'));
			unset($params);
			
			$response = wp_remote_get($url_json, array('User-Agent' => 'WordPress Social Media Tabs'));
			$response_code = wp_remote_retrieve_response_code($response);
			
			if (200 == $response_code){
				$tweets = wp_remote_retrieve_body($response);
				$tweets = json_decode($tweets, true);
				$expire = 900;
				if (!is_array( $tweets ) || isset( $tweets['error'] )){
					$tweets = 'error';
					$expire = 300;
				}
			} else {
				$tweets = 'error';
				$expire = 300;
				wp_cache_add('social-media-tabs-response-code-' . $this->number, $response_code,'widget', $expire);
			}

			wp_cache_add('social-media-tabs-' . $this->number, $tweets, 'widget', $expire);
		
		$tab = '<div class="tab-twitter tab-inner">'.$title.$follow.'<ul class="list-dcsmt-twitter">' . "\n";
		
		if ('error' != $tweets) :
			$before_timesince = ' ';
			if ( isset( $instance['beforetimesince'] ) && !empty( $instance['beforetimesince'] ) )
				$before_timesince = esc_html($instance['beforetimesince']);
			$before_tweet = '';
			if ( isset( $instance['beforetweet'] ) && !empty( $instance['beforetweet'] ) )
				$before_tweet = stripslashes(wp_filter_post_kses($instance['beforetweet']));

			$tweets_out = 0;
			foreach ((array) $tweets as $tweet){
				if ($tweets_out >= $nTweets)
					break;
				if (empty( $tweet['text']))
					continue;
				$text = make_clickable(esc_html($tweet['text']));

				/* Twitter regex patterns - http://github.com/mzsanford/twitter-text-rb/blob/master/lib/regex.rb */
				$text = preg_replace_callback('/(^|[^0-9A-Z&\/]+)(#|\xef\xbc\x83)([0-9A-Z_]*[A-Z_]+[a-z0-9_\xc0-\xd6\xd8-\xf6\xf8\xff]*)/iu',  array($this, '_dcsmt_tweets_hashtag'), $text);
				
				$text = preg_replace_callback('/([^a-zA-Z0-9_]|^)([@\xef\xbc\xa0]+)([a-zA-Z0-9_]{1,20})(\/[a-zA-Z][a-zA-Z0-9\x80-\xff-]{0,79})?/u', array($this, '_dcsmt_tweets_username'), $text);
				
				if (isset($tweet['id_str'])){
					$tweet_id = urlencode($tweet['id_str']);
				} else {
					$tweet_id = urlencode($tweet['id']);
				}
				
				$tweetClass = $odd = $tweets_out%2 ? 'even dcsmt-twitter-item': 'odd dcsmt-twitter-item';
				
				if($tweets_out == 0){
					$tweetClass .= ' first';
				}
				
				$tab .= "<li class='{$tweetClass}'>{$before_tweet}{$text}{$before_timesince}<a href=\"" . esc_url( "http://twitter.com/{$twitterUrl}/statuses/{$tweet_id}" ) . '" class="time">' . str_replace(' ', '&nbsp;', wpcom_time_since(strtotime($tweet['created_at']))) . "&nbsp;ago</a></li>\n";
				unset($tweet_id);
				$tweets_out++;
			}
			
		else :
			$tab .= '<li class="dcsmt-error">';
			if ( 401 == wp_cache_get( 'dcsmt-tweets-response-code-' . $this->number , 'widget' ) )
				$tab .= esc_html__( 'An Error Occurred: Please make sure the Twitter account is public.') . '</li>';
			else
				$tab .= esc_html__('An Error Occurred: No response from Twitter. Please try again in a few minutes.') . '</li>';
		endif;
		
		$tab .= "</ul></div>\n";
		
		return $tab;
	}
	
		/**
	 * Twitter hashtag link to a search results page on Twitter.com
	 * @param array $matches regex match
	 * @return string Tweet text with inserted #hashtag link
	 */
	function _dcsmt_tweets_hashtag( $matches ) { // $matches has already been through wp_specialchars
		return "$matches[1]<a href='" . esc_url( 'http://twitter.com/search?q=%23' . urlencode( $matches[3] ) ) . "'>#$matches[3]</a>";
	}
	
	/**
	 * Twitter link to user profile.
	 * @param array $matches regex match
	 * @return string Tweet text with inserted @user link
	 */
	function _dcsmt_tweets_username( $matches ) { // $matches has already been through wp_specialchars
		return "$matches[1]@<a href='" . esc_url( 'http://twitter.com/' . urlencode( $matches[3] ) ) . "'>$matches[3]</a>";
	}
	
	/* Google +1 profile */
	function dcsmt_inc_plusone($args, $instance){
	
		$tab = '';
		extract( $args );
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );	
		$options = get_option('dcsmt_options');
		$id = $widget_options['googleId'];
		$nBuzz = $widget_options['nBuzz'];
		$title = $widget_options['plusoneTitle'] == '' ? '' : '<h3>'.$widget_options['plusoneTitle'].'</h3>';
		
		$tab .= '<script type="text/javascript">
//<![CDATA[
   jQuery(document).ready(function($) {
      $(".google-plus-activity").googlePlusActivity({
         api_key : "'.$options['google_api'].'"
         ,user:"'.$id.'"
         ,image_width:50
         ,image_height:50
         ,body_height:'.($widget_options['height']-80).'
      });
   });
//]]>
</script>';

		$tab .= '<div class="tab-plusone tab-inner">'.$title;
		$tab .= '<div class="google-plus-activity"></div>';
		$tab .= '</div>';
		
		return $tab;
	}
	
	/* Facebook */
	function dcsmt_inc_facebook($args, $instance){
	
		extract( $args );
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );
		$options = get_option('dcsmt_options');
		$facebookId = $widget_options['facebookId'];
		$width = $widget_options['facebook_width'];
		$height = $widget_options['facebook_height'];
		$connections = $widget_options['facebook_connections'];
		
		$tab .= '<div class="tab-facebook tab-inner"><iframe src="http://www.facebook.com/plugins/likebox.php?id='.$facebookId.'&amp;width='.$width.'&amp;connections='.$connections.'&amp;stream=false&amp;header=true&amp;height='.$height.'" scrolling="no" frameborder="0" style="border: none; background: #fff; overflow: hidden; width: '.$width.'px; height: '.$height.'px;" allowTransparency="true"></iframe></div>';
		
		return $tab;
	}
	
	/* RSS */
	function dcsmt_inc_rss($args, $instance){
	
		extract( $args );
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );
		$rssId = $widget_options['rssId'];
		$icon_close = '<a href="#" class="dcsmt-close dcsmt-close-tab"></a>';
		$title = $widget_options['rssTitle'] == '' ? '' : '<h3><a href="'.$rssId.'">'.$widget_options['rssTitle'].'</a>'.$icon_close.'</h3>';
		$nRss = $widget_options['nRss'];
		$tab = '';
		if($rssId != ''){
		
		require_once(ABSPATH.WPINC.'/feed.php');  

			$tab .= '<div class="tab-rss tab-inner">'.$title;
			$rss = fetch_feed($rssId);
			if (!is_wp_error( $rss ) ) :
				$maxitems = $rss->get_item_quantity($nRss);
				$rss_items = $rss->get_items(0, $maxitems); 
				
			endif;
			
			$tab .= '<ul class="list-dcsmt-rss">';
			
			if ($maxitems == 0) {
				$tab .= '<li class="odd dcsmt-rss-item">No items.</li>';
			} else {
				
				$count = 1;
				foreach ( $rss_items as $item ) :
				
					$time = wpcom_time_since(strtotime($item->get_date()));
					if($odd = $count%2){
						$rssClass = "odd dcsmt-rss-item";
					} else {
						$rssClass = "even dcsmt-rss-item";
					}
					$tab .= '<li class="'.$rssClass.'">'.esc_html( $item->get_title() );
					$tab .= ' ... <a href="'.esc_url( $item->get_permalink() ).'">'.$time.'&nbsp;ago</a></li>';
					$count++;
					
				endforeach;
				
			}
			
			$tab .= '</ul></div>';
			}
			return $tab;
	}
	
	/* YouTube */
	function dcsmt_inc_youtube($args, $instance){
	
		extract( $args );
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );
		$options = get_option('dcsmt_options');
		$youtubeId = $widget_options['youtubeId'];
		$videoId = $widget_options['videoId'];
		$height = $widget_options['height'];
		$width = $widget_options['width'];
		$ratio = 1.641;
		$maxwidth = $width - 20;
		$maxheight = $height - 125;
		$height = $maxwidth/$ratio;
		$width = $maxwidth;
		$tab = '';
		
		if($youtubeId != ''){
		
		if($height > $maxheight){
			$height = $maxheight;
			$width = $height * $ratio;
		}
		$padLeft = ($maxwidth-$width)/2;
		
		$tab .= '<div class="tab-youtube tab-inner"><iframe src="http://www.youtube.com/subscribe_widget?p='.$youtubeId.'" style="overflow: hidden; height: 105px; width: 100%; border: 0;" scrolling="no" frameBorder="0"></iframe>';
		if($videoId != ''){
			$tab .= '<div style="padding-left: '.$padLeft.'px;"><iframe title="YouTube video player" class="youtube-player" type="text/html" width="'.$width.'px" height="'.$height.'px" src="http://www.youtube.com/embed/'.$videoId.'" frameborder="0" allowFullScreen></iframe></div>';
		}
		$tab .= '</div>';
		
		}
		
		return $tab;
	}
	
	/* DC Flickr Images */
	function dcsmt_inc_flickr($args, $instance){

	extract( $args );
	$widget_options = wp_parse_args( $instance, $this->defaults );
	extract( $widget_options, EXTR_SKIP );
	$nFlickr = $widget_options['nFlickr'];
	$api = 'photos_public.gne?';
	$id = $widget_options['flickrId'];
	$icon_close = '<a href="#" class="dcsmt-close dcsmt-close-tab"></a>';
	$title = $widget_options['flickrTitle'] != '' ? '<h3>'.$widget_options['flickrTitle'].$icon_close.'</h3>' : '' ;
	$tag= uniqid('flickr');
	if($id != ''){
	$tab = '<script type="text/javascript">
		jQuery(document).ready(function($){
	
					$("#'.$tag.'").dcFlickr({
					limit: '.$nFlickr.', 
					style: "thumb",
					q: {id: "'.$id.'",
						lang: "en-us",
						format: "json",
						jsoncallback: "?"}
					});
		});
	</script>';
	$tab .= '<div class="tab-flickr tab-inner">'.$title;
	$tab .= '<ul id="'.$tag.'"></ul>';
	$tab .= '</div>';
	}
	return $tab;
}

	/* Pinterest */
	function dcsmt_inc_pinterest($args, $instance){
	
		extract( $args );
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );
		$id = $widget_options['pinterestId'];
		$rssId = 'http://pinterest.com/'.$id.'/feed.rss';
		$icon_close = '<a href="#" class="dcsmt-close dcsmt-close-tab"></a>';
		$title = $widget_options['pinterestTitle'] == '' ? '' : '<h3><a href="http://pinterest.com/'.$id.'/">'.$widget_options['pinterestTitle'].'</a>'.$icon_close.'</h3>';
		$nPinterest = $widget_options['nPinterest'];
		$tab = '';
		if($id != ''){
		
			require_once(ABSPATH.WPINC.'/feed.php');  

			$tab = '<script type="text/javascript">
			jQuery(document).ready(function($) {
			$(".tab-pinterest").jCarouselLitedcsmt({
					btnNext: ".btn-pin.next",
					btnPrev: ".btn-pin.prev",
					visible: 1,
					scroll: 1,
					 speed: 800,
					 auto: 0,
					 width: '.($widget_options['width']-20).',
					  height: '.($widget_options['height']-100).'
				});
		  });
		</script>';
	
			$tab .= '<div class="tab-pinterest tab-inner" style="width: '.$widget_options['width'].';">'.$title;
			$rss = fetch_feed($rssId);
		//	print_r($rss);
			$maxitems = $rss->get_item_quantity((int)$nPinterest);
			$rss_items = $rss->get_items(0,$maxitems);
			
			
			$tab .= '<div class="carousel" style="width: '.$widget_options['width'].';">'."\n";
			$tab .= '<ul class="list-dcsmt-pinterest">';
			$count = 1;
				foreach ( $rss_items as $item ) :
					
					$time = wpcom_time_since(strtotime($item->get_date()));
					$tab .= '<li class="dcsmt-pinterest-item carousel-slide" style="width: '.$widget_options['width'].';"><div class="pin">';
					$tab .= '<p><a href="'.esc_url( $item->get_permalink() ).'">'.$item->get_description().'</a></p>';
					$tab .= '<p class="btm">Pinned '.$time.'&nbsp;ago</p>';
					$tab .= '</div></li>';
					$count++;
					
				endforeach;
				
			}
			
			$tab .= '</ul></div>';
			$tab .= '<a href="#" class="btn-pin prev"></a><a href="#" class="btn-pin next"></a>';
			$tab .= $widget_options['pinterest_follow'] == true ? '<a href="http://pinterest.com/'.$id.'/" class="dcsmt-pin-btn"><img src="http://passets-cdn.pinterest.com/images/follow-on-pinterest-button.png" width="156" height="26" alt="Follow Me on Pinterest" /></a>' : '' ;
			$tab .= '</div>';
			
			return $tab;
	}
	
	/* Custom shortcode */
	function dcsmt_inc_custom($args, $instance){
	
		extract( $args );
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );
		$options = get_option('dcsmt_options');
		$custom = $widget_options['custom'];
		$title = $widget_options['customTitle'] == '' ? '' : '<h3>'.$widget_options['customTitle'].$icon_close.'</h3>';
		
		$tab .= '<div class="tab-custom tab-inner">'.$title;
		$tab .= do_shortcode($custom);
		$tab .= '</div>';
		
		return $tab;
	}
	
	function get_string_between($string, $start, $end){
		$string = " ".$string;
		$ini = strpos($string,$start);
		if ($ini == 0) return "";
		$ini += strlen($start);
		$len = strpos($string,$end,$ini) - $ini;
		return substr($string,$ini,$len);
	}
	
	// Truncate text
		function dcwp_truncate($str, $length=10, $trailing='...'){
			$length-=strlen($trailing);
			if (strlen($str) > $length) {
				 return substr($str,0,$length).$trailing;
			} 
			else { 
				 $res = $str; 
			}
			return $res;
		}
		
} // class dc_jqsocialmediatabs_widget