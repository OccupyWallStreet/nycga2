<?php


class PageLinesDashboard {
	
	
	function __contruct(){
		
		
		
	}
	
	function draw(){
		
		// Updates Dashboard
		
		$dashboards = '';
		
		$updates = $this->get_updates(); 
		
		$args = array(
			'title'	=> __( 'Your Available Updates', 'pagelines' ),
			'data'	=> $updates, 
			'icon'	=> PL_ADMIN_ICONS . '/download.png',
			'excerpt-trim'	=> 0
		); 

		$dashboards = $this->dashboard_pane('updates', $args); 
		
		// PageLines Blog Dashboard
		
		$args = array(
			'title'		=> __( 'News from the PageLines Blog', 'pagelines' ),
			'data'		=> PageLines_RSS::get_dash_rss( array( 'feed' => 'http://www.pagelines.com/feed/' ) ), 
			'classes'	=> 'news-dash pl-dash-half pl-dash-space', 
			'icon'		=> PL_ADMIN_ICONS . '/welcome.png', 
			'footer'	=> sprintf('Visit <a href="%s">PageLines Blog</a>', 'http://www.pagelines.com/blog')
		); 
		
		$dashboards .= $this->dashboard_pane('news', $args);
		
		// Latest from the Community
		$args = array(
			'title'	=> __( 'From the Community', 'pagelines' ),
			'data'	=> PageLines_RSS::get_dash_rss( array( 'feed' => 'http://www.pagelines.com/type/link/feed/', 'community' => true ) ),
			'classes'	=> 'news-dash pl-dash-half', 
			'icon'	=> PL_ADMIN_ICONS . '/users.png', 
			'footer' => sprintf('<a href="%s">Submit</a> a community article', 'mailto:hello@pagelines.com')
		); 
		
		$dashboards .= $this->dashboard_pane('community', $args);
		
		// PageLines Store Latest Dash
		
		$args = array(
			'title'		=> __( 'Updates on PageLines Store', 'pagelines' ),
			'data'		=> PageLines_RSS::get_dash_rss(), 
			'classes'	=> 'news-dash pl-dash-half pl-dash-space', 
			'icon'		=> PL_ADMIN_ICONS . '/store.png', 
			'footer' 	=> sprintf('Visit <a href="%s">PageLines Store</a>', 'http://www.pagelines.com/store/')
		); 
		
		$dashboards .= $this->dashboard_pane('store', $args);
		
		// PageLines Plus
		$args = array(
			'title'		=> __( 'Latest Extensions', 'pagelines' ),
			'data'		=> PageLines_RSS::get_dash_rss( array( 'feed' => 'http://api.pagelines.com/rss/plus.php' ) ), 
			'classes'	=> 'news-dash pl-dash-half', 
			'icon'		=> PL_ADMIN_ICONS . '/plusbtn.png', 
			'footer' 	=> sprintf('Visit <a href="%s">Plus Overview</a>', 'http://www.pagelines.com/plus/')
		); 
		
		$dashboards .= $this->dashboard_pane('extensions', $args);
		
		
		return $this->dashboard_wrap($dashboards); 
		
	}
	
	function dashboard_wrap( $dashboards ){
		
		return sprintf('<div class="pl-dashboards fix">%s</div>', $dashboards);
		
	}
	
	function wrap_dashboard_pane($id, $args = array()){
		return sprintf('<div class="pl-dashboards fix">%s</div>', $this->dashboard_pane( $id, $args ));
	}
	
	
	function dashboard_pane( $id, $args = array() ){
		
		$defaults = array(
			'title' 		=> __( 'Dashboard', 'pagelines' ),
			'icon'			=> PL_ADMIN_ICONS.'/pin.png',  
			'classes'		=> '', 
			'data'			=> array(), 
			'data-format'	=> 'array', 
			'excerpt-trim'	=> 10, 
			'footer'		=> false
		);
		
		$a = wp_parse_args($args, $defaults); 
		
		ob_start()
		?>
		<div id="<?php echo 'pl-dash-'.$id;?>" class="pl-dash <?php echo $a['classes'];?>">
			<div class="pl-dash-pad">
				<div class="pl-vignette">
					<h2 class="dash-title"><?php printf('<img src="%s"/> %s', $a['icon'], $a['title']); ?></h2>
					<?php 
						echo $this->dashboard_stories( $a ); 
						echo $this->dashboard_footer( $a );
					?>
				</div>
			</div>
		</div>
		<?php 
		
		return ob_get_clean();
		
	}
	
	function dashboard_stories( $args = array() ){
		
		if($args['data-format'] == 'array')
			return $this->stories_array_format($args); 
		
		
	}
	
	function dashboard_footer( $args = array() ){
		
		if($args['footer'])
			printf('<div class="dash-foot"><div class="dash-foot-pad">%s</div></div>', $args['footer']);
		
	}

	function stories_array_format($args){
		
		$btn_text = (isset($args['btn-text'])) ? $args['btn-text'] : false; 
		$align_class = (isset($args['align']) && $args['align'] == 'right') ? 'rtimg' : ''; 
		$target = (isset($args['target']) && $args['target'] == 'new') ? 'target="_blank"' : ''; 
		
		$format = (isset($args['format']) && $args['format'] == 'plus-extensions') ? 'plus' : 'standard'; 
		
		ob_start();
		
		$count = 1;
		foreach($args['data'] as $id => $story){
			
			$image = (isset($story['img'])) ? $story['img'] : false; 
			$tag = (isset($story['tag'])) ? $story['tag'] : false; 
			$link = (isset($story['link'])) ? $story['link'] : false; 
			
			$btn_text = (isset($story['btn-text'])) ? $story['btn-text'] : $btn_text; 
			
			$tag_class = (isset($story['tag-class'])) ? $story['tag-class'] : ''; 

			$alt = ($count % 2 == 0) ? 'alt-story' : '';
			
			$excerpt = ( isset( $story['text'] ) ) ? $story['text'] : '';
			
			$title = ( isset( $story['link'] ) ) ? sprintf( '<a href="%s">%s</a>', $story['link'], $story['title'] ) : $story['title'];
			
			
			if ( $excerpt )
				$excerpt = (!$args['excerpt-trim']) ? $story['text'] : custom_trim_excerpt($story['text'], $args['excerpt-trim']);
		?>
		<div class="pl-dashboard-story media <?php echo $alt;?> dashpane">
			<div class="dashpane-pad fix">
				<?php
					if($tag) {
						
						$button = $this->get_upgrade_button( $story['data'] );

						printf('<div class="img %s">%s</div>', $align_class, $button );
						
						
						
					} elseif($btn_text){
						
						printf('<div class="img %s"><a class="extend_button" href="%s" %s>%s</a></div>', $align_class, $link, $target, $btn_text);
						
					} elseif($image)
						printf('<div class="img %s img-frame"><img src="%s" /></div>', $align_class, $image);
				
				?>
				<div class="bd">
					<h4 class="story-title"><?php echo $title; ?></h4>
					<p><?php echo $excerpt; ?></p>
					<?php 
						$this->special_buttons($args, $story);
						
					?>
						
				</div>
			</div>
		</div>
		
		<?php
		$count++;
		}
		
		return ob_get_clean();
	}
	
	function special_buttons($args, $story){
		
		if(!isset($args['format']) || $args['format'] != 'plus-extensions')
			return;
	
		if( pagelines_check_credentials() && ! pagelines_check_credentials( 'plus' ) ):

			printf( '<a href="%s" class="extend_button">%s &rarr;</a>', pl_get_plus_link(), __( 'Get PageLines Plus', 'pagelines' ) );

		endif; 
		
		if(!pagelines_check_credentials()):
			printf( '<a href="%s" class="extend_button discrete">%s &rarr;</a>', admin_url( PL_ACCOUNT_URL ), __( 'Have Plus? Login', 'pagelines' ) );
		endif; 
		
		if( pagelines_check_credentials( 'plus' ) ):

			echo $this->get_upgrade_button( $story, 'install_rss' );
				
		endif;
		
	
	}
	
	
	function get_upgrade_button( $data, $mode = 'upgrade' ) {
		
		global $extension_control;
		
		if ( 'install_rss' === $mode ):
		$button = '';
			// we need to convert a rss url into hardcore API data
			$slug = basename( $data['link'] );
			$type = basename( str_replace( $slug, '', $data['link'] ) );

			$data = $extension_control->get_latest_cached( $type );
			
			$data = $data->$slug;
			
			$type = rtrim( $type, 's' );
			
			$file = ( 'section' === $type ) ? $data->class : $slug;
			
			// if section or plugin, convert to array for is_installed().
			if( 'section' === $type || 'plugin' === $type )
				$ext = json_decode(json_encode($data), true);
			else
				$ext = $data;

			if ( $extension_control->is_installed( $type, $slug, $ext, 'dash_rss' ) )
				$o = array( 
						'mode'		=> 'installed',
						'condition'	=> true,
						'text'		=> __( 'Installed', 'pagelines' )
				);
			else
				$o = array(
					'mode'	=> 'install',
					'case'	=> sprintf( '%s_install', $type ),
					'text'	=> 'Install Now',
					'type'	=> $type,
					'file'	=> $file,
					'path'	=> $slug,
					'dtext'	=> sprintf( 'Installing %s', $data->name ),
					'condition'	=> 1,
					'dashboard'	=> true
				);

			$button = $extension_control->ui->extend_button( $slug, $o);
		endif;

		if ( 'upgrade' === $mode ) :
		$type = rtrim( $data->type, 's' );
						
		$file = ( 'section' === $type ) ? $data->class : $data->slug;
							
			$o = array(
				'mode'	=> 'upgrade',
				'case'	=> sprintf( '%s_upgrade', $type ),
				'text'	=> 'Upgrade Now',
				'type'	=> $type,
				'file'	=> $data->slug,
				'path'	=> $file,
				'dtext'	=> sprintf( 'Upgrading to version %s', $data->version ),
				'condition'	=> 1,
				'dashboard'	=> true
			);

		$button = $extension_control->ui->extend_button( $data->slug, $o);
		endif;
									
		return $button;
	}
	
	function stories_remote_url_format(){
		
	}
		
	function get_updates() {
		
		$default['story0'] = array(
			'title'	=>	__( "No new updates available.", 'pagelines' ),
			'text'	=>	false
		);
		
		if ( EXTEND_NETWORK )
			return $default;
		
		$updates = json_decode( get_theme_mod( 'pending_updates' ) );
		
		if( !is_object( $updates ) )
			return $default;
		
		$data = array();
		$a = 0;
		foreach( $updates as $key => $update ) {
			
			$data["story$a"] = array(
				
				'title'		=>	$update->name,
				'text'		=>	$update->changelog,
				'tag'		=>	$update->type,
				'data'		=>	$update
				
			);		
			$a++;	
		}
	if( empty( $data ) )
		return $default;
	return $data;
	}	
}
