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
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * mime userapi getall_subtypes function
 * @extends MethodClass<UserApi>
 */
class GetallSubtypesMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get details for mime subtypes
     * @param array<mixed> $args
     * @var integer    $typeId the type ID of the mime type to grab subtypes for
     * @var integer    $subtypeId the subtype ID of the mime type, which should fetch just one subtype
     * @var string     $subtypeName the subtype name of the mime type, which should fetch just one subtype
     * @var string     $typeName the type name of the mime type
     * @var string     $mimeName the full two-part mime name
     * @uses UserApi::getSubTypeList()
     * @return array An array of (typeid, subtypeId, subtypeName, subtypeDesc) or an empty array
     * @see UserApi::getallSubtypes()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        /** @var UserApi $userapi */
        $userapi = $this->userapi();

        // The complete mime name can be passed in (type/subtype) and this
        // will be split up here for convenience.
        if (isset($mimeName) && is_string($mimeName)) {
            $parts = explode('/', strtolower(trim($mimeName)), 2);
            if (count($parts) == 2) {
                [$typeName, $subtypeName] = $parts;
            }
        }

        // get type_id and type_name here too
        $typelist = $userapi->getMimeTypeList();
        $mimetypes = $typelist->items;

        // apply where clauses if relevant
        $args['where'] = [];

        if (isset($typeId) && is_int($typeId)) {
            $args['where']['type'] = $typeId;
            unset($args['typeId']);
        }

        if (isset($subtypeId) && is_int($subtypeId)) {
            $args['where']['id'] = $subtypeId;
            unset($args['subtypeId']);
        }

        if (isset($subtypeName) && is_string($subtypeName)) {
            $args['where']['name'] = strtolower($subtypeName);
            unset($args['subtypeName']);
        }

        if (isset($typeName) && is_string($typeName)) {
            // look up type id here first
            foreach ($mimetypes as $type => $mimetype) {
                if ($typeName == $mimetype['name']) {
                    $args['where']['type'] = (int) $mimetype['id'];
                    break;
                }
            }
            unset($args['typeName']);
        }

        if (isset($state)) {
            $args['where']['state'] = (int) $state;
            unset($args['state']);
        }
        $objectlist = $userapi->getSubTypeList($args);

        $subtypeInfo = [];
        foreach ($objectlist->items as $itemid => $item) {
            $mimetype = [];
            if (!empty($item['type']) && !empty($mimetypes[$item['type']])) {
                $mimetype = $mimetypes[$item['type']];
            }
            $subtypeInfo[$item['id']] = [
                'subtypeId'   => (int) $item['id'],
                'subtypeName' => (string) $item['name'],
                'subtypeDesc' => (string) $item['description'],
                'typeId'      => (int) ($mimetype['id'] ?? $item['type']),
                'typeName'    => (string) ($mimetype['name'] ?? ''),
            ];
        }

        return $subtypeInfo;
    }
}
