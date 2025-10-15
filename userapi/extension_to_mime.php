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
use sys;
use Exception;

sys::import('xaraya.modules.method');

/**
 * mime userapi extension_to_mime function
 * @extends MethodClass<UserApi>
 */
class ExtensionToMimeMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Tries to guess the mime type based on the file fileName.
     * If it is unable to do so, it returns FALSE. If there is an error,
     * FALSE is returned along with an exception.
     *
     * Based on the Magic class for horde (www.horde.org)
     * @param array<mixed> $args
     * @var string $fileName Filename to grab fileName and check for mimetype for..
     * @return string|bool mime-type or FALSE with exception on error, FALSE and no exception if unknown mime-type
     * @see UserApi::extensionToMime()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($fileName) || empty($fileName)) {
            $msg = $this->ml('Missing fileName parameter!');
            throw new Exception($msg);
        }

        if (empty($fileName)) {
            return 'application/octet-stream';
        }
        $fileName = strtolower($fileName);
        $parts = explode('.', $fileName);

        // if there is only one part, then there was no '.'
        // seperator, hence no extension. So we fallback
        // to analyze_file()
        if (count($parts) < 2) {
            return 'application/octet-stream';
        }
        /** @var UserApi $userapi */
        $userapi = $this->userapi();

        $extension = $parts[count($parts) - 1];
        $extensionInfo = $userapi->getExtension(['extensionName' => $extension]);
        if (empty($extensionInfo)) {
            return 'application/octet-stream';
        }
        $mimeType = $userapi->getMimetype(['subtypeId' => (int) $extensionInfo['subtypeId']]);
        if (!empty($mimeType)) {
            return $mimeType;
        }
        return 'application/octet-stream';
    }
}
