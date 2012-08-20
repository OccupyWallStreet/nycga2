<?php
function socialize_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'service' => 'something'
	), $atts ) );
        
        switch ($service) {
            case 'twitter':
                return SocializeServices::createSocializeTwitter();
                break;
            case 'facebook':
                return SocializeServices::createSocializeFacebook();
                break;
            case 'digg':
                return SocializeServices::createSocializeDigg();
                break;
            case 'sphinn':
                return SocializeServices::createSocializeSphinn();
                break;
            case 'reddit':
                return SocializeServices::createSocializeReddit();
                break;
            case 'dzone':
                return SocializeServices::createSocializeDzone();
                break;
            case 'stumbleupon':
                return SocializeServices::createSocializeStumble();
                break;
            case 'delicious':
                return SocializeServices::createSocializeDelicous();
                break;
            case 'buffer':
                return SocializeServices::createSocializeBuffer();
                break;
            case 'linkedin':
                return SocializeServices::createSocializeLinkedIn();
                break;
            case 'googleplus':
                return SocializeServices::createSocializePlusOne();
                break;
            case 'pinterest':
                return SocializeServices::createSocializePinterest();
                break;
        }
}
add_shortcode( 'socialize', 'socialize_shortcode' );



?>
