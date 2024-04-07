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

    // @todo apply where clauses if relevant
    $where = [];
    $bind = [];

    // The complete mime name can be passed in (type/subtype) and this
    // will be split up here for convenience.
    if (isset($mimeName) && is_string($mimeName)) {
        $parts = explode('/', strtolower(trim($mimeName)), 2);
        if (count($parts) == 2) {
            [$typeName, $subtypeName] = $parts;
        }
    }

    if (isset($typeId) && is_int($typeId)) {
        $where[] = 'subtype_tab.type_id = ?';
        $bind[] = (int) $typeId;
    }

    if (isset($subtypeId) && is_int($subtypeId)) {
        $where[] = 'subtype_tab.id = ?';
        $bind[] = (int) $subtypeId;
    }

    if (isset($subtypeName) && is_string($subtypeName)) {
        $where[] = 'subtype_tab.name = ?';
        $bind[] = strtolower($subtypeName);
    }

    if (isset($typeName) && is_string($typeName)) {
        $where[] = 'type_tab.name = ?';
        $bind[] = strtolower($typeName);
    }

    if (isset($typeName) && is_string($typeName)) {
        $where[] = 'type_tab.name = ?';
        $bind[] = strtolower($typeName);
    }

    if (isset($state)) {
        if (is_array($state)) {
            $where[] = 'subtype_tab.state in (?)';
            $bind[] = implode(', ', $state) ;
        } else {
            $where[] = 'subtype_tab.state = ?';
            $bind[] = (int) $state;
        }
    }
    $objectlist = UserApi::getSubTypes($args, $context);
    // get type_id and type_name here too
    $typelist = static::getMimeTypes([], $context);
    $mimetypes = $typelist->items;

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
