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
        if (!$this->sec()->checkAccess('EditMime')) {
            return;
        }

        if (!$this->var()->find('name', $name, 'str', 'mime_types')) {
            return;
        }
        if (!$this->var()->find('itemid', $data['itemid'], 'int', 0)) {
            return;
        }
        if (!$this->var()->find('confirm', $data['confirm'], 'bool', false)) {
            return;
        }

        $data['object'] = $this->data()->getObject(['name' => $name]);
        $data['object']->getItem(['itemid' => $data['itemid']]);

        $data['tplmodule'] = 'mime';
        $data['authid'] = $this->sec()->genAuthKey();

        if ($data['confirm']) {
            // Check for a valid confirmation key
            if (!$this->sec()->confirmAuthKey()) {
                return;
            }

            // Get the data from the form
            $isvalid = $data['object']->checkInput();

            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                $data['context'] ??= $this->getContext();
                return $this->mod()->template('modify', $data);
            } else {
                // Good data: create the item
                $itemid = $data['object']->updateItem(['itemid' => $data['itemid']]);

                // Jump to the next page
                $this->ctl()->redirect($this->mod()->getURL('admin', 'view'));
                return true;
            }
        }
        return $data;
    }
}
