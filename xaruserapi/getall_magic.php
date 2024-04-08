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
 *     integer    subtypeId   the magicId of the magic # to lookup   (optional)
 * @uses UserApi::getMagic()
 * @return array      An array of (subtypeid, magicId, magic, offset, length) or an empty array
 */
function mime_userapi_getall_magic(array $args = [], $context = null)
{
    extract($args);

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
                'mime_userapi_getall_magic'
            );
            throw new Exception($msg);
        }
    }
    $objectlist = UserApi::getMagic($args, $context);

    $magicInfo = [];
    foreach ($objectlist->items as $itemid => $item) {
        $magicInfo[$item['id']] = [
            'magicId'     => $item['id'],
            'subtypeId'   => $item['subtype_id'],
            'magicValue'  => $item['value'],
            'magicOffset' => $item['offset'],
            'magicLength' => $item['length'],
        ];
    }

    return $magicInfo;
}
