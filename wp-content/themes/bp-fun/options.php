<?php
global $options, $bp_existed, $bp_front_is_activity, $multi_site_on;

load_template( TEMPLATEPATH . '/_inc/functions/conditional-functions.php' );

foreach ($options as $value) {
if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); } }

?>