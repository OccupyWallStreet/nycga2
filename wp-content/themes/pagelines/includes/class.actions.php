<?php

/**
 * 
 *
 *  PageLines Actions
 *
 *
 *  @package PageLines Framework
 *  @subpackage Actions
 *  @since 1.4.0
 *
 */
class PageLinesActions(){
	

	/**
	*
	* @TODO document
	*
	*/
	function __contruct(){
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function get_hooks_array(){
		
		
		global $pl_section_factory;

		$hooks['core'] = array(
			'pagelines_before_html',
			'pagelines_before_site',
			'pagelines_before_page',
			'pagelines_before_header',
			'pagelines_header',
			'pagelines_before_main',
			'pagelines_after_excerpt',
			'brandnav_after_brand',
			'brandnav_after_nav',
			'pagelines_loop_post_start',
			'pagelines_loop_page_title_after',
			'pagelines_loop_post_header_start',
			'pagelines_loop_before_post_content',
			'pagelines_loop_after_post_content',
			'pagelines_loop_post_end',
			'pagelines_loop_clipbox_start',
			'pagelines_loop_clip_start',
			'pagelines_loop_clip_excerpt_end',
			'pagelines_loop_clip_end',
			'pagelines_loop_clipbox_end',
			'pagelines_soapbox_links',
			'pagelines_soapbox_inside_bottom',
			'pagelines_box_inside_bottom',
			'pagelines_feature_before',
			'pagelines_fcontent_before',
			'pagelines_feature_text_top',
			'pagelines_feature_text_bottom',
			'pagelines_fcontent_after',
			'pagelines_feature_media_top',
			'pagelines_feature_after',
			'pagelines_feature_nav_before',
			'pagelines_before_twitterbar_text',
			'pagelines_before_branding_icons',
			'pagelines_branding_icons_start',
			'pagelines_branding_icons_end',
			'pagelines_after_branding_wrap',
			'pagelines_content_before_columns',
			'pagelines_content_before_maincolumn',
			'pagelines_content_before_sidebar1',
			'pagelines_content_after_sidebar1',
			'pagelines_morefoot',
			'pagelines_after_morefoot'
			);

		foreach($pl_section_factory->sections as $s){

			$hooks[ $s->id ][] = 'pagelines_before_'.$s->id;

			// $hooks[] = 'pagelines_inside_top_'.$s->id;
			// 	$hooks[] = 'pagelines_inside_bottom_'.$s->id;
			// 	$hooks[] = 'pagelines_after_'.$s->id;
		}

		return apply_filters('pagelines_hooks', $hooks);
		
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function actions_template(){

			$action_hooks = get_hooks(); // Get hooks array

		?>
		<form action="options.php" method="post">
			<?php settings_fields( PL_ACTION_SETTINGS ); ?>
			<div id="actions_plugin" class="actions_panel">
				<div class="actions_panel_pad">
					<div class="actions_head">
						<div class="actions_head_pad">
							<div class="actions_title">PageLines Actions</div>
							<div class="actions_title_sub">Insert <abbr title="Hypertext Markup Language">HTML</abbr>, <abbr title="Cascading Style Sheets">CSS</abbr>, JavaScript or <abbr title="PHP: Hypertext Preprocessor">PHP</abbr> through Hooks in PageLines.</div>
						</div>
					</div>
					<div class="actions_sub_head">
						<div class="actions_sub_head_pad">
							<label for="<?php pl_actions_option_id('disabled');?>"><input type="checkbox" class="pl_check" id="<?php pl_actions_option_id('disabled');?>" name="<?php pl_actions_option_name('disabled');?>" <?php checked((bool) actions_option('disabled')); ?> />Disable All</label>
							<label for="<?php pl_actions_option_id('enable_php');?>"><input type="checkbox" class="pl_check" id="<?php pl_actions_option_id('disabled');?>" name="<?php pl_actions_option_name('enable_php');?>" <?php checked((bool) actions_option('enable_php')); ?> />Enable PHP</label>
							<input name="Submit" class="button-primary" type="submit" value="<?php echo esc_attr('Save Changes');?>" />
							<div class="clear"></div>
						</div>
					</div>
					<div class="actions_body">
						<div class="actions_body_pad">

							<div class="action_map">
									<div class="pl_title">Show Action Map</div>
									<div class="action_map_left">
										<div class="action_map_pad">
										<label for="<?php pl_actions_option_id('demo');?>"><input type="checkbox" class="pl_check" id="<?php pl_actions_option_id('demo');?>" name="<?php pl_actions_option_name('demo');?>" <?php checked((bool) actions_option('demo')); ?> />Show Action Map</label>
										</div>
									</div>
									<div class="action_map_right">
										<div class="action_map_pad">
											<div class="pl_title_sub">
												<p>The action map will create a visualization of hooks (places you can insert code) on the front end of your site.</p>

												<p><strong>Note:</strong> This will only be visible to administrators.</p> 
											</div>
										</div>
									</div>
									<div class="clear"></div>

							</div>
							<div class="action_select_container">

								<div class="action_select">
									<label for="<?php pl_actions_option_id('action_select');?>">Select Hook</label><br/>
									<select size=1 id="<?php pl_actions_option_id('action_select');?>" name="<?php pl_actions_option_name('action_select');?>" onChange="ActionsChangeField(this);">
										<?php foreach($action_hooks as $key => $hooks ):?>
											<optgroup label="<?php echo ucfirst($key);?>" class="optgroup_hooks">
											<?php foreach($hooks as $h ):?>
												<option value="<?php echo $h; ?>" <?php selected($h, actions_option('action_select')); ?> ><?php echo $h; ?></option>
										<?php endforeach; ?>
											</optgroup>
										<?php endforeach;?>
									</select>
								</div>

								<div class="action_select_fields">
									<?php foreach($action_hooks as $top ) foreach($top as $h) $this->_do_hook_field( $h ); ?>
									<div class="subinfo">Enter standard HTML. You can also use shortcodes or PHP (if option is enabled)</div>
								</div>
								<div class="clear"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
		<?php
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function _do_hook_field($hook){ 

		$opt_text = actions_option($hook);	

		$last_selected = actions_option('action_select');

		$show_field = ( $hook == $last_selected ) ? true : false;

		?>
		<div id="action_field_<?php echo $hook;?>" class="action_field" style="<?php if(!$show_field) echo 'display: none';?>">
		<label for="<?php pl_actions_option_id($hook);?>" class="hooklabel"><span>Hook:</span> <?php echo $hook;?></label><br/>
		<textarea name="<?php pl_actions_option_name($hook);?>" id="<?php pl_actions_option_id($hook);?>" rows='6' cols='50' size='40'><?php echo $opt_text;?></textarea>
		</div>

	<?php }
	
}


// Each hook needs to be added to this array and have a function added below
// for the action plugin to see it.
// Child themes can use the pagelines_hooks filter and have their functions stored locally

/**
*
* @TODO do
*
*/
function pagelines_get_hooks() {
	
	global $pl_section_factory;
	
	$hooks['core'] = array(
		'pagelines_before_html',
		'pagelines_before_site',
		'pagelines_before_page',
		'pagelines_before_header',
		'pagelines_header',
		'pagelines_before_main',
		'pagelines_after_excerpt',
		'brandnav_after_brand',
		'brandnav_after_nav',
		'pagelines_loop_post_start',
		'pagelines_loop_page_title_after',
		'pagelines_loop_post_header_start',
		'pagelines_loop_before_post_content',
		'pagelines_loop_after_post_content',
		'pagelines_loop_post_end',
		'pagelines_loop_clipbox_start',
		'pagelines_loop_clip_start',
		'pagelines_loop_clip_excerpt_end',
		'pagelines_loop_clip_end',
		'pagelines_loop_clipbox_end',
		'pagelines_soapbox_links',
		'pagelines_soapbox_inside_bottom',
		'pagelines_box_inside_bottom',
		'pagelines_feature_before',
		'pagelines_fcontent_before',
		'pagelines_feature_text_top',
		'pagelines_feature_text_bottom',
		'pagelines_fcontent_after',
		'pagelines_feature_media_top',
		'pagelines_feature_after',
		'pagelines_feature_nav_before',
		'pagelines_before_twitterbar_text',
		'pagelines_before_branding_icons',
		'pagelines_branding_icons_start',
		'pagelines_branding_icons_end',
		'pagelines_after_branding_wrap',
		'pagelines_content_before_columns',
		'pagelines_content_before_maincolumn',
		'pagelines_content_before_sidebar1',
		'pagelines_content_after_sidebar1',
		'pagelines_morefoot',
		'pagelines_after_morefoot'
		);
		
	foreach($pl_section_factory->sections as $s){
		
		$hooks[ $s->id ][] = 'pagelines_before_'.$s->id;
		
		// $hooks[] = 'pagelines_inside_top_'.$s->id;
		// 	$hooks[] = 'pagelines_inside_bottom_'.$s->id;
		// 	$hooks[] = 'pagelines_after_'.$s->id;
	}
	
	return apply_filters('pagelines_hooks', $hooks);
}

/**
*
* @TODO do
*
*/
function pl_pagelines_morefoot() {
	pl_do_hook( 'pagelines_morefoot' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_after_morefoot() {
	pl_do_hook( 'pagelines_after_morefoot' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_before_header() {
	pl_do_hook( 'pagelines_before_header' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_before_main() {
	pl_do_hook( 'pagelines_before_main' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_header() {
	pl_do_hook( 'pagelines_header' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_before_page() {
	pl_do_hook( 'pagelines_before_page' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_before_site() {
	pl_do_hook( 'pagelines_before_site' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_before_html() {
	pl_do_hook( 'pagelines_before_html' );
}

/**
*
* @TODO do
*
*/
function pl_brandnav_after_brand() {
	pl_do_hook( 'brandnav_after_brand' );
}

/**
*
* @TODO do
*
*/
function pl_brandnav_after_nav() {
	pl_do_hook( 'brandnav_after_nav' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_after_excerpt() {
	pl_do_hook( 'pagelines_after_excerpt' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_loop_post_start() {
	pl_do_hook( 'pagelines_loop_post_start' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_loop_page_title_after() {
	pl_do_hook( 'pagelines_loop_page_title_after' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_loop_post_header_start() {
	pl_do_hook( 'pagelines_loop_post_header_start' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_loop_before_post_content() {
	pl_do_hook( 'pagelines_loop_before_post_content' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_loop_after_post_content() {
	pl_do_hook( 'pagelines_loop_after_post_content' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_loop_post_end() {
	pl_do_hook( 'pagelines_loop_post_end' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_loop_clipbox_start() {
	pl_do_hook( 'pagelines_loop_clipbox_start' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_loop_clip_start() {
	pl_do_hook( 'pagelines_loop_clip_start' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_loop_clip_excerpt_end() {
	pl_do_hook( 'pagelines_loop_clip_excerpt_end' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_loop_clip_end() {
	pl_do_hook( 'pagelines_loop_clip_end' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_loop_clipbox_end() {
	pl_do_hook( 'pagelines_loop_clipbox_end' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_soapbox_links() {
	pl_do_hook( 'pagelines_soapbox_links' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_soapbox_inside_bottom() {
	pl_do_hook( 'pagelines_soapbox_inside_bottom' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_box_inside_bottom() {
	pl_do_hook( 'pagelines_box_inside_bottom' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_feature_before() {
	pl_do_hook( 'pagelines_feature_before' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_fcontent_before() {
	pl_do_hook( 'pagelines_fcontent_before' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_feature_text_top() {
	pl_do_hook( 'pagelines_feature_text_top' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_feature_text_bottom() {
	pl_do_hook( 'pagelines_feature_text_bottom' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_fcontent_after() {
	pl_do_hook( 'pagelines_fcontent_after' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_feature_media_top() {
	pl_do_hook( 'pagelines_feature_media_top' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_feature_after() {
	pl_do_hook( 'pagelines_feature_after' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_feature_nav_before() {
	pl_do_hook( 'pagelines_feature_nav_before' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_before_twitterbar_text() {
	pl_do_hook( 'pagelines_before_twitterbar_text' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_before_branding_icons() {
	pl_do_hook( 'pagelines_before_branding_icons' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_branding_icons_start() {
	pl_do_hook( 'pagelines_branding_icons_start' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_branding_icons_end() {
	pl_do_hook( 'pagelines_branding_icons_end' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_after_branding_wrap() {
	pl_do_hook( 'pagelines_after_branding_wrap' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_content_before_columns() {
	pl_do_hook( 'pagelines_content_before_columns' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_content_before_maincolumn() {
	pl_do_hook( 'pagelines_content_before_maincolumn' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_content_before_sidebar1() {
	pl_do_hook( 'pagelines_content_before_sidebar1' );
}

/**
*
* @TODO do
*
*/
function pl_pagelines_content_after_sidebar1() {
	pl_do_hook( 'pagelines_content_after_sidebar1' );
}
