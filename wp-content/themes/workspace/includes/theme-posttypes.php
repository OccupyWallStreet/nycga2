<?php

//Add Slider Post Type

function tj_create_post_type_slider()
{
	$labels = array(
		'name' => __( 'Slider Items','themejunkie'),
		'singular_name' => __( 'Slider','themejunkie' ),
		'add_new' => __('Add New','themejunkie'),
		'add_new_item' => __('Add New Slider','themejunkie'),
		'edit_item' => __('Edit Slider','themejunkie'),
		'new_item' => __('New Slider','themejunkie'),
		'view_item' => __('View Slider','themejunkie'),
		'search_items' => __('Search Slider','themejunkie'),
		'not_found' =>  __('No Slider found','themejunkie'),
		'not_found_in_trash' => __('No Slider found in Trash','themejunkie'),
		'parent_item_colon' => ''
	  );

	  $args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','editor','thumbnail','custom-fields','excerpt','comments')
	  );

	  register_post_type(__( 'slider', 'themejunkie' ),$args);
}


//Add Portfolio Post Type

function tj_create_post_type_portfolio()
{
	$labels = array(
		'name' => __( 'Portfolio Items','themejunkie'),
		'singular_name' => __( 'Portfolio','themejunkie' ),
		'add_new' => __('Add New','themejunkie'),
		'add_new_item' => __('Add New Portfolio','themejunkie'),
		'edit_item' => __('Edit Portfolio','themejunkie'),
		'new_item' => __('New Portfolio','themejunkie'),
		'view_item' => __('View Portfolio','themejunkie'),
		'search_items' => __('Search Portfolio','themejunkie'),
		'not_found' =>  __('No portfolio found','themejunkie'),
		'not_found_in_trash' => __('No portfolio found in Trash','themejunkie'),
		'parent_item_colon' => ''
	  );
	  
	  $args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'query_var' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','editor','thumbnail','custom-fields','excerpt','comments')
	  ); 
	  
	  register_post_type(__( 'portfolio', 'themejunkie' ),$args);
}

function tj_build_taxonomies(){
    
	$args = array(
		"hierarchical" => true, 
		"label" => __( "Portfolio Types", 'themejunkie' ), 
		"singular_label" => __( "Portfolio Type", 'themejunkie' ), 
		"rewrite" => array('slug' => 'portfolio-type', 'hierarchical' => true), 
		"public" => true
	);
    
	register_taxonomy(__( "portfolio-type", 'themejunkie' ), array(__( "portfolio", 'themejunkie' )), $args); 
}


function tj_portfolio_edit_columns($columns){  

        $columns = array(  
            "cb" => "<input type=\"checkbox\" />",  
            "title" => __( 'Portfolio Item Title', 'themejunkie' ),
            "type" => __( 'type', 'themejunkie' )
        );  
  
        return $columns;  
}

function tj_slider_edit_columns($columns){

        $columns = array(
            "cb" => "<input type=\"checkbox\" />",
            "title" => __( 'Slider Item Title', 'themejunkie' ),
            "type" => __( 'type', 'themejunkie' )
        );
  
        return $columns;
}


function tj_portfolio_custom_columns($column){  
        global $post;  
        switch ($column)  
        {    
            case __( 'type', 'themejunkie' ):  
                echo get_the_term_list($post->ID, __( 'portfolio-type', 'themejunkie' ), '', ', ','');  
                break;
        }  
}

add_action( 'init', 'tj_create_post_type_slider' );
add_action( 'init', 'tj_create_post_type_portfolio' );

add_action( 'init', 'tj_build_taxonomies', 0 );

add_filter("manage_edit-portfolio_columns", "tj_slider_edit_columns");
add_filter("manage_edit-portfolio_columns", "tj_portfolio_edit_columns");


add_action("manage_posts_custom_column",  "tj_portfolio_custom_columns");

?>