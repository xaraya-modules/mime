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
 * mime userapi add_type function
 * @extends MethodClass<UserApi>
 */
class AddTypeMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get all mime types
     * @author Carl P. Corliss
     * @access public
     * @param array<mixed> $args
     * @var integer $typeName  the Name of the mime type to lookup (optional)
     * @deprecated 1.5.0 use league/mime-type-detection instead
     * @return array|false An array of (typeId, typeName) or an empty array
     * @see UserApi::addType()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        // Get database setup
        $dbconn = xarDB::getConn();
        $xartable     = & xarDB::getTables();

        if (!isset($typeName) || empty($typeName)) {
            $msg = xarML(
                'Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                'typeName',
                'userapi_add_type',
                'mime'
            );
            throw new Exception($msg);
        }

        // table and column definitions
        $type_table = & $xartable['mime_type'];
        $typeId = $dbconn->genID($type_table);

        $sql = "INSERT
                  INTO $type_table
                     (
                       id,
                       name
                     )
                VALUES (?, ?)";

        $bindvars = [$typeId, (string) $typeName];

        $result = $dbconn->Execute($sql, $bindvars);

        if (!$result) {
            return false;
        } else {
            return $dbconn->PO_Insert_ID($type_table, 'id');
        }
    }
}
