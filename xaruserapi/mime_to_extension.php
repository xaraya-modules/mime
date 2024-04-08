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
 * Attempt to convert a MIME type to a file extension.
 * If we cannot map the type to a file extension, we return false.
 *
 * Code originally based on hordes Magic class (www.horde.org)
 *
 * @param array $args
 * with
 *     string $mime_type MIME type to be mapped to a file extension.
 * @return  string The file extension of the MIME type.
 */
function mime_userapi_mime_to_extension(array $args = [], $context = null)
{
    extract($args);

    if (!isset($mime_type) || empty($mime_type)) {
        $msg = xarML('Missing \'mime_type\' parameter!');
        throw new Exception($msg);
    }

    $typeparts = explode('/', $mime_type);
    if (count($typeparts) < 2) {
        $msg = xarML('Missing mime type or subtype parameter!');
        throw new Exception($msg);
    }

    $args = [
        'typeName' => $typeparts[0],
        'subtypeName' => $typeparts[1],
    ];
    $subtypeInfo = xarMod::apiFunc('mime', 'user', 'get_subtype', $args);
    if (empty($subtypeInfo)) {
        return '';
    }

    $args = [
        'subtypeId' => $subtypeInfo['subtypeId'],
    ];
    $extensions = xarMod::apiFunc('mime', 'user', 'getall_extensions', $args);
    // @todo what if we have more than 1 extension?
    $extensionInfo = reset($extensions);
    if (empty($extensionInfo)) {
        return '';
    }

    return $extensionInfo['extensionName'];
}
