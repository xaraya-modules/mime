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
use xarTpl;
use xarController;
use DataObjectFactory;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * mime admin modify function
 * @extends MethodClass<AdminGui>
 */
class ModifyMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * @see AdminGui::modify()
     */
    public function __invoke(array $args = [])
    {
        if (!$this->checkAccess('EditMime')) {
            return;
        }

        if (!$this->fetch('name', 'str', $name, 'mime_types', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!$this->fetch('itemid', 'int', $data['itemid'], 0, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!$this->fetch('confirm', 'bool', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
            return;
        }

        $data['object'] = DataObjectFactory::getObject(['name' => $name]);
        $data['object']->getItem(['itemid' => $data['itemid']]);

        $data['tplmodule'] = 'mime';
        $data['authid'] = $this->genAuthKey();

        if ($data['confirm']) {
            // Check for a valid confirmation key
            if (!$this->confirmAuthKey()) {
                return;
            }

            // Get the data from the form
            $isvalid = $data['object']->checkInput();

            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                $data['context'] ??= $this->getContext();
                return xarTpl::module('mime', 'admin', 'modify', $data);
            } else {
                // Good data: create the item
                $itemid = $data['object']->updateItem(['itemid' => $data['itemid']]);

                // Jump to the next page
                $this->redirect($this->getUrl('admin', 'view'));
                return true;
            }
        }
        return $data;
    }
}
