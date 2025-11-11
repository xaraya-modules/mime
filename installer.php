<?php

/**
 * Handle module installer functions
 *
 * @package modules\mime
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
 *
 * @author mikespub <mikespub@xaraya.com>
**/

namespace Xaraya\Modules\Mime;

use Xaraya\Modules\InstallerClass;
use xarMasks;
use xarPrivileges;

/**
 * Handle module installer functions
 * @extends InstallerClass<Module>
 */
class Installer extends InstallerClass
{
    /**
     * Configure this module - override this method
     *
     * @return void
     */
    public function configure()
    {
        $this->objects = [
            // add your DD objects here
            'mime_types',
            'mime_subtypes',
            'mime_magic',
            'mime_extensions',
        ];
        $this->variables = [
            // add your module variables here
            'defaultmastertable' => 'mime_types',
        ];
        $this->oldversion = '2.6.0';
    }

    /**
     * initialise the mime module
     * This function is only ever called once during the lifetime of a particular
     * module instance
     * @todo review masks & privileges registration for Xaraya 2.x
     */
    public function init()
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

        // this will call standard init() to create the DD objects, set module variables etc.
        return parent::init();
    }

    /**
    * upgrade the mime module from an old version
    */
    public function upgrade($oldversion)
    {
        // Upgrade dependent on old version number
        switch ($oldversion) {
            case '1.1.0':
                // Upgrade from version 1.1.0
                // @todo remove old tables
            case '1.5.0':
                // Upgrade from version 1.5.0
                // @todo remove old permissions?
            case '2.5.3':
                // Upgrade from version 2.5.3
            case '2.5.7':
                // Upgrade from version 2.5.7
                // Re-create DD objects to fix extension
                parent::delete();
                parent::init();
                // no break
            case '2.6.0':
                // Upgrade from version 2.6.0
        }

        return true;
    }
}
