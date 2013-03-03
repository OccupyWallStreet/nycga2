<?php
/*
 * Are You A Human
 * PHP Integration Library
 * 
 * @version 1.0.2
 * 
 *    - Documentation and latest version
 *          http://portal.areyouahuman.com/help
 *    - Get an AYAH Publisher Key
 *          https://portal.areyouahuman.com
 *    - Discussion group
 *          http://getsatisfaction.com/areyouahuman
 *
 * Copyright (c) 2011 AYAH LLC -- http://www.areyouahuman.com
 * AUTHORS:
 *   Jonathan Brown - jonathan@areyouahuman.com
 *   Sneaky Pete - sneakypete@areyouahuman.com
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 *  History
 * 
 *  1.0.2: 03/21/2012:
 *      - Changed the script tag to include language="JavaScript" and type="text/javascript" for maximum compatibility.
 *
 *  1.0.1: 12/12/2011:
 *      - Add fallback support for JSON decode with Michal Migurski's library
 *      - Add fallback support when there is no cURL library
 *      - Changed the handling for the constants so that, if they don't exist, it doesn't freak out
 *      - Changed the handling for ayah_config.php so that, if it doesn't exist, it doesn't freak out
 * 
 *  1.0.0: 11/15/2011: Initial release with script-implementation support
 * 
 * 
 */

class AYAH {
	protected $ayah_publisher_key;
	protected $ayah_scoring_key;
	protected $ayah_web_service_host;
	protected $session_secret;
	
	/**
	 * Returns the markup for the PlayThru
	 *
	 * @return string
	 */
	public function getPublisherHTML() {
		$url = 'https://' . $this->ayah_web_service_host . "/ws/script/" . 
			urlencode($this->ayah_publisher_key);
			
		return "<div id='AYAH'></div><script src='". $url ."' type='text/javascript' language='JavaScript'></script>";
	}
	
	/**
	 * Check whether the user is a human
	 * Wrapper for the scoreGame API call
	 *
	 * @return boolean
	 */
	public function scoreResult() {
		$result = false;
		if ($this->session_secret) {
			$fields = array(
				'session_secret' => urlencode($this->session_secret),
				'scoring_key' => $this->ayah_scoring_key
			);
			$resp = $this->doHttpsPostReturnJSONArray($this->ayah_web_service_host, "/ws/scoreGame", $fields);
			if ($resp) {
				$result = ($resp->status_code == 1);
			}
		}

		return $result;
	}
	
	/**
	 * Records a conversion
	 * Called on the goal page that A and B redirect to
	 * A/B Testing Specific Function
	 *
	 * @return boolean
	 */
	public function recordConversion(){
		if( isset( $this->session_secret ) ){
			return '<iframe style="border: none;" height="0" width="0" src="https://' . 
				$this->ayah_web_service_host . '/ws/recordConversion/'.
				urlencode($this->session_secret) . '"></iframe>';
		} else {
			error_log('AYAH::recordConversion - AYAH Conversion Error: No Session Secret');
			return FALSE;
		}
	}


	/**
	 * Constructor
	 * If the session secret exists in input, it grabs it
	 * @param $params associative array with keys publisher_key, scoring_key, web_service_host
	 *
	 */
	public function __construct($params = array()) {
		if(array_key_exists("session_secret", $_REQUEST)){
			$this->session_secret = $_REQUEST["session_secret"];
		}

		// Set them to defaults
		$this->ayah_publisher_key = "";
		$this->ayah_scoring_key = "";
		$this->ayah_web_service_host = "ws.areyouahuman.com";
	
		// If the constants exist, override with those
		if (defined('AYAH_PUBLISHER_KEY')) {
			$this->ayah_publisher_key = AYAH_PUBLISHER_KEY;
		}

		if (defined('AYAH_SCORING_KEY')) {
			$this->ayah_scoring_key = AYAH_SCORING_KEY;
		}		

		if (defined('AYAH_WEB_SERVICE_HOST')) {
			$this->ayah_web_service_host = AYAH_WEB_SERVICE_HOST;
		}

		// Lastly grab the parameters input and save them
		foreach (array_keys($params) as $key) {
			if (in_array($key, array("publisher_key", "scoring_key", "web_service_host"))) {
				$variable = "ayah_" . $key;
				$this->$variable = $params[$key];
			} else {
				error_log("AYAH::__construct: Unrecognized key for constructor param: $key");
			}
		}

		// Generate some warnings if a foot shot is coming
		if ($this->ayah_publisher_key == "") {
			error_log("AYAH::__construct: Warning: Publisher key is not defined.  This won't work.");
		}

		if ($this->ayah_scoring_key == "") {
			error_log("AYAH::__construct: Warning: Scoring key is not defined.  This won't work.");
		}

		if ($this->ayah_web_service_host == "") {
			error_log("AYAH::__construct: Warning: Web service host is not defined.  This won't work.");
		}

	}

	/**
 	 * Do a HTTPS POST, return some JSON decoded as array (Internal function)
	 * @param $host hostname
 	 * @param $path path
	 * @param $fields associative array of fields
	 * return JSON decoded data structure or empty data structure
	 */
  protected function doHttpsPostReturnJSONArray($hostname, $path, $fields) {
		
		$result = $this->doHttpsPost($hostname, $path, $fields);

		if ($result) {
			$result = $this->doJSONArrayDecode($result);
		} else {
			error_log("AYAH::doHttpsPostGetJSON: Post to https://$hostname$path returned no result.");
			$result = array();
		}
		
		return $result;
	}

	// Internal function; does an HTTPS post
	protected function doHttpsPost($hostname, $path, $fields) {
		$result = "";
		// URLencode the post string
		$fields_string = "";
		foreach($fields as $key=>$value) { 
			if (is_array($value)) {
				foreach ($value as $v) {
					$fields_string .= $key . '[]=' . $v . '&';
				}
			} else {
				$fields_string .= $key.'='.$value.'&'; 
			}
		}
		rtrim($fields_string,'&');

		// cURL or something else
		if (function_exists('curl_init')) {
			$curlsession = curl_init();		
			curl_setopt($curlsession,CURLOPT_URL,"https://" . $hostname . $path);
			curl_setopt($curlsession,CURLOPT_POST,count($fields));
			curl_setopt($curlsession,CURLOPT_POSTFIELDS,$fields_string);
			curl_setopt($curlsession,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($curlsession,CURLOPT_SSL_VERIFYHOST,0);
			curl_setopt($curlsession,CURLOPT_SSL_VERIFYPEER,false);
			$result = curl_exec($curlsession);
		} else {	
			error_log("No cURL support.");

			// Build a header
			$http_request  = "POST $path HTTP/1.1\r\n";
			$http_request .= "Host: $hostname\r\n";
			$http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
			$http_request .= "Content-Length: " . strlen($fields_string) . "\r\n";
			$http_request .= "User-Agent: AreYouAHuman/PHP 1.0.2\r\n";
			$http_request .= "Connection: Close\r\n";
			$http_request .= "\r\n";
			$http_request .= $fields_string ."\r\n";

			$result = '';
			$errno = $errstr = "";
			$fs = fsockopen("ssl://" . $hostname, 443, $errno, $errstr, 10);
			if( false == $fs ) {
				error_log('Could not open socket');
			} else {
				fwrite($fs, $http_request);
				while (!feof($fs)) {
					$result .= fgets($fs, 4096);
				}

				$result = explode("\r\n\r\n", $result, 2);
				$result = $result[1];
			}		
		}

		return $result;
	}

	// Internal function: does a JSON decode of the string
	protected function doJSONArrayDecode($string) {
		$result = array();

		if (function_exists("json_decode")) {
			try {
				$result = json_decode( $string);
			} catch (Exception $e) { 
				error_log("AYAH::doJSONArrayDecode() - Exception when calling json_decode: " . $e->getMessage());
				$result = null;
			}
		} elseif (file_Exists("json.php")) {
			$json = new Services_JSON();
			$result = $json->decode($string);

			if (!is_array($result)) {
				error_log("AYAH::doJSONArrayDecode: Expected array; got something else: $result");
				$result = array();
			}
		} else {
			error_log("AYAH::doJSONArrayDecode: No JSON decode function available.");
		}

		return $result;
	}
}
