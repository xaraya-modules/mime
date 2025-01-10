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
 * mime userapi get_type function
 */
class GetTypeMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get the name of a mime type
     * @param array $args
     * with
     *     integer    typeId   the ID of the mime type to lookup   (optional)
     *     string     typeName the Name of the mime type to lookup (optional)
     * @uses \UserApi::getMimeTypes()
     * @return array An array of (typeId, typeName) or an empty array
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($typeId) && !isset($typeName)) {
            $msg = xarML('No (usable) parameter to work with (#(1)::#(2)::#(3))', 'mime', 'userapi', 'get_type');
            throw new Exception($msg);
        }
        /** @var UserApi $userapi */
        $userapi = xarMod::getAPI('mime');
        $userapi->setContext($this->getContext());

        // apply where clauses if relevant
        if (isset($typeId)) {
            $args['where'] = [
                'id' => $typeId,
            ];
            unset($args['typeId']);
        } else {
            $args['where'] = [
                'name' => strtolower($typeName),
            ];
            unset($args['typeName']);
        }
        $objectlist = $userapi->getMimeTypes($args, $this->getContext());

        $item = reset($objectlist->items);
        if (empty($item)) {
            return [];
        }
        return [
            'typeId'   => (int) $item['id'],
            'typeName' => $item['name'],
        ];
    }
}
