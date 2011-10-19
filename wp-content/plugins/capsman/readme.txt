=== Capability Manager ===
Contributors: txanny
Donate link: http://alkivia.org/donate
Help link: http://wordpress.org/tags/capsman?forum_id=10
Docs link: http://wiki.alkivia.org/capsman
Tags: roles, capabilities, manager, rights, role, capability
Requires at least: 2.9
Tested up to: 2.9.2
Stable tag: trunk

A simple way to manage WordPress roles and capabilities. With this plugin you will be able to easily create and manage roles and capabilities.

== Description ==

The Capability Manager plugin provides a simple way to manage role capabilities. Using it, you will be able to change the capabilities of any role, add new roles, copy existing roles into new ones, and add new capabilities to existing roles.
You can also delegate capabilities management to other users. In this case, some restrictions apply to this users, as them can only set/unset the capabilities they have.
With the Backup/Restore tool, you can save your Roles and Capabilities before making changes and revert them if something goes wrong. You'll find it on the Tools menu. 

  * Capability manager has been tested to support only one role per user.
  * Only users with 'manage_capabilities' can manage them. This capability is created at install time and assigned only to administrators.
  * Administrator role cannot be deleted.
  * Non-administrators can only manage roles or users with same or lower capabilities.
  
See the <a href="http://wiki.alkivia.org/capsman" target="_blank">plugin manual</a> for more information.

= Features: =

* Manage role capabilities.
* Create new roles or delete existing ones.
* Add new capabilities to any existing role.
* Backup and restore Roles and Capabilities to revert your last changes.
* Revert Roles and Capabilities to WordPress defaults. 
 
= Languages included: =

* English
* Catalan
* Spanish
* Belorussian *by <a href="http://antsar.info/" rel="nofollow">Ilyuha</a>*
* German *by <a href="http://great-solution.de/" rel="nofollow">Carsten Tauber</a>*
* Italian *by <a href="http://gidibao.net" rel="nofollow">Gianni Diurno</a>*
* Russian *by <a href="http://www.fatcow.com" rel="nofollow">Marcis Gasuns</a>*
* Swedish *by <a href="http://jenswedin.com/" rel="nofollow">Jens Wedin</a>
* POT file for easy translation to other languages included. See the <a href="http://wiki.alkivia.org/general/translators">translators page</a> for more information.

== Installation ==

= System Requirements =

* **Requires PHP 5.2**. Older versions of PHP are obsolete and expose your site to security risks.
* Verify the plugin is compatible with your WordPress Version. If not, plugin will not load.

= Installing the plugin =

1. Unzip the plugin archive.
1. Upload the plugin's folder to the WordPress plugins directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Manage the capabilities on the 'Capabilities' page on Users menu.
1. Enjoy your plugin!

== Screenshots ==

1. Setting new capabilities for a role.
2. Actions on roles.
3. Backup/Restore tool.

== Frequently Asked Questions ==

= Where can I find more information about this plugin, usage and support ? =

* Take a look to the <a href="http://alkivia.org/wordpress/capsman">Plugin Homepage</a>.
* A <a href="http://wiki.alkivia.org/capsman">manual is available</a> for users and developers.
* The <a href="http://alkivia.org/cat/capsman">plugin posts archive</a> with new announcements about this plugin.
* If you need help, <a href="http://wordpress.org/tags/capsman?forum_id=10">ask in the Support forum</a>.

= I've found a bug or want to suggest a new feature. Where can I do it? =

* To fill a bug report or suggest a new feature, please fill a report in our <a href="http://tracker.alkivia.org/set_project.php?project_id=7&ref=view_all_bug_page.php">Bug Tracker</a>.

== License ==

Copyright 2009, 2010 Jordi Canals

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License version 2 as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <http://www.gnu.org/licenses/>.

== Changelog ==

= 1.3.2 = 
  * Added Swedish translation.

= 1.3.1 =
  * Fixed a bug where administrators could not create or manage other administrators.
  
= 1.3 =
  * Cannot edit users with more capabilities than current user.
  * Cannot assign to users a role with more capabilities than current user.
  * Solved an incompatibility with Chameleon theme.
  * Migrated to the new Alkivia Framework.
  * Changed license to GPL version 2.

= 1.2.5 =
  * Tested up to WP 2.9.1.

= 1.2.4 =
  * Added Italian translation.

= 1.2.3 =
  * Added German and Belorussian translations.

= 1.2.2 =
  * Added Russian translation.

= 1.2.1 =
  * Coding Standards.
  * Corrected internal links.
  * Updated Framework.

= 1.2 =
  * Added backup/restore tool.

= 1.1 =
  * Role deletion added.

= 1.0.1 =
  * Some code improvements.
  * Updated Alkivia Framework.

= 1.0 =
  * First public version.

== Upgrade Notice ==

= 1.3.2 = 
Only Swedish translation.

= 1.3.1 =
Bug fixes.
  
= 1.3 =
Improved security esiting users. You can now create real user managers. 
