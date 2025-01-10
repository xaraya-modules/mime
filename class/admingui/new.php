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
 */
class NewMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    public function __invoke(array $args = [])
    {
        if (!xarSecurity::check('AddMime')) {
            return;
        }

        if (!xarVar::fetch('name', 'str', $name, 'mime_types', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('confirm', 'bool', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
            return;
        }

        $data['object'] = DataObjectFactory::getObject(['name' => $name]);
        $data['tplmodule'] = 'mime';
        $data['authid'] = xarSec::genAuthKey('mime');

        if ($data['confirm']) {
            // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
            if (!xarVar::fetch('preview', 'str', $preview, null, xarVar::DONT_SET)) {
                return;
            }

            // Check for a valid confirmation key
            if (!xarSec::confirmAuthKey()) {
                return;
            }

            // Get the data from the form
            $isvalid = $data['object']->checkInput();

            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                $data['context'] ??= $this->getContext();
                return xarTpl::module('mime', 'admin', 'new', $data);
            } else {
                // Good data: create the item
                $itemid = $data['object']->createItem();

                // Jump to the next page
                xarController::redirect(xarController::URL('mime', 'admin', 'view'), null, $this->getContext());
                return true;
            }
        }
        return $data;
    }
}
