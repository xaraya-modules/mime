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
use Exception;

sys::import('xaraya.modules.method');

/**
 * mime userapi get_subtype function
 */
class GetSubtypeMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get the name of a mime subtype
     * @param array $args
     * with
     *     integer    subtypeId   the subtypeID of the mime subtype to lookup (optional)
     *     string     subtypeName the Name of the mime sub type to lookup (optional)
     *     string     typeName the type name of the mime type
     *     string     mimeName the full two-part mime name
     * @return array An array of (subtypeId, subtypeName) or an empty array
     */
    public function __invoke(array $args = [])
    {
        // Farm the query off.
        // No need to duplicate the database query here.
        $subtypes = xarMod::apiFunc('mime', 'user', 'getall_subtypes', $args);

        if (empty($subtypes)) {
            // No matches.
            return [];
        }

        if (count($subtypes) > 1) {
            // Too many matches.
            $msg = xarML('Too many matches to work with (#(1)::#(2)::#(3))', 'mime', 'userapi', 'get_subtype');
            throw new Exception($msg);
        }

        // There is a single subtype element - return just that element.
        return reset($subtypes);
    }
}
