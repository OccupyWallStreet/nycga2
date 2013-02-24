<?php do_action( 'bp_before_sidebar' ) ?>

<div id="leftsidebar" class="widgetarea">
  <div class="paddersidebar left-sidebar-padder">
  <?php if(defined('BP_VERSION')){ ?>
      <?php if( ! dynamic_sidebar( 'leftsidebar' )) : ?>
      <?php widget_community_nav( 'leftsidebar' ); ?>
      <?php endif; // end primary widget area ?>
  <?php } else {?>
      <?php if( ! dynamic_sidebar( 'leftsidebar' )) : ?>
      <?php endif ?>  
  <?php } ?>
  </div><!-- #paddersidebar -->	
</div><!-- #leftsidebar -->
<div class="v_line v_line_left visible-desktop"></div>

<?php do_action( 'bp_after_sidebar' ) ?>
