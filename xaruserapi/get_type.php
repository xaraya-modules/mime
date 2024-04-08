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
 *  Get the name of a mime type
 *
 * @param array $args
 * with
 *     integer    typeId   the ID of the mime type to lookup   (optional)
 *     string     typeName the Name of the mime type to lookup (optional)
 * @uses UserApi::getMimeTypes()
 * @return array      An array of (typeId, typeName) or an empty array
 */
function mime_userapi_get_type(array $args = [], $context = null)
{
    extract($args);

    if (!isset($typeId) && !isset($typeName)) {
        $msg = xarML('No (usable) parameter to work with (#(1)::#(2)::#(3))', 'mime', 'userapi', 'get_type');
        throw new Exception($msg);
    }

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
    $objectlist = UserApi::getMimeTypes($args, $context);

    $item = reset($objectlist->items);
    if (empty($item)) {
        return [];
    }
    return [
        'typeId'   => (int) $item['id'],
        'typeName' => $item['name'],
    ];
}
