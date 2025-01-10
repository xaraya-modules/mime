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

use Xaraya\Modules\MethodClass;
use xarMod;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * mime userapi getall_types function
 */
class GetallTypesMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get all mime types
     * @param array $args
     * with
     *     integer    state    the state of the mime type to lookup   (optional)
     * @uses \UserApi::getMimeTypes()
     * @return array An array of (typeId, typeName) or an empty array
     */
    public function __invoke(array $args = [])
    {
        extract($args);
        /** @var UserApi $userapi */
        $userapi = xarMod::getAPI('mime');
        $userapi->setContext($this->getContext());

        // apply where clauses if relevant
        if (isset($state)) {
            $args['where'] = [
                'state' => $state,
            ];
            unset($args['state']);
        }
        $objectlist = $userapi->getMimeTypes($args);

        $typeInfo = [];
        foreach ($objectlist->items as $itemid => $item) {
            $typeInfo[$item['id']] = [
                'typeId'   => $item['id'],
                'typeName' => $item['name'],
            ];
        }

        return $typeInfo;
    }
}
