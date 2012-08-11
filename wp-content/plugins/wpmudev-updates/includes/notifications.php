<?php

class WPMUDEV_Notifications_Output {

	function WPMUDEV_Notifications_Output() {
		add_action( 'all_admin_notices', array( &$this, 'upgrade_notice_output' ), 2);
		add_action( 'all_admin_notices', array( &$this, 'old_plugin_check' ) );
		add_action( 'all_admin_notices', array( &$this, 'apikey_notice_output' ) );
		add_action( 'all_admin_notices', array( &$this, 'admin_notice_output' ) );
		
		add_action( 'admin_footer', array( &$this, 'admin_footer_scripts' ) );
		add_action( 'wp_ajax_wpmudev-dismiss', array( &$this, 'ajax_dismiss' ) );
	}

	function upgrade_notice_output() {
		global $wpmudev_un;
		
		if ( current_user_can('update_plugins') ) {
			//temporary dismissing
			if ( get_site_option('wdp_un_dismissed_upgrade') && get_site_option('wdp_un_dismissed_upgrade') > time() )
				return;

			$updates = get_site_option('wdp_un_updates_available');
			$count = ( is_array($updates) ) ? count( $updates ) : 0;

			if ( $count > 0 && !get_site_option('wdp_un_hide_upgrades') ) {
				$data = $wpmudev_un->get_updates();
				$msg = !empty($data['text_admin_notice']) ? $data['text_admin_notice'] : __('<strong>WPMU DEV updates are available</strong>: These may be critical for the security or performance of this site so please review your available updates today &raquo;', 'wpmudev');
				echo '
					<div class="update-nag">
						<a href="' . $wpmudev_un->updates_url . '">' . $msg . '</a>
						<a class="wpmudev-dismiss" data-key="upgrade-dismiss" data-id="1" title="'.__('Dismiss this notice for this session', 'wpmudev').'" href="' . $wpmudev_un->updates_url . '&upgrade-dismiss=1"><small>'.__('Dismiss', 'wpmudev').'</small></a>
					</div>';
			}
		}
	}

	function apikey_notice_output() {
		global $wpmudev_un;
		
		if ( current_user_can('update_plugins') ) {
			if ( !$wpmudev_un->get_apikey() ) {
				$link = $wpmudev_un->dashboard_url;
				?>
				<div class="wpmudev-message wpdv-connect" id="message">

						<div class="squish">
							<h4><strong><?php _e( 'WPMU DEV is almost ready', 'wpmudev' ); ?></strong> &ndash; <?php _e( 'configure to enable all features!', 'wpmudev' ); ?>
							<a id="api-add" class="button-primary" href="<?php echo $link; ?>"><i class="icon-pencil icon-large"></i> <?php _e( 'Enter your free API Key', 'wpmudev' ); ?></a>
							</h4>
							<div class="clear"></div>
						</div>
	
				</div>
				<?php
			}
		}
	}

	function admin_notice_output() {
		global $wpmudev_un, $current_screen;

		if ( !current_user_can('update_plugins') || !$wpmudev_un->get_apikey() || $current_screen->id == 'update-network' || $current_screen->id == 'update' )
			return;
		
		//check delay
		$delay = get_site_option('wdp_un_delay');
		if (!$delay) {
			$delay = time() + 86400;
			update_site_option('wdp_un_delay', $delay);
		}
		if ($delay > time())
			return;
		
		//handle ad messages
		$data = $wpmudev_un->get_updates();
		$dismissed = get_site_option('wdp_un_dismissed');
		if ( $data['membership'] == 'full' ) { //full member
			if ( false == ($dismissed['id'] == $data['full_notice']['id'] && $dismissed['expire'] > time()) ) {
				$msg = $data['full_notice']['msg'];
				$id = $data['full_notice']['id'];
				if (isset($data['full_notice']['url'])) {
					$button = '<a id="wdv-upgrade" class="button-primary" target="_blank" href="' . esc_url($data['full_notice']['url']) . '"><i class="icon-share-alt icon-large"></i> ' . __( 'Go Now', 'wpmudev' ) . '</a>';
					$class = 'with-button';
				} else {
					$class = '';
					$button = '';
				}
			}
		} else if ( is_numeric($data['membership']) ) { //single member
			if ( false == ($dismissed['id'] == $data['single_notice']['id'] && $dismissed['expire'] > time()) ) {
				$msg = $data['single_notice']['msg'];
				$id = $data['single_notice']['id'];
				$class = 'with-button';
				$button = '<a id="wdv-upgrade" class="button-primary" target="_blank" href="https://premium.wpmudev.org/join/"><i class="icon-arrow-up icon-large"></i> ' . __( 'Upgrade Now', 'wpmudev' ) . '</a>';
			}
		} else { //free member
			if ( false == ($dismissed['id'] == $data['free_notice']['id'] && $dismissed['expire'] > time()) ) {
				$msg = $data['free_notice']['msg'];
				$id = $data['free_notice']['id'];
				$class = 'with-button';
				$button = '<a id="wdv-upgrade" class="button-primary" target="_blank" href="https://premium.wpmudev.org/join/"><i class="icon-arrow-up icon-large"></i> ' . __( 'Signup Now', 'wpmudev' ) . '</a>';
			}
		}

		if ( !empty($msg) && !get_site_option('wdp_un_hide_notices') ) {
			?>
			<div class="wpmudev-message wpdv-msg" id="message">
				<div class="squish <?php echo $class; ?>">
					<h4 class="<?php echo $class; ?>">
					<?php echo $button; ?>
					<?php echo strip_tags(stripslashes($msg), '<a><strong>'); ?>
					</h4>
					<a class="wpmudev-dismiss" data-key="dismiss" data-id="<?php echo $id; ?>" title="<?php _e('Dismiss this notice for one month', 'wpmudev'); ?>" href="<?php echo $wpmudev_un->dashboard_url; ?>&dismiss=<?php echo $id; ?>"><?php _e('Dismiss', 'wpmudev'); ?></a>
					<div class="clear"></div>	
				</div>
			</div>
			<?php
		}

		//show latest project information
		if ( !get_site_option('wdp_un_hide_releases') && isset($data['latest_release']) && isset($data['projects'][$data['latest_release']]) ) {
			$dismissed_release = get_site_option('wdp_un_dismissed_release');
			$local_projects = $wpmudev_un->get_local_projects();
			if ( $dismissed_release != $data['latest_release'] && !isset($local_projects[$data['latest_release']]) ) { //if not dismissed or not installed
				$project = $data['projects'][$data['latest_release']];
				?>
				<div class="wpmudev-new" id="message">
					<div class="dev-widget-content">
						<h4><strong><?php _e('New WPMU DEV Release:', 'wpmudev'); ?></strong></h4>
							<div class="dev-content-wrapper">
								<a id="wdv-release-img" target="_blank" title="<?php _e('More Information &raquo;', 'wpmudev'); ?>" href="<?php echo $project['url']; ?>">
									<img src="<?php echo $project['thumbnail']; ?>" width="186" height="105" />
								</a>
								<h4 id="wdv-release-title"><?php echo esc_html($project['name']); ?></h4>
								<div id="wdv-release-desc"><?php echo esc_html($project['short_description']); ?>
									<div class="dev-cta-wrap">
										<?php if ($url = $wpmudev_un->auto_install_url($data['latest_release'])) { ?>
										<a id="wdv-release-install" class="button-primary" href="<?php echo $url; ?>"><i class="icon-download-alt icon-large"></i> <?php _e( 'INSTALL', 'wpmudev' ); ?></a>
										<?php } else { ?>
										<a id="wdv-release-install" target="_blank" class="button-primary" href="<?php echo esc_url($project['url']); ?>"><i class="icon-download-alt icon-large"></i> <?php _e( 'DOWNLOAD', 'wpmudev' ); ?></a>
										<?php } ?>
									
										<a id="wdv-release-info" target="_blank" class="button-primary" href="<?php echo $project['url']; ?>"><?php _e( 'More Information &raquo;', 'wpmudev' ); ?></a>
									</div>
								</div>
							</div>
						<a class="wpmudev-dismiss" data-key="dismiss-release" data-id="<?php echo $data['latest_release']; ?>" title="<?php _e('Dismiss this announcement', 'wpmudev'); ?>" href="<?php echo $wpmudev_un->dashboard_url; ?>&dismiss-release=<?php echo $data['latest_release']; ?>"><?php _e('Dismiss', 'wpmudev'); ?></a>
						<div class="clear"></div>
					</div>
				</div>
				<?php
			}
		}
		
	}

	function old_plugin_check() {
		if ( !current_user_can('update_plugins') )
			return;

		if ( function_exists( 'update_notificiations_process' ) ) {
			?>
			<div class="wpmudev-message" id="message">
				<div class="squish">
					<h4><strong><?php _e( 'Whoops!', 'wpmudev' ); ?></strong> &ndash; <?php _e( 'You need to remove the old version of the WPMU DEV Update Notifications plugin! Check for the update-notifications.php file in the /mu-plugins/ folder and delete it.', 'wpmudev' ); ?></h4>
				</div>
			</div>
			<?php
		}
	}

	function admin_footer_scripts() {
		?>
		<script type="text/javascript">
		jQuery(function($) {
			$('.wpmudev-dismiss').click(function() {
				var $link = $(this), data = { 'action': 'wpmudev-dismiss' };
				$link.closest('.wpmudev-new, .wpmudev-message, .update-nag').fadeOut('fast');
				data[ $link.attr('data-key') ] = $link.attr('data-id');
				$.post(ajaxurl, data);
				return false;
			});
		});
		</script>
		<?php
	}

	function ajax_dismiss() {
		if ( !current_user_can('update_plugins') )
			return;

		global $wpmudev_un;
		$wpmudev_un->handle_dismiss();
		die;
	}
}

new WPMUDEV_Notifications_Output;
?>