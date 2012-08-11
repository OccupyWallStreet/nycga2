<?php
$location = $options_page; // Form Action URI
?>

<div class="wrap">
	<h2>Featured Posts Slideshow</h2>
	<p>Use these fields below to adjust category ID and Items to display of your Featured Posts Slideshow</p>
	
    <div style="margin-left:0px;">
    <form method="post" action="options.php"><?php wp_nonce_field('update-options'); ?>
		<fieldset name="general_options" class="options">
        
        Category ID:<br />
		<div style="margin:0;padding:0;">
        <input name="category-id" id="category-id" size="25" value="<?php echo get_option('category-id'); ?>"></input>   
        </div><br />
        
        Number of Posts:<br />
		<div style="margin:0;padding:0;">
        <input name="number-posts" id="number-posts" size="25" value="<?php echo get_option('number-posts'); ?>"></input>   
        </div><br />

	  <h2>Featured Posts Slideshow Style Configuration</h2>
	  <p>Use these fields below to adjust the styling of the Slideshow</p>

	  Outer DIV Width: (for Default please type: 945)<br />
	  <div style="margin:0;padding:0;">
        <input name="div-width" id="div-width" size="25" value="<?php echo get_option('div-width'); ?>"></input>   
        </div><br />

	  Outer DIV Background Colour: (for Default please type: 3b3b3b)<br />
	  <div style="margin:0;padding:0;">
        <input name="div-color" id="div-color" size="25" value="<?php echo get_option('div-color'); ?>"></input>   
        </div><br />

	  Images BG Colour: (for Standard please type: E6E6E6)<br />
	  <div style="margin:0;padding:0;">
        <input name="image-bg-color" id="image-bg-color" size="25" value="<?php echo get_option('image-bg-color'); ?>"></input>   
        </div><br />

	  Images Border Colour: (for Standard please type: E6E6E6)<br />
	  <div style="margin:0;padding:0;">
        <input name="image-border-color" id="image-border-color" size="25" value="<?php echo get_option('image-border-color'); ?>"></input>   
        </div><br />

	  Images Border Colour Hover: (for Standard please type: 993399)<br />
	  <div style="margin:0;padding:0;">
        <input name="image-border-hover-color" id="image-border-hover-color" size="25" value="<?php echo get_option('image-border-hover-color'); ?>"></input>   
        </div><br />
                
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="page_options" value="category-id,number-posts,div-width,div-color,image-bg-color,image-border-color,image-border-hover-color" />

		</fieldset>
		<p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Options') ?>" /></p>
	</form>      
</div>