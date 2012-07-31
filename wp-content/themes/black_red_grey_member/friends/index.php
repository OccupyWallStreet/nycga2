<?php get_header() ?>
<div id="profile-wrapper">
<div id="profile-background">

<?php bp_get_userbar() ?>
<?php bp_get_optionsbar() ?>
<div id="main">
<div class="content-header">
	<ul class="content-header-nav">
		<?php bp_friends_header_tabs() ?>
	</ul>
</div>

<div id="content">
	<h2><?php bp_word_or_name( __( "My Friends", 'buddypress' ), __( "%s's Friends", 'buddypress' ) ) ?> &raquo; <?php bp_friends_filter_title() ?></h2>
	
	<div class="left-menu">
		<?php bp_friend_search_form() ?>
	</div>
	
	<div class="main-column">
		<?php do_action( 'template_notices' ) // (error/success feedback) ?>
		
		<?php load_template( TEMPLATEPATH . '/friends/friends-loop.php') ?>
	</div>
</div>
</div> <!-- end #profile bg found in header -->
</div> <!-- end #profile wrapper found in header -->
<div id="profile-background-shade-bottom"></div>
</div><!-- end main found here-->
<?php get_footer() ?>