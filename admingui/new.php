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
 * mime admin new function
 * @extends MethodClass<AdminGui>
 */
class NewMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * @see AdminGui::new()
     */
    public function __invoke(array $args = [])
    {
        if (!$this->sec()->checkAccess('AddMime')) {
            return;
        }

        $this->var()->find('name', $name, 'str', 'mime_types');
        $this->var()->find('confirm', $data['confirm'], 'bool', false);

        $data['object'] = $this->data()->getObject(['name' => $name]);
        $data['tplmodule'] = 'mime';
        $data['authid'] = $this->sec()->genAuthKey();

        if ($data['confirm']) {
            // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
            $this->var()->check('preview', $preview, 'str');

            // Check for a valid confirmation key
            if (!$this->sec()->confirmAuthKey()) {
                return;
            }

            // Get the data from the form
            $isvalid = $data['object']->checkInput();

            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                $data['context'] ??= $this->getContext();
                return $this->mod()->template('new', $data);
            } else {
                // Good data: create the item
                $itemid = $data['object']->createItem();

                // Jump to the next page
                $this->ctl()->redirect($this->mod()->getURL('admin', 'view'));
                return true;
            }
        }
        return $data;
    }
}
