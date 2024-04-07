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
use xarModUserVars;
use xarVar;
use sys;

sys::import('modules.dynamicdata.class.objects.factory');
sys::import('modules.dynamicdata.class.traits.usergui');
sys::import('modules.mime.class.userapi');

/**
 * Class instance to handle the Mime Admin GUI
**/
class AdminGui implements UserGuiInterface
{
    use UserGuiTrait;

    /**
     * Summary of main
     * @param array<string, mixed> $args
     * @return array<mixed>
     */
    public function main(array $args = [])
    {
        // Pass along the context for xarTpl::module() if needed
        $args['context'] ??= $this->getContext();
        return $args;
    }

    /**
     * Summary of view
     * @param array<string, mixed> $args
     * @return array<mixed>
     */
    public function view(array $args = [])
    {
        // Define which object will be shown
        if (!xarVar::fetch('objectname', 'str', $args['objectname'], null, xarVar::DONT_SET)) {
            // Pass along the context for xarTpl::module() if needed
            $args['context'] ??= $this->getContext();
            return $args;
        }
        if (!empty($args['objectname'])) {
            xarModUserVars::set(UserApi::$moduleName, 'defaultmastertable', $args['objectname']);
        }
        $args['objectname'] = xarModUserVars::get(UserApi::$moduleName, 'defaultmastertable');
        $args['object'] = match ($args['objectname']) {
            'mime_types'      => UserApi::getMimeTypes([], $this->getContext()),
            'mime_subtypes'   => UserApi::getSubTypes([], $this->getContext()),
            'mime_extensions' => UserApi::getExtensions([], $this->getContext()),
            'mime_magic'      => UserApi::getMagic([], $this->getContext()),
        };

        // Get the available dropdown options
        $itemtypes = UserApi::getItemTypes();
        $options = [];
        foreach ($itemtypes as $itemtype => $item) {
            $options[] = ['id' => $item['name'], 'name' => $item['name']];
        }
        $args['options'] = $options;

        // Pass along the context for xarTpl::module() if needed
        $args['context'] ??= $this->getContext();
        return $args;
    }
}
