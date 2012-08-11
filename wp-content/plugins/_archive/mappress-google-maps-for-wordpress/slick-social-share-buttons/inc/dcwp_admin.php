<?php
require_once('dcwp_plugin_admin.php');
require_once('dcwp_stats.php');

if(!class_exists('dc_jqslicksocial_admin')) {
	
	class dc_jqslicksocial_admin extends dcwp_dcssb_plugin_admin {
	
		var $hook = 'slick-social-share-buttons';
		var $longname = 'Slick Social Share Buttons Configuration';
		var $shortname = 'Slick Social Share Buttons';
		var $filename = 'slick-social-share-buttons/dcwp_slick_social_buttons.php';
		var $homepage = 'http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-slick-social-share-buttons/';
		var $imageurl = 'http://www.designchemical.com/media/images/slick_social_share_buttons.jpg';
		var $homeshort = 'http://bit.ly/q9oapO';
		var $twitter = 'designchemical';
		var $title = 'Wordpress plugin Slick Social Share Buttons';
		var $description = 'Add twitter, google +1, facebook like, linkedin, stumbleupon, digg, delicious, reddit & pin it social media share buttons in a slick floating or slide out tab';
		
		function __construct() {
		
			parent::__construct();
			
			add_action('admin_init', array(&$this,'settings_init'));
			add_action('init', array(&$this,'dcssb_admin_init'));
			add_action('wp_ajax_dcssb_update', array(&$this,'dcssb_ajax_update'));
			
		}
		 
		function settings_init() {
		
			register_setting('dcssb_options_group', 'dcssb_options');
		
		}
		
		function dcssb_admin_init(){
			if (is_admin()) {
				wp_enqueue_script( 'dcssbjqueryadmin', dc_jqslicksocial::get_plugin_directory().'/inc/js/jquery.admin.js', array('jquery'));
				wp_enqueue_style( 'dcssbadmin', dc_jqslicksocial::get_plugin_directory().'/css/admin_dcssb.css');
			}
		}
		
		// Plugin specific side info box
		function info_box() {}
		
		// AJAX updates for settings
		function dcssb_ajax_update(){
		
			$option_name = $_POST['option_name'];
			$newvalue = $_POST['option_value'];
			
			if ( get_option( $option_name ) != $newvalue ) {
				update_option( $option_name, $newvalue );
			} else {
				$deprecated = ' ';
				$autoload = 'no';
				add_option( $option_name, $newvalue, $deprecated, $autoload );
			}
			
			exit;
		}
		
		// Options page
		function option_page() {
			
			$this->setup_admin_page('Slick Social Buttons Settings','Slick Social Buttons Configuration Settings');
		?>
					  
		<?php if (!empty($message)) : ?>
			<div id="message" class="updated fade"><p><strong><?php echo $message ?></strong></p></div>
		<?php endif; ?>
		
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
		
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('.button-eg').each(function(){
				var type = $(this).attr('rel').replace('dcssb_','');
				var size = $('#dcssb_'+type).val();
				$('span.dcssb-button.'+type).hide();
				$('span.dcssb-button.'+type+'.'+size).fadeIn();
			});
			$('select.dcssb-button').change(function(){
				var type = $(this).attr('id').replace('dcssb_','');
				var size = $(this).val();
				$('span.dcssb-button.'+type).hide();
				$('span.dcssb-button.'+type+'.'+size).fadeIn();
			});
			$('.link-method').click(function(){
				var method = $(this).val();
				$('.method-hide').hide();
				$('.method-'+method).show();
			});
			$('.hide').hide();
			$('#dcssb_position').change(function(){
				var pos = $('#dcssb_position option:selected').val();
				if(pos == 'left' || pos == 'right'){
					$('option.pos-horizontal').hide();
					$('#dcssb_direction').val('vertical');
				} else {
					$('option.pos-horizontal').show();
				}
			});
			var loadOrder = [];
			$("#sortable li").each(function(){
				var rel = $(this).attr('rel');
				var i = $(this).index('#sortable li');
				loadOrder.push(rel);
			});
			$('#dcssb-order').val(loadOrder);
			$("#sortable").sortable({
				placeholder: "sort-holder",
				stop: function(event, ui) {
					var sortOrder = [];
					$("#sortable li").each(function(){
						var rel = $(this).attr('rel');
						var i = $(this).index('#sortable li');
						sortOrder.push(rel);
					});
					$('#dcssb-order').val(sortOrder);
				}
			});
			$('.dcwp-accordion a').click(function(e){
				$(this).parent().toggleClass('active').next().slideToggle();
				$('span',this).toggle();
				e.preventDefault();
			});
			$( "#sortable" ).disableSelection();
			$('.dcwp-category-list li a').live('click',function(){
				$(this).addClass('process');
				var rel = $(this).attr('rel');
				var text = $(this).text();
				var item = '<li><a href="#" rel="'+rel+'" class="cat-exclude">'+text+'</a></li>';
				
				$(this).parent().fadeOut(300,function(){
					$(this).remove();
				});
				var list = '#dcssb_list_include';
				if($(this).hasClass('cat-include')){
					list = '#dcssb_list_exclude';
				}
				$(list).append(item).fadeIn(300);
				return false;
			});
			$('.button-primary').live('click',function(){
				var cat = [];
				$('#dcssb_list_exclude li a').each(function(){
					cat.push($(this).attr('rel'));
				});
				if(cat != ''){
					cat = ','+cat+',';
				}
				$('#dcssb_exclude_category').val(cat);
			});
			$('#dcssb_center').click(function(){
				$('.distance-center').toggle();
			});
		});
		</script>
		<form method="post" id="dcssb_settings_page" class="dcwp-form dcssb-form" action="options.php">
		
		<div class="metabox-holder">
				  <div class="meta-box-sortables">
				    <div class="postbox">
					  <h3 class="hndle"><span>Slick Social Buttons Panel Settings</span></h3>
					  <div class="inside">
		<p class="dcwp-intro">For instructions on how to configure this plugin check out the <a target="_blank" href="http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-slick-social-share-buttons/"><?php echo $this->shortname; ?> project page</a>.</p>
			<?php 
				settings_fields('dcssb_options_group'); $options = get_option('dcssb_options'); 
				
				// Set defaults
				$method = $options['method'] ? $options['method'] : 'float' ;
				$position = $options['position'] ? $options['position'] : 'top-right' ;
				$offsetL = $options['offsetL'] ? $options['offsetL'] : '50' ;
				$offsetA = $options['offsetA'] ? $options['offsetA'] : '50' ;
				$center = $options['center'] ? $options['center'] : '' ;
				$centerpx = $options['centerpx'] ? $options['centerpx'] : '' ;
				$direction = $options['direction'] ? $options['direction'] : 'vertical' ;
				$speedFloat = $options['speedFloat'] ? $options['speedFloat'] : '1600' ;
				$speedMenu = $options['speedMenu'] ? $options['speedMenu'] : '600' ;
				$disableFloat = $options['disableFloat'] ? $options['disableFloat'] : '' ;
				$autoClose = $options['autoClose'] ? $options['autoClose'] : 'false' ;
				$loadOpen = $options['loadOpen'] ? $options['loadOpen'] : 'false' ;
				$skin = $options['skin'] ? $options['skin'] : 'false' ;
				$tabImage = $options['tabImage'] ? $options['tabImage'] : '' ;
				$show_home = $options['show_home'] ? $options['show_home'] : 'false' ;
				$show_blog = $options['show_blog'] ? $options['show_blog'] : 'false' ;
				$show_post = $options['show_post'] ? $options['show_post'] : 'false' ;
				$show_page = $options['show_page'] ? $options['show_page'] : 'false' ;
				$show_category = $options['show_category'] ? $options['show_category'] : 'false' ;
				$show_archive = $options['show_archive'] ? $options['show_archive'] : 'false' ;
				$exclude_category = $options['exclude_category'] ?  $options['exclude_category'] : '' ;
				$default_order = 'twitter,facebook,plusone,linkedin,stumble,digg,delicious,pinit,reddit,buffer' ;
				$dcssb_order = $options['dcssb_order'] ?  $options['dcssb_order'] : $default_order ;
				$shortener = $options['shortener'] ? $options['shortener'] : 'none' ;
				$shortener_api = $options['shortener_api'] ? $options['shortener_api'] : '' ;
				$shortener_login = $options['shortener_login'] ? $options['shortener_login'] : '' ;
				
				$hideFloat = $method == 'stick' ? ' hide' : '' ;
				$hideStick = $method == 'float' ? ' hide' : '' ;
				$hideHoriz = $position == 'left' || $position == 'right' ? ' hide' : '' ;
				$hideCenter = $center == 'true' ? '' : ' hide';
				$showCenter = $center == 'true' ? ' hide' : '';
				
			?>
				<ul class="half">
					<li>
						<label for="dcssb_method_float">Type</label>
						<input type="radio" id="dcssb_method_float" name="dcssb_options[method]" value="float"<?php checked( $method, 'float' ); ?> class="link-method" /> Floating 
						<input type="radio" id="dcssb_method_stick" name="dcssb_options[method]" value="stick"<?php checked( $method, 'stick' ); ?> class="link-method" /> Slide Out
					</li>
	
					<li>
					  <label for="dcssb_position">Location</label>
						<select name="dcssb_options[position]" id="dcssb_position" >
							<option value='top-left' <?php selected( $position, 'top-left'); ?> >Top Left</option>
							<option value='top-right' <?php selected( $position, 'top-right'); ?> >Top Right</option>
							<option value='bottom-left' <?php selected( $position, 'bottom-left'); ?> >Bottom Left</option>
							<option value='bottom-right' <?php selected( $position, 'bottom-right'); ?> >Bottom Right</option>
							<option value='left' <?php selected( $position, 'left'); ?> >Left</option>
							<option value='right' <?php selected( $position, 'right'); ?> >Right</option>
						</select> 
					</li>
					<li class="method-float method-hide<?php echo $hideFloat; ?>">
						<span class="margin-right">Position From Center </span>
						<input type="checkbox" value="true" class="checkbox margin-right" id="dcssb_center" name="dcssb_options[center]"<?php checked( $center, 'true'); ?> />
						 <span class="distance-center<?php echo $hideCenter; ?>">pixels
						 <input type="text" id="dcssb_centerpx" name="dcssb_options[centerpx]" value="<?php echo $centerpx; ?>" size="4" /> </span>
					</li>
					<li>
					  <label for="dcssb_offset">Offset</label>
						<input type="text" id="dcssb_offsetL" name="dcssb_options[offsetL]" value="<?php echo $offsetL; ?>" size="4" /> px  
						<span class="distance-center method-float method-hide<?php echo $hideFloat.$showCenter; ?>"><input type="text" id="dcssb_offsetA" name="dcssb_options[offsetA]" value="<?php echo $offsetA; ?>" size="4" /> px</span>
					</li>
					<li class="method-stick method-hide<?php echo $hideStick; ?>">
					  <label for="dcssb_direction">Direction</label>
						<select name="dcssb_options[direction]" id="dcssb_direction" >
							<option value='vertical' <?php selected( $direction, 'vertical'); ?> >Vertical</option>
							<option value='horizontal' class="pos-horizontal<?php echo $hideHoriz; ?>" <?php selected( $direction, 'horizontal'); ?> >Horizontal</option>
						</select> 
					</li>
					<li>
					  <label for="dcssb_skin">Default Skin</label>
					  <input type="checkbox" value="true" class="checkbox" id="dcssb_skin" name="dcssb_options[skin]"<?php checked( $skin, 'true'); ?> />
					</li>
				  </ul>
				  <ul class="half right">
					<li class="method-float method-hide<?php echo $hideFloat; ?>">
						<span class="margin-right">Disable Floating Effects </span>
						<input type="checkbox" value="true" class="checkbox" id="dcssb_disableFloat" name="dcssb_options[disableFloat]"<?php checked( $disableFloat, 'true'); ?> />
					</li>
					<li class="method-float method-hide<?php echo $hideFloat; ?>">
						<label for="dcssb_speedFloat">Float Speed</label>
						<input type="text" id="dcssb_speedFloat" name="dcssb_options[speedFloat]" value="<?php echo $speedFloat; ?>" size="5" class="margin-right" /> (ms)
					</li>
					<li>
						<label for="dcssb_speedMenu">Slide Speed</label>
						 <input type="text" id="dcssb_speedMenu" name="dcssb_options[speedMenu]" value="<?php echo $speedMenu; ?>" size="5" /> (ms)
						</li>
	
					<li>
						<label for="dcssb_autoClose">Auto-Close</label> 
						<input type="checkbox" value="true" class="checkbox" id="dcssb_autoClose" name="dcssb_options[autoClose]"<?php checked( $autoClose, 'true'); ?> />
					</li>
					<li>
						<label for="dcssb_loadOpen">Load Open</label> 
						<input type="checkbox" value="true" class="checkbox" id="dcssb_loadOpen" name="dcssb_options[loadOpen]"<?php checked( $loadOpen, 'true'); ?> />
					</li>
					<li>
						<label for="dcssb_tabImage">Tab Image URL</label>
						<input type="text" id="dcssb_tabImage" name="dcssb_options[tabImage]" value="<?php echo $tabImage; ?>" size="30" />
						<span class="description dcwp-note">Leave blank to use default tab</span>
					</li>
				  </ul>
				  <p class="submit clear">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			</div></div></div></div>
			<div class="metabox-holder">
				  <div class="meta-box-sortables">
				    <div class="postbox">
					  <h3 class="hndle"><span>Display Pages For Social Share Buttons</span></h3>
					  <div class="inside">
				 <p class="dcwp-intro">Select the pages where you wish the button panel to appear:</p>
				  <ul class="third">
					<li>
					  <input type="checkbox" value="true" class="checkbox" id="dcssb_show_home" name="dcssb_options[show_home]"<?php checked( $show_home, 'true'); ?> class="margin-right" /> Home Page
					</li>
					<li>
					  <input type="checkbox" value="true" class="checkbox" id="dcssb_show_page" name="dcssb_options[show_page]"<?php checked( $show_page, 'true'); ?> class="margin-right" /> Pages
					</li>
				  </ul>
				  <ul class="third">
					<li>
					  <input type="checkbox" value="true" class="checkbox" id="dcssb_show_blog" name="dcssb_options[show_blog]"<?php checked( $show_blog, 'true'); ?> class="margin-right" /> Posts Page
					</li>
					<li>
					  <input type="checkbox" value="true" class="checkbox" id="dcssb_show_category" name="dcssb_options[show_category]"<?php checked( $show_category, 'true'); ?> class="margin-right<?php echo $catClass; ?>" /> Category Pages
					</li>
				  </ul>
				  <ul class="third right">
				    <li>
					  <input type="checkbox" value="true" class="checkbox" id="dcssb_show_post" name="dcssb_options[show_post]"<?php checked( $show_post, 'true'); ?> class="margin-right" /> Posts
					</li>
					<li>
					  <input type="checkbox" value="true" class="checkbox" id="dcssb_show_archive" name="dcssb_options[show_archive]"<?php checked( $show_archive, 'true'); ?> class="margin-right" /> Archive Pages
					</li>
				</ul>
				<div class="dcwp-accordion"><a href="#"><span>Show Categories</span><span class="hide">Hide Categories</span></a></div>
				<div class="hide">
				<p class="dcwp-intro clear">Click a category to change. If "Category Pages" checkbox is unchecked all categories will be excluded</p>
				
				
				<ul class="dcwp-category-list clear" id="dcssb_list_include">
				  <li><strong>Include</strong></li>
					<?php
				
					$args=array('orderby' => 'name', 'order' => 'ASC');
					$categories=get_categories($args);
					foreach($categories as $category) {
						
						if (!strlen(strstr($exclude_category,','.$category->term_id.','))>0) {
							echo '<li><a href="#" rel="'.$category->term_id.'" class="cat-include">'.$check.$category->name.'</a></li>';
						}
					} 
				?>
				</ul>
				<ul class="dcwp-category-list" id="dcssb_list_exclude">
				  <li><strong>Exclude</strong></li>
					<?php
					$excludes = explode(',', $exclude_category);
					foreach($excludes as $exclude) {
					
						echo $exclude != '' ? '<li><a href="#" rel="'.$exclude.'">'.get_cat_name($exclude).'</a></li>' : '';
						
					} 
				?>
				</ul>
				</div>
				<input type="hidden" value="<?php echo $exclude_category; ?>" name="dcssb_options[exclude_category]" id="dcssb_exclude_category" />
				<p class="submit clear">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
				
				</div></div></div></div>
		<div class="metabox-holder">
				  <div class="meta-box-sortables">
				    <div class="postbox">
					  <h3 class="hndle"><span>Social Share Buttons</span></h3>
					  <div class="inside">
			
				<ul id="sortable">
				  <?php
				  
					$functions = explode(',', $dcssb_order);
					$buttons = $default_order;
					
					foreach($functions as $function) {
		
						if($function != '' && $function != 'buzz'){
							$f_name = 'options_'.$function;
							$this->$f_name();
							$buttons = str_replace($function,'',$buttons);
						}
					}
					
					// Add new buttons
					$buttons = explode(',', $buttons);
					
					foreach($buttons as $button) {
		
						if($button != '' && $button != 'buzz'){
							$f_name = 'options_'.$button;
							$this->$f_name();
						}
					}
				  ?>
				</ul>
			
				<input type="hidden" value="<?php echo $dcssb_order; ?>" name="dcssb_options[dcssb_order]" id="dcssb-order" />
				<p class="submit">
				
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />

				</p>
				</div></div></div></div>
		<div class="metabox-holder">
				  <div class="meta-box-sortables">
				    <div class="postbox">
					  <h3 class="hndle"><span>Additional Information</span></h3>
					  <div class="inside">
					  <ul>
						  <li>
						    <label for="dcssb_user_twitter">Twitter Username</label>
						    <input type="text" name="dcssb_options[user_twitter]" id="dcssb_user_twitter" class="dcwp-input-m" value="<?php echo isset($options['user_twitter']) ?  $options['user_twitter'] : $user_twitter; ?>" /> <span class="dcwp-note">(without the '@')</span>
						  </li>
						  <li>
						    <label for="dcssb_app_facebook">Facebook App ID</label>
						    <input type="text" name="dcssb_options[app_facebook]" id="dcssb_app_facebook" class="dcwp-input-m" value="<?php echo isset($options['app_facebook']) ?  $options['app_facebook'] : $app_facebook; ?>" />
						  </li>
						  <li>
						    <label for="dcssb_disable_opengraph">Disable Opengraph</label>
						    <input type="checkbox" name="dcssb_options[disable_opengraph]" id="dcssb_disable_opengraph" value="true" <?php echo $options['disable_opengraph'] == 'true' ? 'checked="checked"' : '' ; ?> />
						  </li>
						  <li>
						    <label for="dcssb_admin_facebook">Facebook Admin ID</label>
							<input type="text" name="dcssb_options[admin_facebook]" id="dcssb_admin_facebook" class="dcwp-input-m" value="<?php echo isset($options['admin_facebook']) ?  $options['admin_facebook'] : $admin_facebook; ?>" />
					       </li>
						   <li>
						    <label for="dcssb_image_facebook">Default Facebook Image</label>
							<input type="text" name="dcssb_options[image_facebook]" id="dcssb_image_facebook" class="dcwp-input-m" value="<?php echo isset($options['image_facebook']) ?  $options['image_facebook'] : $image_facebook; ?>" />
					       </li>
						   <li>
						    <label for="dcssb_image_pinit">Default Pin It Image</label>
							<input type="text" name="dcssb_options[image_pinit]" id="dcssb_image_pinit" class="dcwp-input-m" value="<?php echo isset($options['image_pinit']) ?  $options['image_pinit'] : $image_pinit; ?>" />
					       </li>
					  </ul>
					  <p class="submit">
				
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />

				</p>
				</div></div></div></div>
		<div class="metabox-holder">
				  <div class="meta-box-sortables">
				    <div class="postbox">
					  <h3 class="hndle"><span>Twitter URL Shortener</span></h3>
					  <div class="inside">
						<ul>
						  <li>
							<label for="dcssb_shortener">Shortener</label>
							<select name="dcssb_options[shortener]" id="dcssb_shortener">
								<option value='bitly' <?php selected( $shortener, 'bitly'); ?> >bitly</option>
								<option value='tinyurl' <?php selected( $shortener, 'tinyurl'); ?> >tinyurl</option>
								<option value='digg' <?php selected( $shortener, 'digg'); ?> >digg</option>
								<option value='supr' <?php selected( $shortener, 'supr'); ?> >supr</option>
								<option value='none' <?php selected( $shortener, 'none'); ?> >None</option>
							</select>
						  </li>
						  <li>
						    <label for="dcssb_shortener_api">API Key</label>
							<input type="text" id="dcssb_shortener_api" name="dcssb_options[shortener_api]" value="<?php echo $shortener_api; ?>" size="40" />
							<span class="dcwp-note">bit.ly (required), su.pr (optional)</span>
						  </li>
						  <li>
						    <label for="dcssb_shortener_login">Login</label>
							<input type="text" id="dcssb_shortener_login" name="dcssb_options[shortener_login]" value="<?php echo $shortener_login; ?>" size="40" />
							<span class="dcwp-note">bit.ly (required), su.pr (optional)</span>
						  </li>
						</ul>
					  <p class="submit">
						<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
					  </p>
			<?php

				$this->close_admin_page();

		}
		
		function dcssb_stats_page() {
		
			?>
			<div class="wrap">
			  <h2 class="margin-bottom"><a href="http://www.designchemical.com/blog/" target="_blank" id="dcwp-avatar"></a>Slick Social Buttons Statistics</h2>
			
			<?php 
				$dcjqslicksocial_stats = new dc_jqslicksocial_stats();
			?>
			
			<div class="metabox-holder clear">	
				<div class="meta-box-sortables" id="dcwp-box-stats">						

					<?php
						$this->likebox();
						$this->dcwp_donate();
					?>				
					<div class="clear"></div>
				</div>
			</div>
			<p class="clear dcwp-intro">For instructions on how to configure this plugin check out the <a target="_blank" href="http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-slick-social-share-buttons/"><?php echo $this->shortname; ?> project page</a>.</p>
			</div>
				
			<?php

		}
		
		function options_twitter(){
			
			$options = get_option('dcssb_options');
			$size_twitter = $options['size_twitter'] ? $options['size_twitter'] : 'vertical' ;
			$incTwitter = $options['incTwitter'] ? $options['incTwitter'] : 'false' ;
			
			?>
			<li rel="twitter">
				  <table width="100%" class="dcwp-table" cellspacing="0" border="0" cellpadding="0">
				  <tr>
				    <td class="button-name">
					  <h4><input type="checkbox" value="true" class="checkbox" id="dcssb_inc_twitter" name="dcssb_options[incTwitter]"<?php checked( $incTwitter, 'true'); ?> class="margin-right" /> Twitter</h4>
					  </td>
					<td class="button-size">
						<select name="dcssb_options[size_twitter]" id="dcssb_size_twitter" class="dcssb-button">
							<option value='horizontal' <?php selected( $size_twitter, 'horizontal'); ?> >Horizontal + Count</option>
							<option value='vertical' <?php selected( $size_twitter, 'vertical'); ?> >Vertical + Count</option>
							<option value='none' <?php selected( $size_twitter, 'none'); ?> >Standard</option>
						</select>
					</td>
					<td class="button-others">
					  
					</td>
					<td class="button-eg" rel="dcssb_size_twitter">
						<div class="relative">
						
							<span class="dcssb-button size_twitter vertical">
							  <a href="http://twitter.com/share" data-url="<?php echo $this->homeshort; ?>" data-counturl="<?php echo $this->homepage; ?>" data-text="<?php echo $this->title; ?>" class="twitter-share-button" data-count="vertical" data-via="<?php echo $this->twitter; ?>"></a>
							</span>
							
							<span class="dcssb-button size_twitter horizontal">
							  <a href="http://twitter.com/share" data-url="<?php echo $this->homeshort; ?>" data-counturl="<?php echo $this->homepage; ?>" data-text="<?php echo $this->title; ?>" class="twitter-share-button" data-count="horizontal" data-via="<?php echo $this->twitter; ?>"></a>
							</span>
							
							<span class="dcssb-button size_twitter none">
							  <a href="http://twitter.com/share" data-url="<?php echo $this->homeshort; ?>" data-counturl="<?php echo $this->homepage; ?>" data-text="<?php echo $this->title; ?>" class="twitter-share-button" data-count="none" data-via="<?php echo $this->twitter; ?>"></a>
							</span>
							
						</div>
					</td>
				  </tr>
				  </table>
				  </li>
			<?php
		}
		
		function options_facebook(){
		
			$options = get_option('dcssb_options');
			$size_facebook = $options['size_facebook'] ? $options['size_facebook'] : 'box_count' ;
			$incFacebook = $options['incFacebook'] ? $options['incFacebook'] : 'false' ;
			$method_facebook = $options['method_facebook'] ? $options['method_facebook'] : 'xfbml' ;
			
			?>
			<li rel="facebook">
				  <table width="100%" class="dcwp-table" cellspacing="0" border="0" cellpadding="0">
				  <tr>
				    <td class="button-name">
					  <h4><input type="checkbox" value="true" class="checkbox" id="dcssb_inc_facebook" name="dcssb_options[incFacebook]"<?php checked( $incFacebook, 'true'); ?> class="margin-right" /> Facebook</h4>
					</td>
					<td class="button-size">
						<select name="dcssb_options[size_facebook]" id="dcssb_size_facebook" class="dcssb-button">
							<option value='standard' <?php selected( $size_facebook, 'standard'); ?> >Standard</option>
							<option value='button_count' <?php selected( $size_facebook, 'button_count'); ?> >Button Count</option>
							<option value='box_count' <?php selected( $size_facebook, 'box_count'); ?> >Box Count</option>
						</select>
						<select name="dcssb_options[method_facebook]" id="dcssb_method_facebook" class="dcssb-button">
							<option value='xfbml' <?php selected( $method_facebook, 'xfbml'); ?> >xfbml</option>
							<option value='iframe' <?php selected( $method_facebook, 'iframe'); ?> >iFrame</option>
						</select>
					</td>
					<td class="button-others">
					</td>
					
					<td class="button-eg" rel="dcssb_size_facebook">
						<div class="relative">
						
							<span class="dcssb-button size_facebook standard">
							  <iframe src="http://www.facebook.com/plugins/like.php?app_id=<?php echo $appId; ?>&amp;href=<?php echo urlencode($this->homepage); ?>&amp;send=false&amp;layout=standard&amp;width=48&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=60" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:48px; height:62px;" allowTransparency="true"></iframe>
							</span>
							
							<span class="dcssb-button size_facebook box_count">
							  <iframe src="http://www.facebook.com/plugins/like.php?app_id=<?php echo $appId; ?>&amp;href=<?php echo urlencode($this->homepage); ?>&amp;send=false&amp;layout=box_count&amp;width=48&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=60" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:48px; height:62px;" allowTransparency="true"></iframe>
							</span>
							
							<span class="dcssb-button size_facebook button_count">
							  <iframe src="http://www.facebook.com/plugins/like.php?app_id=<?php echo $appId; ?>&amp;href=<?php echo urlencode($this->homepage); ?>&amp;send=false&amp;layout=button_count&amp;width=48&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=60" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:48px; height:62px;" allowTransparency="true"></iframe>
							</span>
							
						</div>
					</td>
				  </tr>
				  </table>
				  </li>
			<?php
		}
		
		function options_plusone(){
		
			$options = get_option('dcssb_options');
			$size_plusone = $options['size_plusone'] ? $options['size_plusone'] : 'tall_count' ;
			$incPlusone = $options['incPlusone'] ? $options['incPlusone'] : 'false' ;
			
			?>
			<li rel="plusone">
				  <table width="100%" class="dcwp-table" cellspacing="0" border="0" cellpadding="0">
				  <tr>
				    <td class="button-name">
					  <h4><input type="checkbox" value="true" class="checkbox" id="dcssb_inc_plusone" name="dcssb_options[incPlusone]"<?php checked( $incPlusone, 'true'); ?> class="margin-right" /> Google +1</h4>
					</td>
					<td class="button-size">
						<select name="dcssb_options[size_plusone]" id="dcssb_size_plusone" class="dcssb-button">
							<option value='small' <?php selected( $size_plusone, 'small'); ?> >Small</option>
							<option value='small_count' <?php selected( $size_plusone, 'small_count'); ?> >Small + Count</option>
							<option value='medium' <?php selected( $size_plusone, 'medium'); ?> >Medium</option>
							<option value='medium_count' <?php selected( $size_plusone, 'medium_count'); ?> >Medium + Count</option>
							<option value='standard' <?php selected( $size_plusone, 'standard'); ?> >Standard</option>
							<option value='standard_count' <?php selected( $size_plusone, 'standard_count'); ?> >Standard + Count</option>
							<option value='tall_count' <?php selected( $size_plusone, 'tall_count'); ?> >Tall + Count</option>
						</select>
					</td>
					<td class="button-others">&nbsp;</td>
					
					<td class="button-eg" rel="dcssb_size_plusone">
						<div class="relative">
						
							<span class="dcssb-button size_plusone small">
							  <g:plusone size="small" href="<?php echo $this->homepage; ?>" count="false"></g:plusone>
							</span>
							
							<span class="dcssb-button size_plusone small_count">
							  <g:plusone size="small" href="<?php echo $this->homepage; ?>" count="true"></g:plusone>
							</span>
							
							<span class="dcssb-button size_plusone medium">
							  <g:plusone size="medium" href="<?php echo $this->homepage; ?>" count="false"></g:plusone>
							</span>
							
							<span class="dcssb-button size_plusone medium_count">
							  <g:plusone size="medium" href="<?php echo $this->homepage; ?>" count="true"></g:plusone>
							</span>
							
							<span class="dcssb-button size_plusone standard">
							  <g:plusone size="standard" href="<?php echo $this->homepage; ?>" count="false"></g:plusone>
							</span>
							
							<span class="dcssb-button size_plusone standard_count">
							  <g:plusone size="standard" href="<?php echo $this->homepage; ?>" count="true"></g:plusone>
							</span>
							
							<span class="dcssb-button size_plusone tall_count">
							  <g:plusone size="tall" href="<?php echo $this->homepage; ?>" count="true"></g:plusone>
							</span>
							
						</div>
					</td>
					
					
				  </tr>
				  </table>
				  </li>
			<?php
		}
		
		function options_linkedin(){
		
			$options = get_option('dcssb_options');
			$size_linkedin = $options['size_linkedin'] ? $options['size_linkedin'] : 'top' ;
			$incLinkedin = $options['incLinkedin'] ? $options['incLinkedin'] : 'false' ;
			
			?>
			<li rel="linkedin">
				  <table width="100%" class="dcwp-table" cellspacing="0" border="0" cellpadding="0">
				  <tr>
				    <td class="button-name">
					<h4><input type="checkbox" value="true" class="checkbox" id="dcssb_inc_linkedin" name="dcssb_options[incLinkedin]"<?php checked( $incLinkedin, 'true'); ?> class="margin-right" /> Linkedin</h4>
					</td>
					<td class="button-size">
						<select name="dcssb_options[size_linkedin]" id="dcssb_size_linkedin" class="dcssb-button">
							<option value='right' <?php selected( $size_linkedin, 'right'); ?> >Horizontal</option>
							<option value='top' <?php selected( $size_linkedin, 'top'); ?> >Vertical</option>
							<option value='none' <?php selected( $size_linkedin, 'none'); ?> >None</option>
						</select>
					</td>
					<td class="button-others">&nbsp;</td>
					<td class="button-eg" rel="dcssb_size_linkedin">
						<div class="relative">
						
							<span class="dcssb-button size_linkedin right">
							  <script type="in/share" data-url="<?php echo $this->homepage; ?>" data-counter="right"></script>
							</span>
							
							<span class="dcssb-button size_linkedin top">
							  </script><script type="in/share" data-url="<?php echo $this->homepage; ?>" data-counter="top"></script>
							</span>
							
							<span class="dcssb-button size_linkedin none">
							  <script type="in/share" data-url="<?php echo $this->homepage; ?>" data-counter="none"></script>
							</span>
							
						</div>
					</td>
				  </tr>
				  </table>
				  </li>
			<?php
		}
		
		function options_stumble(){
		
			$options = get_option('dcssb_options');
			$size_stumble = $options['size_stumble'] ? $options['size_stumble'] : '5' ;
			$incStumble = $options['incStumble'] ? $options['incStumble'] : 'false' ;
			
			?>
			<li rel="stumble">
				  <table width="100%" class="dcwp-table" cellspacing="0" border="0" cellpadding="0">
				  <tr>
				    <td class="button-name">
					  <h4><input type="checkbox" value="true" class="checkbox" id="dcssb_inc_stumble" name="dcssb_options[incStumble]"<?php checked( $incStumble, 'true'); ?> class="margin-right" /> Stumbleupon</h4>
					</td>
					<td class="button-size">
						<select name="dcssb_options[size_stumble]" id="dcssb_size_stumble" class="dcssb-button">
							<option value='1' <?php selected( $size_stumble, '1'); ?> >Horizontal + Count</option>
							<option value='2' <?php selected( $size_stumble, '2'); ?> >Medium + Count</option>
							<option value='3' <?php selected( $size_stumble, '3'); ?> >Small + Count</option>
							<option value='4' <?php selected( $size_stumble, '4'); ?> >Small</option>
							<option value='5' <?php selected( $size_stumble, '5'); ?> >Vertical + Count</option>
						</select>
					</td>
					<td class="button-others">&nbsp;</td>
					<td class="button-eg" rel="dcssb_size_stumble">
						<div class="relative">
						
							<span class="dcssb-button size_stumble 1">
							  <iframe src="http://www.stumbleupon.com/badge/embed/1/?url=<?php echo urlencode($this->homepage); ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height: 30px;" allowTransparency="true"></iframe>
							</span>
							
							<span class="dcssb-button size_stumble 2">
							  <iframe src="http://www.stumbleupon.com/badge/embed/2/?url=<?php echo urlencode($this->homepage); ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:70px; height: 30px;" allowTransparency="true"></iframe>
							</span>
							
							<span class="dcssb-button size_stumble 3">
							  <iframe src="http://www.stumbleupon.com/badge/embed/3/?url=<?php echo urlencode($this->homepage); ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:50px; height: 30px;" allowTransparency="true"></iframe>
							</span>
							
							<span class="dcssb-button size_stumble 4">
							  <iframe src="http://www.stumbleupon.com/badge/embed/4/?url=<?php echo urlencode($this->homepage); ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:50px; height: 30px;" allowTransparency="true"></iframe>
							</span>
							
							<span class="dcssb-button size_stumble 5">
							  <iframe src="http://www.stumbleupon.com/badge/embed/5/?url=<?php echo urlencode($this->homepage); ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:50px; height: 60px;" allowTransparency="true"></iframe>
							</span>
							
						</div>
					</td>
				  </tr>
				</table>
				</li>
			<?php
		}
		
		function options_digg(){
		
			$options = get_option('dcssb_options');
			$size_digg = $options['size_digg'] ? $options['size_digg'] : '5' ;
			$incDigg = $options['incDigg'] ? $options['incDigg'] : 'false' ;
			
			?>
			<li rel="digg">
				  <table width="100%" class="dcwp-table" cellspacing="0" border="0" cellpadding="0">
				  <tr>
				    <td class="button-name">
					  <h4><input type="checkbox" value="true" class="checkbox" id="dcssb_inc_digg" name="dcssb_options[incDigg]"<?php checked( $incDigg, 'true'); ?> class="margin-right" /> Digg</h4>
					</td>
					<td class="button-size">
						<select name="dcssb_options[size_digg]" id="dcssb_size_digg" class="dcssb-button">
							<option value='DiggMedium' <?php selected( $size_digg, 'DiggMedium'); ?> >Medium</option>
							<option value='DiggCompact' <?php selected( $size_digg, 'DiggCompact'); ?> >Compact</option>
							<option value='DiggIcon' <?php selected( $size_digg, 'DiggIcon'); ?> >Icon</option>
						</select>
					</td>
					<td class="button-others">&nbsp;</td>
					<td class="button-eg" rel="dcssb_size_digg">
						<div class="relative">
						
							<span class="dcssb-button size_digg DiggMedium">
							  <a href="http://digg.com/submit?url=<?php echo urlencode($this->homepage); ?>&amp;title=<?php echo urlencode($this->title); ?>" class="DiggThisButton DiggMedium"><span style="display: none;"><?php echo $this->description; ?></span></a>
							</span>
							
							<span class="dcssb-button size_digg DiggCompact">
							  <a href="http://digg.com/submit?url=<?php echo urlencode($this->homepage); ?>&amp;title=<?php echo urlencode($this->title); ?>" class="DiggThisButton DiggCompact"><span style="display: none;"><?php echo $this->description; ?></span></a>
							</span>
							
							<span class="dcssb-button size_digg DiggIcon">
							  <a href="http://digg.com/submit?url=<?php echo urlencode($this->homepage); ?>&amp;title=<?php echo urlencode($this->title); ?>" class="DiggThisButton DiggIcon"><span style="display: none;"><?php echo $this->description; ?></span></a>
							</span>
							
						</div>
					</td>
				  </tr>
				</table>
				</li>
			<?php
		}
		function options_delicious(){
		
			$options = get_option('dcssb_options');
			$size_delicious = $options['size_delicious'] ? $options['size_delicious'] : 'vertical' ;
			$incDelicious = $options['incDelicious'] ? $options['incDelicious'] : 'false' ;
			
			?>
			<li rel="delicious">
				  <table width="100%" class="dcwp-table" cellspacing="0" border="0" cellpadding="0">
				  <tr>
				    <td class="button-name">
					  <h4><input type="checkbox" value="true" class="checkbox" id="dcssb_inc_delicious" name="dcssb_options[incDelicious]"<?php checked( $incDelicious, 'true'); ?> class="margin-right" /> Delicious</h4>
					</td>
					<td class="button-size">
						<select name="dcssb_options[size_delicious]" id="dcssb_size_delicious" class="dcssb-button">
							<option value='wide' <?php selected( $size_delicious, 'wide'); ?> >Horizontal</option>
							<option value='tall' <?php selected( $size_delicious, 'tall'); ?> >Vertical</option>
						</select>
					</td>
					<td class="button-others">&nbsp;</td>
					<td class="button-eg" rel="dcssb_size_delicious">
						<div class="relative dcssb_size_delicious" style="padding-left: 32px;">
						
							<span class="dcssb-button size_delicious wide">
							  <a class="delicious-button" href="http://delicious.com/save">
 <!-- {
 url:"<?php echo $this->homepage; ?>"
 ,title:"<?php echo $this->title; ?>"
 ,button:"wide"
 } -->
 Save on Delicious
</a>
							</span>
							
							<span class="dcssb-button size_delicious tall">
							  <a class="delicious-button" href="http://delicious.com/save">
 <!-- {
 url:"<?php echo $this->homepage; ?>"
 ,title:"<?php echo $this->title; ?>"
 ,button:"tall"
 } -->
 Save on Delicious
</a>
							</span>
					
							
						</div>
					</td>
				  </tr>
				</table>
				</li>
			<?php
		}
		
		function options_reddit(){
		
			$options = get_option('dcssb_options');
			$size_reddit = $options['size_reddit'] ? $options['size_reddit'] : 'vertical' ;
			$incReddit = $options['incReddit'] ? $options['incReddit'] : 'false' ;
			
			?>
			<li rel="reddit">
				  <table width="100%" class="dcwp-table" cellspacing="0" border="0" cellpadding="0">
				  <tr>
				    <td class="button-name">
					  <h4><input type="checkbox" value="true" class="checkbox" id="dcssb_inc_reddit" name="dcssb_options[incReddit]"<?php checked( $incReddit, 'true'); ?> class="margin-right" /> Reddit</h4>
					</td>
					<td class="button-size">
						<select name="dcssb_options[size_reddit]" id="dcssb_size_reddit" class="dcssb-button">
							<option value='horizontal' <?php selected( $size_reddit, 'horizontal'); ?> >Horizontal</option>
							<option value='vertical' <?php selected( $size_reddit, 'vertical'); ?> >Vertical</option>
							<option value='none' <?php selected( $size_reddit, 'none'); ?> >No Count</option>
						</select>
					</td>
					<td class="button-others">&nbsp;</td>
					<td class="button-eg" rel="dcssb_size_reddit">
						<div class="relative">
						
							<span class="dcssb-button size_reddit horizontal">
							  <script type="text/javascript">
							  reddit_url = "<?php echo urlencode($this->homepage); ?>";
							  reddit_title = "<?php echo $this->title; ?>";
							  reddit_newwindow='1'
							  </script>
							  <script type="text/javascript" src="http://www.reddit.com/static/button/button1.js"></script>
							</span>
							
							<span class="dcssb-button size_reddit vertical">
							  <script type="text/javascript">
							  reddit_url = "<?php echo urlencode($this->homepage); ?>";
							  reddit_title = "<?php echo $this->title; ?>";
							  reddit_newwindow='1'
							  </script>
							  <script type="text/javascript" src="http://www.reddit.com/static/button/button2.js"></script>
							</span>
							
							<span class="dcssb-button size_reddit none">
							  <script type="text/javascript">
							  reddit_url = "<?php echo urlencode($this->homepage); ?>";
							  reddit_title = "<?php echo $this->title; ?>";
							  reddit_newwindow='1'
							  </script>
							  <script type="text/javascript" src="http://www.reddit.com/buttonlite.js?i=2"></script>
							</span>
							
						</div>
					</td>
				  </tr>
				</table>
				</li>
			<?php
		}
		
		function options_pinit(){
		
			$options = get_option('dcssb_options');
			$size_pinit = $options['size_pinit'] ? $options['size_pinit'] : 'vertical' ;
			$incPinit = $options['incPinit'] ? $options['incPinit'] : 'false' ;
			$method_pinit = $options['method_pinit'] ? $options['method_pinit'] : 'featured' ;
			
			?>
			<li rel="pinit">
				  <table width="100%" class="dcwp-table" cellspacing="0" border="0" cellpadding="0">
				  <tr>
				    <td class="button-name">
					  <h4><input type="checkbox" value="true" class="checkbox" id="dcssb_inc_pinit" name="dcssb_options[incPinit]"<?php checked( $incPinit, 'true'); ?> class="margin-right" /> Pin It</h4>
					</td>
					<td class="button-size">
						<select name="dcssb_options[size_pinit]" id="dcssb_size_pinit" class="dcssb-button">
							<option value='horizontal' <?php selected( $size_pinit, 'horizontal'); ?> >Horizontal</option>
							<option value='vertical' <?php selected( $size_pinit, 'vertical'); ?> >Vertical</option>
							<option value='none' <?php selected( $size_pinit, 'none'); ?> >No Count</option>
						</select>
						
						<select name="dcssb_options[method_pinit]" id="dcssb_method_pinit" class="dcssb-button">
							<option value='featured' <?php selected( $method_pinit, 'featured'); ?> >Featured Image</option>
							<option value='preview' <?php selected( $method_pinit, 'preview'); ?> >Preview Image</option>
						</select>
					</td>
					<td class="button-others">&nbsp;</td>
					<td class="button-eg" rel="dcssb_size_pinit">
						<div class="relative">
						
							<span class="dcssb-button size_pinit horizontal">
							  <a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode($this->homepage); ?>&media=<?php echo urlencode($this->imageurl); ?>&description=<?php echo urlencode($this->description); ?>" class="pin-it-button" count-layout="horizontal">Pin It</a>
							</span>
							
							<span class="dcssb-button size_pinit vertical">
							  <a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode($this->homepage); ?>&media=<?php echo urlencode($this->imageurl); ?>&description=<?php echo urlencode($this->description); ?>" class="pin-it-button" count-layout="vertical">Pin It</a>
							</span>
							
							<span class="dcssb-button size_pinit none">
							  <a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode($this->homepage); ?>&media=<?php echo urlencode($this->imageurl); ?>&description=<?php echo urlencode($this->description); ?>" class="pin-it-button" count-layout="none">Pin It</a>
							</span>
							
						</div>
					</td>
				  </tr>
				</table>
				</li>
			<?php
		}
		
		function options_buffer(){
			
			$options = get_option('dcssb_options');
			$size_buffer = $options['size_buffer'] ? $options['size_buffer'] : 'vertical' ;
			$incBuffer = $options['incBuffer'] ? $options['incBuffer'] : 'false' ;
			
			?>
			<li rel="buffer">
				  <table width="100%" class="dcwp-table" cellspacing="0" border="0" cellpadding="0">
				  <tr>
				    <td class="button-name">
					  <h4><input type="checkbox" value="true" class="checkbox" id="dcssb_inc_buffer" name="dcssb_options[incBuffer]"<?php checked( $incBuffer, 'true'); ?> class="margin-right" /> Buffer</h4>
					  </td>
					<td class="button-size">
						<select name="dcssb_options[size_buffer]" id="dcssb_size_buffer" class="dcssb-button">
							<option value='horizontal' <?php selected( $size_buffer, 'horizontal'); ?> >Horizontal + Count</option>
							<option value='vertical' <?php selected( $size_buffer, 'vertical'); ?> >Vertical + Count</option>
							<option value='none' <?php selected( $size_buffer, 'none'); ?> >Standard</option>
						</select>
					</td>
					<td class="button-others">
					  
					</td>
					<td class="button-eg" rel="dcssb_size_buffer">
						<div class="relative">
						
							<span class="dcssb-button size_buffer vertical">
							  <a href="http://bufferapp.com/add" data-url="<?php echo $this->homepage; ?>" data-text="<?php echo $this->title; ?>" class="buffer-add-button" data-count="vertical" data-via="<?php echo $this->twitter; ?>">Buffer</a>
							</span>
							
							<span class="dcssb-button size_buffer horizontal">
							  <a href="http://bufferapp.com/add" data-url="<?php echo $this->homeshort; ?>" data-text="<?php echo $this->title; ?>" class="buffer-add-button" data-count="horizontal" data-via="<?php echo $this->twitter; ?>">Buffer</a>
							</span>
							
							<span class="dcssb-button size_buffer none">
							  <a href="http://bufferapp.com/add" data-url="<?php echo $this->homeshort; ?>" data-text="<?php echo $this->title; ?>" class="buffer-add-button" data-count="none" data-via="<?php echo $this->twitter; ?>">Buffer</a>
							</span>
							
						</div>
					</td>
				  </tr>
				  </table>
				  </li>
			<?php
		}

	}
}