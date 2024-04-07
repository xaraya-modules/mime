<?php
/**
 * @package modules\mime
 * @category Xaraya Web Applications Framework
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
 *
 * @author mikespub <mikespub@xaraya.com>
**/

namespace Xaraya\Modules\Mime;

use League\MimeTypeDetection\FinfoMimeTypeDetector;
use League\MimeTypeDetection\GeneratedExtensionToMimeTypeMap;

/**
 * Class to handle the actual mime type detection
 * @uses \sys::autoload()
**/
class MimeTypeDetector
{
    protected FinfoMimeTypeDetector $detector;
    protected GeneratedExtensionToMimeTypeMap $map;

    public function __construct() {}

    public function checkFileType(string $path)
    {
        if (is_file($path)) {
            if (filesize($path) > 100000) {
                // @todo use sample, see detectMimeTypeFromBuffer()
                $mimeType = $this->getDetector()->detectMimeTypeFromFile($path);
                if (is_null($mimeType) || $mimeType == 'text/plain') {
                    $resource = fopen($path, 'r');
                    // @todo this doesn't actually support resources yet :-)
                    $mimeType = $this->getDetector()->detectMimeType($path, $resource);
                }
                return $mimeType;
            }
            $contents = file_get_contents($path);
            return $this->getDetector()->detectMimeType($path, $contents);
        }
        return $this->getDetector()->detectMimeTypeFromPath($path);
    }

    /**
     * @todo check with getimagesize() or imagecreatefrom...
     * @see comment at https://www.php.net/manual/en/function.finfo-file.php
     */
    public function checkImageType(string $path)
    {
        // ...
        return true;
    }

    /**
     * @todo unsupported extensions .xt and .*.twig
     */
    public function getMimeType(string $extension)
    {
        return $this->getMap()->lookupMimeType($extension);
    }

    /**
     * @todo only one mimetype supported for now
     * @see https://github.com/thephpleague/mime-type-detection/issues/20
     */
    public function getAllMimeTypes(string $extension)
    {
        return [ $this->getMap()->lookupMimeType($extension) ];
    }

    public function getExtension(string $mimeType)
    {
        return $this->getDetector()->lookupExtension($mimeType);
    }

    public function getAllExtensions(string $mimeType)
    {
        return $this->getDetector()->lookupAllExtensions($mimeType);
    }

    protected function getDetector()
    {
        if (!isset($this->detector)) {
            $this->detector = new FinfoMimeTypeDetector();
        }
        return $this->detector;
    }

    protected function getMap()
    {
        if (!isset($this->map)) {
            $this->map = new GeneratedExtensionToMimeTypeMap();
        }
        return $this->map;
    }
}
