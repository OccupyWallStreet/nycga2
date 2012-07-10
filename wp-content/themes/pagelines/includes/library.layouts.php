<?php
/**
 * Includes functions for use in layouts
 * 
 **/



/**
 *
 * @TODO document
 *
 */
function grid( $data, $args = array() ){
	
	$defaults = array(
		'data'			=> 'query',
		'per_row'		=> 3, 
		'format'		=> 'img_grid', 
		'paged'			=> false, 
		'has_img'		=> true,
		'image_field'	=> false,
		'img_default'	=> null, 
		'img_width'		=> '100%', 
		'title'			=> '',
		'title_link'	=> '',
		'class'			=> 'pagelines-grid', 
		'row_class'		=> 'gridrow', 
		'content_len'	=> 10, 
		'callback'		=> false,
		'margin'		=> true, 
		'hovercard'		=> false
	);
	
	$a = wp_parse_args($args, $defaults);
	
	if( $a['data'] == 'users' || $a['data'] == 'array_callback'){
		
		$posts = $data;
	
	}else{
		// The Query
		global $wp_query; 
	
		$wp_query = $data;
	
		$posts = $data->posts;
	
		if( !is_array( $posts ) )
			return;
		
	}
	
	// Standard Variables
	$out = '';
	$total = count($posts);
	$count = 1;
	$default_img = ( isset($a['img_default']) ) ? sprintf('<img src="%s" alt="%s"/>', $a['img_default'], __('No Image', 'pagelines')) : '';
	
	$margin_class = ($a['margin']) ? '' : 'ppfull';
	
	
	if($a['hovercard'])
		$out .= pl_js_wrap(sprintf('jQuery(".vignette").hover(function(){jQuery(this).find(".hovercard").fadeIn();}, function(){jQuery(this).find(".hovercard").fadeOut();});'));


	// Grid loop
	foreach($posts as $pid => $p){
			
		// Grid Stuff
		$start = (grid_row_start( $count, $total, $a['per_row'])) ? sprintf('<div class="pprow grid-row fix %s">', $margin_class) : '';
		$end = (grid_row_end( $count, $total, $a['per_row'])) ? '</div>' : '';
		$last_class = (grid_row_end( $count, $total, $a['per_row'])) ? 'pplast' : '';
		

		// Content 
		$content = '';
		
		if($a['callback'])
			$content = call_user_func( $a['callback'], $p, $a );
		else {
			
			setup_postdata($p); 
			
			$oset = array('post_id' => $p->ID);
		
			// The Image
			if( $a['image_field'] && ploption($a['image_field'], $oset) )
				$thumb = sprintf('<img src="%s" alt="thumb" />', ploption($a['image_field'], $oset) );
			elseif( has_post_thumbnail( $p->ID ) )
				$thumb = get_the_post_thumbnail( $p->ID );
			else
				$thumb = $default_img;
			
			$hovercard = ($a['hovercard']) ? sprintf('<div class="hovercard"><span>%s</span></div>', $p->post_title) : '';
			
			$image = sprintf( 
				'<a href="%s" class="img grid-img" style="width: %s"><div class="grid-img-pad"><div class="grid-img-frame"><div class="vignette">%s%s</div></div></div></a>',
				get_permalink($p->ID), 
				$a['img_width'], 
				$thumb, 
				$hovercard
			);
	
			$content .= $image;
	
			// Text
			
			if($a['format'] == 'media'){
				
				$content .= sprintf(
					'<div class="bd grid-content"><h4><a href="%s">%s</a></h4> <p>%s %s %s</p></div>', 
					get_permalink($p->ID), 
					$p->post_title, 
					custom_trim_excerpt($p->post_content, $a['content_len']), 
					sprintf('<a href="%s" >More &rarr;</a>', get_permalink($p->ID)),
					pledit( $p->ID )
					
				);
				
			}
		
		}
		
		// Column Box Wrapper
		$out .= sprintf(
			'%s<div class="grid-element pp%s %s %s"><div class="grid-element-pad">%s</div></div>%s', 
			$start, 
			$a['per_row'], 
			$a['format'], 
			$last_class, 
			$content, 
			$end
			);
	
		$count++;
	}
	
	if( $a['paged'] ){
		ob_start();
		pagelines_pagination();
		$pages = ob_get_clean();
	} else
		$pages = '';
	
	$title_link = ($a['title_link'] != '') ? sprintf('<a href="%s" class="button title-link">See All</a>', $a['title_link']) : '';
	
	$title = ($a['title'] != '') ? sprintf('<div class="grid-title"><div class="grid-title-pad fix"><h4 class="gtitle">%s</h4>%s</div></div>', $a['title'], $title_link) : '';
	
	$wrap = sprintf('<div class="plgrid %s"><div class="plgrid-pad">%s%s%s</div></div>', $a['class'], $title, $out, $pages);

	return $wrap;
	
}


/**
 *  Returns true on the first element in a row of elements
 **/
function grid_row_start( $count, $total_count, $perline){

	$row_count = $count + ( $perline - 1 );
	
	$grid_row_start = ( $row_count % $perline == 0 ) ? true : false;
	
	return $grid_row_start;

}

/**
 *  Returns false on the last element in a row of elements
 **/
function grid_row_end( $count, $total_count, $perline){

	
	$row_count = $count + ( $perline - 1 );
	
	$box_row_end = ( ( $row_count + 1 ) % $perline == 0 || $count == $total_count ) ? true : false;
	
	return $box_row_end;
}
