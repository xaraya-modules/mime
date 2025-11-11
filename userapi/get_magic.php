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
use Exception;

/**
 * mime userapi get_magic function
 * @extends MethodClass<UserApi>
 */
class GetMagicMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get the magic number(s) for a particular mime subtype
     * @param array<mixed> $args
     * @var integer  $magicId     the magicId of the magic # to lookup   (optional)
     * @var string   $magicValue  the magic value of the magic # to lookup (optional)
     * @uses UserApi::getMagicList()
     * @return array An array of (subtypeid, magicId, magic, offset, length) or an empty array
     * @see UserApi::getMagic()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($magicId) && !isset($magicValue)) {
            $msg = $this->ml('Missing parameter [#(1)] for function [#(2)] in module[#(3)].', 'magicId', 'userapi_get_magic', 'mime');
            throw new Exception($msg);
        }
        /** @var UserApi $userapi */
        $userapi = $this->userapi();

        // apply where clauses if relevant
        if (isset($magicId)) {
            $args['where'] = [
                'id' => (int) $magicId,
            ];
            unset($args['magicId']);
        } else {
            // @checkme no strtolower() here!
            $args['where'] = [
                'value' => $magicValue,
            ];
            unset($args['magicValue']);
        }
        $objectlist = $userapi->getMagicList($args);

        $item = reset($objectlist->items);
        if (empty($item)) {
            return [];
        }
        return [
            'subtypeId'   => (int) $item['subtype'],
            'magicId'     => (int) $item['id'],
            'magicValue'  => (string) $item['value'],
            'magicOffset' => (int) $item['offset'],
            'magicLength' => (int) $item['length'],
        ];
    }
}
