<?php
/**
 * Admin main init.
 *
 * 
 * @author PageLines
 *
 * @since 2.0.b21
 */

/**
 * Load account handling
 */
require_once ( PL_ADMIN . '/paths.admin.php' );

/**
 * Load Drag and Drop UI
 */
require_once( PL_ADMIN . '/class.ui.templates.php' );

/**
 * Load Layout Controls
 */
require_once( PL_ADMIN . '/class.ui.layout.php' );

/**
 * Load Type Control
 */
require_once( PL_ADMIN . '/class.ui.typography.php' );

/**
 * Load Color Controls
 */
require_once( PL_ADMIN . '/class.ui.color.php' );

/**
 * Load options UI
 */

require_once( PL_ADMIN . '/class.options.ui.php' );

/**
 * Load options engine and breaker
 */
require_once( PL_ADMIN . '/class.options.engine.php' );

/**
 * Load Panel UI
 */

require_once( PL_ADMIN . '/class.options.panel.php' );

/**
 * Load inline help
 */
require_once (PL_ADMIN . '/library.help.php' );

/**
 * Load account handling
 */
require_once ( PL_ADMIN . '/class.account.php' );
global $account_control;
$account_control = new PageLinesAccount;

/**
 * Load store class
 */
require_once ( PL_ADMIN . '/class.extend.php' );
require_once ( PL_ADMIN . '/class.extend.ui.php' );
require_once ( PL_ADMIN . '/class.extend.actions.php' );

require_once ( PL_ADMIN . '/class.extend.integrations.php' );
require_once ( PL_ADMIN . '/class.extend.themes.php' );
require_once ( PL_ADMIN . '/class.extend.plugins.php' );
require_once ( PL_ADMIN . '/class.extend.sections.php' );

global $extension_control;
$extension_control = new PagelinesExtensions;

require_once( PL_ADMIN . '/class.rss.php' );

/**
 * Load admin actions
 */
require_once (PL_ADMIN.'/actions.admin.php'); 

/**
 * Load option actions
 */
require_once (PL_ADMIN.'/actions.options.php');

/**
 * Load Dashboard Template
 */
require_once (PL_ADMIN.'/t.dashboard.php');
require_once (PL_ADMIN.'/t.extensions.php');
require_once (PL_ADMIN.'/t.support.php');
