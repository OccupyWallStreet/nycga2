<?php
/**
 * @package WordPress
 * @subpackage PulsePress
 */
?>
<?php get_header(); ?>

<div class="sleeve_main">

	<div id="main">

		<h1 id="page-title"><?php _e('404 - Doh!','pulse_press');?></h1>
		<p><?php _e("Something has gone wrong and the page you're looking for can't be found.",'pulse_press');?></p>
		<p><?php _e("Hopefully one of the options below will help you",'pulse_press');?></p>

	<ul>
		<li><?php _e("You could visit",'pulse_press');?> <a href="<?php echo site_url(); ?>"><?php _e("the homepage",'pulse_press');?></a></li>
		<li><?php _e("You can search the site using the search box to the below",'pulse_press');?></li>
	</ul>
		<?php get_search_form(); ?>

	</div> <!-- main -->

</div> <!-- sleeve -->

<?php get_footer(); ?>