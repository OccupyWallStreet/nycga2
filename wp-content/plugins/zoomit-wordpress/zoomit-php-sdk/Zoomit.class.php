<?php

// Prerequisites
if (!function_exists('curl_init'))
    throw new Exception('Zoom.it needs the cURL PHP extension.');

if (!function_exists('json_decode'))
    throw new Exception('Zoom.it needs the JSON PHP extension.');

/**
 * Provides access to the Zoom.it API.
 *
 * @author Daniel Gasienica <daniel@gasienica.ch>
 */
class Zoomit
{
    /**
     * Version.
     */
    const VERSION = '1.0';

    /**
     * Default options for cURL.
     */
    public static $CURL_OPTS = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        // CURLOPT_FAILONERROR => true, // fail silently for status codes >= 400
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_USERAGENT      => 'zoomit-php-1.0',
    );

    /**
     * @param String url Target URL
     * @return ContentInfo ContentInfo object for given URL if possible,
     *                     null otherwise.
     */
    public function getContentInfoByURL($url)
    {
        return json_decode($this->load("http://api.zoom.it/v1/content/?url=" . urlencode($url)), true);
    }

    /**
     * @param String id Zoom.it ID
     * @return ContentInfo ContentInfo object for given ID if it exists,
     *                     null otherwise.
     */
    public function getContentInfoByID($id)
    {
        return json_decode($this->load("http://api.zoom.it/v1/content/" . $id), true);
    }

    /**
     * @private
     */
    private function load($url)
    {
        $curl_handle = curl_init();
        $opts = self::$CURL_OPTS;
        $opts[CURLOPT_URL] = $url;
        curl_setopt_array($curl_handle, $opts);
        $result = curl_exec($curl_handle);

        // cURL error
        if ($result === false)
        {
            $exception = new Exception(curl_error($curl_handle),
                                       curl_errno($curl_handle));
            curl_close($curl_handle);
            throw $exception;
        }

        // HTTP error
        $http_status_code = intval(curl_getinfo($curl_handle,
                                                CURLINFO_HTTP_CODE));
        if ($http_status_code >= 400)
        {
            $exception = new Exception($result, $http_status_code);
            curl_close($curl_handle);
            throw $exception;
        }

        curl_close($curl_handle);
        return $result;
    }
}

?>