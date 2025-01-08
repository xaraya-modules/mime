<?php
/**
 * Mime Module
 *
 * @package modules
 * @subpackage mime module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/999
 * @author Carl Corliss <rabbitt@xaraya.com>
 */
sys::import('modules.mime.class.userapi');
use Xaraya\Modules\Mime\UserApi;

/**
 *  Get details for mime subtypes
 *
 * @param array $args
 * with
 *     integer    typeId the type ID of the mime type to grab subtypes for
 *     integer    subtypeId the subtype ID of the mime type, which should fetch just one subtype
 *     string     subtypeName the subtype name of the mime type, which should fetch just one subtype
 *     string     typeName the type name of the mime type
 *     string     mimeName the full two-part mime name
 * @uses UserApi::getSubTypes()
 * @return array      An array of (typeid, subtypeId, subtypeName, subtypeDesc) or an empty array
 */
function mime_userapi_getall_subtypes(array $args = [], $context = null)
{
    extract($args);
    /** @var UserApi $userapi */
    $userapi = xarMod::getAPI('mime');
    $userapi->setContext($context);

    // The complete mime name can be passed in (type/subtype) and this
    // will be split up here for convenience.
    if (isset($mimeName) && is_string($mimeName)) {
        $parts = explode('/', strtolower(trim($mimeName)), 2);
        if (count($parts) == 2) {
            [$typeName, $subtypeName] = $parts;
        }
    }

    // get type_id and type_name here too
    $typelist = $userapi->getMimeTypes([], $context);
    $mimetypes = $typelist->items;

    // apply where clauses if relevant
    $args['where'] = [];

    if (isset($typeId) && is_int($typeId)) {
        $args['where']['type'] = (int) $typeId;
        unset($args['typeId']);
    }

    if (isset($subtypeId) && is_int($subtypeId)) {
        $args['where']['id'] = (int) $subtypeId;
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
        $args['where']['state'] = $state;
        unset($args['state']);
    }
    $objectlist = $userapi->getSubTypes($args, $context);

    $subtypeInfo = [];
    foreach ($objectlist->items as $itemid => $item) {
        $mimetype = [];
        if (!empty($item['type']) && !empty($mimetypes[$item['type']])) {
            $mimetype = $mimetypes[$item['type']];
        }
        $subtypeInfo[$item['id']] = [
            'subtypeId'   => $item['id'],
            'subtypeName' => $item['name'],
            'subtypeDesc' => $item['description'],
            'typeId'      => $mimetype['id'] ?? $item['type'],
            'typeName'    => $mimetype['name'] ?? '',
        ];
    }

    return $subtypeInfo;
}
