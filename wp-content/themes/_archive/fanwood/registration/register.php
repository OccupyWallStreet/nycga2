<?php
/**
 * BuddyPress - Register
 *
 * Account registration template for BuddyPress.
 *
 * @package BuddyPress
 * @subpackage theme
 */
 
get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // fanwood_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // fanwood_open_content ?>
		
		<div class="hfeed">
		
			<?php if ( current_theme_supports( 'breadcrumb-trail' ) ) breadcrumb_trail( array( 'separator' => '&raquo;' ) ); ?>
			
			<?php get_sidebar( 'before-content' ); // Loads the sidebar-before-content.php template. ?>

			<?php do_action( 'bp_before_register_page' ) ?>

			<div class="page" id="register-page">

				<form action="" name="signup_form" id="signup_form" class="standard-form" method="post" enctype="multipart/form-data">
				
					<div class="hentry page">
					
						<?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>
						
						<h1 class="entry-title"><?php _e( 'Create An Account', 'buddypress' ) ?></h1>
							
						<?php endif; ?>
						
						<div class="entry-content">

							<?php if ( 'registration-disabled' == bp_get_current_signup_step() ) : ?>

								<?php do_action( 'template_notices' ) ?>
								
								<?php do_action( 'bp_before_registration_disabled' ) ?>
								
								<p><?php _e( 'User registration is currently not allowed.', 'buddypress' ); ?></p>
								
								<?php do_action( 'bp_after_registration_disabled' ); ?>

							<?php endif; // registration-disabled signup setp ?>

							<?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>

								<?php do_action( 'template_notices' ) ?>

								<p class="note"><?php _e( 'Registering for this site is easy, just fill in the fields below and we\'ll get a new account set up for you in no time.', 'buddypress' ) ?></p>

								<?php do_action( 'bp_before_account_details_fields' ) ?>

								<div class="register-section" id="basic-details-section">

									<?php /***** Basic Account Details ******/ ?>

									<h3><?php _e( 'Account Details', 'buddypress' ) ?></h3>

									<p>
										<label for="signup_username"><?php _e( 'Username', 'buddypress' ) ?> <?php _e( '(required)', 'buddypress' ) ?></label><br />
										<input type="text" name="signup_username" id="signup_username" value="<?php bp_signup_username_value() ?>" />
									</p>
									<?php do_action( 'bp_signup_username_errors' ) ?>

									<p>
										<label for="signup_email"><?php _e( 'Email Address', 'buddypress' ) ?> <?php _e( '(required)', 'buddypress' ) ?></label><br />
										<input type="text" name="signup_email" id="signup_email" value="<?php bp_signup_email_value() ?>" />
									</p>
									<?php do_action( 'bp_signup_email_errors' ) ?>

									<p>
										<label for="signup_password"><?php _e( 'Choose a Password', 'buddypress' ) ?> <?php _e( '(required)', 'buddypress' ) ?></label><br />
										<input type="password" name="signup_password" id="signup_password" value="" />
									</p>
									<?php do_action( 'bp_signup_password_errors' ) ?>

									<p>
										<label for="signup_password_confirm"><?php _e( 'Confirm Password', 'buddypress' ) ?> <?php _e( '(required)', 'buddypress' ) ?></label><br />
										<input type="password" name="signup_password_confirm" id="signup_password_confirm" value="" />
									</p>
									<?php do_action( 'bp_signup_password_confirm_errors' ) ?>

								</div><!-- #basic-details-section -->

								<?php do_action( 'bp_after_account_details_fields' ) ?>

								<?php /***** Extra Profile Details ******/ ?>

								<?php if ( bp_is_active( 'xprofile' ) ) : ?>

									<?php do_action( 'bp_before_signup_profile_fields' ) ?>

									<div class="register-section" id="profile-details-section">

										<h3><?php _e( 'Profile Details', 'buddypress' ) ?></h3>

										<?php /* Use the profile field loop to render input fields for the 'base' profile field group */ ?>
										
										<?php if ( bp_is_active( 'xprofile' ) ) : if ( bp_has_profile( 'profile_group_id=1' ) ) : while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

										<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

											<div class="editfield">

												<?php if ( 'textbox' == bp_get_the_profile_field_type() ) : ?>
												
													<p>
														<label for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ) ?><?php endif; ?></label><br />
														<input type="text" name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" value="<?php bp_the_profile_field_edit_value() ?>" />
													</p>
													
													<?php do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' ) ?>

												<?php endif; ?>

												<?php if ( 'textarea' == bp_get_the_profile_field_type() ) : ?>

													<p>
														<label for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ) ?><?php endif; ?></label><br />
														<textarea rows="5" cols="40" name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_edit_value() ?></textarea>
													</p>
													<?php do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' ) ?>

												<?php endif; ?>

												<?php if ( 'selectbox' == bp_get_the_profile_field_type() ) : ?>
												
													<p>
														<label for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ) ?><?php endif; ?></label><br />
														<select name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>">
															<?php bp_the_profile_field_options() ?>
														</select>
													</p>
													<?php do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' ) ?>

												<?php endif; ?>

												<?php if ( 'multiselectbox' == bp_get_the_profile_field_type() ) : ?>
												
													<p>
														<label for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ) ?><?php endif; ?></label><br />
														<select name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" multiple="multiple">
															<?php bp_the_profile_field_options() ?>
														</select>
													</p>
													
													<?php do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' ) ?>

												<?php endif; ?>

												<?php if ( 'radio' == bp_get_the_profile_field_type() ) : ?>

													<div class="radio">
														<p>
															<label><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ) ?><?php endif; ?></label>
															
															<?php bp_the_profile_field_options() ?>

															<?php if ( !bp_get_the_profile_field_is_required() ) : ?>
															<a class="clear-value" href="javascript:clear( '<?php bp_the_profile_field_input_name() ?>' );"><?php _e( 'Clear', 'buddypress' ) ?></a>
															<?php endif; ?>
														</p>
														<?php do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' ) ?>
													</div>

												<?php endif; ?>

												<?php if ( 'checkbox' == bp_get_the_profile_field_type() ) : ?>

													<div class="checkbox">
														<p>
															<label><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ) ?><?php endif; ?></label>
															<?php bp_the_profile_field_options() ?>
														</p>
														<?php do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' ) ?>
													</div>

												<?php endif; ?>

												<?php if ( 'datebox' == bp_get_the_profile_field_type() ) : ?>

													<div class="datebox">
													
														<p>
															<label for="<?php bp_the_profile_field_input_name() ?>_day"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ) ?><?php endif; ?></label><br />

															<select name="<?php bp_the_profile_field_input_name() ?>_day" id="<?php bp_the_profile_field_input_name() ?>_day">
																<?php bp_the_profile_field_options( 'type=day' ) ?>
															</select>

															<select name="<?php bp_the_profile_field_input_name() ?>_month" id="<?php bp_the_profile_field_input_name() ?>_month">
																<?php bp_the_profile_field_options( 'type=month' ) ?>
															</select>

															<select name="<?php bp_the_profile_field_input_name() ?>_year" id="<?php bp_the_profile_field_input_name() ?>_year">
																<?php bp_the_profile_field_options( 'type=year' ) ?>
															</select>
														</p>
														<?php do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' ) ?>
													</div>

												<?php endif; ?>

												<?php do_action( 'bp_custom_profile_edit_fields' ) ?>

												<?php if ( bp_get_the_profile_field_description() !== '' ) : ?>
													<div class="field-description profile-field-description"><?php bp_the_profile_field_description() ?></div>
												<?php endif; ?>

											</div>

										<?php endwhile; ?>

										<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php bp_the_profile_group_field_ids() ?>" />

										<?php endwhile; endif; endif; ?>

									</div><!-- #profile-details-section -->

									<?php do_action( 'bp_after_signup_profile_fields' ) ?>

								<?php endif; ?>

								<?php if ( bp_get_blog_signup_allowed() ) : ?>

									<?php do_action( 'bp_before_blog_details_fields' ) ?>

									<?php /***** Blog Creation Details ******/ ?>

									<div class="register-section" id="blog-details-section">

										<h3><?php _e( 'Blog Details', 'buddypress' ) ?></h3>

										<p><input type="checkbox" name="signup_with_blog" id="signup_with_blog" value="1"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?> checked="checked"<?php endif; ?> /> <label for="signup_with_blog"><?php _e( 'Yes, I\'d like to create a new site', 'buddypress' ) ?></label></p>

										<div id="blog-details"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?>class="show"<?php endif; ?>>
										
										<p>
											<label for="signup_blog_url"><?php _e( 'Blog URL', 'buddypress' ) ?> <?php _e( '(required)', 'buddypress' ) ?></label><br />
											<input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value() ?>" />
										</p>
										
										<div class="field-description">
											<?php if ( is_subdomain_install() ) : ?>
												<?php _e( 'For example: http://your-blog-url-goes-here/', 'buddypress' ); ?> <?php bp_blogs_subdomain_base() ?>
											<?php else: ?>
												<?php _e( 'For example: ' . site_url() . '/your-blog-url-goes-here', 'buddypress' ); ?>
											<?php endif; ?>
										</div>
										
										<?php do_action( 'bp_signup_blog_url_errors' ) ?>
										
										<p>
											<label for="signup_blog_title"><?php _e( 'Site Title', 'buddypress' ) ?> <?php _e( '(required)', 'buddypress' ) ?></label><br />
											<input type="text" name="signup_blog_title" id="signup_blog_title" value="<?php bp_signup_blog_title_value() ?>" />
										</p>
										<?php do_action( 'bp_signup_blog_title_errors' ) ?>

										<p>
											<span class="label"><?php _e( 'I would like my site to appear in search engines, and in public listings around this network.', 'buddypress' ) ?>:</span>

											<label><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_public" value="public"<?php if ( 'public' == bp_get_signup_blog_privacy_value() || !bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes', 'buddypress' ) ?></label>
											
											<label><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_private" value="private"<?php if ( 'private' == bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'No', 'buddypress' ) ?></label>
										</p>
										
										<?php do_action( 'bp_signup_blog_privacy_errors' ) ?>

										</div>

									</div><!-- #blog-details-section -->

									<?php do_action( 'bp_after_blog_details_fields' ) ?>

								<?php endif; ?>

								<?php do_action( 'bp_before_registration_submit_buttons' ) ?>

								<p class="submit">
									<input type="submit" name="signup_submit" id="signup_submit" value="<?php _e( 'Complete Sign Up', 'buddypress' ) ?>" />
								</p>

								<?php do_action( 'bp_after_registration_submit_buttons' ) ?>

								<?php wp_nonce_field( 'bp_new_signup' ) ?>

							<?php endif; // request-details signup step ?>

							<?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>

								<h2><?php _e( 'Sign Up Complete!', 'buddypress' ) ?></h2>

								<?php do_action( 'template_notices' ) ?>
								
								<?php do_action( 'bp_before_registration_confirmed' ) ?>

								<?php if ( bp_registration_needs_activation() ) : ?>
									<p><?php _e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'buddypress' ) ?></p>
								<?php else : ?>
									<p><?php _e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'buddypress' ) ?></p>
								<?php endif; ?>

								<?php do_action( 'bp_after_registration_confirmed' ) ?>

							<?php endif; // completed-confirmation signup step ?>

							<?php do_action( 'bp_custom_signup_steps' ) ?>
							
						</div><!-- .entry-content -->
					
					</div><!-- .hentry -->

				</form>

			</div>

			<?php do_action( 'bp_after_register_page' ) ?>
			
			<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>
	
		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // fanwood_after_content ?>
	
	<script type="text/javascript">
		jQuery(document).ready( function() {
			if ( jQuery('div#blog-details').length && !jQuery('div#blog-details').hasClass('show') )
				jQuery('div#blog-details').toggle();

			jQuery( 'input#signup_with_blog' ).click( function() {
				jQuery('div#blog-details').fadeOut().toggle();
			});
		});
	</script>

<?php get_footer(); // Loads the footer.php template. ?>







