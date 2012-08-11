<?php 

//Gets category and author info 
global $wp_query; 
$cats = get_the_category(); 
$postAuthor = $wp_query->post->post_author; 
$tempQuery = $wp_query; 
$currentId = $post->ID; 

// related author posts 
$newQuery = "posts_per_page=4&author=" . $authorPosts; 
query_posts( $newQuery ); 
$authorPosts = ""; $count = 0; 
if (have_posts()) { 
while (have_posts()) { 
$count++; 
the_post(); 
if( $count<4 && $currentId!=$post->ID) { 
$count++; 
$authorPosts .= '<li><a href="' . get_permalink() . '">' . the_title( "", "", false ) . '</a></li>'; 
} 
} 
} 

// related category posts 
$catlist = ""; 
forEach( $cats as $c ) { 
if( $catlist != "" ) { $catlist .= ","; } 
$catlist .= $c->cat_ID; 
} 
$newQuery = "posts_per_page=5&cat=" . $catlist; 
query_posts( $newQuery ); 
$categoryPosts = ""; 
$count = 0; 
if (have_posts()) { 
while (have_posts()) { 
the_post(); 
if( $count<4 && $currentId!=$post->ID) { 
$count++; 
$categoryPosts .= '<li><a href="' . get_permalink() . '">' . the_title( "", "", false ) . '</a></li>'; 
} 
} 
} 
$wp_query = $tempQuery; 
?>