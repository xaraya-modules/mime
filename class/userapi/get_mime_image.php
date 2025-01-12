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
use xarTpl;
use sys;
use Exception;

sys::import('xaraya.modules.method');

/**
 * mime userapi get_mime_image function
 * @extends MethodClass<UserApi>
 */
class GetMimeImageMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Retrieves the name of the image file to use for a given mimetype.
     * If no image file exists for the given mimtype, the unknown image file
     * will be used.
     * @param array<mixed> $args
     *     string mimeType     The mime type to find a correlating image for
     *     string fileSuffix   Image file suffix list (default: '.png')
     *     string defaultBase  Default file base name (default: 'default')
     * @return string
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($mimeType)) {
            // API location handled centrally.
            $msg = xarML('Missing parameter [#(1)].', 'mimeType');
            throw new Exception($msg);
        }

        // Defaults.
        if (empty($fileSuffix)) {
            $fileSuffix = '.png';
        }
        if (empty($defaultBase)) {
            $defaultBase = 'default';
        }

        // Explode the list of suffixes.
        // A list of suffixes to try can be given, e.g. '-8x8.gif|.png'
        $fileSuffixes = explode('|', $fileSuffix);

        $mimeType = explode('/', $mimeType);
        if (count($mimeType) != 2) {
            $imageFile = $defaultBase . $fileSuffix;
        }

        // Try the complete mimetype-subtype image.
        foreach ($fileSuffixes as $fileSuffix) {
            $imageFile = $mimeType[0] . '-' . $mimeType[1] . $fileSuffix;
            if ($imageURI = xarTpl::getImage($imageFile, 'mime')) {
                break;
            }
        }

        // Otherwise, try the top level mimetype image.
        if ($imageURI == null) {
            foreach ($fileSuffixes as $fileSuffix) {
                $imageFile = $mimeType[0] . $fileSuffix;
                if ($imageURI = xarTpl::getImage($imageFile, 'mime')) {
                    break;
                }
            }

            if ($imageURI == null) {
                foreach ($fileSuffixes as $fileSuffix) {
                    $imageFile = $defaultBase . $fileSuffix;
                    if ($imageURI = xarTpl::getImage($imageFile, 'mime')) {
                        break;
                    }
                }
            }
        }

        // Single point of return.
        // We also have 'imageFile' set, which could be a useful (alternative) return value.
        return $imageURI;
    }
}
