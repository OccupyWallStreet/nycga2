<?php if(bp_is_group()){ ?>
<div id="community-single-nav" class="widget-title" >
  <ul class="item-list">
  <h3 class="widgettitle"><?php _e( '@ Group', 'buddypress' ) ?></h3>
		<?php bp_get_options_nav() ?>
		<?php do_action( 'bp_group_options_nav' ) ?>
	</ul>

</div>	
<?php } ?>

<?php if(bp_is_member()){ ?>
<div id="community-single-nav" class="widget-title" >
  <ul class="item-list">
  <h3 class="widgettitle"><?php _e( '@ Member', 'buddypress' ) ?></h3>
  <?php bp_get_displayed_user_nav() ?>
		<?php do_action( 'bp_group_options_nav' ) ?>
	</ul>

</div>	
<?php } ?>