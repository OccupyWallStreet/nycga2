<?php
/*
Plugin Name: Flickr Gallery
Plugin URI: http://co.deme.me/projects/flickr-gallery/
Description: Use easy shortcodes to insert Flickr galleries and photos (and videos) in your blog.
Author: Dan Coulter
Version: 1.5.2
Author URI: http://dancoulter.com/
*/ 

/**
 * LICENSE
 * This file is part of Flickr Gallery.
 *
 * Flickr Gallery is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package    flickr-gallery
 * @author     Dan Coulter <dan@dancoulter.com>
 * @copyright  Copyright 2009 Dan Coulter
 * @license    http://www.gnu.org/licenses/gpl.txt GPL 2.0
 * @version    1.5.2
 * @link       http://co.deme.me/projects/flickr-gallery/
 */

if ( !defined('WP_CONTENT_URL') )
	define( 'WP_CONTENT_URL', get_option('wpurl') . '/wp-content');
if ( !defined('WP_CONTENT_DIR') )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	
if ( defined('CODEMEME_TEST') ) {
	define('CM_FLICKR_GALLERY_VERSION', rand());
} else {
	define('CM_FLICKR_GALLERY_VERSION', '1.5.2');
}
	
class DC_FlickrGallery {
	/**
	 * Return the filesystem path that the plugin lives in.
	 *
	 * @return string
	 */
	function getPath() {
		return dirname(__FILE__) . '/';
	}
	
	/**
	 * Returns the URL of the plugin's folder.
	 *
	 * @return string
	 */
	function getURL() {
		return WP_CONTENT_URL.'/plugins/'.basename(dirname(__FILE__)) . '/';
	}
	
	function get_major_version() {
		global $wp_version;
		return (float) $wp_version;
	}
	
	/**
	 * Initializes the phpFlickr object.  Called on WP's init hook.
	 *
	 */
    function init() {
		load_plugin_textdomain('flickr-gallery', 'wp-content/plugins/' . basename(dirname(__FILE__)) . '/i18n');
		if ( get_option('fg-API-key') ) {
			global $phpFlickr;
			include_once dirname(__FILE__) . '/phpFlickr.php';
			$phpFlickr = new phpFlickr(get_option('fg-API-key'), get_option('fg-secret') ? get_option('fg-secret') : null);
			if ( get_option('fg-token') ) {
				$phpFlickr->setToken(get_option('fg-token'));
			}
			if ( function_exists('curl_init') ) {
				$phpFlickr->custom_post = array('DC_FlickrGallery', 'curl_post');
			} elseif ( class_exists('WP_Http') ) {
				$phpFlickr->custom_post = array('DC_FlickrGallery', 'wp_http_post');
			} 
			
			if ( get_option('fg-db-cache') == 1 ) {
				$phpFlickr->enableCache('custom', array(array('DC_FlickrGallery', 'cache_get'), array('DC_FlickrGallery', 'cache_set')));
			}
			wp_enqueue_script('jquery-ui-tabs');			
			wp_enqueue_script('jquery-flightbox', DC_FlickrGallery::getURL() . 'flightbox/jquery.flightbox.js', array(), CM_FLICKR_GALLERY_VERSION);			
			wp_enqueue_style('flickr-gallery', DC_FlickrGallery::getURL() . 'flickr-gallery.css', array(), CM_FLICKR_GALLERY_VERSION, 'all');			
			if ( DC_FlickrGallery::get_major_version() >= 2.8 ) {
				wp_enqueue_style('fg-jquery-ui', DC_FlickrGallery::getURL() . 'tab-theme/jquery-ui-1.7.3.css', array(), '1.7.3', 'all');			
			} else {
				wp_enqueue_style('fg-jquery-ui', DC_FlickrGallery::getURL() . 'tab-theme/jquery-ui-1.5.2.css', array(), '1.5.2', 'all');
			}
			wp_enqueue_style('jquery-flightbox', DC_FlickrGallery::getURL() . 'flightbox/jquery.flightbox.css', array(), CM_FLICKR_GALLERY_VERSION, 'all');			
		}
		
		if ( $_GET['action'] == 'flickr-gallery-photoset' ) {
			DC_FlickrGallery::ajax_photoset($_GET['id'], $_GET['page']);
		} elseif ( $_POST['action'] == 'flickr-gallery-page' ) {
			DC_FlickrGallery::ajax_pagination();
		} elseif ( $_POST['action'] == 'flickr-gallery-sizes' ) {
			DC_FlickrGallery::ajax_sizes();
		}
		
		if ( is_admin() && $_GET['page'] == 'flickr-gallery/flickr-gallery.php' ) {
			wp_enqueue_script('jquery-form');
		}
		
		if ( !is_admin() && (get_option('fg-flightbox') === false || get_option('fg-flightbox')) ) {
			add_action('wp_head', array('DC_FlickrGallery', 'header'));
			add_action('wp_footer', array('DC_FlickrGallery', 'footer'));
		}
    }
	
	function wp_http_post($url, $data) {
		$http = new WP_Http();
		$response = $http->post($url, array('body' => $data));
		return $response['body'];
	}
	
	function curl_post($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
	    @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_close($ch);
		return $result;
	}
	
	function cache_get($key) {
		global $wpdb;
		$result = $wpdb->get_row('
			SELECT 
				* 
			FROM 
				`' . $wpdb->prefix . 'phpflickr_cache` 
			WHERE 
				request = "' . $wpdb->escape($key) . '" AND
				expiration >= NOW()
		');
		if ( is_null($result) ) return false;
		return $result->response;
	}
	
	function cache_set($key, $value, $expire) {
		global $wpdb;
		$query = '
			INSERT INTO `' . $wpdb->prefix . 'phpflickr_cache`
				(
					request, 
					response, 
					expiration
				)
			VALUES
				(
					"' . $wpdb->escape($key) . '", 
					"' . $wpdb->escape($value) . '", 
					FROM_UNIXTIME(' . (time() + (int) $expire) . ')
				)
			ON DUPLICATE KEY UPDATE 
				response = VALUES(response),
				expiration = VALUES(expiration)
			
		';
		$wpdb->query($query);
	}

    /**
     * Handles the [flickr-gallery] shortcode
     *
     * @param array $attr
     * @param string $content
     * @return string
     */
    function gallery($attr, $content = '') {
		if ( get_option('fg-API-key') ) {
			$mv = DC_FlickrGallery::get_major_version() >= 2.8;
			$id = substr(md5(microtime()), 0, 8);
			global $phpFlickr;
			$attr = array_merge(array(
				'pagination' => 1,
				'photoset' => null,
				'sort' => null,
				'tags' => null,
				'mode' => null,
				'tag_mode' => 'any',
				'user_id' => (isset($attr['mode']) && $attr['mode'] == 'search') ? null : get_option('fg-user_id'),
				'per_page' => get_option('fg-per_page'),
			), (is_array($attr) ? $attr : array()));

			$per_page = $attr['per_page'];
			
			if ( strpos($attr['extras'], 'media') === false ) $attr['extras'] .= ',media';
			ob_start();
			switch ($attr['mode']) {
				case 'photoset' :
					$url = DC_FlickrGallery::get_photo_url($photos['owner']);
					$pager = new phpFlickr_pager($phpFlickr, 'flickr.photosets.getPhotos', array(
						'photoset_id' => $attr['photoset'],
						'extras' => $attr['extras'],
					), $attr['per_page']);

					break;
				case 'tag' :
					$url = DC_FlickrGallery::get_photo_url($attr['user_id']);

					$pager = new phpFlickr_pager($phpFlickr, "flickr.photos.search", array(
						'user_id' => $attr['user_id'],
						'tags' => $attr['tags'],
						'sort' => $attr['sort'],
						'tag_mode' => $attr['tag_mode'],
						'extras' => $attr['extras'],
					), $attr['per_page']);

					break;
				case 'recent' :
					$args = $attr;
					unset($args['mode']);
					$url = DC_FlickrGallery::get_photo_url($attr['user_id']);
					$pager = new phpFlickr_pager($phpFlickr, "flickr.photos.search", $args, $attr['per_page']);
					break;
				case 'interesting' :
					$args = $attr;
					unset($args['mode']);
					$args['sort'] = "interestingness-desc";
					$pager = new phpFlickr_pager($phpFlickr, "flickr.photos.search", $args, $attr['per_page']);
					break;
				case 'search' :
					$args = $attr;
					unset($args['mode']);
					$pager = new phpFlickr_pager($phpFlickr, "flickr.photos.search", $args, $attr['per_page']);
					break;
				default :
					if ( !empty($attr['user_id']) ) {
						$url = DC_FlickrGallery::get_photo_url($attr['user_id']);
						$tabs = apply_filters('flickr_gallery_tabs', array(
							array('id'=>'photostream', 'name'=> __('Photostream', 'flickr-gallery')),
							array('id'=>'sets', 'name' => __('Photosets', 'flickr-gallery')),
							array('id'=>'collections', 'name' => __('Collections', 'flickr-gallery')),
							array('id'=>'interesting', 'name' => __('Interesting', 'flickr-gallery')),
						));
						$tab_count = 0;
						?>
							<div id="gallery-<?php echo $id ?>" class="flickr-gallery <?php if ( $mv ) echo 'ui-tabs ui-widget ui-widget-content ui-corner-all' ?>">
								<<?php echo $mv ? 'ul' : 'ol' ?> class="<?php if ( $mv ) echo 'ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all' ?>">
									<?php foreach ( $tabs as $tab ) : ?>
										<li class="ui-state-default ui-corner-top <?php if ( $tab_count++ == 0 ) echo 'ui-tabs-selected ui-state-active' ?>"><a href="#<?php echo $tab['id'] ?>"><?php echo $tab['name'] ?></a></li>
									<?php endforeach ?>
								</<?php echo $mv ? 'ul' : 'ol' ?>>
								<?php $tab_count = 0; ?>
								<?php foreach ( $tabs as $tab ) : ?>
									<?php if ( $tab['id'] == 'photostream' && !isset($tab['callback']) ) : ?>
										<div id="photostream" class="<?php if ( $mv ) echo 'ui-tabs-panel ui-widget-content ui-corner-bottom' ?> <?php if ( $tab_count++ != 0 ) echo 'ui-tabs-hide' ?>">
											<?php 
												$pager = new phpFlickr_pager($phpFlickr, "flickr.photos.search", array(
													'user_id' => $attr['user_id'], 
													'extras' => 'media',
												), $attr['per_page']);
											?>
											<div class="flickr-photos">
												<?php foreach ( $pager->get(1) as $key => $photo ) : ?>
													<div class="flickr-thumb">
														<a href="<?php echo $url . $photo['id'] ?>"><img class="<?php echo $photo['media'] ?>" title="<?php echo str_replace("\"", "\\\"", $photo['title']) ?>" src="<?php echo $phpFlickr->buildPhotoURL($photo, 'square') ?>" alt="<?php echo str_replace("\"", "\\\"", $photo['title']) ?>" /></a>
													</div>
												<?php endforeach; ?>
											</div>
											<?php if ( $attr['pagination'] && $pager->pages > 1 ) : ?>
												<div class="fg-clear">
													<div id="photostream-next" class="flickr-gallery-next" style="float: right"><a href="#"><?php _e('Next Page &rsaquo;', 'flickr-gallery') ?></a></div>
													<div id="photostream-prev" class="flickr-gallery-prev" style="display: none; float: left"><a href="#"><?php _e('&lsaquo; Previous Page', 'flickr-gallery') ?></a></div>
												</div>
											<?php endif; ?>
											<div class="fg-clear"></div>
											
										</div>
										<script type="text/javascript">
											//<!--
											var flickr_gallery_photostream_page = 1;
											(function($){
												$(document).ready(function(){
													$("#photostream-next a, #photostream-prev a").click(function(e){
														if ( $(e.target).parent().is("#photostream-next") ) {
															flickr_gallery_photostream_page++;
														} else {
															flickr_gallery_photostream_page--;
														}
														$("#photostream .flickr-thumb").css("visibility", "hidden");
														//$("#photostream").css("background", "transparent url(<?php echo DC_FlickrGallery::getURL() ?>flightbox/images/loading-2.gif) scroll no-repeat center center");
														$.post("<?php echo get_bloginfo('wpurl') ?>", {
															action: 'flickr-gallery-page',
															pager: "<?php echo str_replace('"', '\\"', serialize($pager)) ?>",
															page: flickr_gallery_photostream_page
														}, function(rsp){
															//console.log(rsp);
															//$("#photostream").css("background-image", "none");
															$("#photostream .flickr-photos").html(rsp.html);
															<?php if ( get_option('fg-flightbox') === false || get_option('fg-flightbox') ) : ?>
																$("#photostream .flickr-thumb img").flightbox({size_callback: get_sizes});
															<?php endif; ?>
															if ( rsp.page == 1 ) {
																$("#photostream-prev").hide();
															} else {
																$("#photostream-prev").show();
															}
															if ( rsp.page == rsp.pages ) {
																$("#photostream-next").hide();
															} else {
																$("#photostream-next").show();
															}
														}, 'json');
														return false;
													});
												});
											})(jQuery);
											//-->
										</script>
									<?php elseif ( $tab['id'] == 'sets' && !isset($tab['callback']) ) : ?>
										<div id="sets" class="<?php if ( $mv ) echo 'ui-tabs-panel ui-widget-content ui-corner-bottom' ?> <?php if ( $tab_count++ != 0 ) echo 'ui-tabs-hide' ?>">
											<?php
												$set_url = $url == 'http://flickr.com/photo.gne?id=' ? 'http://flickr.com/photos/' . $attr['user_id'] . '/sets/' : $url . 'sets/';
												$sets = $phpFlickr->photosets_getList($attr['user_id']);
												foreach( $sets['photoset'] as $set ) :
													?>
														<div class="flickr-set" id="set-<?php echo $set['id'] ?>">
															<div class="flickr-set-thumb">
																<a href="<?php echo $set_url . $set['id']; ?>/">
																	<img title="<?php echo str_replace("\"", "\\\"", $set['title']) ?>" alt="<?php echo str_replace("\"", "\\\"", $set['title']) ?>" class="flickr-thumb" src="http://farm<?php echo $set['farm']; ?>.static.flickr.com/<?php echo $set['server']; ?>/<?php echo $set['primary']; ?>_<?php echo $set['secret']; ?>_s.jpg" /></a>
															</div>
															<div class="flickr-set-meta">
																<div class="flickr-set-title"><a href="<?php echo $url ?>sets/<?php echo $set['id']; ?>/"><?php echo $set['title']; ?></a> (<?php echo $set['photos']; ?> photos)</div>
																<div class="flickr-set-description"><?php echo $set['description']; ?></div>
															</div>
															<div class="flickr-set-display">
																<?php _e('Loading...', 'flickr-gallery') ?>
															</div>
															<div class="fg-clear"></div>
														</div>
													<?php
												endforeach;
											?>
										</div>
										
										<script type="text/javascript">
											//<!--
											(function($){
												$(document).ready(function(){
													function flickr_gallery_load_photoset($display, id, page) {
														$display.find(".flickr-thumb").css("visibility", "hidden");
														$display.load("<?php echo trailingslashit(get_bloginfo('wpurl')) ?>?action=flickr-gallery-photoset&pagination=<?php echo $attr['pagination'] ?>&page=" + page + "&id=" + id, null, function(){
															<?php if ( get_option('fg-flightbox') === false || get_option('fg-flightbox') ) : ?>
																$display.find(".flickr-thumb img").flightbox({size_callback: get_sizes});
															<?php endif; ?>
															$display.find(".flickr-gallery-next a, .flickr-gallery-prev a").click(function(e){
																if ( $(e.target).parent().is(".flickr-gallery-next") ) {
																	flickr_gallery_load_photoset($display, id, page + 1);
																} else {
																	flickr_gallery_load_photoset($display, id, page - 1);
																}
																return false;
															});
														});
													}
													$(".flickr-set .flickr-set-thumb a, .flickr-set .flickr-set-title a").click(function(){
														var $set = $(this).parents(".flickr-set");
														var id = $set.attr("id").split("-")[1];
														var $display = $set.children(".flickr-set-display");
														//if ( $display.css("display") == "none" ) {
														if ( $display.is(":hidden") ) {
															$display.show().html("Loading...");
															flickr_gallery_load_photoset($display, id, 1);
														} else {
															$display.hide();
														}
														
														return false;
													});
												});
											})(jQuery);
											//-->
										</script>
									<?php elseif ( $tab['id'] == 'collections' && !isset($tab['callback']) ) : ?>
										<div id="collections" class="<?php if ( $mv ) echo 'ui-tabs-panel ui-widget-content ui-corner-bottom' ?> <?php if ( $tab_count++ != 0 ) echo 'ui-tabs-hide' ?>">
											<?php
												$collection_url = $url == 'http://flickr.com/photo.gne?id=' ? 'http://flickr.com/photos/' . $attr['user_id'] . '/collections/' : $url . 'collections/';
												$collections = $phpFlickr->call('flickr.collections.getTree', array('user_id' => $attr['user_id']));
												
												foreach( $collections['collections']['collection'] as $collection ) :
													?>
														<div class="flickr-collection" id="collection-<?php echo $collection['id'] ?>">
															<div class="flickr-collection-thumb">
																<a href="<?php echo $collection_url . next(explode('-', $collection['id'])); ?>/" >
																	<img title="<?php echo str_replace("\"", "\\\"", $collection['title']) ?>" alt="<?php echo str_replace("\"", "\\\"", $collection['title']) ?>" class="flickr-collection-thumb" src="<?php echo preg_replace('|^/|', 'http://www.flickr.com/', $collection['iconlarge']) ?>" />
																</a>
															</div>
															<div class="flickr-collection-meta">
																<div class="flickr-collection-title"><a href="<?php echo $collection_url . next(explode('-', $collection['id'])); ?>/"><?php echo $collection['title']; ?></a></div>
																<div class="flickr-collection-description"><?php echo $collection['description']; ?></div>
															</div>
															<div class="fg-clear"></div>
														</div>
													<?php
												endforeach;
												
											?>
										</div>
										
										<script type="text/javascript">
											//<!--
											(function($){
												$(function(){
													function flickr_gallery_load_photoset($display, id, page) {
														$display.find(".flickr-thumb").css("visibility", "hidden");
														$display.load("<?php echo trailingslashit(get_bloginfo('wpurl')) ?>?action=flickr-gallery-photoset&pagination=<?php echo $attr['pagination'] ?>&page=" + page + "&id=" + id, null, function(){
															<?php if ( get_option('fg-flightbox') === false || get_option('fg-flightbox') ) : ?>
																$display.find(".flickr-thumb img").flightbox({size_callback: get_sizes});
															<?php endif; ?>
															$display.find(".flickr-gallery-next a, .flickr-gallery-prev a").click(function(e){
																if ( $(e.target).parent().is(".flickr-gallery-next") ) {
																	flickr_gallery_load_photoset($display, id, page + 1);
																} else {
																	flickr_gallery_load_photoset($display, id, page - 1);
																}
																return false;
															});
														});
													}
													$(".flickr-set .flickr-set-thumb a, .flickr-set .flickr-set-title a").click(function(){
														var $set = $(this).parents(".flickr-set");
														var id = $set.attr("id").split("-")[1];
														var $display = $set.children(".flickr-set-display");
														if ( !$display.is(":hidden") ) {
															$display.show().html("Loading...");
															flickr_gallery_load_photoset($display, id, 1);
														} else {
															$display.hide();
														}
														
														return false;
													});
												});
											})(jQuery);
											//-->
										</script>
									<?php elseif ( $tab['id'] == 'interesting' && !isset($tab['callback']) ) : ?>
										<div id="interesting" class="<?php if ( $mv ) echo 'ui-tabs-panel ui-widget-content ui-corner-bottom' ?> <?php if ( $tab_count++ != 0 ) echo 'ui-tabs-hide' ?>">
											<?php
												$pager = new phpFlickr_pager($phpFlickr, "flickr.photos.search", array(
													'user_id' => $attr['user_id'],
													'sort' => 'interestingness-desc',
													'extras' => 'media',
												), $attr['per_page']);
											?>
											<div class="flickr-photos">
												<?php foreach ( $pager->get(1) as $key => $photo ) : if ( $key >= $per_page ) break;?>
													<div class="flickr-thumb">
														<a href="<?php echo $url . $photo['id'] ?>"><img class="<?php echo $photo['media'] ?>" title="<?php echo str_replace("\"", "\\\"", $photo['title']) ?>" src="<?php echo $phpFlickr->buildPhotoURL($photo, 'square') ?>" alt="<?php echo str_replace("\"", "\\\"", $photo['title']) ?>" /></a>
													</div>
												<?php endforeach; ?>
											</div>
											<?php if ( $attr['pagination'] && $pager->pages > 1 ) : ?>
												<div class="fg-clear">
													<div id="interesting-next" class="flickr-gallery-next" style="float: right"><a href="#"><?php _e('Next Page &rsaquo;', 'flickr-gallery') ?></a></div>
													<div id="interesting-prev" class="flickr-gallery-prev" style="display: none; float: left"><a href="#"><?php _e('&lsaquo; Previous Page', 'flickr-gallery') ?></a></div>
												</div>
											<?php endif; ?>
											<div class="fg-clear"></div>
										</div>
										<script type="text/javascript">
											//<!--
											var flickr_gallery_interesting_page = 1;
											(function($){
												$(document).ready(function(){
													$("#interesting-next a, #interesting-prev a").click(function(e){
														if ( $(e.target).parent().is("#interesting-next") ) {
															flickr_gallery_interesting_page++;
														} else {
															flickr_gallery_interesting_page--;
														}
														$("#interesting .flickr-thumb").css("visibility", "hidden");
														//$("#interesting").css("background", "transparent url(<?php echo DC_FlickrGallery::getURL() ?>flightbox/images/loading-2.gif) scroll no-repeat center center");
														$.post("<?php echo $_SERVER['REQUEST_URI'] ?>", {
															action: 'flickr-gallery-page',
															pager: "<?php echo str_replace('"', '\\"', serialize($pager)) ?>",
															page: flickr_gallery_interesting_page
														}, function(rsp){
															//$("#interesting").css("background-image", "none");
															$("#interesting .flickr-photos").html(rsp.html);
															<?php if ( get_option('fg-flightbox') === false || get_option('fg-flightbox') ) : ?>
																$("#interesting .flickr-thumb img").flightbox({size_callback: get_sizes});
															<?php endif; ?>
															if ( rsp.page == 1 ) {
																$("#interesting-prev").hide();
															} else {
																$("#interesting-prev").show();
															}
															if ( rsp.page == rsp.pages ) {
																$("#interesting-next").hide();
															} else {
																$("#interesting-next").show();
															}
														}, 'json');
														return false;
													});
												});
											})(jQuery);
											//-->
										</script>

									<?php elseif ( isset($tab['callback']) ) : ?>
										<div id="<?php echo $tab['id'] ?>" class="<?php if ( $mv ) echo 'ui-tabs-panel ui-widget-content ui-corner-bottom' ?> <?php if ( $tab_count++ != 0 ) echo 'ui-tabs-hide' ?>">
											<?php call_user_func($tab['callback']); ?>
											<div class="fg-clear"></div>
										</div>
									<?php endif ?>
								<?php endforeach ?>
								<?php if ( get_option('fg-credit-link') ) : ?>
									<div class="fg-credit fg-clear alignright"><?php _e('Powered by <a href="http://co.deme.me/projects/flickr-gallery/">Flickr Gallery</a>', 'flickr-gallery') ?></div>
								<?php endif; ?>
							</div>
							<script type="text/javascript">
								jQuery(document).ready(function(){
									<?php if ( DC_FlickrGallery::get_major_version() < 2.8 ) : ?>
										jQuery("#gallery-<?php echo $id ?> > ol").tabs();
									<?php else : ?>
										jQuery("#gallery-<?php echo $id ?>").tabs();
									<?php endif; ?>
									jQuery('#sets .flickr-set:not(:first)').css({borderTop:"1px solid #D3D3D3", paddingTop: ".5em"});
									<?php if ( get_option('fg-flightbox') === false || get_option('fg-flightbox') ) : ?>
										jQuery("#gallery-<?php echo $id ?> .flickr-thumb img").flightbox({size_callback: get_sizes});
									<?php endif; ?>
								});
							</script>
						<?php
					}
					$result = ob_get_clean();
					return $result;
					break;
				
			}

			$url = DC_FlickrGallery::get_photo_url();
			?>
				<div id="gallery-<?php echo $id ?>" class="flickr-gallery <?php echo $attr['mode'] ?>">
					<?php 
						if ( isset($attr['fg_filter']) ) {
							$pager->_extra = 'flickr_gallery_filter_' . $attr['fg_filter'];
							$pager->page = 1;
							do_action($pager->_extra, $pager, $attr, $url);
						} else {
							foreach ( $pager->get(1) as $key => $photo ) : ?>
								<div class="flickr-thumb">
									<a href="<?php echo $url . $photo['id'] ?>"><img class="<?php echo $photo['media'] ?>" title="<?php echo str_replace("\"", "\\\"", $photo['title']) ?>" src="<?php echo $phpFlickr->buildPhotoURL($photo, 'square') ?>" alt="<?php echo str_replace("\"", "\\\"", $photo['title']) ?>" /></a>
								</div>
							<?php endforeach;
						}
					?>
					<div class="fg-clear"></div>
				</div>
				<?php if ( $attr['pagination'] && $pager->pages > 1 ) : ?>
					<div class="fg-clear" id="fg-<?php echo $id ?>-nav">
						<div id="fg-<?php echo $id ?>-next" class="flickr-gallery-next" style="float: right"><a href="#"><?php _e('Next Page &rsaquo;', 'flickr-gallery') ?></a></div>
						<div id="fg-<?php echo $id ?>-prev" class="flickr-gallery-prev" style="display: none; float: left"><a href="#"><?php _e('&lsaquo; Previous Page', 'flickr-gallery') ?></a></div>
					</div>
				<?php endif; ?>
				<?php if ( $pager->total > 0 && get_option('fg-credit-link') ) : ?>
					<div class="fg-clear alignright"><?php _e('Powered by <a href="http://co.deme.me/projects/flickr-gallery/">Flickr Gallery</a>', 'flickr-gallery') ?></div>
				<?php endif; ?>
				<div class="fg-clear"></div>
			<?php
			?>
				<script type="text/javascript">
					<?php if ( get_option('fg-flightbox') === false || get_option('fg-flightbox') ) : ?>
						jQuery(document).ready(function(){
							jQuery("#gallery-<?php echo $id ?> .flickr-thumb img").flightbox({size_callback: get_sizes});
						});
					<?php endif; ?>
					
					<?php if ( $attr['pagination'] && $pager->pages > 1 ) : ?>
						var flickr_gallery_<?php echo $id ?>_page = 1;
						(function($){
							$(document).ready(function(){
								$("#fg-<?php echo $id ?>-next a, #fg-<?php echo $id ?>-prev a").click(function(e){
									if ( $(e.target).parent().is("#fg-<?php echo $id ?>-next") ) {
										flickr_gallery_<?php echo $id ?>_page++;
									} else {
										flickr_gallery_<?php echo $id ?>_page--;
									}
									$("#gallery-<?php echo $id ?> .flickr-thumb").css("visibility", "hidden");
									//$("#gallery-<?php echo $id ?>").css("background", "transparent url(<?php echo DC_FlickrGallery::getURL() ?>flightbox/images/loading-2.gif) scroll no-repeat center center");
									$.post("<?php echo $_SERVER['REQUEST_URI'] ?>", {
										action: 'flickr-gallery-page',
										pager: "<?php echo str_replace('"', '\\"', serialize($pager)) ?>",
										page: flickr_gallery_<?php echo $id ?>_page
									}, function(rsp){
										$("#gallery-<?php echo $id ?>").html(rsp.html);
										<?php if ( get_option('fg-flightbox') === false || get_option('fg-flightbox') ) : ?>
											$("#gallery-<?php echo $id ?> .flickr-thumb img").flightbox({size_callback: get_sizes});
										<?php endif; ?>
										if ( rsp.page == 1 ) {
											$("#fg-<?php echo $id ?>-prev").hide();
										} else {
											$("#fg-<?php echo $id ?>-prev").show();
										}
										if ( rsp.page == rsp.pages ) {
											$("#fg-<?php echo $id ?>-next").hide();
										} else {
											$("#fg-<?php echo $id ?>-next").show();
										}
									}, 'json');
									return false;
								});
							});
						})(jQuery);
					<?php endif ?>
					//-->
				</script>
			<?php
			$result = ob_get_clean();
			return $result;
		}
    }
	
    /**
     * Handles the [flickr][/flickr] shortcode
     *
     * @param array $attr
     * @param string $content
     * @return string
     */
    function image($attr, $content) {
		if ( get_option('fg-API-key') ) {
			global $phpFlickr;
			$attr = shortcode_atts(array(
				'size' => 'medium',
				'float' => 'none',
				'height' => null,
				'width' => null,
			), $attr);

			if ( preg_match( '|/([0-9]+)/?$|', $content, $match) ) {
				$id = $match[1];
			} else {
				$id = $content;
			}

			$photo = $phpFlickr->photos_getInfo($id);
			
			if ( isset($photo['id']) ) {
				$url = $phpFlickr->urls_getUserPhotos($photo['owner']['nsid']);
				ob_start();
				if ( 'video' == $photo['media'] ) {
					$sizes = $phpFlickr->photos_getSizes($photo['id']);
					foreach ( $sizes as $size ) {
						if ( $size['label'] == 'Video Player' ) {
							break;
						}
					}
					
					if ( is_null($attr['height']) ) $attr['height'] = $size['height'];
					if ( is_null($attr['width']) ) $attr['width'] = $size['width'];
					?>
						<div class="flickr-gallery video <?php echo $attr['size'] . ' ' . $attr['float'] ?>">
							<object type="application/x-shockwave-flash" wmode="opaque" width="<?php echo $attr['width'] ?>" height="<?php echo $attr['height'] ?>" data="<?php echo urlencode($size['source']) ?>" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">
								<param name="movie" value="<?php echo urlencode($size['source']) ?>"></param>
								<param name="bgcolor" value="#000000"></param>
								<param name="allowFullScreen" value="true"></param>
								<param name="wmode" value="opaque" />
								<embed type="application/x-shockwave-flash" src="<?php echo $size['source'] ?>" bgcolor="#000000" allowfullscreen="true" flashvars="intl_lang=en-us&amp;photo_secret=<?php echo $photo['secret'] ?>&amp;photo_id=<?php echo $photo['id'] ?>" height="<?php echo $attr['height'] ?>" width="<?php echo $attr['width'] ?>" wmode="opaque"></embed>
							</object>
						</div>
					<?php
				} else {
					?>
						<div class="flickr-gallery image <?php echo $attr['float'] ?>"><a href="<?php echo $url . $photo['id'] ?>"><img class="flickr <?php echo $attr['size'] ?>" title="<?php echo str_replace("\"", "\\\"", $photo['title']) ?>" alt="<?php echo str_replace("\"", "\\\"", $photo['title']) ?>" src="<?php echo $phpFlickr->buildPhotoURL($photo, $attr['size']) ?>" /></a></div>
					<?php
				}
				return ob_get_clean();
			} else {
				return '';
			}
		}
		
    }
	
    /**
     * Sets up the settings page
     *
     */
	function add_settings_page() {
		load_plugin_textdomain('flickr-gallery', DC_FlickrGallery::getPath() . 'i18n/');
		add_options_page(__('Flickr Gallery', 'flickr-gallery'), __('Flickr Gallery', 'flickr-gallery'), 8, __FILE__, array('DC_FlickrGallery', 'settings_page'));	
	}
	 
	function save_settings() {
		global $wpdb;
		check_admin_referer('flickr-gallery');
		$options = explode(',', $_POST['page_options']);
		$out = array();

		include_once dirname(__FILE__) . '/phpFlickr.php';
		$phpFlickr = new phpFlickr($_POST['fg-API-key'], (empty($_POST['fg-secret']) ? null : $_POST['fg-secret']));
		switch ( $_POST['fg-user_id-type'] ) {
			case 'name':
				$user = $phpFlickr->people_findByUsername($_POST['fg-user_id']);
				$_POST['fg-user_id'] = $user['id'];
				break;
			case 'email':
				$user = $phpFlickr->people_findByEmail($_POST['fg-user_id']);
				$_POST['fg-user_id'] = $user['id'];
				break;
			case 'url':
				$user = $phpFlickr->urls_lookupUser($_POST['fg-user_id']);
				$_POST['fg-user_id'] = $user['id'];
				break;
		}
			
		if ( $_POST['fg-db-cache'] == 1 ) {
			if ( isset($wpdb->charset) && !empty($wpdb->charset) ) {
				$charset = ' DEFAULT CHARSET=' . $wpdb->charset;
			} elseif ( defined(DB_CHARSET) && DB_CHARSET != '' ) {
				$charset = ' DEFAULT CHARSET=' . DB_CHARSET;
			} else {
				$charset = '';
			}

			$query = '
				CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'phpflickr_cache` (
					`request` CHAR( 35 ) NOT NULL ,
					`response` MEDIUMTEXT NOT NULL ,
					`expiration` DATETIME NOT NULL ,
					PRIMARY KEY ( `request` )
				)  ' . $charset . '
			';
			$wpdb->query($query);
		}
			/*
		if ( empty($_POST['fg-token']) && !empty($_POST['fg-frob']) ) {
			$token = $phpFlickr->auth_getToken($_POST['fg-frob']);
			$_POST['fg-token'] = $token['token'];
		}
		*/
		
		foreach ( $options as $o ) {
			if ( get_option($o) === false ) {
				add_option($o, $_POST[$o], null, no);
			} else {
				update_option($o, $_POST[$o]);
			}
			if ( is_array($_POST[$o]) ) {
				$out[] = '"' . $o . '":["' . implode('","', $_POST[$o]) . '"]';
			} else {
				$out[] = '"' . $o . '":"' . addslashes($_POST[$o]) . '"';
			}
		}
		echo '{' . implode(', ', $out) . '}';
		exit;
	}
	
	function get_auth_token() {
		check_admin_referer('flickr-gallery-auth');
		global $phpFlickr;
		$token = $phpFlickr->auth_getToken($_POST['frob']);
		if ( get_option('fg-token') === false ) {
			add_option('fg-token', $token['token'], null, no);
		} else {
			update_option('fg-token', $token['token']);
		}
		echo '{token: "' . $token['token'] . '"}';
		exit;
	}
	 
	/**
	 * Generates the settings page
	 *
	 */
	function settings_page() {
		global $phpFlickr;
		?>
			<div class="wrap">
				<h2><?php _e('Flickr Gallery', 'flickr-gallery') ?></h2>
				<form method="post" action="admin-ajax.php" id="flickr-gallery-settings">
					<input type="hidden" name="action" value="flickr_gallery_settings" />
					<?php wp_nonce_field('flickr-gallery'); ?>
					<input type="hidden" name="page_options" value="fg-tabs,fg-API-key,fg-secret,fg-token,fg-user_id,fg-per_page,fg-db-cache,fg-credit-link,fg-flightbox,fg-flightbox-large,fg-flightbox-description" />
					<p>
						<?php _e('Flickr <a href="http://www.flickr.com/services/api/keys/">API Key</a>:', 'flickr-gallery') ?>
						<input type="text" name="fg-API-key" value="<?php echo get_option('fg-API-key'); ?>" />
					</p>
					<p>
						<label for="fg-db-cache"><?php _e('Enable database result caching:', 'flickr-gallery') ?></label>
						<input id="fg-db-cache" type="checkbox" value="1" name="fg-db-cache" <?php echo get_option('fg-db-cache') ? 'checked="checked"' : '' ?> />
					</p>
					<p>
						<?php _e('Flickr User ID:', 'flickr-gallery') ?>
						<input type="text" name="fg-user_id" id="fg-user_id" value="<?php echo get_option('fg-user_id'); ?>" />
						<select name="fg-user_id-type" id="fg-user_id-type">
							<option value="id"><?php _e('User ID (12345678@N00)', 'flickr-gallery'); ?></option>
							<option value="name"><?php _e('User Name (the name displayed at the top of your photopage)', 'flickr-gallery'); ?></option>
							<option value="email"><?php _e('Email Address', 'flickr-gallery'); ?></option>
							<option value="url"><?php _e('Flickr photopage URL', 'flickr-gallery'); ?></option>
						</select>
						<?php 
							if ( get_option('fg-token') ) {
								$_token = $phpFlickr->auth_checkToken();
								if ( $_token !== false ) {
									echo '<button class="button" id="fg-use-authed-user">' . __('Use your authed user', 'flickr-gallery') . '</button>';
								}
							}
						?>
					</p>
					<p>
						<?php _e('Number of photos to display:', 'flickr-gallery') ?>
						<input type="text" name="fg-per_page" value="<?php echo get_option('fg-per_page') ? get_option('fg-per_page') : 30; ?>" />
					</p>
					<p>
						<label for="fg-flightbox"><?php _e('Use a lightbox to display your photos:', 'flickr-gallery') ?></label>
						<input id="fg-flightbox" type="checkbox" value="1" name="fg-flightbox" <?php echo ( get_option('fg-flightbox') === false || get_option('fg-flightbox') ) ? 'checked="checked"' : '' ?> />
					</p>
					<p>
						<label for="fg-flightbox-large"><?php _e('Display the largest photo that will fit in the user\'s window in the lightbox (slows it down slightly):', 'flickr-gallery') ?></label>
						<input id="fg-flightbox-large" type="checkbox" value="1" name="fg-flightbox-large" <?php echo ( get_option('fg-flightbox-large') ) ? 'checked="checked"' : '' ?> /><br />
						<label for="fg-flightbox-description"><?php _e('Display the photo\'s description in the lightbox:', 'flickr-gallery') ?></label>
						<input id="fg-flightbox-description" type="checkbox" value="1" name="fg-flightbox-description" <?php echo ( get_option('fg-flightbox-description') ) ? 'checked="checked"' : '' ?> /><br />
					</p>
					<p>
						Tabs to display for the default [flickr-gallery] shortcode:
						<?php
							if ( !($tabs = get_option('fg-tabs')) ) {
								$tabs = array('photostream', 'sets', 'interesting');
							}
						?>
						<ul style="padding-left: 20px;">
							<li><label><input type="checkbox" name="fg-tabs[]" value="photostream" <?php if ( in_array('photostream', $tabs) ) echo 'checked="checked"' ?> /> <?php _e('Photostream', 'flickr-gallery') ?></label></li>
							<li><label><input type="checkbox" name="fg-tabs[]" value="sets" <?php if ( in_array('sets', $tabs) ) echo 'checked="checked"' ?> /> <?php _e('Photosets', 'flickr-gallery') ?></label></li>
							<li><label><input type="checkbox" name="fg-tabs[]" value="collections" <?php if ( in_array('collections', $tabs) ) echo 'checked="checked"' ?> /> <?php _e('Collections', 'flickr-gallery') ?></label></li>
							<li><label><input type="checkbox" name="fg-tabs[]" value="interesting" <?php if ( in_array('interesting', $tabs) ) echo 'checked="checked"' ?> /> <?php _e('Interesting', 'flickr-gallery') ?></label></li>
						</ul>
					</p>
					<p><strong><?php _e("If you want the plugin to include private photos or photos in private groups, you will need to fill in the following fields.  Leave them blank otherwise.  Also, you will need to make sure that your API key's authentication type is set to Web Application at Flickr and the \"Callback URL\" should point to your \"wp-admin\" URL.", 'flickr-gallery') ?></strong></p>
					<p>
						<?php _e('Flickr API Secret:', 'flickr-gallery') ?>
						<input type="text" name="fg-secret" value="<?php echo get_option('fg-secret'); ?>" />
					</p>
					<p>
						<?php if ( get_option('fg-secret') && get_option('fg-api-key') ) : ?>
							<?php printf(__('Authentication Token (<a target="" id="auth-link" href="%s">generate</a>):', 'flickr-gallery'), 'admin-ajax.php?action=flickr_gallery_auth') ?>
						<?php else : ?>
							<?php printf(__('Authentication Token (<em>Save your API Key and Secret to generate</em>):', 'flickr-gallery'), 'http://flickr.com') ?>
						<?php endif; ?>
						<input type="text" id="fg-token" name="fg-token" value="<?php echo get_option('fg-token'); ?>" />
					</p>
					<p><?php _e("<strong>A quick note about private photos...</strong> Because of the Flickr TOS, all images in this plugin link back to the photo page.  If an image is private (or marked for friends and family only) some people clicking through might get a message that they don't have permissions to see the photo.  For now, that's just the way it has to be (until I add on a few other cool features).", 'flickr-gallery') ?></p>

					<p>
						<label for="fg-credit-link"><?php _e('Show a small credit link for the plugin under your galleries:', 'flickr-gallery') ?></label>
						<input id="fg-credit-link" type="checkbox" value="1" name="fg-credit-link" <?php echo get_option('fg-credit-link') ? 'checked="checked"' : '' ?> />
					</p>

					<p class="submit">
						<input type="submit" name="Submit" value="<?php _e('Save Changes', 'flickr-gallery') ?>" />
					</p>
				</form>
				<p id="shameless">
					If you like this plugin, please consider <a href="http://co.deme.me/donate/">donating</a> a couple of dollars.
				</p>
				<div id="flickr-test">
					<?php if ( isset($phpFlickr) && get_option('fg-user_id') ) : ?>
						<?php 
							$photos = $phpFlickr->people_getPublicPhotos(get_option('fg-user_id'), null, null, 8);
							if ( $photos === false ) {
								_e('The Flickr API has returned the following error message.', 'flickr-gallery');
								echo "<br />" . $phpFlickr->getErrorCode() . ': ' .$phpFlickr->getErrorMsg();
							} else {
								?>
									<p><?php _e("Some of your Flickr photos should appear below.", 'flickr-gallery'); ?></p>
									<div>
										<?php foreach ( $photos['photos']['photo'] as $photo ) : ?>
											<a style="border: 0px; padding-right: 10px;" href="http://flickr.com/photos/<?php echo get_option('fg-user_id') ?>/<?php echo $photo['id'] ?>">
												<img src="<?php echo $phpFlickr->buildPhotoURL($photo, 'square') ?>" alt="<?php echo addslashes($photo['title']) ?>" title="<?php echo addslashes($photo['title']) ?>: <?php echo addslashes($photo['description']) ?>" />
											</a>
										<?php endforeach; ?>
									</div>
								<?php
							}
						?>
						
					
					<?php else : ?>
						<?php _e('When you enter your API key and Flickr User ID, your photos should appear here.', 'flickr-gallery') ?>
					<?php endif; ?>
				</div>
				<script type="text/javascript">
					;(function($){
						$(document).ready(function(){
							$("#flickr-gallery-settings").ajaxForm({
								dataType: 'json',
								success:function(rsp){
									if ( $("#flickr-gallery-notice").length == 0 ) {
										$("#fg-user_id").val(rsp["fg-user_id"]);
										$("#fg-user_id-type").val("id");
										$(".wrap h2").after("<div id='flickr-gallery-notice' class='updated fade'><p>Updated Flickr Gallery Settings</p></div>");
										setTimeout(function(){
											$("#flickr-gallery-notice").fadeOut("slow", function(){
												$("#flickr-gallery-notice").remove();
											});
										}, 5000);
									}
								}
							});
							/*
							$("#auth-link").click(function(){
								if ( !$(this).hasClass("step-2") ) {
									$(this).addClass("step-2").html("<?php _e("Done authenticating at Flickr", 'flickr-gallery') ?>");
									$("#fg-token").val("");
								} else {
									$.post('admin-ajax.php', {
										frob: $("#fg-frob").val(),
										action: "flickr_gallery_auth",
										"_wpnonce":"<?php echo wp_create_nonce('flickr-gallery-auth') ?>", 
										"_wp_http_referer":"<?php echo $_SERVER['REQUEST_URI'] ?>"
									}, function(rsp){
										console.log(rsp);
										$("#fg-token").val(rsp.token);
									}, 'json');
									return false;
								}
							});
							*/
						});
					})(jQuery);
					
					
					<?php if ( $_token !== false ) : ?>
						jQuery("#fg-use-authed-user").click(function(){
							jQuery("#fg-user_id").val("<?php echo $_token['user']['nsid'] ?>");
							jQuery("#fg-user_id-type").val("id");
							return false;
						});
					<?php endif; ?>
				</script>
			</div>
		<?php
	}
	
	/**
	 * Inserts the settings link on the plugin's page
	 *
	 */
	function settings_link($links) {
		$settings_link = '<a href="options-general.php?page=' . plugin_basename(__FILE__) . '">' . __('Settings', 'flickr-gallery') . '</a>'; 
		array_unshift( $links, $settings_link ); 
		return $links; 
	}
	
	function get_photo_url($nsid = null) {
		if ( is_null($nsid) ) return 'http://flickr.com/photo.gne?id=';
		
		global $phpFlickr;
		$url = $phpFlickr->urls_getUserPhotos($nsid);
		return empty($url) ? 'http://flickr.com/photo.gne?id=' : $url;
	}
	
	function ajax_pagination() {
		global $phpFlickr;
		$pager = unserialize(stripslashes($_POST['pager']));
		$pager->set_phpFlickr($phpFlickr);
		$html = '';
		if ( !is_null($pager->_extra) ) {
			ob_start();
			$pager->page = $_POST['page'];
			do_action($pager->_extra, $pager, 'http://flickr.com/photo.gne?id=');
			$html = str_replace(array("\n", "\r"), '', ob_get_clean());
		} else {
			foreach ( $pager->get($_POST['page']) as $key => $photo ) : 
				$html .= '<div class="flickr-thumb"><a href="http://flickr.com/photo.gne?id=' . $photo['id'] . '"><img class="' . $photo['media'] . '" title="' . str_replace("\"", "\\\"", $photo['title']) . '" src="' . $phpFlickr->buildPhotoURL($photo, 'square') .'" alt="' .  str_replace("\"", "\\\"", $photo['title']) .'" /></a></div>';
			endforeach;
		}
		$html .= '<div class="fg-clear"></div>';
		echo json_encode(array(
			'page' => $_POST['page'],
			'pages' => $pager->pages,
			'html' => $html,
		));
		exit;
	}
	
	function ajax_photoset($id, $page = 1) {
		global $phpFlickr;
		$pager = new phpFlickr_pager($phpFlickr, 'flickr.photosets.getPhotos', array(
			'photoset_id' => $id,
			'extras' => 'media',
		), get_option('fg-per_page'));
				
		?>
			<div class="flickr-photos">
				<?php foreach ( $pager->get($page) as $key => $photo ) : ?>
					<div class="flickr-thumb">
						<a href="http://flickr.com/photo.gne?id=<?php echo $photo['id'] ?>"><img class="<?php echo $photo['media'] ?>" title="<?php echo str_replace("\"", "\\\"", $photo['title']) ?>" alt="<?php echo str_replace("\"", "\\\"", $photo['title']) ?>" src="<?php echo $phpFlickr->buildPhotoURL($photo, 'square') ?>" /></a>
					</div>
				<?php endforeach ?>
				<div class="fg-clear"></div>
			</div>
			
			<?php if ( $_GET['pagination'] && $pager->pages > 1 ) : ?>
				<div class="clear">
					<?php if ( $page != $pager->pages ) : ?>
						<div id="photoset-<?php echo $id ?>-<?php echo $page + 1 ?>" class="flickr-gallery-next" style="float: right"><a href="#"><?php _e('Next Page &rsaquo;', 'flickr-gallery') ?></a></div>
					<?php endif; ?>
					<?php if ( $page != 1 ) : ?>
						<div id="photoset-<?php echo $id ?>-<?php echo $page - 1 ?>" class="flickr-gallery-prev" style="float: left"><a href="#"><?php _e('&lsaquo; Previous Page', 'flickr-gallery') ?></a></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		<?php
		exit;
	}
	
	function ajax_sizes() {
		global $phpFlickr;
		if ( get_option('fg-flightbox-description') ) {
			$result = $phpFlickr->photos_getInfo($_POST['id']);
			$description = nl2br($result['description']);
		} else {
			$description = '';
		}
		$result = array('description' => $description, 'sizes'=>$phpFlickr->photos_getSizes($_POST['id']));
		die(json_encode($result));

	}
	
	function header() {
		?>
			<script type="text/javascript">
				var get_sizes = null;
				<?php if ( get_option('fg-flightbox-large') ) : ?>
					(function($){
						get_sizes = function(id){
							var sizes;
							$.ajax({
								async: false,
								data: {
									action: "flickr-gallery-sizes",
									id: id
								},
								dataType: "json",
								type: "POST",
								url: "<?php echo trailingslashit(get_bloginfo('wpurl')) ?>",
								success: function(rsp) {
									sizes = rsp;
								}
							});
							return sizes;
						};
					})(jQuery);
				<?php endif ?>
			</script>
		<?php 
	}
	
	function footer() {
		?>
			<script type="text/javascript">
				(function($){
					$(function(){
						$("img.flickr.square,img.flickr.thumbnail,img.flickr.small").flightbox({size_callback: get_sizes});
					});
				})(jQuery);
			</script>
		<?php
	}
	
	function load_tabs_setting($tabs) {
		if ( !$selected = get_option('fg-tabs') ) {
			$selected = array('photostream', 'sets', 'interesting');
		}
		foreach ( $tabs as $key => $tab ) {
			if ( !in_array($tab['id'], $selected) ) {
				unset($tabs[$key]);
			}
		}
		return $tabs;
	}
	
	function auth_init() {
		session_start();
		global $phpFlickr;
		unset($_SESSION['phpFlickr_auth_token']);
		$phpFlickr->setToken('');
		$phpFlickr->auth('read', $_SERVER['HTTP_REFERER']);
		exit;
	}
	
	function auth_read() {
		if ( is_user_logged_in() && isset($_GET['frob']) ) {
			global $phpFlickr;
			$auth = $phpFlickr->auth_getToken($_GET['frob']);
			update_option('fg-token', $auth['token']);
			header('Location: ' . $_SESSION['phpFlickr_auth_redirect']);
			exit;
		}
	}

}

add_shortcode('flickr-gallery', array('DC_FlickrGallery', 'gallery'));
add_shortcode('flickr', array('DC_FlickrGallery', 'image'));

add_action('admin_menu', array('DC_FlickrGallery', 'add_settings_page'));
add_action('init', array('DC_FlickrGallery', 'init'));
add_action('wp_ajax_flickr_gallery_settings', array('DC_FlickrGallery', 'save_settings'));
add_action('wp_ajax_flickr_gallery_auth', array('DC_FlickrGallery', 'auth_init'));

add_action('admin_init', array('DC_FlickrGallery', 'auth_read'));

add_filter("plugin_action_links_" . plugin_basename(__FILE__), array('DC_FlickrGallery', 'settings_link') ); 

add_filter("flickr_gallery_tabs", array('DC_FlickrGallery', 'load_tabs_setting'), 9); 

?>