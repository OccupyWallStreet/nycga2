<?php
			// ====================================
			// Register a new setting GROUP
			// ====================================
			register_setting(
				'ContentScheduler_Options_Group',
				'ContentScheduler_Options',
				array($this, 'validate_settings'));
			// ====================================
			// Add SECTIONS to the setting group
			// ====================================
			// Add a section to wrap our settings controls in.
			// I think we can add more than one section if we want??
			// However, it is very common to only have one section.
			// Expirations
			add_settings_section(
				'ContentScheduler_Options_ID',
				__('Content Scheduler Expiration Options', 'contentscheduler'),
				array($this, 'draw_overview'),
				'ContentScheduler_Page_Title');
			// Notifications
			add_settings_section(
				'ContentScheduler_Not_ID',
				__('Content Scheduler Notification Options', 'contentscheduler'),
				array($this, 'draw_overview_not'),
				'ContentScheduler_Page_Title');
			// Display Options
			add_settings_section(
				'ContentScheduler_Disp_ID',
				__('Content Scheduler Display Options', 'contentscheduler'),
				array($this, 'draw_overview_disp'),
				'ContentScheduler_Page_Title');
			// ========================================
			// Add FIELDS to the setting group's sections
			// ========================================
			// Add a settings field to the controls section
			// Will need an "add_settings_field" call for every options field
			// Note the first param is an ID but does NOT have to match HTML id rendered later
			/*
			== Global Options for Expiration ==
			Radio Buttons: exp-status: 'expire' or 'hold'
			* This allows us to suspend expiring any content, without disabling the plugin.
			*/
			add_settings_field(
				'exp-status',
				__('Expiration status', 'contentscheduler'),
				array($this, 'draw_set_expstatus_fn'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Options_ID');
			/*
			Radio Buttons: exp-period: 'weekly,' 'daily,' 'hourly,' 'other'
			Text Field: epx-period-other: integer, number of minutes
			* This allows folks to specify how often wp-cron will check schedules.
			*/
			add_settings_field(
				'exp-period',
				__('Expiration frequency (in minutes)', 'contentscheduler'),
				array($this, 'draw_set_expperiod_fn'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Options_ID');
			// Setting for a default expiration time.
			add_settings_field(
				'exp-default',
				__( 'Default expiration', 'contentscheduler' ),
				array( $this, 'draw_set_expdefault_fn' ),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Options_ID' );
			/*			
			Button: exp-disable-all
			* This button requires yes / cancel confirmation, and if passes confirmation, will turn off the "enable expiration" flag for all content on the site.
			*/
			// Note: We'll come back to this later, but leave it here so we don't forget about it.
			/*
			== Change Rules ==
			Radio Buttons: chg-status: 'no-change', 'pending', 'draft'
			* This changes the "Status" setting for the Post / Page.
			*/
			add_settings_field(
				'chg-status',
				__('Change status to:', 'contentscheduler'),
				array($this, 'draw_set_chgstatus_fn'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Options_ID');
			/*
			Radio Buttons: chg-sticky: 'no-change', 'unstick'
			* This changes the checkbox of "Stickiness" under "Visibility"
			*/
			add_settings_field(
				'chg-sticky',
				__('Change stickiness to:', 'contentscheduler'),
				array($this, 'draw_set_chgsticky_fn'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Options_ID');
			/*
			Radio Buttons: chg-cat-method: 'no-change', 'add', 'subtract', 'exact'
			* This changes the categories of Posts, using the Category picker coming up.
			* 'add' will add the post to the selected categories
			* 'subtract' will remove the post from the selected categories
			* 'exact' will make the post be in exactly the selected categories, adding or removing as needed
			*/
			add_settings_field(
				'chg-cat-method',
				__('Apply Category changes as:', 'contentscheduler'),
				array($this, 'draw_set_chgcatmethod_fn'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Options_ID');
			/*
			Category Picker Checkboxes: I'm hoping there is some automatic way to generate this.
			* I guess the option would be an array of categories (tags?)
			* If so, let's call it selcats
			*/
			add_settings_field(
				'selcats',
				__('Selected Categories:', 'contentscheduler'),
				array($this, 'draw_set_categories_fn'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Options_ID');
			// 3/21/2011 3:05:01 PM -pk
			// Adding ability to add tags to expired content
			// Must check to see if the content supports post_tags first
			add_settings_field(
				'tags-to-add',
				__('Change tag(s):', 'contentscheduler'),
				array($this, 'draw_add_tags_fn'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Options_ID');
			/*
			== Notification ==
			Checkbox: Use notification: 'notify-on'
			*/
			add_settings_field(
				'notify-on',
				__('Enable notification:', 'contentscheduler'),
				array($this, 'draw_notify_on_fn'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Not_ID');
			/*
			Textbox: Notify before expiration: 'exp-notify-before'
			* This is a number of days before expiration.
			*/
			add_settings_field(
				'notify-before',
				__('Notify before expiration:', 'contentscheduler'),
				array($this, 'draw_notify_before_fn'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Not_ID');
			
			// 8/8/2011 11:52:49 PM -pk
			// We are removing this option
			/*
			Checkbox: Notify upon expiration: 'exp-notify-when'
			*/
			/*
			add_settings_field(
				'notify-expire',
				__('Notify upon expiration:', 'contentscheduler'),
				array($this, 'draw_notify_expire_fn'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Not_ID');
			*/
			
			/*
			Checkbox: Notify admin: 'exp-notify-admin'
			*/
			add_settings_field(
				'notify-admin',
				__('Notify Site Administrator:', 'contentscheduler'),
				array($this, 'draw_notify_admin_fn'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Not_ID');
			/*
			Checkbox: Notify author: 'exp-notify-author'
			*/
			add_settings_field(
				'notify-author',
				__('Notify Content Author:', 'contentscheduler'),
				array($this, 'draw_notify_author_fn'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Not_ID');
			/*
			Select Menu: 'min-level': For minimum user role that can see Content Scheduler forms and shortcode output.
			*/
			add_settings_field(
				'min-level',
				__('Minimum user role to see Content Scheduler fields and shortcodes:', 'contentscheduler'),
				array($this, 'draw_min_level_fn'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Disp_ID');
			/*
			== Columns option ==
			Checkbox: Show expiration date in column views: 'exp-show-column'
			* This determines whether or not an expiration date column is shown when viewing a list of Posts or Pages.
			*/
			add_settings_field(
				'show-columns',
				__('Show expiration in columns:', 'contentscheduler'),
				array($this, 'draw_show_columns_fn'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Disp_ID');
			/*
			== jQuery Option ==
			Checkbox: Show popup calendar for date: 'use-popup'
			* Determines whether or not the popup jQuery calendar will be used when selecting a date.
			* Based on this, we will load / not load the needed scripts and styles.
			*/
			add_settings_field(
				'datepicker',
				__('Use datepicker for Date:', 'contentscheduler'),
				array($this, 'draw_show_datepicker_fn'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Disp_ID');
			/*
			== Settings Removal Option ==
			Checkbox: remove all data upon uninstall? (Not deactivation...)
			This affects an action that uninstall.php will take, whether or not to remove OPTIONS and METADATA
			*/
			add_settings_field(
				'remove-cs-data',
				__('Remove all Content Scheduler data upon uninstall:', 'contentscheduler'),
				array($this, 'draw_remove_data_fn'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Disp_ID');
			// Version
			add_settings_field(
				'version',
				__('Plugin version:', 'contentscheduler'),
				array($this, 'draw_plugin_version'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Disp_ID');
			// CRON debug
			if( WP_DEBUG === TRUE )
			{
			add_settings_field(
				'crondebug',
				__('CRON Debug:', 'contentscheduler'),
				array($this, 'cs_view_cron_settings'),
				'ContentScheduler_Page_Title',
				'ContentScheduler_Disp_ID');
			}
?>