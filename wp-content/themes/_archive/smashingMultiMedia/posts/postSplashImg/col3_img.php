<?php 
$output = my_attachment_image(0, 'large', 'alt="' . $post->post_title . '"','return'); 

if((get_post_meta($post->ID, "3colmediaSplashImg_value", $single = true) != "") || (get_post_meta($post->ID, "3colmediaSplashImgAlt_value", $single = true) != "") || (strlen($output[img_path])>0)) {  
	if (is_category()) {
		switch(get_option('wps_mediaPostDisplay_catOption')){
			case 'option1': ?>
				<a class="mediaLink hover_link"> 
			<?php
			break;
													
			case 'option2': ?>
				<a href="<?php the_permalink(); ?>" class="mediaLink mediaLinkAlt"> 
			<?php
			break;
		} 
	} else {
		switch(get_option('wps_mediaPostDisplay_frPgOption')){
			case 'option1': ?>
				<a class="mediaLink hover_link"> 
			<?php
			break;
													
			case 'option2': ?>
				<a href="<?php the_permalink(); ?>" class="mediaLink mediaLinkAlt"> 
			<?php
			break;
		} 
	} 
	
		if (is_category()) {
		 
			if ((strlen($output[img_path])>0) && (get_post_meta($post->ID, "3colmediaSplashImg_value", $single = true) == "") ) {$img_src 	= $output[img_path];}
			elseif (get_post_meta($post->ID, "3colmediaSplashImgAlt_value", $single = true) != "") {$img_src 	= get_post_meta($post->ID, "3colmediaSplashImgAlt_value", $single = true); } 
			else {$img_src 	= get_post_meta($post->ID, "3colmediaSplashImg_value", $single = true);}
		
		} else {
		
			if ((strlen($output[img_path])>0) && (get_post_meta($post->ID, "3colmediaSplashImg_value", $single = true) == "")) {$img_src 	= $output[img_path]; }
			else {$img_src 	= get_post_meta($post->ID, "3colmediaSplashImg_value", $single = true);}
		}
		
		$des_src 	= 'wp-content/uploads/image-183/';	
		$img_file 	= mkthumb($img_src,$des_src,183,'width');    
		$imgURL 	= get_option('home').'/'.$des_src.''.$img_file; ?>
		<img src="<?php echo $imgURL;?>" alt="<?php the_title(); ?>" />
	</a>								
<?php  } 