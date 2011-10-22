<?php
/**
 * Functions library for theme management.
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
 * Creates and stores an object in the $_akv global.
 * Can be called at the same time we create the object: ak_store_object( 'obj_name', new objectName() );
 *
 * @param string $name	Internal object name.
 * @param object $object The object reference to store in the global.
 * @return object The newly stored object reference.
 */
function & ak_create_theme ( $theme )
{
    $GLOBALS['_akv']['theme'] =& $theme;
    return $theme;
}

/**
 * Gets and returns the theme object.
 * @return akThemeAbstract
 */
function & ak_theme ()
{
    return ak_get_object('theme');
}

/**
 * Returns a theme option/setting.
 *
 * @param string $option Option name to return.
 * @param mixed $default Default value if option not found.
 * @return mixed The option value.
 */
function ak_theme_option ( $option = '', $default = false )
{
    return ak_get_option ( 'theme', $option, $default );
}

/**
 * Returns parent theme data from style.css file.
 *
 * @param string $name Data name to return.
 * @return mixed Data value.
 */
function ak_theme_data ( $name = '' )
{
    if ( is_object($GLOBALS['_akv']['theme']) && method_exists($GLOBALS['_akv']['theme'], 'getModData') ) {
        return $GLOBALS['_akv']['theme']->getModData($name);
    } else {
        return false;
    }
}

/**
 * Returns child theme data from style.css file.
 *
 * @param string $name Data name to return.
 * @return mixed Data value.
 */
function ak_child_theme_data ( $name = '' )
{
    if ( is_object($GLOBALS['_akv']['theme']) && method_exists($GLOBALS['_akv']['theme'], 'getChildData') ) {
        return $GLOBALS['_akv']['theme']->getChildData($name);
    } else {
        return false;
    }
}

/**
 * Redirects user to a new page.
 * This is done if the custom field 'redirect' is defined as:
 * 		+ Filed Name:	redirect
 * 		+ Field Value:	New target URL.
 *
 * @return void
 */
function ak_theme_redirect()
{
	global $post;

	if ( is_page() || is_single() ) {
		if ( $meta = get_post_meta($post->ID, 'redirect', true) ) {
			wp_redirect($meta, 301);
			exit;
		}
	}
}

/**
 * Checks if the sidebar is used.
 * Do it by checking if there is any widget on a sidebar number.
 *
 * @param int|string $index	Sidebar number or id
 * @return boolean		True if this sidebar is used and contains any widget, false if not.
 */
function ak_theme_is_sidebar( $index )
{
	$sidebar = wp_get_sidebars_widgets();
	if ( is_numeric($index) ) {
	    $index = 'sidebar-' . $index;
	}

	if (isset($sidebar[$index]) && count($sidebar[$index]) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Authoring widget for admin pages.
 * You can add a readme.txt file for themes to add aditional links not found on style.css
 * Additional strings searched at readme.txt are: 'Help link:' and 'Docs link'.
 * All others follow plugins readme.txt guidelines.
 *
 * @since 0.5
 *
 * @param string $mod_id Module ID
 * @return void
 */
function ak_admin_authoring ( $mod_id )
{
    $mod = ak_get_object($mod_id);
    if ( ! $mod ) {
        return;
    }
    $data = $mod->getModData();
    $class = ( $mod->isComponent() ) ? $mod->PID : $mod_id;
?>

	<dl>
		<dt><?php echo $data['Name']; ?></dt>
		<dd>
			<ul>
				<?php if ( ! empty($data['PluginURI']) ) : ?>
					<li><a href="<?php echo $data['PluginURI']; ?>" class="<?php echo $class; ?>" target="_blank"><?php _e('Plugin Homepage', 'akfw'); ?></a></li>
				<?php endif; ?>

				<?php if ( ! empty($data['URI']) ) : ?>
					<li><a href="<?php echo $data['URI']; ?>" class="theme" target="_blank"><?php _e('Theme Homepage', 'akfw'); ?></a></li>
				<?php endif; ?>

				<?php if ( ! empty($data['DocsURI']) ) : ?>
					<li><a href="<?php echo $data['DocsURI']; ?>" class="docs" target="_blank"><?php _e('Documentation', 'akfw'); ?></a></li>
				<?php endif; ?>

				<?php if ( ! empty($data['HelpURI']) ) : ?>
					<li><a href="<?php echo $data['HelpURI']; ?>" class="help" target="_blank"><?php _e('Support Forum', 'akfw'); ?></a></li>
				<?php endif; ?>

				<?php if ( ! empty($data['AuthorURI']) ) : ?>
					<li><a href="<?php echo $data['AuthorURI']; ?>" class="home" target="_blank"><?php _e('Author Homepage', 'akfw')?></a></li>
				<?php endif; ?>

				<?php if ( ! empty($data['DonateURI']) ) : ?>
					<li><a href="<?php echo $data['DonateURI']; ?>" class="donate" target="_blank"><?php _e('Donate to project', 'akfw')?></a></li>
				<?php endif; ?>
			</ul>
		</dd>
	</dl>
<?php
}

/**
 * Copyright, authoring and versions for admin pages footer.
 *
 * @since 0.5
 *
 * @param string $mod_id Module ID
 * @param int $year First copyrigh year.
 * @return void
 */
function ak_admin_footer ( $mod_id, $year = 2009 )
{
    $mod = ak_get_object($mod_id);
    if ( ! $mod ) {
        return;
    }
    $data = $mod->getModData();

    if ( $mod->isPlugin() || $mod->isComponent() ) {
    	echo '<p class="footer"><a href="' . $mod->getModData('PluginURI') . '">' . $mod->getModData('Name') . ' ' . $mod->getModData('Version') .
    	     '</a> &nbsp; &copy; Copyright ';
		if ( 2010 != $year ) {
		    echo $year . '-';
		}
		echo date('Y') . ' ' . $mod->getModData('Author');
    } elseif ( $mod->isTheme() ) {
    	echo '<p class="footer"><a href="' . $mod->getModData('URI') . '">' . $mod->getModData('Name') . ' ' . $mod->getModData('Version') .
    	     '</a> &nbsp; &copy; Copyright ';
		if ( 2010 != $year ) {
		    echo $year . '-';
		}
		echo date('Y') . ' ';
		echo $mod->getModData('Author');
    }

    echo '<br />Framework Version: ' . get_option('ak_framework_version');
	if ( $mod->isChildTheme() ) {
        echo ' - Child theme: ' . $mod->getChildData('Name') . ' ' . $mod->getChildData('Version');
	}
	if ( $mod->isComponent() ) {
        echo ' - Component: ' . $mod->getChildData('Name') . ' ' . $mod->getChildData('Version');
	}
	echo '</p>';
}
