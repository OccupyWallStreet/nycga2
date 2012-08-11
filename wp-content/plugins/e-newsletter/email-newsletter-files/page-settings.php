<?php

    $siteurl = get_option( 'siteurl' );

    $settings = $this->get_settings();

    $page_title =  __( 'eNewsletter Settings', 'email-newsletter' );

    if ( ! $settings ) {
        $page_title =  __( 'eNewsletter plugin Installation', 'email-newsletter' );

        $mode = "install";

    }
	global $email_newsletter;
	if (!class_exists('WpmuDev_HelpTooltips')) require_once $email_newsletter->plugin_dir . '/email-newsletter-files/class_wd_help_tooltips.php';
	$tips = new WpmuDev_HelpTooltips();
	$tips->set_icon_url($email_newsletter->plugin_url.'/email-newsletter-files/images/information.png');
	

    //Display status message
    if ( isset( $_GET['updated'] ) ) {
        ?><div id="message" class="updated fade"><p><?php echo urldecode( $_GET['dmsg'] ); ?></p></div><?php
    }

?>

    <script type="text/javascript">
	
        jQuery( document ).ready( function($) {
			
			$('.newsletter-settings-tabs > div').not('.active').hide();
			$('#newsletter-tabs a').click(function(e) {
				var tab = $(this).attr('href');
				$(this).addClass('nav-tab-active').siblings('a').removeClass('nav-tab-active');
				$(tab).show().siblings('div').hide();
				$(tab).addClass('nav-tab-active');
				return false;
			});
			
            $( "input[type=button][name='save']" ).click( function() {
                if ( "" == $( "#smtp_host" ).val() && $( "#smtp_method" ).attr( 'checked' ) ) {
                    alert('<?php _e( 'Please write SMTP Outgoing Server, or select another Sending Method!', 'email-newsletter' ) ?>');
                    return false;
                }

                $( "#newsletter_action" ).val( "save_settings" );
                $( "#settings_form" ).submit();
            });

            //install plugin data
            $( "#install" ).click( function() {
                if ( "" == $( "#smtp_host" ).val() && $( "#smtp_method" ).attr( 'checked' ) ) {
                    alert('<?php _e( 'Please write SMTP Outgoing Server, or select another Sending Method!', 'email-newsletter' ) ?>');
                    return false;
                }

                $( "#newsletter_action" ).val( "install" );
                $( "#settings_form" ).submit();
                return false;

            });



            //uninstall plugin data
            $( "#uninstall_yes" ).click( function() {
                $( "#newsletter_action" ).val( "uninstall" );
                $( "#settings_form" ).submit();
                return false;

            });

            $( "#uninstall" ).click( function() {
                $( "#uninstall_confirm" ).show( );
                return false;
            });

            $( "#uninstall_no" ).click( function() {
                $( "#uninstall_confirm" ).hide( );
                return false;
            });


            //Test connection to bounces email
            $( "#test_bounce_conn" ).click( function() {
                var bounce_email    = $( "#bounce_email" ).val();
                var bounce_host     = $( "#bounce_host" ).val();
                var bounce_port     = $( "#bounce_port" ).val();
                var bounce_username = $( "#bounce_username" ).val();
                var bounce_password = $( "#bounce_password" ).val();

                $( "#test_bounce_loading" ).show();
                $( "#test_bounce_conn" ).attr( 'disabled', true );

                $.ajax({
                    type: "POST",
                    url: "<?php echo $siteurl;?>/wp-admin/admin-ajax.php",
                    data: "action=test_bounces&bounce_email=" + bounce_email + "&bounce_host=" + bounce_host + "&bounce_port=" + bounce_port + "&bounce_username=" + bounce_username + "&bounce_password=" + bounce_password,
                    success: function( html ){
                        $( "#test_bounce_conn" ).attr( 'disabled', false );
                        $( "#test_bounce_loading" ).hide();
                        alert( html );
                    }
                 });
            });


        });


        function set_out_option() {
            $('.email_out_type' ).each( function() {
                if( $( this )[0].checked ){
                    $( '.email_out' ).hide();
                    $( '.email_out_' + $( this ).val() ).show();
                }
            });
        }

        $( function() {
            set_out_option();
            $( '.email_out_type' ).change( function() {
                set_out_option();
                if( $( this )[0].checked ){
                    $( '.email_out' ).hide();
                    $( '.email_out_' + $( this ).val() ).show();
                }
            });
        });




    </script>


    <div class="wrap">
        <h2><?php echo $page_title; ?></h2>

        <form method="post" name="settings_form" id="settings_form" >
            <input type="hidden" name="newsletter_action" id="newsletter_action" value="" />
            <input type="hidden" name="mode"  value="<?php echo $mode; ?>" />
			
            <div class="newsletter-settings-tabs">
               
					<h3 id="newsletter-tabs" class="nav-tab-wrapper">
						<a href="#tabs-1" class="nav-tab nav-tab-active"><?php _e( 'General Settings', 'email-newsletter' ) ?></a>
						<a href="#tabs-2" class="nav-tab"><?php _e( 'Outgoing Email Settings', 'email-newsletter' ) ?></a>
						<a href="#tabs-3" class="nav-tab"><?php _e( 'Bounce Settings', 'email-newsletter' ) ?></a>
						 <?php if ( ! isset( $mode ) || "install" != $mode ): ?>
						 	<a class="nav-tab" href="#tabs-4"><?php _e( 'Uninstall', 'email-newsletter' ) ?></a>
						 <?php endif; ?>
					</h3>
                    <div class="active" id="tabs-1">
                        <h3><?php _e( 'General Settings', 'email-newsletter' ) ?></h3>
                        <table class="settings-form">
                            <tr>
                                <td>
                                    <?php _e( 'Double Opt In:', 'email-newsletter' ) ?>
                                </td>
                                <td>
                                    <input type="checkbox" name="settings[double_opt_in]" value="1" <?php echo (isset($settings['double_opt_in'])&&$settings['double_opt_in']) ? ' checked':'';?> />
                                    <span class="description"><?php _e( 'Yes, members will get confirmation email to subscribe to newsletters (only for not registered users)', 'email-newsletter' ) ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php _e( 'From email:', 'email-newsletter' ) ?>
                                </td>
                                <td>
                                    <input type="text" name="settings[from_email]" value="<?php echo htmlspecialchars( $settings['from_email'] ? $settings['from_email'] : get_option( 'admin_email' ) );?>" />
                                    <span class="description"><?php _e( 'Default "from" email address when sending newsletters.', 'email-newsletter' ) ?></span>
                                    <?php
                                    if ( "smtp" == $settings['outbound_type'] )
                                        echo '<span class="red">' . __( 'You use SMTP method for sending email. Check this field!', 'email-newsletter' ) . '</span>';
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php _e( 'From name:', 'email-newsletter' ) ?>
                                </td>
                                <td>
                                    <input type="text" name="settings[from_name]" value="<?php echo htmlspecialchars( $settings['from_name'] ? $settings['from_name'] : get_option( 'blogname' ) );?>" />
                                    <span class="description"><?php _e( 'Default "from" name when sending newsletters.', 'email-newsletter' ) ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php _e( 'Contact information:', 'email-newsletter' ) ?>
                                </td>
                                <td>
                                    <textarea name="settings[contact_info]" class="contact-information" ><?php echo $settings['contact_info'] ? $settings['contact_info'] : "";?></textarea>
                                    <br />
                                    <span class="description"><?php _e( 'Default contact information will be added to the bottom of each email', 'email-newsletter' ) ?></span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div id="tabs-2">
                        <h3><?php _e( 'Outgoing Email Settings', 'email-newsletter' ) ?></h3>
                        <table class="settings-form">
                            <tbody>
                                <tr>
                                    <td>
                                        <?php echo _e( 'Email Sending Method:', 'email-newsletter' );?>
                                    </td>
                                    <td>
                                        <label id="tip_smtp">
                                            <input type="radio" name="settings[outbound_type]" id="smtp_method" value="smtp" class="email_out_type" <?php echo ( $settings['outbound_type'] == 'smtp' || ! $settings['outbound_type']) ? 'checked="checked"' : '';?> /><?php echo _e( 'SMTP (recommended)', 'email-newsletter' );?></label>
											
											<?php $tips->bind_tip(__("The SMTP method allows you to use your SMTP server (or Gmail, Yahoo, Hotmail etc. ) for sending newsletters and emails. It's usually the best choice, especially if your host has restrictions on sending email and to help you to avoid being blacklisted as a SPAM sender",'email-newsletter'), '#tip_smtp'); ?>
											
                                        &nbsp; &nbsp; &nbsp;
                                        <label id="tip_php">
                                            <input type="radio" name="settings[outbound_type]" value="mail" class="email_out_type" <?php echo $settings['outbound_type'] == 'mail' ? 'checked="checked"' : '';?> /><?php echo _e( 'php mail', 'email-newsletter' );?>
                                        </label>
										<?php $tips->bind_tip(__( "This method uses php functions for sending newsletters and emails. Be careful because some hosts may set restrictions on using this method. If you can't edit settings of your server, we recommend to use the SMTP method for optimal results!", 'email-newsletter' ), '#tip_php'); ?>
                                    </td>
                                </tr>
                            </tbody>

                            <tbody class="email_out email_out_smtp">
                                <tr>
                                    <td colspan="2">
                                         <span class="red"><?php _e( 'Note: for SMTP method - in "From email" you should use only emails which related with your SMTP server! Check it on "General Settings" and "Create\Edit newsletter"!', 'email-newsletter' ) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'SMTP Outgoing Server', 'email-newsletter' ) ?>:</td>
                                    <td>
                                        <input type="text" id="smtp_host" name="settings[smtp_host]" value="<?php echo htmlspecialchars($settings['smtp_host']);?>" />
                                        <span class="description"><?php _e( '(eg: smtp.someserver.com, ssl://smtp.gmail.com:465)', 'email-newsletter' ) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'SMTP Username:', 'email-newsletter' ) ?></td>
                                    <td>
                                        <input type="text" name="settings[smtp_user]" value="<?php echo htmlspecialchars($settings['smtp_user']);?>" />
                                        <span class="description"><?php _e( '(leave blank for none)', 'email-newsletter' ) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'SMTP Password:', 'email-newsletter' ) ?></td>
                                    <td>
                                        <input type="password" name="settings[smtp_pass]" value="<?php echo ( isset( $settings['smtp_pass'] ) && '' != $settings['smtp_pass'] ) ? '********' : ''; ?>" />
                                        <span class="description"><?php _e( '(leave blank for none)', 'email-newsletter' ) ?></span>
                                    </td>
                                </tr>
                            </tbody>
                                <tr>
                                    <td>
                                        <?php _e( 'Enable', 'email-newsletter' ) ?>
                                        <span class="description"><?php _e( ' (CRON)', 'email-newsletter' ) ?></span>
                                    </td>
                                    <td>
                                        <select name="settings[cron_enable]" >
                                            <option value="1" <?php echo ( 1 == $settings['cron_enable'] ) ? 'selected' : ''; ?> ><?php _e( 'Enable', 'email-newsletter' ) ?></option>
                                            <option value="2" <?php echo ( 2 == $settings['cron_enable'] ) ? 'selected' : ''; ?> ><?php _e( 'Disable', 'email-newsletter' ) ?></option>
                                        </select>
                                        <span class="description"><?php _e( "('Disable' - not use CRON for sending emails)", 'email-newsletter' ) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php _e( 'Limit send:', 'email-newsletter' ) ?>
                                        <span class="description"><?php _e( ' (CRON)', 'email-newsletter' ) ?></span>
                                    </td>
                                    <td>
                                        <input type="text" name="settings[send_limit]" value="<?php echo htmlspecialchars($settings['send_limit']);?>" />
                                        <span class="description"><?php _e( '(0 or blank for unlimited)', 'email-newsletter' ) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php _e( 'Emails per', 'email-newsletter' ) ?>
                                        <span class="description"><?php _e( ' (CRON)', 'email-newsletter' ) ?></span>
                                    </td>
                                    <td>
                                        <select name="settings[cron_time]" >
                                            <option value="1" <?php echo ( 1 == $settings['cron_time'] ) ? 'selected="selected"' : ''; ?> ><?php _e( 'Hour', 'email-newsletter' ) ?></option>
                                            <option value="2" <?php echo ( 2 == $settings['cron_time'] ) ? 'selected="selected"' : ''; ?> ><?php _e( 'Day', 'email-newsletter' ) ?></option>
                                            <option value="3" <?php echo ( 3 == $settings['cron_time'] ) ? 'selected="selected"' : ''; ?> ><?php _e( 'Month', 'email-newsletter' ) ?></option>
                                        </select>
                                    </td>
                                </tr>
                        </table>
                    </div>

                    <div id="tabs-3">
                        <h3><?php _e( 'Bounce Settings', 'email-newsletter' ) ?></h3>
                        <p><?php _e( 'This controls how bounce emails are handled by the system. Please create a new separate POP3 email account to handle bounce emails. Enter these POP3 email details below.', 'email-newsletter' ) ?></p>
                        <table cellpadding="5">
                            <tbody>
                                <tr>
                                    <td><?php _e( 'Email Address:', 'email-newsletter' ) ?></td>
                                    <td>
                                        <input type="text" name="settings[bounce_email]" id="bounce_email" value="<?php echo htmlspecialchars($settings['bounce_email']);?>" />
                                        <span class="description"><?php _e( 'email address where bounce emails will be sent by default', 'email-newsletter' ) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'POP3 Host:', 'email-newsletter' ) ?></td>
                                    <td>
                                        <input type="text" name="settings[bounce_host]" id="bounce_host" value="<?php echo htmlspecialchars($settings['bounce_host']);?>" />:
                                        <input type="text" name="settings[bounce_port]" id="bounce_port" value="<?php echo htmlspecialchars($settings['bounce_port']?$settings['bounce_port']:110);?>" size="2" />
                                        <span class="description"><?php _e( 'the hostname for the POP3 account, eg: mail.', 'email-newsletter' ) ?><?php echo $_SERVER['HTTP_HOST'];?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'POP3 Username:', 'email-newsletter' ) ?></td>
                                    <td>
                                        <input type="text" name="settings[bounce_username]" id="bounce_username" value="<?php echo htmlspecialchars($settings['bounce_username']);?>" />
                                        <span class="description"><?php _e( 'username for this bounce email account (usually the same as the above email address) ', 'email-newsletter' ) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'POP3 Password:', 'email-newsletter' ) ?></td>
                                    <td>
                                        <input type="text" name="settings[bounce_password]" id="bounce_password" value="<?php echo htmlspecialchars($settings['bounce_password']);?>" />
                                        <span class="description"><?php _e( 'password to access this bounce email account', 'email-newsletter' ) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><div id="test_bounce_loading"></div></td>
                                    <td>
                                        <br />
                                        <input type="button" name="" id="test_bounce_conn" value="<?php _e( 'Test Connection', 'email-newsletter' ) ?>" />
                                        <span class="description"><?php _e( 'We will send test email on Bounce address and will try read this email', 'email-newsletter' ) ?></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <?php if ( ! isset( $mode ) || "install" != $mode ): ?>
                    <div id="tabs-4">
                        <h3><?php _e( 'Uninstall', 'email-newsletter' ) ?></h3>
                        <p><?php _e( 'Here you can delete all data associated with the plugin from the database.', 'email-newsletter' ) ?></p>
                        <table cellpadding="5">
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td>
                                        <br />
                                        <input type="button" name="uninstall" id="uninstall" value="<?php _e( 'Delete data', 'email-newsletter' ) ?>" />
                                        <span class="description" style="color: red;"><?php _e( "Delete all plugin's data from DB.", 'email-newsletter' ) ?></span>
                                        <div id="uninstall_confirm" style="display: none;">
                                            <span class="description"><?php _e( 'Are you sure?', 'email-newsletter' ) ?></span>
                                            <br />
                                            <input type="button" name="uninstall" id="uninstall_no" value="<?php _e( 'No', 'email-newsletter' ) ?>" />
                                            <input type="button" name="uninstall" id="uninstall_yes" value="<?php _e( 'Yes', 'email-newsletter' ) ?>" />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>

            </div><!--/.newsletter-tabs-settings-->
		
            <br />
            <?php if ( isset( $mode ) && "install" == $mode ) { ?>
                <input type="button" class="button" name="install" id="install" value="<?php _e( 'Install', 'email-newsletter' ) ?>" />
            <?php } else { ?>
                <input type="button" class="button" name="save" value="<?php _e( 'Save all Settings', 'email-newsletter' ) ?>" />
            <?php } ?>

        </form>

    </div><!--/wrap-->