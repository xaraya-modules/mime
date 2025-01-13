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
use xarSecurity;
use xarVar;
use xarSec;
use xarController;
use DataObjectFactory;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * mime admin delete function
 * @extends MethodClass<AdminGui>
 */
class DeleteMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * @see AdminGui::delete()
     */
    public function __invoke(array $args = [])
    {
        if (!xarSecurity::check('ManageMime')) {
            return;
        }

        if (!xarVar::fetch('name', 'str:1', $name, 'mime_types', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('itemid', 'int', $data['itemid'], '', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('confirm', 'str:1', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
            return;
        }

        $data['object'] = DataObjectFactory::getObject(['name' => $name]);
        $data['object']->getItem(['itemid' => $data['itemid']]);

        $data['tplmodule'] = 'mime';
        $data['authid'] = xarSec::genAuthKey('mime');

        if ($data['confirm']) {
            // Check for a valid confirmation key
            if (!xarSec::confirmAuthKey()) {
                return;
            }

            // Delete the item
            $item = $data['object']->deleteItem();

            // Jump to the next page
            xarController::redirect(xarController::URL('mime', 'admin', 'view'), null, $this->getContext());
            return true;
        }
        return $data;
    }
}
