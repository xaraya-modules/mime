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
use xarDB;
use sys;
use Exception;

sys::import('xaraya.modules.method');

/**
 * mime userapi get_mimetype function
 */
class GetMimetypeMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get the name of a mime type
     * @author Carl P. Corliss
     * @access public
     * @param array $args
     * with
     *      integer    subtypeId   the subtypeID of the mime subtype to lookup (optional)
     *      integer    subtypeName the Name of the mime sub type to lookup (optional)
     * @deprecated 1.5.0 use league/mime-type-detection instead
     * @return array|string|void An array of (subtypeId, subtypeName) or an empty array
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($subtypeId) && !isset($subtypeName)) {
            $msg = xarML('No (usable) parameter to work with (#(1)::#(2)::#(3))', 'mime', 'userapi', 'get_subtype');
            throw new Exception($msg);
        }

        // Get database setup
        $dbconn = xarDB::getConn();
        $xartable     = & xarDB::getTables();

        $where = ' WHERE ';

        if (isset($subtypeId)) {
            $where .= ' xmstype.id = ' . $subtypeId;
        } else {
            $where .= " xmstype.name = '" . strtolower($subtypeName) . "'";
        }

        // table and column definitions
        $subtype_table = & $xartable['mime_subtype'];
        $type_table    = & $xartable['mime_type'];

        $sql = "SELECT xmtype.sname AS mimetype,
                       xmstype.name AS mimesubtype
                  FROM $type_table AS xmtype, $subtype_table AS xmstype
                $where
                   AND xmtype.id = xmstype.type_id";

        $result = $dbconn->Execute($sql);

        if (!$result || $result->EOF) {
            return;
        }

        $row = $result->GetRowAssoc(false);

        return $row['mimetype'] . '/' . $row['mimesubtype'];
    }
}
