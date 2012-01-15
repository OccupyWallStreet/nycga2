<?php
/**
 * @package WordPress
 * @subpackage Magazeen_Theme
 */
?>
				<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
				
					<li><h5>Widget Ready</h5>
						<ul>
							<li>This theme was built &quot;widget-ready&quot;. It is recommeneded that you add widgets via the Admin panel. If not, please delete this in <code>sidebar.php</code></li>
						</ul>
						<a href="<?php bloginfo( 'template_directory' ); ?>/readme.html" class="sidebar-read-more">Figure Out How &raquo;</a>
					</li>
						
					<li><h5>Pages</h5>
						<ul>
							<?php wp_list_pages('title_li=' ); ?>
						</ul>
					</li>
		
					<li><h5>Archives</h5>
						<ul>
							<?php wp_get_archives('type=monthly'); ?>
						</ul>
					</li>
		
					<?php wp_list_categories('show_count=1&title_li=<h5>Categories</h5>'); ?>
		
					<?php if ( is_home() || is_page() ) { ?>
		
						<li><h5>Meta</h5>
						<ul>
							<?php wp_register(); ?>
							<li><?php wp_loginout(); ?></li>
							<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
							<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
							<li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>
							<?php wp_meta(); ?>
						</ul>
						</li>
					<?php } ?>
										
				<?php endif; ?>