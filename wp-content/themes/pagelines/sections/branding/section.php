<?php
/*
	Section: Branding
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Shows the main site logo or the site title and description.
	Class Name: PageLinesBranding
	Workswith: header 
*/

/**
 * Branding Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesBranding extends PageLinesSection {

	/**
	* Section template.
	*/
   function section_template() { 
			
			printf('<div class="branding_wrap">');
			
				pagelines_main_logo(); 
			
				pagelines_register_hook( 'pagelines_before_branding_icons', 'branding' ); // Hook 
					
				printf('<div class="icons" style="bottom: %spx; right: %spx;">', intval(pagelines_option('icon_pos_bottom')), pagelines_option('icon_pos_right'));
					
					pagelines_register_hook( 'pagelines_branding_icons_start', 'branding' ); // Hook 
			
					if(ploption('rsslink'))
						printf('<a target="_blank" href="%s" class="rsslink"><img src="%s" alt="RSS"/></a>', apply_filters( 'pagelines_branding_rssurl', get_bloginfo('rss2_url') ), $this->base_url.'/rss.png' );
					
					if(VPRO) {
						if(ploption('twitterlink'))
							printf('<a target="_blank" href="%s" class="twitterlink"><img src="%s" alt="Twitter"/></a>', ploption('twitterlink'), $this->base_url.'/twitter.png');
					
						if(ploption('facebooklink'))
							printf('<a target="_blank" href="%s" class="facebooklink"><img src="%s" alt="Facebook"/></a>', ploption('facebooklink'), $this->base_url.'/facebook.png');
						
						if(ploption('linkedinlink'))
							printf('<a target="_blank" href="%s" class="linkedinlink"><img src="%s" alt="LinkedIn"/></a>', ploption('linkedinlink'), $this->base_url.'/linkedin.png');
						
						if(ploption('youtubelink'))
							printf('<a target="_blank" href="%s" class="youtubelink"><img src="%s" alt="Youtube"/></a>', ploption('youtubelink'), $this->base_url.'/youtube.png');
						
						if(ploption('gpluslink'))
							printf('<a target="_blank" href="%s" class="gpluslink"><img src="%s" alt="Google+"/></a>', ploption('gpluslink'), $this->base_url.'/google.png');
						
						pagelines_register_hook( 'pagelines_branding_icons_end', 'branding' ); // Hook 
				
					}
					
			printf('</div></div>');
					
			pagelines_register_hook( 'pagelines_after_branding_wrap', 'branding' ); // Hook
				
			?>		
			<script type="text/javascript"> 
				jQuery('.icons a').hover(function(){ jQuery(this).fadeTo('fast', 1); },function(){ jQuery(this).fadeTo('fast', 0.5);});
			</script>
<?php 	
				
		}
}
