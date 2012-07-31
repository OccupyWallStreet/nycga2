<div id="lbar">	

<?php
if ( function_exists( 'bp_is_active' ) ){
	get_template_part('bpbar');
} else {
}
?>

<?php if ( !function_exists('dynamic_sidebar')
	        || !dynamic_sidebar('buddybar') ) : ?>
	        
<h3><?php _e( 'Topics', 'Detox') ?></h3>
<div class="cats">
<ul>
<?php wp_list_cats('sort_column=name&hide_empty=0'); ?>
</ul>
</div>

<div class="clearfix"></div>
	
<h3><?php _e( 'Recent Photos', 'Detox') ?></h3>    
<div class="ff">  
<?php get_flickrRSS(); ?>
</div>

<div class="clearfix"></div>
     
<?php endif; ?>
</div>