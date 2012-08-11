<?php

/*

  FILE STRUCTURE:

- FONTS CLASS
- MENU DESCRIPTION CLASS

*/

/* FONTS CLASS (originally used in Thesis theme) */
/*------------------------------------------------------------------*/

class Fonts {
	function set_fonts() {
		$this->fonts = array(
			'arial' => array(
				'name' => 'Arial',
				'family' => 'Arial, "Helvetica Neue", Helvetica, sans-serif',
				'web_safe' => true,
				'monospace' => false
			),
			'arial_black' => array(
				'name' => 'Arial Black',
				'family' => '"Arial Black", "Arial Bold", Arial, sans-serif',
				'web_safe' => true,
				'monospace' => false
			),
			'arial_narrow' => array(
				'name' => 'Arial Narrow',
				'family' => '"Arial Narrow", Arial, "Helvetica Neue", Helvetica, sans-serif',
				'web_safe' => true,
				'monospace' => false
			),
			'courier_new' => array(
				'name' => 'Courier New',
				'family' => '"Courier New", Courier, Verdana, sans-serif',
				'web_safe' => true,
				'monospace' => true
			),
			'georgia' => array(
				'name' => 'Georgia',
				'family' => 'Georgia, "Times New Roman", Times, serif',
				'web_safe' => true,
				'monospace' => false
			),
			'tahoma' => array(
				'name' => 'Tahoma',
				'family' => 'Tahoma, Geneva, Verdana, sans-serif',
				'web_safe' => true,
				'monospace' => false
			),
			'times_new_roman' => array(
				'name' => 'Times New Roman',
				'family' => '"Times New Roman", Times, Georgia, serif',
				'web_safe' => true,
				'monospace' => false
			),
			'trebuchet_ms' => array(
				'name' => 'Trebuchet MS',
				'family' => '"Trebuchet MS", "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", Arial, sans-serif',
				'web_safe' => true,
				'monospace' => false
			),
			'verdana' => array(
				'name' => 'Verdana',
				'family' => 'Verdana, sans-serif',
				'web_safe' => true,
				'monospace' => false
			),
			'andale' => array(
				'name' => 'Andale Mono',
				'family' => '"Andale Mono", Consolas, Monaco, Courier, "Courier New", Verdana, sans-serif',
				'web_safe' => false,
				'monospace' => true
			),
			'baskerville' => array(
				'name' => 'Baskerville',
				'family' => 'Baskerville, "Times New Roman", Times, serif',
				'web_safe' => false,
				'monospace' => false
			),
			'bookman_old_style' => array(
				'name' => 'Bookman Old Style',
				'family' => '"Bookman Old Style", Georgia, "Times New Roman", Times, serif',
				'web_safe' => false,
				'monospace' => false
			),
			'calibri' => array(
				'name' => 'Calibri',
				'family' => 'Calibri, "Helvetica Neue", Helvetica, Arial, Verdana, sans-serif',
				'web_safe' => false,
				'monospace' => false
			),
			'cambria' => array(
				'name' => 'Cambria',
				'family' => 'Cambria, Georgia, "Times New Roman", Times, serif',
				'web_safe' => false,
				'monospace' => false
			),
			'candara' => array(
				'name' => 'Candara',
				'family' => 'Candara, Verdana, sans-serif',
				'web_safe' => false,
				'monospace' => false
			),
			'century_gothic' => array(
				'name' => 'Century Gothic',
				'family' => '"Century Gothic", "Apple Gothic", Verdana, sans-serif',
				'web_safe' => false,
				'monospace' => false
			),
			'century_schoolbook' => array(
				'name' => 'Century Schoolbook',
				'family' => '"Century Schoolbook", Georgia, "Times New Roman", Times, serif',
				'web_safe' => false,
				'monospace' => false
			),
			'consolas' => array(
				'name' => 'Consolas',
				'family' => 'Consolas, "Andale Mono", Monaco, Courier, "Courier New", Verdana, sans-serif',
				'web_safe' => false,
				'monospace' => true
			),
			'constantia' => array(
				'name' => 'Constantia',
				'family' => 'Constantia, Georgia, "Times New Roman", Times, serif',
				'web_safe' => false,
				'monospace' => false
			),
			'corbel' => array(
				'name' => 'Corbel',
				'family' => 'Corbel, "Lucida Grande", "Lucida Sans Unicode", Arial, sans-serif',
				'web_safe' => false,
				'monospace' => false
			),
			'franklin_gothic' => array(
				'name' => 'Franklin Gothic Medium',
				'family' => '"Franklin Gothic Medium", Arial, sans-serif',
				'web_safe' => false,
				'monospace' => false
			),
			'garamond' => array(
				'name' => 'Garamond',
				'family' => 'Garamond, "Hoefler Text", "Times New Roman", Times, serif',
				'web_safe' => false,
				'monospace' => false
			),
			'gill_sans' => array(
				'name' => 'Gill Sans',
				'family' => '"Gill Sans MT", "Gill Sans", Calibri, "Trebuchet MS", sans-serif',
				'web_safe' => false,
				'monospace' => false
			),
			'helvetica' => array(
				'name' => 'Helvetica',
				'family' => '"Helvetica Neue", Helvetica, Arial, sans-serif',
				'web_safe' => false,
				'monospace' => false
			),
			'hoefler' => array(
				'name' => 'Hoefler Text',
				'family' => '"Hoefler Text", Garamond, "Times New Roman", Times, sans-serif',
				'web_safe' => false,
				'monospace' => false
			),
			'lucida_bright' => array(
				'name' => 'Lucida Bright',
				'family' => '"Lucida Bright", Cambria, Georgia, "Times New Roman", Times, serif',
				'web_safe' => false,
				'monospace' => false
			),
			'lucida_grande' => array(
				'name' => 'Lucida Grande',
				'family' => '"Lucida Grande", "Lucida Sans", "Lucida Sans Unicode", sans-serif',
				'web_safe' => false,
				'monospace' => false
			),
			'palatino' => array(
				'name' => 'Palatino',
				'family' => '"Palatino Linotype", Palatino, Georgia, "Times New Roman", Times, serif',
				'web_safe' => false,
				'monospace' => false
			),
			'rockwell' => array(
				'name' => 'Rockwell',
				'family' => 'Rockwell, "Arial Black", "Arial Bold", Arial, sans-serif',
				'web_safe' => false,
				'monospace' => false
			),
			'cantarell' => array(
				'name' => 'Cantarell',
				'family' => '"Cantarell", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'cardo' => array(
				'name' => 'Cardo',
				'family' => '"Cardo", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'crimsontext' => array(
				'name' => 'Crimson+Text',
				'family' => '"Crimson Text", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'droidsans' => array(
				'name' => 'Droid+Sans',
				'family' => '"Droid Sans", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'droidsansmono' => array(
				'name' => 'Droid+Sans+Mono',
				'family' => '"Droid Sans Mono", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'droidserif' => array(
				'name' => 'Droid+Serif',
				'family' => '"Droid Serif", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'imfellenglish' => array(
				'name' => 'IM+Fell+English',
				'family' => '"IM Fell English", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'inconsolata' => array(
				'name' => 'Inconsolata',
				'family' => '"Inconsolata", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'josefinsansstdlight' => array(
				'name' => 'Josefin+Sans+Std+Light',
				'family' => '"Josefin Sans Std Light", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'lobster' => array(
				'name' => 'Lobster',
				'family' => '"Lobster", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'molengo' => array(
				'name' => 'Molengo',
				'family' => '"Molengo", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'nobile' => array(
				'name' => 'Nobile',
				'family' => '"Nobile", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'oflsortsmillgoudytt' => array(
				'name' => 'OFL+Sorts+Mill+Goudy+TT',
				'family' => '"OFL Sorts Mill Goudy TT", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'oldstandardtt' => array(
				'name' => 'Old+Standard+TT',
				'family' => '"Old Standard TT", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'reeniebeanie' => array(
				'name' => 'Reenie+Beanie',
				'family' => '"Reenie Beanie", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'tangerine' => array(
				'name' => 'Tangerine',
				'family' => '"Tangerine", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'vollkorn' => array(
				'name' => 'Vollkorn',
				'family' => '"Vollkorn", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			),
			'yanonekaffeesatz' => array(
				'name' => 'Yanone+Kaffeesatz',
				'family' => '"Yanone Kaffeesatz", arial, serif',
				'web_safe' => false,
				'monospace' => false,
				'google' => true
			)
		);
	}
}

function bizz_get_fonts() {
	$all_fonts = new Fonts;
	$all_fonts->set_fonts();
	return $all_fonts->fonts;
}

/* MENU DESCRIPTION CLASS (originally created by Kriesi: http://www.kriesi.at/archives/improve-your-wordpress-navigation-menu-output) */
/*------------------------------------------------------------------*/

class description_walker extends Walker_Nav_Menu {
    
	function start_el(&$output, $item, $depth, $args) {
        
		global $wp_query;
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
		
		$class_names = $value = '';
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
        $class_names = ' class="'. esc_attr( $class_names ) . '"';

        $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

        $description  = ($item->description <> ' ') ? ' <br/><span class="desc">'.esc_attr( $item->description ).'</span>' : '';

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before .apply_filters( 'the_title', $item->title, $item->ID );
        $item_output .= $description.$args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		
    }
	
}

/**
 * Akismet anti-comment spam service
 *
 * The class in this package allows use of the {@link http://akismet.com Akismet} anti-comment spam service in any PHP5 application.
 *
 * This service performs a number of checks on submitted data and returns whether or not the data is likely to be spam.
 *
 * Please note that in order to use this class, you must have a vaild {@link http://wordpress.com/api-keys/ WordPress API key}.  They are free for non/small-profit types and getting one will only take a couple of minutes.
 *
 * For commercial use, please {@link http://akismet.com/commercial/ visit the Akismet commercial licensing page}.
 *
 * Please be aware that this class is PHP5 only.  Attempts to run it under PHP4 will most likely fail.
 *
 * See the Akismet class documentation page linked to below for usage information.
 *
 * @package Akismet
 * @author Alex Potsides, {@link http://www.achingbrain.net http://www.achingbrain.net}, Bret Kuhns {@link http://www.miphp.net}
 * @version 0.1
 * @copyright Alex Potsides, {@link http://www.achingbrain.net http://www.achingbrain.net}
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

/**
 *      The Akismet PHP4 Class
 *
 *  This class takes the functionality from the Akismet WordPress plugin written by {@link http://photomatt.net/ Matt Mullenweg} and allows it to be integrated into any PHP5 application or website.
 *
 *  The original plugin is {@link http://akismet.com/download/ available on the Akismet website}.
 *
 *  <b>Usage:</b>
 *  <code>
 *    $akismet = new Akismet('http://www.example.com/blog/', 'aoeu1aoue');
 *    $akismet->setCommentAuthor($name);
 *    $akismet->setCommentAuthorEmail($email);
 *    $akismet->setCommentAuthorURL($url);
 *    $akismet->setCommentContent($comment);
 *    $akismet->setPermalink('http://www.example.com/blog/alex/someurl/');
 *    if($akismet->isCommentSpam())
 *      // store the comment but mark it as spam (in case of a mis-diagnosis)
 *    else
 *      // store the comment normally
 *  </code>
 *
 *      @package        akismet
 *      @name           Akismet
 *      @version        0.2
 *  @author             Alex Potsides (converted to PHP4 by Bret Kuhns)
 *  @link               http://www.achingbrain.net/
 */
class Akismet {
        var $version = '0.2';
        var $wordPressAPIKey;
        var $blogURL;
        var $comment;
        var $apiPort;
        var $akismetServer;
        var $akismetVersion;

        // This prevents some potentially sensitive information from being sent accross the wire.
        var $ignore = array(
                        'HTTP_COOKIE',
                        'HTTP_X_FORWARDED_FOR',
                        'HTTP_X_FORWARDED_HOST',
                        'HTTP_MAX_FORWARDS',
                        'HTTP_X_FORWARDED_SERVER',
                        'REDIRECT_STATUS',
                        'SERVER_PORT',
                        'PATH',
                        'DOCUMENT_ROOT',
                        'SERVER_ADMIN',
                        'QUERY_STRING',
                        'PHP_SELF'
                );


        /**
         *      @throws Exception       An exception is thrown if your API key is invalid.
         *      @param  string  Your WordPress API key.
         *      @param  string  $blogURL                        The URL of your blog.
         */
        function Akismet($blogURL, $wordPressAPIKey) {
                $this->blogURL = $blogURL;
                $this->wordPressAPIKey = $wordPressAPIKey;

                // Set some default values
                $this->apiPort = 80;
                $this->akismetServer = 'rest.akismet.com';
                $this->akismetVersion = '1.1';

                // Start to populate the comment data
                $this->comment['blog'] = $blogURL;
                $this->comment['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                $this->comment['referrer'] = $_SERVER['HTTP_REFERER'];

                // This is necessary if the server PHP5 is running on has been set up to run PHP4 and
                // PHP5 concurently and is actually running through a separate proxy al a these instructions:
                // http://www.schlitt.info/applications/blog/archives/83_How_to_run_PHP4_and_PHP_5_parallel.html
                // and http://wiki.coggeshall.org/37.html
                // Otherwise the user_ip appears as the IP address of the PHP4 server passing the requests to the
                // PHP5 one...
                $this->comment['user_ip'] = $_SERVER['REMOTE_ADDR'] != getenv('SERVER_ADDR') ? $_SERVER['REMOTE_ADDR'] : getenv('HTTP_X_FORWARDED_FOR');

                // Check to see if the key is valid
                $response = $this->http_post('key=' . $this->wordPressAPIKey . '&blog=' . $this->blogURL, $this->akismetServer, '/' . $this->akismetVersion . '/verify-key');

                if($response[1] != 'valid') {
                        // Whoops, no it's not.  Throw an exception as we can't proceed without a valid API key.
                        trigger_error('Invalid API key.  Please obtain one from http://wordpress.com/api-keys/', E_USER_ERROR);
                }
        }

        function http_post($request, $host, $path) {
                $http_request  = 
                                "POST " . $path . " HTTP/1.1\r\n" .
                                "Host: " . $host . "\r\n" .
                                "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n" .
                                "Content-Length: " . strlen($request) . "\r\n" .
                                "User-Agent: Akismet PHP5 Class " . $this->version . " | Akismet/1.11\r\n" .
                                "\r\n" .
                                $request
                        ;

                $socketWriteRead = new SocketWriteRead($host, $this->apiPort, $http_request);
                $socketWriteRead->send();

                return explode("\r\n\r\n", $socketWriteRead->getResponse(), 2);
        }

        // Formats the data for transmission    echo $sql;
        function getQueryString() {
                foreach($_SERVER as $key => $value) {
                        if(!in_array($key, $this->ignore)) {
                                if($key == 'REMOTE_ADDR') {
                                        $this->comment[$key] = $this->comment['user_ip'];
                                } else {
                                        $this->comment[$key] = $value;
                                }
                        }
                }

                $query_string = '';

                foreach($this->comment as $key => $data) {
                        $query_string .= $key . '=' . urlencode(stripslashes($data)) . '&';
                }

                return $query_string;
        }

        /**
         *      Tests for spam.
         *
         *      Uses the web service provided by {@link http://www.akismet.com Akismet} to see whether or not the submitted comment is spam.  Returns a boolean value.
         *
         *      @return         bool    True if the comment is spam, false if not
         */
        function isSpam() {
                $response = $this->http_post($this->getQueryString(), $this->wordPressAPIKey . '.rest.akismet.com', '/' . $this->akismetVersion . '/comment-check');

                return ($response[1] == 'true');
        }

        /**
         *      Submit spam that is incorrectly tagged as ham.
         *
         *      Using this function will make you a good citizen as it helps Akismet to learn from its mistakes.  This will improve the service for everybody.
         */
        function submitSpam() {
                $this->http_post($this->getQueryString(), $this->wordPressAPIKey . '.' . $this->akismetServer, '/' . $this->akismetVersion . '/submit-spam');
        }

        /**
         *      Submit ham that is incorrectly tagged as spam.
         *
         *      Using this function will make you a good citizen as it helps Akismet to learn from its mistakes.  This will improve the service for everybody.
         */
        function submitHam() {
                $this->http_post($this->getQueryString(), $this->wordPressAPIKey . '.' . $this->akismetServer, '/' . $this->akismetVersion . '/submit-ham');
        }

        /**
         *      To override the user IP address when submitting spam/ham later on
         *
         *      @param string $userip   An IP address.  Optional.
         */
        function setUserIP($userip) {
                $this->comment['user_ip'] = $userip;
        }

        /**
         *      To override the referring page when submitting spam/ham later on
         *
         *      @param string $referrer The referring page.  Optional.
         */
        function setReferrer($referrer) {
                $this->comment['referrer'] = $referrer;
        }

        /**
         *      A permanent URL referencing the blog post the comment was submitted to.
         *
         *      @param string $permalink        The URL.  Optional.
         */
        function setPermalink($permalink) {
                $this->comment['permalink'] = $permalink;
        }

        /**
         *      The type of comment being submitted.
         *
         *      May be blank, comment, trackback, pingback, or a made up value like "registration" or "wiki".
         */
        function setType($commentType) {
                $this->comment['comment_type'] = $commentType;
        }

        /**
         *      The name that the author submitted with the comment.
         */
        function setAuthor($commentAuthor) {
                $this->comment['comment_author'] = $commentAuthor;
        }

        /**
         *      The email address that the author submitted with the comment.
         *
         *      The address is assumed to be valid.
         */
        function setAuthorEmail($authorEmail) {
                $this->comment['comment_author_email'] = $authorEmail;
        }

        /**
         *      The URL that the author submitted with the comment.
         */
        function setAuthorURL($authorURL) {
                $this->comment['comment_author_url'] = $authorURL;
        }

        /**
         *      The comment's body text.
         */
        function setContent($commentBody) {
                $this->comment['comment_content'] = $commentBody;
        }

        /**
         *      Defaults to 80
         */
        function setAPIPort($apiPort) {
                $this->apiPort = $apiPort;
        }

        /**
         *      Defaults to rest.akismet.com
         */
        function setAkismetServer($akismetServer) {
                $this->akismetServer = $akismetServer;
        }

        /**
         *      Defaults to '1.1'
         */
        function setAkismetVersion($akismetVersion) {
                $this->akismetVersion = $akismetVersion;
        }
}


/**
 *      Utility class used by Akismet
 *
 *  This class is used by Akismet to do the actual sending and receiving of data.  It opens a connection to a remote host, sends some data and the reads the response and makes it available to the calling program.
 *
 *  The code that makes up this class originates in the Akismet WordPress plugin, which is {@link http://akismet.com/download/ available on the Akismet website}.
 *
 *      N.B. It is not necessary to call this class directly to use the Akismet class.  This is included here mainly out of a sense of completeness.
 *
 *      @package        akismet
 *      @name           SocketWriteRead
 *      @version        0.1
 *  @author             Alex Potsides
 *  @link               http://www.achingbrain.net/
 */
class SocketWriteRead {
        var $host;
        var $port;
        var $request;
        var $response;
        var $responseLength;
        var $errorNumber;
        var $errorString;

        /**
         *      @param  string  $host                   The host to send/receive data.
         *      @param  int             $port                   The port on the remote host.
         *      @param  string  $request                The data to send.
         *      @param  int             $responseLength The amount of data to read.  Defaults to 1160 bytes.
         */
        function SocketWriteRead($host, $port, $request, $responseLength = 1160) {
                $this->host = $host;
                $this->port = $port;
                $this->request = $request;
                $this->responseLength = $responseLength;
                $this->errorNumber = 0;
                $this->errorString = '';
        }

        /**
         *  Sends the data to the remote host.
         *
         * @throws      An exception is thrown if a connection cannot be made to the remote host.
         */
        function send() {
                $this->response = '';

                $fs = fsockopen($this->host, $this->port, $this->errorNumber, $this->errorString, 3);

                if($this->errorNumber != 0) {
                        trigger_error('Error connecting to host: ' . $this->host . ' Error number: ' . $this->errorNumber . ' Error message: ' . $this->errorString, E_USER_ERROR);
                }

                if($fs !== false) {
                        @fwrite($fs, $this->request);

                        while(!feof($fs)) {
                                $this->response .= fgets($fs, $this->responseLength);
                        }

                        fclose($fs);
                }
        }

        /**
         *  Returns the server response text
         *
         *  @return     string
         */
        function getResponse() {
                return $this->response;
        }

        /**
         *      Returns the error number
         *
         *      If there was no error, 0 will be returned.
         *
         *      @return int
         */
        function getErrorNumner() {
                return $this->errorNumber;
        }

        /**
         *      Returns the error string
         *
         *      If there was no error, an empty string will be returned.
         *
         *      @return string
         */
        function getErrorString() {
                return $this->errorString;
        }
}

