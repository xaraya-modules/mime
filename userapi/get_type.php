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
use sys;
use Exception;

sys::import('xaraya.modules.method');

/**
 * mime userapi get_type function
 * @extends MethodClass<UserApi>
 */
class GetTypeMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get the name of a mime type
     * @param array<mixed> $args
     * @var integer    $typeId   the ID of the mime type to lookup   (optional)
     * @var string     $typeName the Name of the mime type to lookup (optional)
     * @uses UserApi::getMimeTypes()
     * @return array An array of (typeId, typeName) or an empty array
     * @see UserApi::getType()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($typeId) && !isset($typeName)) {
            $msg = $this->ml('No (usable) parameter to work with (#(1)::#(2)::#(3))', 'mime', 'userapi', 'get_type');
            throw new Exception($msg);
        }
        /** @var UserApi $userapi */
        $userapi = $this->userapi();

        // apply where clauses if relevant
        if (isset($typeId)) {
            $args['where'] = [
                'id' => (int) $typeId,
            ];
            unset($args['typeId']);
        } else {
            $args['where'] = [
                'name' => strtolower($typeName),
            ];
            unset($args['typeName']);
        }
        $objectlist = $userapi->getMimeTypeList($args);

        $item = reset($objectlist->items);
        if (empty($item)) {
            return [];
        }
        return [
            'typeId'   => (int) $item['id'],
            'typeName' => (string) $item['name'],
        ];
    }
}
