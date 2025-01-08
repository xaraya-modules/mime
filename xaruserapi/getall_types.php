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
 *  Get all mime types
 *
 * @param array $args
 * with
 *     integer    state    the state of the mime type to lookup   (optional)
 * @uses UserApi::getMimeTypes()
 * @return array      An array of (typeId, typeName) or an empty array
 */
function mime_userapi_getall_types(array $args = [], $context = null)
{
    extract($args);
    /** @var UserApi $userapi */
    $userapi = xarMod::getAPI('mime');
    $userapi->setContext($context);

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
