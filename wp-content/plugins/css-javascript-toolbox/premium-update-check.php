<?php
/**
* @version $Id ; premium-update-check.php 080 00:00:00 28-01-2012 Ahmed Said $
*/

/**
* Avoid direct calls to this file where wp core files not present
*/
if (!function_exists ('add_action')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

/**
* CjToolbox premium version checker.
* 
* @author Ahmed Said
*/
abstract class CJTPremiumUpdate {
	
	/**
	* Run once everyday at the time the Plugin activated.
	* Callback for wp_schedule_event.
	*/
	public static function check() {
		// Ask the server for updates.
		$response = wp_remote_get(cssJSToolbox::CHECK_UPDATE_URL);
		// If check success, store the response.
		if (is_array($response)) { // response = WP_Error on failure.
			// We need only response array.
			$response = @unserialize($response['body']);
			// Make sure that we got array from the server.
			if (is_array($response['response'])) {
				$response = $response['response'];
				$currentVersion = is_array(cssJSToolbox::$premiumUpgradeTransient) ? cssJSToolbox::$premiumUpgradeTransient['version'] : '';
				$newVersion = $response['version'];
				// Upload and update image URL only if current transient version
				// is different from the new version.
				if ($currentVersion != $newVersion) {
					// Get image from URL.
					$image = wp_remote_get($response['imageURL']);
					// $image = WP_Error on failure.
					if (is_array($image)) { 
						$imagesUploadPath = CJTOOLBOX_PATH . '/public/media/' . cssJSToolbox::IMAGES_UPLOAD_DIR;	
						$imageStream = $image['body'];
						$imageName = basename($response['imageURL']);
						$imageFile = "{$imagesUploadPath}/{$imageName}";
						$localImageLength = @file_put_contents($imageFile, $imageStream);
						// If file is created change the URL of the image to the local image.
						if ($localImageLength == strlen($imageStream)) {
							$response['imageURL'] = CJTOOLBOX_MEDIA_URL . '/' . cssJSToolbox::IMAGES_UPLOAD_DIR . "/{$imageName}";
							// Store the response/transient with the new ImageURL, make sense!
						}
					}
				}
				// Update current transient.
				set_site_transient('cjt_premium_upgrade', $response);
				cssJSToolbox::$premiumUpgradeTransient = $response;
			}
		}
	}
	
} // End class.