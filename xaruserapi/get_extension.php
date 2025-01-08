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
 *  Get the name of an extension
 *
 * @param array $args
 * with
 *     integer   extensionId       the ID of the extension to lookup   (optional)
 *     string    extensionName     the Name of the extension to lookup (optional)
 * @uses UserApi::getExtensions()
 * @return array      An array of (subtypeId, extension) or an empty array
 */
function mime_userapi_get_extension(array $args = [], $context = null)
{
    extract($args);

    if (!isset($extensionId) && !isset($extensionName)) {
        $msg = xarML('No (usable) parameter to work with (#(1)::#(2)::#(3))', 'mime', 'userapi', 'get_extension');
        throw new Exception($msg);
    }
    /** @var UserApi $userapi */
    $userapi = xarMod::getAPI('mime');
    $userapi->setContext($context);

    // apply where clauses if relevant
    if (isset($extensionId)) {
        $args['where'] = [
            'id' => $extensionId,
        ];
        unset($args['extensionId']);
    } else {
        $args['where'] = [
            'name' => strtolower($extensionName),
        ];
        unset($args['extensionName']);
    }
    $objectlist = $userapi->getExtensions($args, $context);

    $item = reset($objectlist->items);
    if (empty($item)) {
        return [];
    }
    return [
        'subtypeId'     => $item['subtype_id'],
        'extensionId'   => $item['id'],
        'extensionName' => $item['name'],
    ];
}
