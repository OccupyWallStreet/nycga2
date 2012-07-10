<?php 

add_action('before_signup_form', 'add_multisite_markup_top');

/**
 *
 * @TODO document
 *
 */
function add_multisite_markup_top(){
	printf('<section id="multisite_area" class="content"><div class="content-pad">');
}

add_action('after_signup_form', 'add_multisite_markup_bottom');

/**
 *
 * @TODO document
 *
 */
function add_multisite_markup_bottom(){
	printf('</div></section>');
}
