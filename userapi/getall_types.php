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

/**
 * mime userapi getall_types function
 * @extends MethodClass<UserApi>
 */
class GetallTypesMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get all mime types
     * @param array<mixed> $args
     * @var integer    $state    the state of the mime type to lookup   (optional)
     * @uses UserApi::getMimeTypeList()
     * @return array An array of (typeId, typeName) or an empty array
     * @see UserApi::getallTypes()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        /** @var UserApi $userapi */
        $userapi = $this->userapi();

        // apply where clauses if relevant
        if (isset($state)) {
            $args['where'] = [
                'state' => (int) $state,
            ];
            unset($args['state']);
        }
        $objectlist = $userapi->getMimeTypeList($args);

        $typeInfo = [];
        foreach ($objectlist->items as $itemid => $item) {
            $typeInfo[$item['id']] = [
                'typeId'   => (int) $item['id'],
                'typeName' => (string) $item['name'],
            ];
        }

        return $typeInfo;
    }
}
