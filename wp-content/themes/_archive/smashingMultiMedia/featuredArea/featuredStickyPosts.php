<?php // collect sticky posts
	$showPosts 	= get_option('wps_sticky_showposts');
	$sticky 	= get_option( 'sticky_posts' );
	$sticky 	= array_slice( $sticky, 0, $showPosts );
	$stickyArgs	= array(
		'post__in' 			=> $sticky,
		'caller_get_posts'	=> 1,
		'showposts'			=> $showPosts,
	);
?>
	
		<ul id="imageTabs" class="alignleft">
			
			<?php 
			$a				= 1; // iterator needed to stand in place of thumb image
			$myStickyQuery 	= new WP_Query($stickyArgs);
			while ($myStickyQuery->have_posts()) : $myStickyQuery->the_post(); ?>
				
				<li><a href="#post-<?php the_ID(); ?>"><span>
					<?php if(get_post_meta($post->ID, "stickyMediaSplashThumbImg_value", $single = true) != "") { 
						$img_src 	= get_post_meta($post->ID, "stickyMediaSplashThumbImg_value", $single = true); 
						$des_src 	= 'wp-content/uploads/image-80/';	
						$img_file 	= mkthumb($img_src,$des_src,80,'width');    
						$imgURL 	= get_option('home').'/'.$des_src.''.$img_file;?>
						<img class="alignleft" src="<?php echo $imgURL; ?>" alt="Thumbnail for <?php the_title(); ?>"/>
					<?php } else { echo $a;} ?>
				</span></a></li>
				
			<?php 
				$a++;
				endwhile; 
			?>
		</ul>	
		
		<ul id="featuredPostPanes" class="alignleft">	
			
			<?php rewind_posts(); 

			while ($myStickyQuery->have_posts()) : $myStickyQuery->the_post(); 
				// Set the post content to a variable
				$subject = $post->post_content;
				// Look for embeded videos
				$pattern  = '/\[(\w*)\sid="([^"]+)"\swidth="([^"]+)"\sheight="([^"]+)/';
				// Run preg_match_all to grab all videos and save the results in $videoMatches
				preg_match_all( $pattern , $subject, $videoMatches); 
				
				$pattern2  = '/\[(\w*)\sfile="([^"]+)/';
				preg_match_all( $pattern2 , $subject, $flashPlayerMatches); 
				
				//print_r ($flashPlayerMatches);
				
				$output = my_attachment_image(0, 'large', 'alt="' . $post->post_title . '"','return'); ?>
				
				<li <?php post_class('clearfix'); ?> id="post-<?php the_ID(); ?>" >
					
					<?php 
					$teaserClass = 'teaser'; 
					$mediaWrapClass = 'mediaWrap';
					$mediaPanesWrapClass = 'mediaPanesWrap';
					?>
					<div class="<?php echo $teaserClass;?> alignleft">
						<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( __('Permalink to %s', 'smashingMultiMedia'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
						
						<?php
						//	what type of content teaser did the user choose?					
						$wordLimit 	= get_option('wps_stickyWordLimit');
						switch(get_option('wps_stickyContent_option')){
						
							case 'content_btn': ?>
								<p><?php the_content_rss('', TRUE, '', $wordLimit); ?></p>
								<a class="readMore" href="<?php the_permalink(); ?>"><?php _e('Read More', 'smashingMultiMedia'); ?></a>
							<?php
							break;
							
							case 'content_link': ?>
								<p class="readMoreLink">
									<?php the_content_rss('', TRUE, '', $wordLimit); ?>
									<a href="<?php the_permalink(); ?>"><?php echo get_option('wps_readMoreLink'); ?></a>
								</p>
							<?php
							break;
													
							case 'excerpt_btn': 
								the_excerpt();?> 
								<a class="readMore" href="<?php the_permalink(); ?>"><?php _e('Read More', 'smashingMultiMedia'); ?></a>
								<?php 
							break;
							
							case 'excerpt_link': 
								the_excerpt();?> 
								<a href="<?php the_permalink(); ?>"><?php echo get_option('wps_readMoreLink'); ?></a>
							<?php 
							break;
						} ?>
						
						 <div class="footnotes clearfix">
							<?php if(function_exists('the_ratings')) { the_ratings(); } ?>  
							<span class="comments-link"><?php comments_popup_link( __( '0', 'smashingMultiMedia' ), __( '1', 'smashingMultiMedia' ), __( '%', 'smashingMultiMedia' ) ) ?></span>
						</div><!-- footnotes -->   
                        
					</div><!-- teaser -->
									
					<div class="<?php echo $mediaPanesWrapClass;?> alignright">
								
						<?php 
						if((!empty($videoMatches[1][0])) && (get_post_meta($post->ID, "stickyMediaSplashImg_value", $single = true) == "")) { // was a video added? ?>
							<div class="mediaPanes videoMediaPanes alignleft">	
								<div class="<?php echo $mediaWrapClass;?> videoMediaWrap videoWrapFull">
									
									<?php switch($videoMatches[1][0]){
									
										case 'vimeo': 
											if(get_post_meta($post->ID, "videoHeight_value", $single = true) != "") { ?>
												<object width="442" height="<?php echo get_post_meta($post->ID, "videoHeight_value", $single = true); ?>" data="http://vimeo.com/moogaloop.swf?clip_id=<?php echo $videoMatches[2] [0];?>&amp;server=vimeo.com" type="application/x-shockwave-flash">
											<?php } else { ?>
												<object width="<?php echo $videoMatches[3] [0];?>" height="<?php echo $videoMatches[4] [0];?>" data="http://vimeo.com/moogaloop.swf?clip_id=<?php echo $videoMatches[2] [0];?>&amp;server=vimeo.com" type="application/x-shockwave-flash">
											<?php } ?>
												<param name="allowfullscreen" value="true" />
												<param name="allowscriptaccess" value="always" />
												<param name="wmode" value="opaque" />
												<param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=<?php echo $videoMatches[2] [0]; ?>&amp;server=vimeo.com"/>
											</object>								
										<?php break;
										
										case 'youtube': 
											if(get_post_meta($post->ID, "videoHeight_value", $single = true) != "") { ?>
												<object width="442" height="<?php echo get_post_meta($post->ID, "videoHeight_value", $single = true); ?>" data="http://www.youtube.com/v/<?php echo $videoMatches[2] [0];?>" type="application/x-shockwave-flash">
											<?php } else { ?>
												<object width="<?php echo $videoMatches[3] [0];?>" height="<?php echo $videoMatches[4] [0];?>" data="http://www.youtube.com/v/<?php echo $videoMatches[2] [0];?>" type="application/x-shockwave-flash">
											<?php } ?>
												<param name="allowfullscreen" value="true" />
												<param name="allowscriptaccess" value="always" />
												<param name="wmode" value="opaque" />
												<param name="movie" value="http://www.youtube.com/v/<?php echo $videoMatches[2] [0];?>"/>
											</object>
										<?php break;
										
										case 'googlevideo': 
											if(get_post_meta($post->ID, "videoHeight_value", $single = true) != "") { ?>
												<object width="442" height="<?php echo get_post_meta($post->ID, "videoHeight_value", $single = true); ?>" data="http://video.google.com/googleplayer.swf?docid=<?php echo $videoMatches[2] [0];?>" type="application/x-shockwave-flash">
											<?php } else { ?>
												<object width="<?php echo $videoMatches[3] [0];?>" height="<?php echo $videoMatches[4] [0];?>" data="http://video.google.com/googleplayer.swf?docid=<?php echo $videoMatches[2] [0];?>" type="application/x-shockwave-flash">
											<?php } ?>
												<param name="allowfullscreen" value="true" />
												<param name="allowscriptaccess" value="always" />
												<param name="wmode" value="opaque" />
												<param name="movie" value="http://video.google.com/googleplayer.swf?docid=<?php echo $videoMatches[2] [0];?>"/>
											</object>
										<?php break;
										
										case 'metacafe': 
											if(get_post_meta($post->ID, "videoHeight_value", $single = true) != "") { ?>
												<object width="442" height="<?php echo get_post_meta($post->ID, "videoHeight_value", $single = true); ?>" data="http://www.metacafe.com/fplayer/<?php echo $videoMatches[2] [0];?>" type="application/x-shockwave-flash">
											<?php } else { ?>
												<object width="<?php echo $videoMatches[3] [0];?>" height="<?php echo $videoMatches[4] [0];?>" data="http://www.metacafe.com/fplayer/<?php echo $videoMatches[2] [0];?>" type="application/x-shockwave-flash">
											<?php } ?>
												<param name="allowfullscreen" value="true" />
												<param name="allowscriptaccess" value="always" />
												<param name="wmode" value="opaque" />
												<param name="movie" value="http://www.metacafe.com/fplayer/<?php echo $videoMatches[2] [0];?>"/>
											</object>
										<?php break;
										
										case 'bliptv': 
											if(get_post_meta($post->ID, "videoHeight_value", $single = true) != "") { ?>
												<object width="442" height="<?php echo get_post_meta($post->ID, "videoHeight_value", $single = true); ?>" data="http://blip.tv/play/<?php echo $videoMatches[2] [0];?>" type="application/x-shockwave-flash">
											<?php } else { ?>
												<object width="<?php echo $videoMatches[3] [0]?>" height="<?php echo $videoMatches[4] [0];?>" data="http://blip.tv/play/<?php echo $videoMatches[2] [0];?>" type="application/x-shockwave-flash">
											<?php } ?>
												<param name="allowfullscreen" value="true" />
												<param name="allowscriptaccess" value="always" />
												<param name="wmode" value="opaque" />
												<param name="movie" value="http://blip.tv/play/<?php echo $videoMatches[2] [0];?>"/>
											</object>
										<?php break;
										
										case 'veoh': 
											if(get_post_meta($post->ID, "videoHeight_value", $single = true) != "") { ?>
												<object width="442" height="<?php echo get_post_meta($post->ID, "videoHeight_value", $single = true); ?>" data="http://www.veoh.com/static/swf/webplayer/WebPlayer.swf?version=AFrontend.5.4.2.20.1002&permalinkId=<?php echo $videoMatches[2] [0];?>&player=videodetailsembedded&videoAutoPlay=0&id=anonymous" type="application/x-shockwave-flash">
											<?php } else { ?>
												<object width="<?php echo $videoMatches[3] [0];?>" height="<?php echo $videoMatches[4] [0];?>" data="http://www.veoh.com/static/swf/webplayer/WebPlayer.swf?version=AFrontend.5.4.2.20.1002&permalinkId=<?php echo $videoMatches[2] [0];?>&player=videodetailsembedded&videoAutoPlay=0&id=anonymous" type="application/x-shockwave-flash">
											<?php } ?>
												<param name="allowfullscreen" value="true" />
												<param name="allowscriptaccess" value="always" />
												<param name="wmode" value="opaque" />
												<param name="movie" value="http://www.veoh.com/static/swf/webplayer/WebPlayer.swf?version=AFrontend.5.4.2.20.1002&permalinkId=<?php echo $videoMatches[2] [0];?>&player=videodetailsembedded&videoAutoPlay=0&id=anonymous"/>
											</object>
										<?php break;
										
										case 'viddler': 
											if(get_post_meta($post->ID, "videoHeight_value", $single = true) != "") { ?>
												<object width="442" height="<?php echo get_post_meta($post->ID, "videoHeight_value", $single = true); ?>" data="http://www.viddler.com/player/<?php echo $videoMatches[2] [0];?>" type="application/x-shockwave-flash">
											<?php } else { ?>
												<object width="<?php echo $videoMatches[3] [0];?>" height="<?php echo $videoMatches[4] [0];?>" data="http://www.viddler.com/player/<?php echo $videoMatches[2] [0];?>" type="application/x-shockwave-flash">
											<?php } ?>
												<param name="allowfullscreen" value="true" />
												<param name="allowscriptaccess" value="always" />
												<param name="wmode" value="opaque" />
												<param name="movie" value="http://www.viddler.com/player/<?php echo $videoMatches[2] [0];?>"/>
											</object>
										<?php break;
										
										case 'revver': 
											if(get_post_meta($post->ID, "videoHeight_value", $single = true) != "") { ?>
												<object width="442" height="<?php echo get_post_meta($post->ID, "videoHeight_value", $single = true); ?>" data="http://flash.revver.com/player/1.0/player.swf?mediaId=<?php echo $videoMatches[2] [0];?>" type="application/x-shockwave-flash">
											<?php } else { ?>
												<object width="<?php echo $videoMatches[3] [0];?>" height="<?php echo $videoMatches[4] [0];?>" data="http://flash.revver.com/player/1.0/player.swf?mediaId=<?php echo $videoMatches[2] [0];?>" type="application/x-shockwave-flash">
											<?php } ?>
												<param name="allowfullscreen" value="true" />
												<param name="allowscriptaccess" value="always" />
												<param name="wmode" value="opaque" />
												<param name="movie" value="http://flash.revver.com/player/1.0/player.swf?mediaId=<?php echo $videoMatches[2] [0];?>"/>
											</object>								
										<?php break;
									} ?>
								</div><!--mediaWrap-->
							</div><!-- mediaPanes -->
							
						<?php } elseif (get_post_meta($post->ID, "stickyMediaSplashImg_value", $single = true) != "") { // was a splash image chosen? 
							if (strlen($output[img_path])>0) { ?>
								<div class="mediaPanes imageMediaPanes alignleft">
									<div class="<?php echo $mediaWrapClass; ?> imageMediaWrap imageWrapFull">
							<?php } else { ?>	
								<div class="mediaPanes videoMediaPanes alignleft">	
									<div class="<?php echo $mediaWrapClass;?> videoMediaWrap videoWrapFull">
							<?php } ?>
									<a href="<?php the_permalink(); ?>"> 
										<?php 
											$img_src 	= get_post_meta($post->ID, "stickyMediaSplashImg_value", $single = true); 
											$des_src 	= 'wp-content/uploads/image-442/';	
											
											$img_file 	= mkthumb($img_src,$des_src,442,'width');    
											
											$imgURL 	= get_option('home').'/'.$des_src.''.$img_file;
										?>
										<img src="<?php echo $imgURL;?>" alt="<?php the_title(); ?>" />
									</a> 
								</div><!-- mediaWrap -->
							</div><!-- mediaPanes -->
							
						<?php } elseif (strlen($output[img_path])>0) { // was an image added? ?>
							<div class="mediaPanes imageMediaPanes alignleft">
								<div class="<?php echo $mediaWrapClass; ?> imageMediaWrap imageWrapFull">
									<?php if (get_post_meta($post->ID, "stickyMediaSplashImg_value", $single = true) != "") { ?>
										<a href="<?php the_permalink(); ?>"> 
											<?php 
												$img_src 	= get_post_meta($post->ID, "stickyMediaSplashImg_value", $single = true); 
												$des_src 	= 'wp-content/uploads/image-442/';	
												$img_file 	= mkthumb($img_src,$des_src,442,'width');    
												$imgURL 	= get_option('home').'/'.$des_src.''.$img_file;
											?>
											<img src="<?php echo $imgURL;?>" alt="<?php the_title(); ?>" />
										</a> 
									<?php } else { ?>
										<p class="alignleft error">
											<?php _e('Oops! No Media File was found.','smashingMultiMedia'); ?><br/>
											<?php _e('Make sure you upload a Media Splash Image for this post!','smashingMultiMedia'); ?>
										</p>
									<?php } ?>
									
								</div><!-- mediaWrap -->
							</div><!-- mediaPanes -->
						<?php } else { // no media found? ?>
							<p class="alignleft error">
								<?php _e('Oops! No Media File was found.','smashingMultiMedia'); ?><br/>
								<?php _e('How about embedding a Video or adding Image to this post?','smashingMultiMedia'); ?>
							</p>
						<?php } ?>
							
						<ul class="shareTabs alignright">
							<li class="rss"><?php post_comments_feed_link( $link_text = 'RSS' ) ?></li>
							<li class="email"><a href="<?php echo get_option('wps_feedburner_emaillink'); ?>" target="_blank"><?php _e('Email','smashingMultiMedia'); ?></a></li>
							<li class="twitter"><?php echo  dd_tiny_tweet_init($content);?></li>
							<li class="read"><a href="<?php the_permalink(); ?>"><?php _e('Read','smashingMultiMedia'); ?></a></li>
							<li class="comment"><a href="<?php comments_link(); ?>"><?php _e('Comment','smashingMultiMedia'); ?></a></li>
						</ul>
					</div><!-- mediaPanesWrap -->
				</li><!-- post -->	
			<?php endwhile;?>
		</ul><!-- featuredPostPanes -->
