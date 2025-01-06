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

use Xaraya\Modules\AdminGuiInterface;
use Xaraya\Modules\AdminGuiTrait;
use xarModUserVars;
use xarVar;
use sys;

sys::import('modules.dynamicdata.class.objects.factory');
sys::import('modules.mime.class.userapi');
sys::import('xaraya.modules.adminguitrait');

/**
 * Class instance to handle the Mime Admin GUI
**/
class AdminGui implements AdminGuiInterface
{
    use AdminGuiTrait;

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
     * @return array<mixed>|void
     */
    public function view(array $args = [])
    {
        if (!$this->checkAccess('ManageMime')) {
            return;
        }
        // Define which object will be shown
        if (!xarVar::fetch('objectname', 'str', $args['objectname'], null, xarVar::DONT_SET)) {
            // Pass along the context for xarTpl::module() if needed
            $args['context'] ??= $this->getContext();
            return $args;
        }
        /** @var UserApi $userapi */
        $userapi = $this->getAPI();
        if (!empty($args['objectname'])) {
            xarModUserVars::set($this->moduleName, 'defaultmastertable', $args['objectname']);
        }
        $args['objectname'] = xarModUserVars::get($this->moduleName, 'defaultmastertable');
        $args['object'] = match ($args['objectname']) {
            'mime_types'      => $userapi::getMimeTypes([], $this->getContext()),
            'mime_subtypes'   => $userapi::getSubTypes([], $this->getContext()),
            'mime_extensions' => $userapi::getExtensions([], $this->getContext()),
            'mime_magic'      => $userapi::getMagic([], $this->getContext()),
        };

        // Get the available dropdown options
        $itemtypes = $userapi->getItemTypes();
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
