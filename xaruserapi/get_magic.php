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
 *  Get the magic number(s) for a particular mime subtype
 *
 * @param array $args
 * with
 *     integer  magicId     the magicId of the magic # to lookup   (optional)
 *     string   magicValue  the magic value of the magic # to lookup (optional)
 * @uses UserApi::getMagic()
 * @return array      An array of (subtypeid, magicId, magic, offset, length) or an empty array
 */
function mime_userapi_get_magic(array $args = [], $context = null)
{
    extract($args);

    if (!isset($magicId) && !isset($magicValue)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module[#(3)].', 'magicId', 'userapi_get_magic', 'mime');
        throw new Exception($msg);
    }
    /** @var UserApi $userapi */
    $userapi = xarMod::getAPI('mime');
    $userapi->setContext($context);

    // apply where clauses if relevant
    if (isset($magicId)) {
        $args['where'] = [
            'id' => $magicId,
        ];
        unset($args['magicId']);
    } else {
        // @checkme no strtolower() here!
        $args['where'] = [
            'value' => $magicValue,
        ];
        unset($args['magicValue']);
    }
    $objectlist = $userapi->getMagic($args, $context);

    $item = reset($objectlist->items);
    if (empty($item)) {
        return [];
    }
    return [
        'subtypeId'   => $item['subtype_id'],
        'magicId'     => $item['id'],
        'magicValue'  => $item['value'],
        'magicOffset' => $item['offset'],
        'magicLength' => $item['length'],
    ];
}
