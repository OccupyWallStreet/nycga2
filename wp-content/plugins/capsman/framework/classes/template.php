<?php
/**
 * Class to manage simple templates for HTML output.
 *
 * @version		$Rev: 203758 $
 * @author		Jordi Canals
 * @copyright   Copyright (C) 2008, 2009, 2010 Jordi Canals
 * @license		GNU General Public License version 2
 * @link		http://alkivia.org
 * @package		Alkivia
 * @subpackage	Framework
 *

	Copyright 2008, 2009, 2010 Jordi Canals <devel@jcanals.cat>

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	version 2 as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * A very simple class for an easy tenplate management.
 * This is an abstract class that have to be extended for use.
 *
 * TODO: Provide some sort of caching template contents.
 *
 * @package Alkivia
 * @subpackage Framework
 */
class akTemplate
{
	/**
	 * Templates folder (Slash ended).
	 * @var string
	 */
	protected $tpl_dir = array();

	/**
	 * Config files folder (Slah ended).
	 * @var string
	 */
	private $cfg_dir = array();

	/**
	 * Variables and values available to template.
	 * 		- 'var_name' => 'value'
	 * @var array
	 */
	protected $vars = array();

	/**
	 * Data readed from template config files.
	 * @var array
	 */
	private $config = array();

	/**
	 * Holds notice messages to be shown in output.
	 * On the template the method displayMessages() must be called.
	 * @var array
	 */
	private $notices = array();

	/**
	 * Holds error messages to be shown in output.
	 * On the template the method displayMessages() must be called.
	 * @var array
	 */
	private $errors = array();

	/**
	 * Class constructor.
	 *
	 * @param array|string $tpl_dir Full paths to template folders.
	 * @param string $cfg_dir Full path to config files.
	 * @return TemplateAbstract	The class object or false if $tpl_dir is not a directory.
	 */
	public function __construct ( $tpl_dir, $cfg_dir = '' )
	{
	    $this->tpl_dir = $this->checkDirectories($tpl_dir);
	    if ( empty($this->tpl_dir) ) {
		    wp_die(__('Template class: Received template paths are not valid directories.'));
	    }

		if ( empty($cfg_dir) ) {
			$this->cfg_dir = $this->tpl_dir;
		} else {
		    $this->cfg_dir = $this->checkDirectories($cfg_dir);
		    if ( empty($this->tpl_dir) ) {
		        wp_die(__('Template class: Received config paths are not valid directories.'));
		    }
		}

	}

	/**
	 * Checks an array of paths are valid directories.
	 *
	 * @since 0.8
	 *
	 * @param array|string $directories Absolute paths array
	 * @return array An array with only valid directories, wrong directories are removed.
	 */
	private function checkDirectories ( $directories )
	{
        $valid = array();
        foreach ( (array) $directories as $path ) {
    		if ( is_dir($path) ) {
	    		$valid[] = trailingslashit($path);
		    }
		}

		return $valid;
	}

	/**
	 * Assigns the translation textDomain as an available variable name.
	 * This will be available in template as $i18n.
	 *
	 * @param $context Translation context textDomain.
	 * @return void
	 */
	final public function textDomain ( $context )
	{
		$this->vars['i18n'] = $context;
	}

	/**
	 * Assigns a variable name with it's value.
	 * Reserved vars names: i18n, tpl, cfg.
	 *
	 * @param $name	Variable name.
	 * @param $value Value of the variable.
	 * @return void
	 */
	final public function assign ( $name, $value )
	{
	    $this->checkReserved($name);
		$this->vars[$name] = $value;
	}

	/**
	 * Assigns a variable name with it's value by reference.
	 *
	 * @param $name	Variable name.
	 * @param $value Value of the variable, received by reference.
	 * @return void
	 */
	public function assignByRef( $name, &$value )
	{
	    $this->checkReserved($name);
		$this->vars[$name] =& $value;
	}

	/**
	 * Checks if a template var name is reserved and dies if yes.
	 *
	 * @since 0.8
	 *
	 * @param string $name Variable name to check.
	 * @return void
	 */
	private function checkReserved ( $name )
	{
	    if ( in_array($name, array('i18n', '_template', '_default', '_config', '_filename')) ) {
	        wp_die( sprintf(__('Template class: %s is a reserved template variable.'), $name) );
	    }
	}

	/**
	 * Loads an INI file from config folder, and merges the content with previous read files.
	 *
	 * @param $file	File name (With no extension)
	 * @return void
	 */
	final public function loadConfig ( $file )
	{
	    foreach ( $this->cfg_dir as $path ) {
            $filename = $path . $name . '.ini';
    		if ( file_exists($filename) ) {
			    $config = parse_ini_file( $filename, true);
			    $this->config = array_merge($this->config, $config);
            }
	    }
	}

	/**
	 * Sets config values to empty.
	 * Can be used to start over loading new INI files.
	 *
	 * @return void
	 */
	final public function resetConfig ()
	{
		$this->config = array();
	}

	/**
	 * Sets template vars to empty.
	 * Can be used to start over with a new template.
	 *
	 * @return void
	 */
	final public function resetVars ()
	{
		$this->vars = array();
	}

	/**
	 * Sets config and vars to empty.
	 * Used to start a new clean template.
	 *
	 * @return void
	 */
	final public function resetAll ()
	{
		$this->config = array();
		$this->vars = array();
	}

	/**
	 * Adds an error to the erros queue.
	 * Only adds it if error does not already exists on errors queue.
	 *
	 * @param string $message Message to be added to the queue.
	 * @return void
	 */
	final public function addError ( $message )
	{
        if ( ! empty($message) && ! in_array($message, $this->errors)) {
            $this->errors[] = $message;
        }
	}

	/**
	 * Chechs if errors were found and have to be displayed.
	 *
	 * @return boolean Found errors.
	 */
	final public function foundErrors()
	{
	    return ! empty($this->errors);
	}

	/**
	 * Adds a notice to the notices queue.
	 * Only adds the notice if it does not alredy exists on notices queue.
	 *
	 * @param string $message Message to be added to the queue.
	 * @return void
	 */
	final public function addNotice ( $message )
	{
        if ( ! empty($message) && ! in_array($message, $this->notices)) {
            $this->notices[] = $message;
        }
	}

	/**
	 * Empties all messages queues (notices and errors).
	 *
	 * @return void
	 */
    final public function resetMessages ()
    {
        $this->errors = array();
        $this->notices = array();
    }

	/**
	 * Displays notices and/or error messages and empties the messages queue.
	 * This function should only be called within a template.
	 *
	 * @return void
	 */
    final public function displayMessages ()
    {
        if ( ! empty($this->errors) ) {
            $errors = implode('<br />' . PHP_EOL, $this->errors);
            echo '<div id="message" class="error">' . $errors . '</div>' . PHP_EOL;
        }

        if ( ! empty($this->notices) ) {
            $notices = implode('<br />' . PHP_EOL, $this->notices);
            echo '<div id="message" class="notice">' . $notices . '</div>' . PHP_EOL;
        }

        $this->resetMessages();
    }

    /**
     * Checks if a template file is available.
     *
     * @since 0.8
     *
     * @param string $template Template name (With no .php extension)
     * @return boolean If template file was found or not.
     */
    final public function available( $template )
    {
        return ( false === $this->locateFile($template) ) ? false : true;
    }

	/**
	 * Displays a template from the templates folder.
	 * Inside the template all assigned vars will be available.
	 * Also 'cfg' and 'tpl' vars will be available:
	 * 		- cfg is an array which cointains all config values.
	 * 		- tpl is an string cointaining template absolute name.
	 *
	 * TODO: Load config file with same name as template.
	 *
	 * @param string $_template Template name with no extension.
	 * @param string $_default Alternate default template name.
	 * @return void.
	 */
	final public function display ( $_template, $_default = '' )
	{
	    $_filename = $this->locateFile($_template);
	    if ( false === $_filename && ! empty($_default) ) {
            $_filename = $this->locateFile($_default);
	    }

	    if ( $_filename ) {
    		$_config =& $this->config;
		    extract($this->vars);
   			include ( $_filename );

    	} else {
            wp_die(sprintf(__('Template file %1$s not found. Default template %2$s not found.'), $_template, $_default));
	    }
}

	/**
	 * Returns the template contents after processing it.
	 * Calls to TemplateAbstract::display() for template processing.
	 *
	 * @param string $template Template name with no extension.
	 * @param string $default Alternate default template name.
	 * @return string|false	The template contents or false if failed processing.
	 */
	final public function getDisplay ( $template, $default = '' )
	{
		if ( ob_start() ) {
			$this->display($template, $default);
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		} else {
			return false;
		}
	}

	/**
	 * Locates the path for a template filename.
	 * If template is not found, returns false.
	 *
	 * @param string $name Template name (with no .php extension)
	 * @return string|false Absolute path to template file. False if not found.
	 */
	private function locateFile( $name )
	{
	    foreach ( $this->tpl_dir as $path ) {
		    $template = $path . $name . '.php';
    		if ( file_exists($template) ) {
    		    return $template;
    		}
	    }

	    return false;
	}
}
