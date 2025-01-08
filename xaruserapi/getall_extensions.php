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
 *     integer    subtypeId       the subtype ID of the type to grab extensions for
 * @uses UserApi::getExtensions()
 * @return array      An array of (subtypeId, extension) or an empty array
 */
function mime_userapi_getall_extensions(array $args = [], $context = null)
{
    extract($args);
    /** @var UserApi $userapi */
    $userapi = xarMod::getAPI('mime');
    $userapi->setContext($context);

    // apply where clauses if relevant
    if (isset($subtypeId)) {
        if (is_int($subtypeId)) {
            $args['where'] = [
                'subtype_id' => $subtypeId,
            ];
            unset($args['subtypeId']);
        } else {
            $msg = xarML(
                'Supplied parameter [#(1)] for function [#(2)], is not an integer!',
                'subtypeId',
                'mime_userapi_getall_extensions'
            );
            throw new Exception($msg);
        }
    }
    $objectlist = $userapi->getExtensions($args, $context);

    $extensionInfo = [];
    foreach ($objectlist->items as $itemid => $item) {
        $extensionInfo[$item['id']] = [
            'extensionId'   => $item['id'],
            'extensionName' => $item['name'],
        ];
    }

    return $extensionInfo;
}
