<?php

/**
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

use Xaraya\Modules\AdminGuiClass;
use sys;

sys::import('modules.dynamicdata.class.objects.factory');
sys::import('modules.mime.class.userapi');
sys::import('xaraya.modules.admingui');

/**
 * Handle the mime admin GUI
 *
 * @method mixed delete(array $args = [])
 * @method mixed main(array $args = []) Main admin GUI function, entry point
 * @method mixed modify(array $args = [])
 * @method mixed new(array $args = [])
 * @method mixed view(array $args = [])
 * @extends AdminGuiClass<Module>
 */
class AdminGui extends AdminGuiClass
{
    // ...
}
