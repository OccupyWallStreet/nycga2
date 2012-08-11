<div id="mow">
<h5><?php _e( 'Browse our members', 'Detox') ?></h5>


<div id="mygallery" class="stepcarousel">

<div class="belt">
<?php if ( bp_has_members() ) : ?>
<?php do_action( 'bp_before_directory_members_list' ) ?>
<?php while ( bp_members() ) : bp_the_member(); ?>

<div class="panel">
<div class="lead">
<a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar('type=full&amp;width=70&amp;height=70') ?></a>
</div>

<h4><a href="<?php bp_member_permalink() ?>"><?php bp_member_name() ?></a></h4>
<div class="read"><a href="<?php bp_member_permalink() ?>">View profile</a></div>

</div>

<?php do_action( 'bp_directory_members_item' ) ?>
<?php endwhile; ?>
<?php do_action( 'bp_after_directory_members_list' ) ?>
<?php bp_member_hidden_fields() ?>
<?php else: ?>

<div class="panel">
<div class="lead">
<img src="<?php bloginfo('stylesheet_directory'); ?>/images/detox.png" alt="no members" />
<div class="read"><?php _e( "Sorry, no members were found.", 'buddypress' ) ?></div>
</div>
</div>

<?php endif; ?>

</div>
</div>
</div>