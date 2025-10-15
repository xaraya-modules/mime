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
use sys;

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
        $this->ctl()->redirect($this->mod()->getURL('admin', 'view'));
        return true;

        if (!$this->sec()->checkAccess('ManageMime')) {
            return;
        }

        if (!$this->mod()->disableOverview()) {
            return [];
        } else {
            $this->ctl()->redirect($this->mod()->getURL('admin', 'view'));
        }
        // success
        return true;
    }
}
