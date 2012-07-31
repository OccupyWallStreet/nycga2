<?php
/**
 * 
 *
 *  Account Handling In Admin
 *
 *
 *
 */


class PageLinesAccount {

	function __construct(){
		
		add_action( 'admin_init', array(&$this, 'update_lpinfo' ) );
		add_filter( 'pagelines_account_array', array( &$this, 'get_intro' ) );		
	}
	
	/**
	 * Save our credentials
	 * 
	 */	
	function update_lpinfo() {

		if ( isset( $_POST['form_submitted'] ) && $_POST['form_submitted'] === 'plinfo' ) {

			if ( isset( $_POST['creds_reset'] ) )
				update_option( 'pagelines_extend_creds', array( 'user' => '', 'pass' => '' ) );
			else
				set_pagelines_credentials( $_POST['lp_username'], $_POST['lp_password'] );

			PagelinesExtensions::flush_caches();		

			wp_redirect( PLAdminPaths::account( '&plinfo=true' ) );

			exit;
		}
	}
	
	/**
	 *
	 *  Returns Extension Array Config
	 *
	 */
	function pagelines_account_array(){

		$d = array();


			$d['updates']	= $this->pl_add_dashboard();

			$d['_getting_started'] = $this->pl_add_welcome();

			$d['_plus_extensions'] = $this->pl_add_extensions_dash();
			$d['_live_chat'] = $this->pl_add_live_chat_dash();
			$d['_resources'] = $this->pl_add_support_dash();



			$d['Your_Account']	= array(
				'icon'			=> PL_ADMIN_ICONS.'/user.png',
				'credentials' 	=> array(
					'type'		=> 'updates_setup',
					'title'		=> __( 'Configure PageLines Account &amp; Auto Updates', 'pagelines' ),
					'shortexp'	=> __( 'Get your latest updates automatically, direct from PageLines.', 'pagelines' ),
					'layout'	=> 'full',
				)
			);
			$d['Import-Export']	= array(
				'icon'			=> PL_ADMIN_ICONS.'/extend-inout.png',
				'import_set'	=> array(
					'default'	=> '',
					'type'		=> 'import_export',
					'layout'	=> 'full',
					'title'		=> __( 'Import/Export PageLines Settings', 'pagelines' ),						
					'shortexp'	=> __( 'Use this form to upload PageLines settings from another install.', 'pagelines' ),
				)
			);

		return apply_filters( 'pagelines_account_array', $d ); 
	}

	/**
     * Get Intro
     *
     * Includes the 'welcome.php' file from Child-Theme's root folder if it exists.
     *
     * @uses    default_headers
     *
     * @return  string
     */
	function get_intro( $o ) {
		
		if ( is_file( get_stylesheet_directory() . '/welcome.php' ) ) {
			
			ob_start();
				include( get_stylesheet_directory() . '/welcome.php' );
			$welcome =  ob_get_clean();	
			
			$a = array();
			
			if ( is_file( get_stylesheet_directory() . '/welcome.png' ) )
				$icon = get_stylesheet_directory_uri() . '/welcome.png';
			else
				$icon =  PL_ADMIN_ICONS . '/welcome.png';
			$a['welcome'] = array(
				'icon'			=> $icon,
				'hide_pagelines_introduction'	=> array(
					'type'			=> 'text_content',
					'flag'			=> 'hide_option',
					'exp'			=> $welcome
				)
			);		
		$o = array_merge( $a, $o );
		}
	return $o;
	}

	function pl_add_live_chat_dash(){
		$ext = new PageLinesSupportPanel();

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/balloon.png',
			'pagelines_dashboard'	=> array(
				'type'			=> 'text_content',
				'flag'			=> 'hide_option',
				'exp'			=> $this->get_live_bill()
			),
		);

		return $a;
	}

	function get_live_bill(){

		$url = pagelines_check_credentials( 'vchat' );

		$iframe = ( $url ) ? sprintf( '<iframe class="live_chat_iframe" src="%s"></iframe>', $url ) : false;
		$rand = 
		ob_start();
		?>

		<div class="admin_billboard">
			<div class="admin_billboard_pad fix">
					<h3 class="admin_header_main">
					 <?php _e( 'PageLines Live Chat (Beta)', 'pagelines'); ?>
					</h3>
					<div class='admin_billboard_text'>
					 <?php _e( 'A moderated live community chat room for discussing technical issues. (Plus Only)', 'pagelines' ); ?>
					</div>
					<?php if ( pagelines_check_credentials( 'plus' ) ) printf( '<div class="plus_chat_header">%s</div>', $this->pagelines_livechat_rules() ); ?>
			</div>
		</div>
		<div class="live_chat_wrap fix">

			<?php 

			if($iframe):
				echo $iframe; 
			else:?>

				<div class="live_chat_up_bill">
					<h3><?php _e( 'Live Chat Requires an active PageLines Plus account', 'pagelines' ); ?></h3>
					<?php
					if ( !pagelines_check_credentials() )
						printf( '<a class="button" href="%s">Login</a>', admin_url(PL_ACCOUNT_URL) );

					else
						if ( !VPLUS )
							printf( '<a class="button" href="%s">%s</a>', pl_get_plus_link(), __( 'Upgrade to PageLines Plus', 'pagelines' ) );?>			 
				</div>
			<?php endif;	?>
		</div>
		<?php 

		$bill = ob_get_clean();

		return apply_filters('pagelines_welcome_billboard', $bill);
	}

	function pagelines_livechat_rules() {

		$url = 'api.pagelines.com/plus_latest';
		if( $welcome = get_transient( 'pagelines_pluschat' ) )
			return json_decode( $welcome );

		$response = pagelines_try_api( $url, false );

		if ( $response !== false ) {
			if( ! is_array( $response ) || ( is_array( $response ) && $response['response']['code'] != 200 ) ) {
				$out = '';
			} else {

			$welcome = wp_remote_retrieve_body( $response );
			set_transient( 'pagelines_pluschat', $welcome, 86400 );
			$out = json_decode( $welcome );
			}
		}
	return $out;
	}

	function pl_add_support_dash(){

		$ext = new PageLinesSupportPanel();

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/toolbox.png',
			'pagelines_dashboard'	=> array(
				'type'			=> 'text_content',
				'flag'			=> 'hide_option',
				'exp'			=> $ext->draw()
			),
		);

		return $a;

	}


	function pl_add_extensions_dash(){

		$ext = new PageLinesCoreExtensions();

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/plusbtn.png',
			'pagelines_dashboard'	=> array(
				'type'			=> 'text_content',
				'flag'			=> 'hide_option',
				'exp'			=> $ext->draw()
			),
		);

		return $a;
	}

	/**
	 * Welcome Message
	 *
	 * @since 2.0.0
	 */
	function pl_add_dashboard(){

		$dash = new PageLinesDashboard();

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/newspapers.png',
			'pagelines_dashboard'	=> array(
				'type'			=> 'text_content',
				'flag'			=> 'hide_option',
				'exp'			=> $dash->draw()
			),
		);

		return $a;
	}

	/**
	 * Welcome Message
	 *
	 * @since 2.0.0
	 */
	function pl_add_welcome(){

		$welcome = new PageLinesWelcome();

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/book.png',
			'hide_pagelines_introduction'	=> array(
				'type'			=> 'text_content',
				'flag'			=> 'hide_option',
				'exp'			=> $welcome->get_welcome()
			),
		);

		return apply_filters('pagelines_options_welcome', $a);
	}	
}
