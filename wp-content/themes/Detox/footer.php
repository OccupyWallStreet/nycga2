<div id="footer">

<div class="finner">

<div class="col4">
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('footer-column1') ) : ?>

<h3><?php _e( 'Topi', 'Detox') ?><span><?php _e( 'cs', 'Detox') ?></span></h3>
<div class="cats">
<ul>
<?php wp_list_categories('orderby=name&show_count=0&title_li=&number=8'); ?>
</ul>
</div>

<?php endif; ?>
</div>

<div class="col5">

<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('footer-column2') ) : ?>

<h3><?php _e( 'Page', 'Detox') ?><span><?php _e( 's', 'Detox') ?></span></h3>
<ul>
  <?php wp_list_pages('sort_column=menu_order&title_li=&number=8'); ?>
</ul>

<?php endif; ?>

</div>

<div class="col6">
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('footer-column3') ) : ?>

<h3><?php _e( 'Pop', 'Detox') ?><span><?php _e( 'ular', 'Detox') ?></span></h3>

<ul id="popular-comments">
<?php
$pc = new WP_Query('orderby=comment_count&posts_per_page=6&cat=');
?>
<?php while ($pc->have_posts()) : $pc->the_post(); ?>

<li>
<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?> <small>&#38; <?php comments_popup_link('No Comments;', '1 Comment', '% Comments'); ?></small></a>
</li>

<?php endwhile; ?>
</ul>


<?php endif; ?>
</div>

<div class="col7">
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('footer-column4') ) : ?>

<h3><?php _e( 'Archi', 'Detox') ?><span><?php _e( 'ves', 'Detox') ?></span></h3>
<div class="cats">
<ul>
<?php wp_get_archives('type=monthly&show_post_count=0&limit=8'); ?>
</ul>
</div>

<?php endif; ?>
</div>

</div>
</div>

<div class="clearfix"></div><hr class="clear" />

<div class="credits">
<div class="finner">

<div class="navbarf">
<?php
if(function_exists('wp_nav_menu')) {
wp_nav_menu(array(
'theme_location' => 'footernav',
'container' => 'moo',
'container_id' => 'moo',
'menu_id' => 'moo',
'fallback_cb' => 'footernav_fallback',
));
} else {
?>
<?php
}
?>
</div>

<div class="clearfix"></div><hr class="clear" />

<h1 class="clearfix"><?php bloginfo('name'); ?></h1>
<div class="de"><?php bloginfo('description'); ?></div>

<div class="clearfix"></div><hr class="clear" />

<div class="creditsl">
<?php $aOptions = Detox::initOptions(false); ?>
<a href="http://3oneseven.com/"><img src="<?php echo($aOptions['featured111-image']); ?>" alt="<?php bloginfo('name'); ?>" /></a>
<?php _e( 'Copyright', 'Detox') ?> &copy; <?php echo date("Y"); ?> <?php bloginfo('name'); ?><br />
<a title="<?php _e('Design by milo.'); ?>" href="http://3oneseven.com/"><?php _e('Design by'); ?> milo</a> 
</div>

<div class="creditsr">
<p><?php _e( 'Updates via', 'Detox') ?> <a href="<?php bloginfo('rss2_url'); ?>" class="rss"><?php _e( 'RSS', 'Detox') ?></a> 
<br /><?php _e( 'or', 'Detox') ?> <a href="<?php bloginfo('rss2_url'); ?>" class="rss"><?php _e( 'Email', 'Detox') ?></a></p>
</div>

<?php
	get_template_part('social');
?>

</div>
</div>
</div>

<?php do_action('wp_footer'); ?>

<?php if ( (is_home())  ) { ?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/featuredcontentglider.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/stepcarousel.js"></script>
<script type="text/javascript">
stepcarousel.setup({
	galleryid: 'mygallery', //id of carousel DIV
	beltclass: 'belt', //class of inner "belt" DIV containing all the panel DIVs
	panelclass: 'panel', //class of panel DIVs each holding content
	autostep: {enable:true, moveby:3, pause:3000},
	panelbehavior: {speed:500, wraparound:true, persist:true},
	defaultbuttons: {enable: true, moveby: 1, leftnav: ['http://dl.dropbox.com/u/1933107/themes/detox/larr.png', -32, 80], rightnav: ['http://dl.dropbox.com/u/1933107/themes/detox/rarr.png', 0, 80]},
	statusvars: ['statusA', 'statusB', 'statusC'], //register 3 variables that contain current panel (start), current panel (last), and total panels
	contenttype: ['inline'] //content setting ['inline'] or ['ajax', 'path_to_external_file']
})
</script>
<script type="text/javascript">
featuredcontentglider.init({
	gliderid: "glidercontent",
	contentclass: "glidecontent",
	togglerid: "togglebox",
	remotecontent: "", 
	selected: 1,
	persiststate: true,
	speed: 1000,
	direction: "leftright", 
	autorotate: true, 
	autorotateconfig: [9000, 1] //if auto rotate enabled, set [milliseconds_btw_rotations, cycles_before_stopping]
})
</script>
<?php } ?>

</body>
</html>