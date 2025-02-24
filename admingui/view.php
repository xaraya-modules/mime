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

namespace Xaraya\Modules\Mime\AdminGui;


use Xaraya\Modules\Mime\AdminGui;
use Xaraya\Modules\MethodClass;
use Xaraya\Modules\Mime\UserApi;
use xarModUserVars;
use xarVar;
use sys;

sys::import('xaraya.modules.method');

/**
 * Admin view GUI function
 * @extends MethodClass<AdminGui>
 */
class ViewMethod extends MethodClass
{
    /**
     * Summary of view
     * @param array<string, mixed> $args
     * @return array<mixed>|void
     * @see AdminGui::view()
     */
    public function __invoke(array $args = [])
    {
        if (!$this->sec()->checkAccess('ManageMime')) {
            return;
        }
        // Define which object will be shown
        $this->var()->check('objectname', $args['objectname'], 'str');

        /** @var UserApi $userapi */
        $userapi = $this->userapi();

        if (!empty($args['objectname'])) {
            xarModUserVars::set($this->getModName(), 'defaultmastertable', $args['objectname']);
        }
        $args['objectname'] = xarModUserVars::get($this->getModName(), 'defaultmastertable');
        $args['object'] = match ($args['objectname']) {
            'mime_types'      => $userapi->getMimeTypeList(),
            'mime_subtypes'   => $userapi->getSubTypeList(),
            'mime_extensions' => $userapi->getExtensionList(),
            'mime_magic'      => $userapi->getMagicList(),
        };

        // Get the available dropdown options
        $itemtypes = $userapi->getItemTypes();
        $options = [];
        foreach ($itemtypes as $itemtype => $item) {
            $options[] = ['id' => $item['name'], 'name' => $item['name']];
        }
        $args['options'] = $options;

        // Pass along the context for $this->tpl()->module() if needed
        $args['context'] ??= $this->getContext();
        return $args;
    }
}
