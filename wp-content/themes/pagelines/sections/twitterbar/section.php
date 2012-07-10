<?php
/*
	Section: TwitterBar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Loads twitter feed into the site footer
	Class Name: PageLinesTwitterBar
	Workswith: morefoot, footer
	Edition: Pro
*/

/**
 * Twitter Feed Section
 *
 * Uses pagelines_get_tweets() to display the latest tweet in the morefoot area.
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesTwitterBar extends PageLinesSection {

	/**
	* Section template.
	*/
	function section_template() { 

		if( !pagelines('twittername') ) :
			printf('<div class="tbubble"><div class="tbubble-pad">%s</div></div>', __('Set your Twitter account name in your settings to use the TwitterBar Section.', 'pagelines'));

			return;
		endif;
	
		$account = ploption('twittername');
	
		$twitter = sprintf(
			'<span class="twitter">%s &nbsp;&mdash;&nbsp;<a class="twitteraccount" href="http://twitter.com/#!/%s">%s</a></span>',
			pagelines_tweet_clickable( pagelines_get_tweets( $account, true ) ), 
			$account,
			$account
		);
	
		printf('<div class="tbubble"><div class="tbubble-pad">%s</div></div>', $twitter);	
	}
}
