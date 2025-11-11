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
        if (!$this->sec()->checkAccess('ManageMime')) {
            return;
        }

        $this->var()->find('name', $name, 'str:1', 'mime_types');
        $this->var()->find('itemid', $data['itemid'], 'int', '');
        $this->var()->find('confirm', $data['confirm'], 'str:1', false);

        $data['object'] = $this->data()->getObject(['name' => $name]);
        $data['object']->getItem(['itemid' => $data['itemid']]);

        $data['tplmodule'] = 'mime';
        $data['authid'] = $this->sec()->genAuthKey();

        if ($data['confirm']) {
            // Check for a valid confirmation key
            if (!$this->sec()->confirmAuthKey()) {
                return;
            }

            // Delete the item
            $item = $data['object']->deleteItem();

            // Jump to the next page
            $this->ctl()->redirect($this->mod()->getURL('admin', 'view'));
            return true;
        }
        return $data;
    }
}
