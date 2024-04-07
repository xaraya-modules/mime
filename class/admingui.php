<?php
/**
 * @package modules\mime
 * @category Xaraya Web Applications Framework
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
 *
 * @author mikespub <mikespub@xaraya.com>
**/

namespace Xaraya\Modules\Mime;

use Xaraya\DataObject\Traits\UserGuiInterface;
use Xaraya\DataObject\Traits\UserGuiTrait;
use sys;

sys::import('modules.dynamicdata.class.objects.factory');
sys::import('modules.dynamicdata.class.traits.usergui');

/**
 * Class instance to handle the Mime Admin GUI
**/
class AdminGui implements UserGuiInterface
{
    use UserGuiTrait;
}
