<?php

/**
 * @package modules\mime
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Mime\UserApi;


use Xaraya\Modules\Mime\UserApi;
use Xaraya\Modules\MethodClass;
use xarMod;
use sys;
use Exception;

sys::import('xaraya.modules.method');

/**
 * mime userapi getall_extensions function
 * @extends MethodClass<UserApi>
 */
class GetallExtensionsMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get the name of a mime type
     * @param array $args
     * with
     *     integer    subtypeId       the subtype ID of the type to grab extensions for
     * @uses \UserApi::getExtensions()
     * @return array An array of (subtypeId, extension) or an empty array
     */
    public function __invoke(array $args = [])
    {
        extract($args);
        /** @var UserApi $userapi */
        $userapi = xarMod::getAPI('mime');
        $userapi->setContext($this->getContext());

        // apply where clauses if relevant
        if (isset($subtypeId)) {
            if (is_int($subtypeId)) {
                $args['where'] = [
                    'subtype_id' => $subtypeId,
                ];
                unset($args['subtypeId']);
            } else {
                $msg = xarML(
                    'Supplied parameter [#(1)] for function [#(2)], is not an integer!',
                    'subtypeId',
                    'mime_userapi_getall_extensions'
                );
                throw new Exception($msg);
            }
        }
        $objectlist = $userapi->getExtensions($args);

        $extensionInfo = [];
        foreach ($objectlist->items as $itemid => $item) {
            $extensionInfo[$item['id']] = [
                'extensionId'   => $item['id'],
                'extensionName' => $item['name'],
            ];
        }

        return $extensionInfo;
    }
}
