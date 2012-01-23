<?php

/**
 * Windows Azure Storage CDN engine
 */
if (!defined('W3TC')) {
    die();
}

require_once W3TC_LIB_W3_DIR . '/Cdn/Base.php';

/**
 * Class W3_Cdn_Azure
 */
class W3_Cdn_Azure extends W3_Cdn_Base {
    /**
     * Storage client object
     *
     * @var Microsoft_WindowsAzure_Storage_Blob
     */
    var $_client = null;

    /**
     * PHP5 Constructor
     *
     * @param array $config
     */
    function __construct($config = array()) {
        $config = array_merge(array(
            'user' => '',
            'key' => '',
            'container' => '',
            'cname' => array(),
        ), $config);

        parent::__construct($config);
    }

    /**
     * PHP4 Constructor
     *
     * @param array $config
     */
    function W3_Cdn_Azure($config = array()) {
        $this->__construct($config);
    }

    /**
     * Inits storage client object
     *
     * @param string $error
     * @return boolean
     */
    function _init(&$error) {
        if (empty($this->_config['user'])) {
            $error = 'Empty account name.';

            return false;
        }

        if (empty($this->_config['key'])) {
            $error = 'Empty account key.';

            return false;
        }

        if (empty($this->_config['container'])) {
            $error = 'Empty container name.';

            return false;
        }

        set_include_path(get_include_path() . PATH_SEPARATOR . W3TC_LIB_DIR);

        require_once 'Microsoft/WindowsAzure/Storage/Blob.php';

        $this->_client = new Microsoft_WindowsAzure_Storage_Blob(
            Microsoft_WindowsAzure_Storage::URL_CLOUD_BLOB,
            $this->_config['user'],
            $this->_config['key'],
            false,
            Microsoft_WindowsAzure_RetryPolicy_RetryPolicyAbstract::noRetry()
        );

        return true;
    }

    /**
     * Uploads files to S3
     *
     * @param array $files
     * @param array $results
     * @param boolean $force_rewrite
     * @return boolean
     */
    function upload($files, &$results, $force_rewrite = false) {
        $error = null;

        if (!$this->_init($error)) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, $error);

            return false;
        }

        foreach ($files as $local_path => $remote_path) {
            $results[] = $this->_upload($local_path, $remote_path, $force_rewrite);

            if ($this->_config['compression'] && $this->_may_gzip($remote_path)) {
                $remote_path_gzip = $remote_path . $this->_gzip_extension;

                $results[] = $this->_upload_gzip($local_path, $remote_path_gzip, $force_rewrite);
            }
        }

        return !$this->_is_error($results);
    }

    /**
     * Uploads file
     *
     * @param string $local_path
     * @param string $remote_path
     * @param bool $force_rewrite
     * @return array
     */
    function _upload($local_path, $remote_path, $force_rewrite = false) {
        if (!file_exists($local_path)) {
            return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Source file not found.');
        }

        $md5 = @md5_file($local_path);
        $content_md5 = $this->_get_content_md5($md5);

        if (!$force_rewrite) {
            try {
                $properties = $this->_client->getBlobProperties($this->_config['container'], $remote_path);
                $size = @filesize($local_path);

                if ($size === (int) $properties->Size && $content_md5 === $properties->ContentMd5) {
                    return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'File up-to-date.');
                }
            } catch (Exception $exception) {
            }
        }

        $headers = $this->_get_headers($local_path);
        $headers = array_merge($headers, array(
            'Content-MD5' => $content_md5
        ));

        try {
            $this->_client->putBlob($this->_config['container'], $remote_path, $local_path, array(), null, $headers);
        } catch (Exception $exception) {
            return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf('Unable to put blob (%s).', $exception->getMessage()));
        }

        return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'OK');
    }

    /**
     * Uploads gzipped file
     *
     * @param string $local_path
     * @param string $remote_path
     * @param bool $force_rewrite
     * @return array
     */
    function _upload_gzip($local_path, $remote_path, $force_rewrite = false) {
        if (!function_exists('gzencode')) {
            return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, "GZIP library doesn't exists.");
        }

        if (!file_exists($local_path)) {
            return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Source file not found.');
        }

        $contents = @file_get_contents($local_path);

        if ($contents === false) {
            return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Unable to read file.');
        }

        $data = gzencode($contents);
        $md5 = md5($data);
        $content_md5 = $this->_get_content_md5($md5);

        if (!$force_rewrite) {
            try {
                $properties = $this->_client->getBlobProperties($this->_config['container'], $remote_path);
                $size = @filesize($local_path);

                if ($size === (int) $properties->Size && $content_md5 === $properties->ContentMd5) {
                    return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'File up-to-date.');
                }
            } catch (Exception $exception) {
            }
        }

        $headers = $this->_get_headers($local_path);
        $headers = array_merge($headers, array(
            'Content-MD5' => $content_md5,
            'Content-Encoding' => 'gzip'
        ));

        try {
            $this->_client->putBlobData($this->_config['container'], $remote_path, $data, array(), null, $headers);
        } catch (Exception $exception) {
            return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf('Unable to put blob (%s).', $exception->getMessage()));
        }

        return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'OK');
    }

    /**
     * Deletes files from storage
     *
     * @param array $files
     * @param array $results
     * @return boolean
     */
    function delete($files, &$results) {
        $error = null;

        if (!$this->_init($error)) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, $error);

            return false;
        }

        foreach ($files as $local_path => $remote_path) {
            try {
                $this->_client->deleteBlob($this->_config['container'], $remote_path);
                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'OK');
            } catch (Exception $exception) {
                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf('Unable to delete blob (%s).', $exception->getMessage()));
            }

            if ($this->_config['compression']) {
                $remote_path_gzip = $remote_path . $this->_gzip_extension;

                try {
                    $this->_client->deleteBlob($this->_config['container'], $remote_path_gzip);
                    $results[] = $this->_get_result($local_path, $remote_path_gzip, W3TC_CDN_RESULT_OK, 'OK');
                } catch (Exception $exception) {
                    $results[] = $this->_get_result($local_path, $remote_path_gzip, W3TC_CDN_RESULT_ERROR, sprintf('Unable to delete blob (%s).', $exception->getMessage()));
                }
            }
        }

        return !$this->_is_error($results);
    }

    /**
     * Tests S3
     *
     * @param string $error
     * @return boolean
     */
    function test(&$error) {
        if (!parent::test($error)) {
            return false;
        }

        $string = 'test_azure_' . md5(time());

        if (!$this->_init($error)) {
            return false;
        }

        try {
            $containers = $this->_client->listContainers();
        } catch (Exception $exception) {
            $error = sprintf('Unable to list containers (%s).', $exception->getMessage());

            return false;
        }

        $container = null;

        foreach ((array) $containers as $_container) {
            if ($_container->Name == $this->_config['container']) {
                $container = $_container;
                break;
            }
        }

        if (!$container) {
            $error = sprintf('Container doesn\'t exist: %s.', $this->_config['container']);

            return false;
        }

        try {
            $this->_client->putBlobData($this->_config['container'], $string, $string);
        } catch (Exception $exception) {
            $error = sprintf('Unable to put blob data (%s).', $exception->getMessage());

            return false;
        }

        try {
            $data = $this->_client->getBlobData($this->_config['container'], $string);
        } catch (Exception $exception) {
            $error = sprintf('Unable to get blob data (%s).', $exception->getMessage());

            return false;
        }

        if ($data != $string) {
            try {
                $this->_client->deleteBlob($this->_config['container'], $string);
            } catch (Exception $exception) {
            }

            $error = 'Blob datas are not equal.';

            return false;
        }

        try {
            $this->_client->deleteBlob($this->_config['container'], $string);
        } catch (Exception $exception) {
            $error = sprintf('Unable to delete blob (%s).', $exception->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Returns CDN domain
     *
     * @return array
     */
    function get_domains() {
        if (!empty($this->_config['cname'])) {
            return (array) $this->_config['cname'];
        } elseif (!empty($this->_config['user'])) {
            $domain = sprintf('%s.blob.core.windows.net', $this->_config['user']);

            return array(
                $domain
            );
        }

        return array();
    }

    /**
     * Returns via string
     *
     * @return string
     */
    function get_via() {
        return sprintf('Windows Azure Storage: %s', parent::get_via());
    }

    /**
     * Creates bucket
     *
     * @param string $container_id
     * @param string $error
     * @return boolean
     */
    function create_container(&$container_id, &$error) {
        if (!$this->_init($error)) {
            return false;
        }

        try {
            $containers = $this->_client->listContainers();
        } catch (Exception $exception) {
            $error = sprintf('Unable to list containers (%s).', $exception->getMessage());

            return false;
        }

        if (in_array($this->_config['container'], (array) $containers)) {
            $error = sprintf('Container already exists: %s.', $this->_config['container']);

            return false;
        }

        try {
            $this->_client->createContainer($this->_config['container']);
            $this->_client->setContainerAcl($this->_config['container'], Microsoft_WindowsAzure_Storage_Blob::ACL_PUBLIC_BLOB);
        } catch (Exception $exception) {
            $error = sprintf('Unable to create container: %s (%s)', $this->_config['container'], $exception->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Returns Content-MD5 header value
     *
     * @param string $string
     * @return string
     */
    function _get_content_md5($md5) {
        return base64_encode(pack('H*', $md5));
    }

    /**
     * Formats object URL
     *
     * @param string $path
     * @return string
     */
    function _format_url($path) {
        $domain = $this->get_domain($path);

        if ($domain && !empty($this->_config['container'])) {
            $scheme = $this->_get_scheme();
            $url = sprintf('%s://%s/%s/%s', $scheme, $domain, $this->_config['container'], $path);

            return $url;
        }

        return false;
    }

    /**
     * Returns array of headers
     *
     * @param string $file
     * @return array
     */
    function _get_headers($file) {
        $allowed_headers = array(
            'Content-Length',
            'Content-Type',
            'Content-Encoding',
            'Content-Language',
            'Content-MD5',
            'Cache-Control',
        );

        $headers = parent::_get_headers($file);

        foreach ($headers as $header => $value) {
            if (!in_array($header, $allowed_headers)) {
                unset($headers[$header]);
            }
        }

        return $headers;
    }
}
