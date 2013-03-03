<?php
/**
 * @package WordPress
 * @subpackage PulsePress
 */
?>
<?php if ( !pulse_press_get_hide_sidebar() ) : ?>
	<div id="sidebar">
		<?php if(pulse_press_get_option( 'show_voting' ) || pulse_press_get_option( 'show_unpopular' ) || pulse_press_get_option( 'show_most_voted_on' ) ): ?>
		<ul>
			<li>
				<ul>
					<?php if(pulse_press_get_option( 'show_voting' )): ?>
					<li><a href="?popular"><?php pulse_press_display_option(pulse_press_get_option( 'popular_text'),"Popular"); ?></a></li>
					<?php if(pulse_press_get_option( 'show_unpopular' )): ?>
					<li><a href="?unpopular"><?php pulse_press_display_option(pulse_press_get_option( 'unpopular_text' ),"Unpopular"); ?></a></li>
					<?php endif; ?>
					<?php if(pulse_press_get_option( 'show_most_voted_on' )): ?>
					<li><a href="?most-voted"><?php pulse_press_display_option(pulse_press_get_option( 'most_voted_on_text' ),"Most voted-on"); ?></a></li>
					<?php endif; ?>
					<?php 
					endif;
					if(current_user_can( 'read' ) && pulse_press_get_option( 'show_fav' )): ?>
					<li><a href="<?php echo home_url();?>/?starred" id="starred"><?php pulse_press_display_option(pulse_press_get_option( 'star_text' ) ,"My Starred"); ?></a></li>
					<?php endif; ?>
				</ul>
			</li>
		</ul>
		<?php endif; ?>
		<ul>
			<?php 
			if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar() ) {
				the_widget( 'PulsePress_Recent_Comments', array(), array( 'before_widget' => '<li> ', 'after_widget' => '</li>', 'before_title' =>'<h2>', 'after_title' => '</h2>' ) );
				the_widget( 'PulsePress_Recent_Tags', array(), array( 'before_widget' => '<li> ', 'after_widget' => '</li>', 'before_title' =>'<h2>', 'after_title' => '</h2>' ) );
			}
			?>
		</ul>
	
		<div class="clear"></div>
	
	</div> <!-- // sidebar -->
<?php endif; ?>