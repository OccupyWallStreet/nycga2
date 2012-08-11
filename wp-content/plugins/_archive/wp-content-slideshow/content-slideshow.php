<?php

        $direct_path =  get_bloginfo('wpurl')."/wp-content/plugins/wp-content-slideshow";
        $c_slideshow_class = c_slideshow_get_dynamic_class();

?>
<style>

#content-slideshow {
width: <?php $width = get_option('content_width'); if(!empty($width)) {echo $width;} else {echo "570";}?>px;
padding:0px !important;
background-color: #<?php $bg = get_option('content_bg'); if(!empty($bg)) {echo $bg;} else {echo "FFF";}?>;
height: <?php $height = get_option('content_height'); if(!empty($height)) {echo $height;} else {echo "250";}?>px;
overflow:hidden;
border: 5px solid #CCC;
position: relative;
}

#content-slideshow ul {
background:transparent !important;
margin: 0 !important;
border: none !important;
padding: 0 !important;
list-style-type: none !important;
position: relative;
}           

#content-slideshow .content_slideshow ul {
float:left;
overflow: hidden;
width: 300px;
margin: 0px !important;
padding: 0px !important;
height: 250px;
position: relative;
}

#content-slideshow .content_slideshow ul li {
display:none;
width: 300px !important;
height: 250px !important;
display:block;
overflow: hidden;
position:relative;
top: 0px !important;
left: 0px !important;
float: left;
margin: 0px !important;
padding: 0px !important;
z-index:1;
}

#content-slideshow .content_slideshow ul li img {
margin: 0px !important;
padding: 0px !important;
border: none !important;
float: left;
width: <?php $img_width = get_option('content_img_width'); if(!empty($img_width)) {echo $img_width;} else {echo "300";}?>px;
position: absolute;
top: 0px;
height: <?php $height = get_option('content_height'); if(!empty($height)) {echo $height;} else {echo "250";}?>px;
}

#content-slideshow  ul.slideshow-nav {
height:<?php $height = get_option('content_height'); if(!empty($height)) {echo $height;} else {echo "250";}?>px;
width:<?php $content_nav_width = get_option('content_nav_width'); if(!empty($content_nav_width)) {echo $content_nav_width;} else {echo "270";}?>px;
margin:0;
padding: 0;
float:right;
overflow:hidden;
}

#content-slideshow .slideshow-nav li {
display:block;
margin:0;
padding:0;
list-style-type:none;
display:block;
}

.slideme {
font-size: 9px;
float: right;
}

.slideme a {
font-size: 8px;
text-decoration: none;
color: #CCC;
}

#content-slideshow .slideshow-nav li {
width: <?php $content_nav_width = get_option('content_nav_width'); if(!empty($content_nav_width)) {echo $content_nav_width;} else {echo "270";}?>px;
display:block;
margin:0px !important;
float: left;
padding: 0px !important;
}

#content-slideshow .slideshow-nav li a {
width: <?php $content_nav_width = get_option('content_nav_width'); if(!empty($content_nav_width)) {echo $content_nav_width;} else {echo "270";}?>px;
display:block;
margin:0;
padding:9px;
list-style-type:none;
display:block;
height:<?php $content_nav_height = get_option('content_nav_height'); if(!empty($content_nav_height)) {echo $content_nav_height;} else {echo "31";}?>px;
color:#<?php $content_nav_color = get_option('content_nav_color'); if(!empty($content_nav_color)) {echo $content_nav_color;} else {echo "333";}?>;
overflow:hidden;
background-color: #<?php $content_nav_bg = get_option('content_nav_bg'); if(!empty($content_nav_bg)) {echo $content_nav_bg;} else {echo "EEE";}?>;
font-size: 14px;
font-weight: bold;
border-bottom: 1px solid #CCC;
line-height:1.35em;
}

#content-slideshow .slideshow-nav li p {
float: left;
font-size: 12px;
font-weight: normal;
padding-top: 1px;
}

#content-slideshow .slideshow-nav li.on a {
background-color: #<?php $nav_bg_active_color = get_option('content_nav_active_bg'); if(!empty($nav_bg_active_color)) {echo $nav_bg_active_color;} else {echo "CCC";}?>;
color:#fff;
}

#content-slideshow .slideshow-nav li a:hover,
#content-slideshow .slideshow-nav li a:active {
color:#<?php $nav_color = get_option('content_nav_active_color'); if(!empty($nav_color)) {echo $nav_color;} else {echo "FFF";}?>;
background-color: #<?php $nav_bg_active_color = get_option('content_nav_active_bg'); if(!empty($nav_bg_active_color)) {echo $nav_bg_active_color;} else {echo "CCC";}?>;
}

.<?php echo $c_slideshow_class;?> {
font-size: 10px;
float: right;
clear: both;
position: relative;
top: -2px;
background-color: #CCC;
padding: 3px 3px;
line-height: 10px !important;
}

</style>


	<div id="content-slideshow">

		<div class="content_slideshow">

			<ul>

			<?php
                        
                        $counting = 1;
                        
                        $sort = get_option('content_sort'); if(empty($sort)){$sort = "post_date";}
                        $order = get_option('content_order'); if(empty($order)){$order = "DESC";}
                        
                        global $wpdb;
                
                        global $post;
                        
                        $args = array( 'meta_key' => 'content_slider', 'meta_value'=> '1', 'suppress_filters' => 0, 'post_type' => array('post', 'page'), 'orderby' => $sort, 'order' => $order);
                        
                        $myposts = get_posts( $args );
                        
                        foreach( $myposts as $post ) :	setup_postdata($post);
                                				
				$custom = get_post_custom($post->ID);
				
				$thumb = get_generated_thumb("content_slider");
				
			?>

				<li id="main-post-<?php echo $counting;?>" onclick="location.href='<?php the_permalink(); ?>';" title="<?php _e("Permanent Link to"); ?> <?php the_title(); ?>">
					<img src="<?php echo $thumb;?>" />
				</li>

			<?php
			
			$counting = $counting + 1;
			
			endforeach; ?>

			</ul>

		</div>

		<ul class="slideshow-nav">

			<?php
                        
			global $wpdb;
			
			$counting = 1;
			
			global $post;
                        
                        $args = array( 'meta_key' => 'content_slider', 'meta_value'=> '1', 'suppress_filters' => 0, 'post_type' => array('post', 'page'), 'orderby' => $sort, 'order' => $order);
                        
                        $myposts = get_posts( $args );
                        
                        foreach( $myposts as $post ) :	setup_postdata($post);
                                				
				$custom = get_post_custom($post->ID);
                                
                        ?>

			<?php if ( $counting == 1 ) { ?>
				<li class="on clearfix" id="post-<?php echo $counting; ?>">
					<a href="#main-post-<?php echo $counting; ?>" title="<?php the_title(); ?>">
						<?php echo cut_content_feat(get_the_title(), 35, ""); ?><br />
						<?php $excerpt = get_the_excerpt();?>
						<p><?php echo cut_content_feat($excerpt, 48, "..."); ?> </p> 
					</a>
				</li>
			<?php } else { ?>
				<li id="post-<?php echo $counting; ?>" class="clearfix">
					<a href="#main-post-<?php echo $counting; ?>" title="<?php the_title(); ?>">
						<?php echo cut_content_feat(get_the_title(), 35, ""); ?><br />
						<?php $excerpt = get_the_excerpt();?>
						<p><?php echo cut_content_feat($excerpt, 48, "..."); ?> </p>
					</a>
				</li>
			<?php } ?>

			<?php
			
			$counting = $counting + 1;
			
			endforeach; ?>

		</ul>

	</div>
        
<p class="<?php echo $c_slideshow_class;?>"><a href="http://www.iwebix.de" target="_blank" title="Individuelles Webdesign vom Fachmann aus Berlin">IWEBIX</a> Content Slider</p>