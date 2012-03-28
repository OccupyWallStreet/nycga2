<?php require_once( ABSPATH . WPINC . '/feed.php' ); ?>

<ul>
	<?php $max_items = 0; ?>
	<?php if ( function_exists( 'fetch_feed' ) ) { 
		
		// Get a SimplePie feed object from the specified feed source.
		$rss = fetch_feed( 'http://www.bravenewcode.com/tag/wptouch/feed/' );
		if ( !is_wp_error( $rss ) ) { // Checks that the object is created correctly 
		    // Figure out how many total items there are, but limit it to 5. 
		    $max_items = $rss->get_item_quantity(5);
		    $rss_items = $rss->get_items( 0, $max_items ); 
		}
	
	    if ( $max_items == 0 ) {
	    	echo __( '<li class="ajax-error">No feed items found to display.</li>', 'wptouch' );
	    } else {
		    // Loop through each feed item and display each item as a hyperlink.
		    foreach ( $rss_items as $item ) { ?>
		    <li>
				<a target="_blank" class="orange-link" href='<?php echo $item->get_permalink(); ?>' title='<?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?>'>
				<?php echo $item->get_title(); ?>
				</a>
		    </li> <?php 
			} 
		}
    } else { 
    	echo __(' <li class="ajax-error">No feed items found to display.</li>', 'wptouch' );
    } ?>
</ul>