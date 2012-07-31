<?php get_header() ?>
<div id="profile-wrapper">
<div id="profile-background">

<?php bp_get_userbar() ?>
<?php bp_get_optionsbar() ?>
<div id="main">
<div class="content-header">
	<?php bp_blogs_blog_tabs() ?>
</div>

<div id="content">
	<h2><?php bp_word_or_name( __( "My Blogs", 'buddypress' ), __( "%s's Blogs", 'buddypress' ) ) ?></h2>
	<?php do_action( 'template_notices' ) // (error/success feedback) ?>

	<?php if ( bp_has_blogs() ) : ?>
		<ul id="blog-list" class="item-list">
		<?php while ( bp_blogs() ) : bp_the_blog(); ?>
			<li>
				<h4><a href="<?php bp_blog_permalink() ?>"><?php bp_blog_title() ?></a></h4>
				<p><?php bp_blog_description() ?></p>
			</li>
		<?php endwhile; ?>
		</ul>
	<?php else: ?>

		<div id="message" class="info">
			<p><?php bp_word_or_name( __( "You haven't created any blogs yet.", 'buddypress' ), __( "%s hasn't created any public blogs yet.", 'buddypress' ) ) ?> <?php bp_create_blog_link() ?> </p>
		</div>

	<?php endif;?>

</div>
</div> <!-- end #profile bg found in header -->
</div> <!-- end #profile wrapper found in header -->
<div id="profile-background-shade-bottom"></div>
</div><!-- end main found here-->
<?php get_footer() ?>