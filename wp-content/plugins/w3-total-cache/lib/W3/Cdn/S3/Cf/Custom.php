<?php

/**
 * Amazon CloudFront (Custom origin) CDN engine
 */
if (!defined('ABSPATH')) {
    die();
}

require_once W3TC_LIB_W3_DIR . '/Cdn/S3/Cf.php';

class W3_Cdn_S3_Cf_Custom extends W3_Cdn_S3_Cf {
    var $type = W3TC_CDN_CF_TYPE_CUSTOM;
}
