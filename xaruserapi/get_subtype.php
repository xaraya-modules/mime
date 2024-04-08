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

/**
 *  Get the name of a mime subtype
 *
 * @param array $args
 * with
 *     integer    subtypeId   the subtypeID of the mime subtype to lookup (optional)
 *     string     subtypeName the Name of the mime sub type to lookup (optional)
 *     string     typeName the type name of the mime type
 *     string     mimeName the full two-part mime name
 * @return array      An array of (subtypeId, subtypeName) or an empty array
 */
function mime_userapi_get_subtype(array $args = [], $context = null)
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
