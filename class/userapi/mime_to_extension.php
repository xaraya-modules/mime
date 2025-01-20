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
use xarMod;
use sys;
use Exception;

sys::import('xaraya.modules.method');

/**
 * mime userapi mime_to_extension function
 * @extends MethodClass<UserApi>
 */
class MimeToExtensionMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Attempt to convert a MIME type to a file extension.
     * If we cannot map the type to a file extension, we return false.
     *
     * Code originally based on hordes Magic class (www.horde.org)
     * @param array<mixed> $args
     * @var string $mime_type MIME type to be mapped to a file extension.
     * @return string The file extension of the MIME type.
     * @see UserApi::mimeToExtension()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($mime_type) || empty($mime_type)) {
            $msg = $this->ml('Missing \'mime_type\' parameter!');
            throw new Exception($msg);
        }

        $typeparts = explode('/', $mime_type);
        if (count($typeparts) < 2) {
            $msg = $this->ml('Missing mime type or subtype parameter!');
            throw new Exception($msg);
        }
        /** @var UserApi $userapi */
        $userapi = $this->userapi();

        $args = [
            'typeName' => $typeparts[0],
            'subtypeName' => $typeparts[1],
        ];
        $subtypeInfo = $userapi->getSubtype($args);
        if (empty($subtypeInfo)) {
            return '';
        }

        $args = [
            'subtypeId' => (int) $subtypeInfo['subtypeId'],
        ];
        $extensions = $userapi->getallExtensions($args);
        // @todo what if we have more than 1 extension?
        $extensionInfo = reset($extensions);
        if (empty($extensionInfo)) {
            return '';
        }

        return $extensionInfo['extensionName'];
    }
}
