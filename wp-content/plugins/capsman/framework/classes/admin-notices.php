<?php
/**
 * Admin notices (or warnings).
 *
 * @version		$Rev: 198515 $
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
 * Class to show admin warnings (or notices)
 * Throws the hook 'admin_notices' to show the warning only on allowed places.
 * To use the class, just have to instantiate it as new akcAdminNotice('message').
 *
 * @author		Jordi Canals
 * @package 	Alkivia
 * @subpackage	Framework
 *
 * @link		http://wiki.alkivia.org/framework/classes/admin-notice
 */
class akAdminNotice
{
    /**
     * Warning message to be shown.
     * @var string
     */
    private $message;

    /**
     * Class constructor.
     * Gets the message, and sets the admin_notices hook.
     *
     * @param $message	Message to show.
     * @return aocAdminNotice
     */
    public function __construct( $message )
    {
        $this->message = $message;
        add_action('admin_notices', array($this, '_showMessage') );
    }

    /**
     * The hook function to display the warning.
     *
     * @return void
     */
    public function _showMessage()
    {
        echo '<div id="error" class="error"><p><strong>' . $this->message . '</strong></p></div>';
    }
}
