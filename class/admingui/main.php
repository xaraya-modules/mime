<?php

/**
 * @package modules\mime
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Mime\AdminGui;


use Xaraya\Modules\Mime\AdminGui;
use Xaraya\Modules\MethodClass;
use xarController;
use xarSecurity;
use xarModVars;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * mime admin main function
 * @extends MethodClass<AdminGui>
 */
class MainMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Main admin GUI function, entry point
     * @see AdminGui::main()
     */
    public function __invoke(array $args = [])
    {
        xarController::redirect(xarController::URL('mime', 'admin', 'view'), null, $this->getContext());

        if (!xarSecurity::check('ManageMime')) {
            return;
        }

        if (xarModVars::get('modules', 'disableoverview') == 0) {
            return [];
        } else {
            xarController::redirect(xarController::URL('mime', 'admin', 'view'), null, $this->getContext());
        }
        // success
        return true;
    }
}
