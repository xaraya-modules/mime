<?php
/**
 * Mime Module
 *
 * @package modules
 * @subpackage mime module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/999
 * @author Carl Corliss <rabbitt@xaraya.com>
 */

/**
 * initialise the mime module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function mime_init()
{
    # --------------------------------------------------------
    #
    # Set up masks
    #
    xarMasks::register('EditMime', 'All', 'mime', 'All', 'All', 'ACCESS_EDIT');
    xarMasks::register('AddMime', 'All', 'mime', 'All', 'All', 'ACCESS_ADD');
    xarMasks::register('ManageMime', 'All', 'mime', 'All', 'All', 'ACCESS_DELETE');
    xarMasks::register('AdminMime', 'All', 'mime', 'All', 'All', 'ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up privileges
    #
    xarPrivileges::register('EditMime', 'All', 'mime', 'All', 'All', 'ACCESS_EDIT');
    xarPrivileges::register('AddMime', 'All', 'mime', 'All', 'All', 'ACCESS_ADD');
    xarPrivileges::register('ManageMime', 'All', 'mime', 'All', 'All', 'ACCESS_DELETE');
    xarPrivileges::register('AdminMime', 'All', 'mime', 'All', 'All', 'ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Create DD objects
    #
    $module = 'mime';
    $objects = [
        'mime_types',
        'mime_subtypes',
        'mime_magic',
        'mime_extensions',
    ];

    if (!xarMod::apiFunc('modules', 'admin', 'standardinstall', ['module' => $module, 'objects' => $objects])) {
        return;
    }

    # --------------------------------------------------------
    #
    # Set up modvars
    #
    //        $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'mime'));
    //        $module_settings->initialize();

    xarModVars::set('mime', 'defaultmastertable', 'mime_types');

    // Initialisation successful
    return true;
}

/**
* upgrade the mime module from an old version
*/
function mime_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '1.1.0':
            // Upgrade from version 1.1.0
            // @todo remove old tables
        case '1.5.0':
            // Upgrade from version 1.5.0
            // @todo remove old permissions?
        }

    return true;
}

/**
 *  Uninstall this module
 */

function mime_delete()
{
    $module = 'mime';
    return xarMod::apiFunc('modules', 'admin', 'standarddeinstall', ['module' => $module]);
}
