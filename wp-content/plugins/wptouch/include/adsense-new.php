<?php global $wptouch_settings; global $wpdb; ?>
<?php echo '<div id="adsense-area">'; ?>
<script type="text/javascript"><!--
window.googleAfmcRequest = {
  client: 'ca-mb-<?php echo $wptouch_settings['adsense-id']; ?>',
  ad_type: 'text_image',
  output: 'html',
<?php if ( !isset( $wptouch_settings['adsense-channel'] ) ) { ?>
  channel: '',
<?php } else { ?>
  channel: '<?php echo $wptouch_settings['adsense-channel']; ?>',
<?php } ?>
  format: '320x50_mb',
<?php if ( $wpdb->charset ) { ?>
  oe: '<?php echo $wpdb->charset; ?>',
<?php } else { ?>
  oe: 'utf8',
<?php } ?>
};
//--></script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_afmc_ads.js"></script>
<?php echo '</div>'; ?>