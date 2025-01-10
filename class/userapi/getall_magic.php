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

use Xaraya\Modules\MethodClass;
use xarMod;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * mime userapi getall_magic function
 */
class GetallMagicMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get the magic number(s) for a particular mime subtype
     * @param array $args
     * with
     *     integer    subtypeId   the magicId of the magic # to lookup   (optional)
     * @uses \UserApi::getMagic()
     * @return array An array of (subtypeid, magicId, magic, offset, length) or an empty array
     */
    public function __invoke(array $args = [])
    {
        extract($args);
        /** @var UserApi $userapi */
        $userapi = xarMod::getAPI('mime');
        $userapi->setContext($this->getContext());

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
        $objectlist = $userapi->getMagic($args, $this->getContext());

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
}
