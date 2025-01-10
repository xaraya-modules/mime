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
 * mime userapi add_extension function
 */
class AddExtensionMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get the name of a mime type
     * @author Carl P. Corliss
     * @access public
     * @param array $args
     * with
     *     integer    $subtypeId      the subtype ID to add an extension for
     *     string     $extensionName  the extension name to add
     * @deprecated 1.5.0 use league/mime-type-detection instead
     * @return array|false An array of (subtypeId, extension) or an empty array
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($subtypeId)) {
            $msg = xarML(
                'Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                'subtypeId',
                'userapi_add_extension',
                'mime'
            );
            throw new Exception($msg);
        }

        if (!isset($extensionName)) {
            $msg = xarML(
                'Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                'extensionName',
                'userapi_add_extension',
                'mime'
            );
            throw new Exception($msg);
        }

        // Get database setup
        $dbconn = xarDB::getConn();
        $xartable     = & xarDB::getTables();

        // table and column definitions
        $extension_table = & $xartable['mime_extension'];
        $extensionId     = $dbconn->genID($extension_table);

        $sql = "INSERT
                  INTO $extension_table
                     ( subtype_id,
                       id,
                       name
                     )
                VALUES (?, ?, ?)";
        $bindvars = [(int) $subtypeId, $extensionId, (string) strtolower($extensionName)];

        $result = $dbconn->Execute($sql, $bindvars);

        if (!$result) {
            return false;
        } else {
            return $dbconn->PO_Insert_ID($extension_table, 'id');
        }
    }
}
