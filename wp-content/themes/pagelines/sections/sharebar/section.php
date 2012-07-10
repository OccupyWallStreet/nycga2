<?php
/*
	Section: ShareBar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Adds ways to share content on pages/single posts
	Class Name: PageLinesShareBar
	Workswith: main-single
	Failswith: pagelines_special_pages() 
	Cloning: true
*/

/**
 * ShareBar Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesShareBar extends PageLinesSection {

	/**
     * Section template.
     *
     * @version 2.2 - added conditional check for no social sites being chosen.
     */
    function section_template() {

        if( ! $this->get_shares() ) {
            echo setup_section_notify( $this, __( 'You have no shares setup, please look at PageLines Settings > Blog and Posts > Sharebar Social Sharing Buttons; or deactivate the Sharebar from the Blog Post Template.', 'pagelines' ), admin_url( 'admin.php?page=pagelines' ), __( 'Setup Sharebar', 'pagelines' ), false );
            return;
        }

        $text = __( 'Share &rarr;', 'pagelines' );
        ?>

        <div class="pl-sharebar">
            <div class="pl-sharebar-pad media">
                <div class="img">
                    <?php
                    printf( '<em class="pl-sharebar-text">%s</em>', $text );
                    ?>
                </div>
                <div class="bd fix">
                    <?php
                    echo $this->get_shares();
                    ?>
                </div>

                <div class="clear"></div>
            </div>
        </div>
    <?php }

	function get_shares(){
		
		global $post; 

		$perm = get_permalink($post->ID);
		$title = wp_strip_all_tags( get_the_title( $post->ID ) );
		$thumb = (has_post_thumbnail($post->ID)) ? pl_the_thumbnail_url( $post->ID ) : '';

		$desc = wp_strip_all_tags( pl_short_excerpt($post->ID, 10, '') );

		$out = '';
		
		if(ploption('share_facebook'))
			$out .= self::facebook(array('permalink' => $perm));
	
		if(ploption('share_google'))
			$out .= self::google(array('permalink' => $perm));
		
		if(ploption('share_twitter'))
			$out .= self::twitter(array('permalink' => $perm, 'title' => $title));
			
		if(ploption('share_pinterest'))
			$out .= self::pinterest(array('permalink' => $perm, 'image' => $thumb, 'desc' => $desc));
			
		if(ploption('share_buffer'))
			$out .= self::buffer(array('permalink' => $perm, 'title' => $title));
		
		if(ploption('share_stumble'))
			$out .= self::stumbleupon(array('permalink' => $perm, 'title' => $title));
		
		if(ploption('share_linkedin'))	
			$out .= self::linkedin(array('permalink' => $perm, 'title' => $title));

		return $out;
	}

	/**
	 *
	 * Pinterest Button
	 *
	 */
	function pinterest( $args ){
		
		$defaults = array(
			'permalink'	=> '', 
			'width'		=> '80',
			'hash'		=> ploption('site-hashtag'), 
			'handle'	=> ploption('twittername'), 
			'title'		=> '',
			'image'		=> '', 
			'desc'		=> ''
		); 	
		
		$a = wp_parse_args($args, $defaults);
		ob_start();
		?>
		
		<a href="http://pinterest.com/pin/create/button/?url=<?php echo $a['permalink'];?>&media=<?php echo urlencode($a['image']);?>&description=<?php echo urlencode($a['desc']);?>" class="pin-it-button" count-layout="none"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
		<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
		<?php 

		return ob_get_clean();
		
		
	}

	
	/**
	 *
	 * LinkedIn Button
	 *
	 */
	function linkedin( $args ){
		
		$defaults = array(
			'permalink'	=> '', 
			'width'		=> '80',
			'hash'		=> ploption('site-hashtag'), 
			'handle'	=> ploption('twittername'), 
			'title'		=> '',
		); 	
		
		$a = wp_parse_args($args, $defaults);
		ob_start();
		?>
			<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>
			<script width="100" type="IN/Share" data-url="<?php echo $a['perm'];?>" data-width="<?php echo $a['width'];?>" data-counter="right"></script>

		<?php 

		return ob_get_clean();
		
		
	}


	/**
	 *
	 * StumbleUpon Button
	 *
	 */
	function stumbleupon( $args ){
		
		$defaults = array(
			'permalink'	=> '', 
			'width'		=> '80',
			'hash'		=> ploption('site-hashtag'), 
			'handle'	=> ploption('twittername'), 
			'title'		=> '',
		); 	
		
		$a = wp_parse_args($args, $defaults);
		ob_start();
		?>
			<su:badge layout="2" ></su:badge>

			 <script type="text/javascript"> 
			 (function() { 
			     var li = document.createElement('script'); li.type = 'text/javascript'; li.async = true; 
			      li.src = 'https://platform.stumbleupon.com/1/widgets.js'; 
			      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(li, s); 
			 })(); 
			 </script>

		<?php 

		return ob_get_clean();
		
		
	}
	
	
	/**
	*
	* Buffer Social Button
	*
	*/
	function buffer( $args ){
		
		$defaults = array(
			'permalink'	=> '', 
			'width'		=> '80',
			'hash'		=> ploption('site-hashtag'), 
			'handle'	=> ploption('twittername'), 
			'title'		=> '',
		); 	
		
		$a = wp_parse_args($args, $defaults);
		
		
		return sprintf(
			'<a href="http://bufferapp.com/add" class="buffer-add-button" data-text="%s" data-url="%s" data-count="horizontal" data-via="%s">Buffer</a><script type="text/javascript" src="http://static.bufferapp.com/js/button.js"></script>', 
			$a['title'], 
			$a['permalink'], 
			$a['handle']
		);
		
		
	}

	/**
	*
	* Twitter Button
	*
	*/
	function twitter( $args ){
		
		$defaults = array(
			'permalink'	=> '', 
			'width'		=> '80',
			'hash'		=> ploption('site-hashtag'), 
			'handle'	=> ploption('twittername'), 
			'title'		=> '',
		); 	
		
		$a = wp_parse_args($args, $defaults);
		
		ob_start();
		
			// Twitter
			printf(
				'<a href="https://twitter.com/share" class="twitter-share-button" data-url="%s" data-text="%s" data-via="%s" data-hashtags="%s">Tweet</a>', 
				$a['permalink'], 
				$a['title'],
				(ploption('twitter_via')) ? $a['handle'] : '', 
				(ploption('twitter_hash')) ? $a['hash'] : ''
			);
		
		?>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		
		<?php 
		
		return ob_get_clean();
		
	}


	/**
	*
	* Google Plus Button
	*
	*/
	function google( $args ){
		
		$defaults = array(
			'permalink'	=> '', 
			'width'		=> '80',
		); 
		
		$a = wp_parse_args($args, $defaults);
		
		ob_start();
		
			// G+
			printf('<div class="g-plusone" data-size="medium" data-width="%s" data-href="%s"></div>', $a['width'], $a['permalink']);
	
		?>
		<!-- Place this render call where appropriate -->
		<script type="text/javascript">
		  (function() {
		    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		    po.src = 'https://apis.google.com/js/plusone.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		  })();
		</script>
		
		<?php 
		
		return ob_get_clean();
		
	}


	/**
	*
	* @TODO document
	*
	*/
	function facebook( $args ){
		
		$defaults = array(
			'permalink'	=> '', 
			'width'		=> '80',
		); 
		
		$a = wp_parse_args($args, $defaults);
		
		
		ob_start();
			// Facebook
			?>
			<script>(function(d, s, id) {
					var js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) return;
					js = d.createElement(s); js.id = id;
					js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
					fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));
			</script>
			<?php
			printf(
				'<div class="fb-like" data-href="%s" data-send="false" data-layout="button_count" data-width="%s" data-show-faces="false" data-font="arial" style="vertical-align: top"></div>', 
				$a['permalink'], 
				$a['width']);
				
		return ob_get_clean();
		
	}

}
