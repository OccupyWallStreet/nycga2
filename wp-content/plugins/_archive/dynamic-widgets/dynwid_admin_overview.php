<?php
/**
 * dynwid_admin_overview.php - Overview page
 *
 * @version $Id: dynwid_admin_overview.php 488903 2012-01-12 18:17:27Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	if ( isset($_GET['action']) ) {
		switch ( $_GET['action'] ) {
			case 'dynwid_set_method':
				if ( $_GET['oldmethod'] == 'on' ) {
					update_option('dynwid_old_method', TRUE);
				} else {
					update_option('dynwid_old_method', FALSE);
				}

				$text = __('Method set to', DW_L10N_DOMAIN) . ' ' . ( get_option('dynwid_old_method') ? '\''. __('OLD', DW_L10N_DOMAIN) .'\'' : '\'' . __('FILTER', DW_L10N_DOMAIN) . '\'' );
				DWMessageBox::create($text, '');
				break;

			case 'dynwid_set_page_limit':
				$limit = (int) $_GET['page_limit'];
				if ( $limit > 0 ) {
					update_option('dynwid_page_limit', $limit);
					$text = __('Page limit set to', DW_L10N_DOMAIN) . ' ' . $limit . '.';
					DWMessageBox::create($text, '');
				} else {
					$text = __('ERROR', DW_L10N_DOMAIN) . ': ' . strip_tags($_GET['page_limit']) . ' ' . __('is not a valid limit.', DW_L10N_DOMAIN);
					DWMessageBox::setTypeMsg('error');
					DWMessageBox::create($text, '');
				}
				break;

			case 'reset':
				check_admin_referer('plugin-name-action_reset_' . $_GET['id']);
				$DW->resetOptions($_GET['id']);
				DWMessageBox::create(__('Widget options have been reset to default.', DW_L10N_DOMAIN), '');
				break;
		} // switch
	}

	if ( isset($_GET['dynwid_save']) && $_GET['dynwid_save'] == 'yes' ) {
		$lead = __('Widget options saved.', DW_L10N_DOMAIN);
		$msg = '';
		DWMessageBox::create($lead, $msg);
	}

  foreach ( $DW->sidebars as $sidebar_id => $widgets ) {
    if ( count($widgets) > 0 ) {
      if ( $sidebar_id == 'wp_inactive_widgets' ) {
        $name = __('Inactive Widgets');
      } else {
        $name = $DW->getName($sidebar_id, 'S');
      }
?>

<div class="postbox-container" style="width:48%;margin-top:10px;margin-right:10px;">
<table cellspacing="0" class="widefat fixed">
	<thead>
	<tr>
	  <th class="managage-column" scope="col"><?php echo $name; ?></th>
	  <th style="width:70px">&nbsp;</th>
  </tr>
  </thead>

  <tbody class="list:link-cat" id="<?php echo str_replace('-', '_', $sidebar_id); ?>">
  <?php foreach ( $widgets as $widget_id ) {
          $name = $DW->getName($widget_id);
          // When $name is empty, we have a widget which not belongs here
          if (! empty($name) ) {
  ?>
  <tr>
    <td class="name">
      <p class="row-title"><a title="<?php _e('Edit this widget options', DW_L10N_DOMAIN); ?>" href="themes.php?page=dynwid-config&amp;action=edit&amp;id=<?php echo $widget_id; ?>"><?php echo $name; ?></a></p>
      <div class="row-actions">
       <span class="edit">
          <a title="<?php _e('Edit this widget options', DW_L10N_DOMAIN); ?>" href="themes.php?page=dynwid-config&amp;action=edit&amp;id=<?php echo $widget_id; ?>"><?php _e('Edit'); ?></a>
        </span>
        <?php if ( $DW->hasOptions($widget_id) ) { ?>
        <span class="delete">
        <?php $href = wp_nonce_url('themes.php?page=dynwid-config&amp;action=reset&amp;id=' . $widget_id, 'plugin-name-action_reset_' . $widget_id); ?>
          | <a class="submitdelete" title="<?php _e('Reset widget to Static', DW_L10N_DOMAIN); ?>" onclick="if ( confirm('You are about to reset this widget \'<?php echo strip_tags($DW->getName($widget_id)); ?>\'\n \'Cancel\' to stop, \'OK\' to reset.') ) { return true;}return false;" href="<?php echo $href; ?>"><?php _e('Reset', DW_L10N_DOMAIN); ?></a>
        </span>
        <?php } ?>
      </div>
    </td>
    <td>
      <?php echo ( $DW->hasOptions($widget_id) ) ? __('Dynamic', DW_L10N_DOMAIN) : __('Static', DW_L10N_DOMAIN); ?>
    </td>
  </tr>
  <?php   } // END if (! empty($name) ) ?>
  <?php } // END foreach ( $widgets as $widget_id ) ?>
  </tbody>
 </table>
 </div>
<?php
    } // END if ( count($widgets) > 0 )
  } // END foreach ( $DW->sidebars as $sidebar_id => $widgets )
?>

<div class="clear"><br /><br /></div>

<a href="#" onclick="jQuery('#un').slideToggle('fast'); return false;"><?php _e('Advanced', DW_L10N_DOMAIN); ?> &gt;</a>
<div id="un" style="display:none">
<br />

<!-- wp_head() check //-->
<strong><?php _e('wp_head() check:', DW_L10N_DOMAIN); ?> </strong>
<?php
  $c = $DW->checkWPhead();
  switch ( $c ) {
    case 0:
      echo '<span style="color:red">' . __('wp_head() is NOT called (at the most obvious place)', DW_L10N_DOMAIN) . '</span>';
      break;

    case 1:
      echo '<span style="color:green">' . __('wp_head() is called', DW_L10N_DOMAIN) . '</span>';
      break;

    case 2:
      echo '<span style="color:orange">' . __('Unable to determine if wp_head() is called', DW_L10N_DOMAIN) . '</span>';
      break;
  }
?>
.<br />
<br />

<!-- method //-->
<div id="method">
	<form id="dynwid_method" action="" method="get">
		<input type="hidden" name="page" value="dynwid-config" />
		<input type="hidden" name="action" value="dynwid_set_method" />
		<input type="checkbox" id="oldmethod" name="oldmethod" <?php echo ( get_option('dynwid_old_method') ? 'checked="checked"' : '' ) ?> onchange="jQuery('#dynwid_method').submit();" /> <label for="oldmethod"><?php _e('Use \'OLD\' method', DW_L10N_DOMAIN); ?></label>
</form>
</div>
<br />

<!-- page limit //-->
<div id ="page_limit">
<form action="" method="get">
<input type="hidden" name="page" value="dynwid-config" />
<input type="hidden" name="action" value="dynwid_set_page_limit" />
<b><?php _e('Page limit', DW_L10N_DOMAIN) ?></b>: <input type="text" name="page_limit" value="<?php echo ( isset($_GET['page_limit']) ) ? $_GET['page_limit'] : DW_PAGE_LIMIT; ?>" style="width:50px" maxlength="4" /> <input class="button-primary" type="submit" value="<? _e('Save'); ?>" />
<br />
<?php _e('The page limit sets the limit of number of pages to prevent a timeout when building the hierarchical tree. When the number of pages is above this limit, a flat list will be displayed which is less time consuming.', DW_L10N_DOMAIN); ?>
<br />
<?php echo __('Currently the number of pages is', DW_L10N_DOMAIN) . ' ' . count(get_pages()); ?>.
</form>
</div>
<br />

<?php if ( defined('WPSC_TABLE_PRODUCT_CATEGORIES') ) { ?>
<!-- WPEC dump //--><br /><br />
<?php _e('When upgrading to WPEC 3.8 the Dynamic Widgets rules for WPEC are lost. The WPEC dump gives you an overview of the rules before the update.'); ?><br />
<span style="color:red;font-weight:bold;"><?php _e('WARNING', DW_L10N_DOMAIN); ?></span> <?php _e('This only works correct when you did not add or change anything in the Dynamic Widgets rules.', DW_L10N_DOMAIN); ?>
<br /><br />
<div id="wpec_dump">
  <form action="" method="get">
    <input type="hidden" name="action" value="wpec_dump" />
    <input class="button-primary" type="submit" value="<?php _e('Create WPEC dump', DW_L10N_DOMAIN); ?>" />
  </form>
</div>
<br /><br />
<?php } ?>

<!-- dump //-->
<?php _e('For debugging purposes it is possible you\'re asked to create a dump. Click the \'Create dump\' button and save the text file.', DW_L10N_DOMAIN); ?>
<br /><br />
<div id="dump">
  <form action="" method="get">
    <input type="hidden" name="action" value="dynwid_dump" />
    <input class="button-primary" type="submit" value="<?php _e('Create dump', DW_L10N_DOMAIN); ?>" />
  </form>
</div>

<br /><br />

<!-- uninstall //-->
<?php _e('When you deceide not to use this plugin anymore (sorry to hear that!). You can cleanup all settings and data related to this plugin by clicking on the \'Uninstall\' button. This process is irreversible! After the cleanup the plugin is deactivated automaticly.', DW_L10N_DOMAIN); ?>
<br /><br />
<div id="uninstall">
  <form action="" method="get">
    <input type="hidden" name="action" value="dynwid_uninstall" />
    <input class="button-primary" type="submit" value="<?php _e('Uninstall', DW_L10N_DOMAIN); ?>" onclick="if ( confirm('Are you sure you want to uninstall Dynamic Widgets?') ) { return true; } return false;" />
  </form>
</div>
</div>