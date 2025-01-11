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
use Exception;

sys::import('xaraya.modules.method');

/**
 * mime userapi get_magic function
 * @extends MethodClass<UserApi>
 */
class GetMagicMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get the magic number(s) for a particular mime subtype
     * @param array $args
     * with
     *     integer  magicId     the magicId of the magic # to lookup   (optional)
     *     string   magicValue  the magic value of the magic # to lookup (optional)
     * @uses \UserApi::getMagic()
     * @return array An array of (subtypeid, magicId, magic, offset, length) or an empty array
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($magicId) && !isset($magicValue)) {
            $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module[#(3)].', 'magicId', 'userapi_get_magic', 'mime');
            throw new Exception($msg);
        }
        /** @var UserApi $userapi */
        $userapi = xarMod::getAPI('mime');
        $userapi->setContext($this->getContext());

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
        $objectlist = $userapi->getMagic($args);

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
}
