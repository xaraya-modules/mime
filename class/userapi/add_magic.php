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
use xarDB;
use sys;
use Exception;

sys::import('xaraya.modules.method');

/**
 * mime userapi add_magic function
 * @extends MethodClass<UserApi>
 */
class AddMagicMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get the magic number(s) for a particular mime subtype
     * @author Carl P. Corliss
     * @access public
     * @param array<mixed> $args
     * @var integer $subtypeId   the magicId of the magic # to lookup   (optional)788888888888888888888890
     * @deprecated 1.5.0 use league/mime-type-detection instead
     * @return array|false An array of (subtypeid, magicId, magic, offset, length) or an empty array
     * @see UserApi::addMagic()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($subtypeId)) {
            $msg =  $this->translate(
                'Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                'subtypeId',
                'userapi_add_magic',
                'mime'
            );
            throw new Exception($msg);
        }

        if (!isset($magicValue)) {
            $msg =  $this->translate(
                'Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                'magicValue',
                'userapi_add_magic',
                'mime'
            );
            throw new Exception($msg);
        }

        if (!isset($magicOffset)) {
            $msg =  $this->translate(
                'Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                'magicOffset',
                'userapi_add_magic',
                'mime'
            );
            throw new Exception($msg);
        }

        if (!isset($magicLength)) {
            $msg =  $this->translate(
                'Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                'magicLength',
                'userapi_add_magic',
                'mime'
            );
            throw new Exception($msg);
        }

        // Get database setup
        $dbconn = xarDB::getConn();
        $xartable     = & xarDB::getTables();

        // table and column definitions
        $magic_table = & $xartable['mime_magic'];
        $magicId     =  $dbconn->genID($magic_table);

        $sql = "INSERT
                  INTO $magic_table
                     (
                       id,
                       subtype_id,
                       value,
                       offset,
                       length
                     )
                VALUES (?, ?, ?, ?, ?)";

        $bindvars = [(int) $magicId, $subtypeId, (string) $magicValue, (int) $magicOffset, (int) $magicLength];

        $result = $dbconn->Execute($sql, $bindvars);

        if (!$result) {
            return false;
        } else {
            return $dbconn->PO_Insert_ID($magic_table, 'id');
        }
    }
}
