<?php
/*
 * Update functions
 */

/**
 * Main upgrade function.
 */
function wpcf_upgrade() {
    $upgrade_failed = false;
    $upgrade_debug = array();
    $version = get_option('wpcf-version', false);
    if (empty($version)) {
        $version = WPCF_VERSION;
    }
    if (version_compare($version, WPCF_VERSION, '<')) {
        $first_step = str_replace('.', '', $version);
        $last_step = str_replace('.', '', WPCF_VERSION);
        for ($index = $first_step; $index <= $last_step; $index++) {
            if (function_exists('wpcf_upgrade_' . $index)) {
                $response = call_user_func('wpcf_upgrade_' . $index);
                if ($response !== true) {
                    $upgrade_failed = true;
                    $upgrade_debug[$first_step][$index] = $response;
                }
            }
        }
    }
    if ($upgrade_failed == true) {
        update_option('wpcf_upgrade_debug', $upgrade_debug);
        // @todo Add perm message to display for admin
    }
    update_option('wpcf-version', WPCF_VERSION);
}