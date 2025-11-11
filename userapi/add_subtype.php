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
 * mime userapi add_subtype function
 * @extends MethodClass<UserApi>
 */
class AddSubtypeMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get the name of a mime type
     * @author Carl P. Corliss
     * @access public
     * @param array<mixed> $args
     * @var integer $typeId the type ID of the mime type to attch subtypes to
     * @var string $subtypeName the name of the subtype to add
     * @var string $subtypeDesc the description of the subtype to add
     * @deprecated 1.5.0 use league/mime-type-detection instead
     * @return array|false false on error, the sub type id otherwise
     * @see UserApi::addSubtype()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($typeId)) {
            $msg = $this->ml(
                'Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                'typeId',
                'userapi_add_subtypes',
                'mime'
            );
            throw new Exception($msg);
        }

        if (!isset($subtypeName)) {
            $msg = $this->ml(
                'Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                'subtypeName',
                'userapi_add_subtype',
                'mime'
            );
            throw new Exception($msg);
        }

        if (!isset($subtypeDesc) || !is_string($subtypeDesc)) {
            $subtypeDesc = null;
        }

        // Get database setup
        $dbconn = $this->db()->getConn();
        $xartable = & $this->db()->getTables();

        // table and column definitions
        $subtype_table = & $xartable['mime_subtype'];
        $subtypeId     = $dbconn->genID($subtype_table);

        $sql = "INSERT
                  INTO $subtype_table
                     (
                       id,
                       name,
                       type_id,
                       description
                     )
                VALUES (?, ?, ?, ?)";

        $bindvars = [$subtypeId, (string) $subtypeName, (int) $typeId, (string) $subtypeDesc];

        $result = $dbconn->Execute($sql, $bindvars);

        if (!$result) {
            return false;
        } else {
            return $dbconn->PO_Insert_ID($subtype_table, 'id');
        }
    }
}
