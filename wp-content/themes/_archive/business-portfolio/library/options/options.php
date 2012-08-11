<?php

global $options, $options2, $options3, $bp_existed, $multi_site_on;

include( get_template_directory() . '/library/functions/conditional-functions.php' );

foreach ($options as $value) {
if (get_option( $value['id'] ) == FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); } }

foreach ($options2 as $value) {
if (get_option( $value['id'] ) == FALSE) { $value['id'] = $value['std']; } else { $value['id'] = get_option( $value['id'] ); } }

foreach ($options3 as $value) {
if (get_option( $value['id'] ) == FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); } }

?>