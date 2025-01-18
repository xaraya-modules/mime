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
 * mime userapi analyze_file function
 * @extends MethodClass<UserApi>
 */
class AnalyzeFileMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Uses variants of the UNIX "file" command to attempt to determine the
     * MIME type of an unknown file.
     * (note: based off of the Magic class in Horde <www.horde.org>)
     * @todo replace with more recent equivalent - see MimeTypeDetector()
     * @param array<mixed> $args
     * @var string $fileName The path to the file to analyze.
     * @var string $altFileName Alternate file name to analyze extension (Optional).
     * @var int $skipTest Skip a number of tests to verify methods (Optional).
     * @return string returns the mime type and optional charset of the file, or FALSE on error.
     * If it can't figure out the type based on the magic entries
     * it will try to guess one of either text/plain or
     * application/octet-stream by reading the first 256 bytes of the file
     * @see UserApi::analyzeFile()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($fileName)) {
            $msg = $this->ml('Unable to retrieve mime type. No filename supplied!');
            throw new Exception($msg);
        }
        if (!file_exists($fileName)) {
            $msg = $this->ml('Unable to retrieve mime type. File does not exist!');
            throw new Exception($msg);
        }

        if (!isset($altFileName) || !strlen($altFileName)) {
            $altFileName = $fileName;
        }
        if (empty($skipTest)) {
            $skipTest = 0;
        }

        // Start off trying mime_content_type
        if ($skipTest < 1 && function_exists('mime_content_type')) {
            $ftype = mime_content_type($fileName);
            if (isset($ftype) && strlen($ftype)) {
                return $ftype;
            }
        }

        // Try to use if disponible pecl fileinfo extension
        // Note: as of PHP 5.3 this is included in the PHP distribution. Leave the if condition here, doesn't do any harm.
        if ($skipTest < 2 && extension_loaded('fileinfo')) {
            $res = finfo_open(FILEINFO_MIME);
            $mime_type = finfo_file($res, $fileName);
            finfo_close($res);
            if (isset($mime_type) && strlen($mime_type)) {
                if (str_contains($mime_type, ';')) {
                    [$mime_type, $charset] = explode(';', $mime_type);
                }
                return $mime_type;
            }
        }

        // If that didn't work, try getimagesize to see if the file is an image
        if ($skipTest < 3) {
            $fileInfo = @getimagesize($fileName);
            if (is_array($fileInfo) && isset($fileInfo['mime'])) {
                return $fileInfo['mime'];
            }
        }
        $userapi = $this->getParent();

        // Otherwise, see if the file is empty and, if so
        // return it as octet-stream
        $fileSize = filesize($fileName);
        if (!$fileSize) {
            $parts = explode('.', $altFileName);
            if (is_array($parts) && count($parts)) {
                $extension = basename(end($parts));
                $typeInfo = $userapi->getExtension(['extensionName' => $extension]);
                if (is_array($typeInfo) && count($typeInfo)) {
                    $mimeType = $userapi->getMimetype(['subtypeId' => (int) $typeInfo['subtypeId']]);
                    return $mimeType;
                } else {
                    return 'application/octet-stream';
                }
            } else {
                return 'application/octet-stream';
            }
        }
        // Otherwise, actually test the contents of the file
        if (!($fp = @fopen($fileName, 'rb'))) {
            $msg = $this->ml('Unable to analyze file [#(1)]. Cannot open for reading!', $fileName);
            throw new Exception($msg);
        } else {
            $mime_list = $userapi->getallMagic();


            foreach ($mime_list as $mime_type => $mime_info) {
                // if this mime_type doesn't have a
                // magic string to check against, then
                // go ahead and skip to the next one
                if (!isset($mime_info['needles'])) {
                    continue;
                }

                foreach ($mime_info as $magicInfo) {
                    // if the offset is beyond the range of the file
                    // continue on to the next item
                    if ($magicInfo['offset'] >= $fileSize) {
                        continue;
                    }

                    if ($magicInfo['offset'] >= 0) {
                        if (@fseek($fp, $magicInfo['offset'], SEEK_SET)) {
                            $msg = $this->ml(
                                'Unable to seek to offset [#(1)] within file: [#(2)]',
                                $magicInfo['offset'],
                                $fileName
                            );
                            throw new Exception($msg);
                        }
                    }

                    if (!($value = @fread($fp, $magicInfo['length']))) {
                        $msg = $this->ml(
                            'Unable to read (#(1) bytes) from file: [#(2)].',
                            $magicInfo['length'],
                            $fileName
                        );
                        throw new Exception($msg);
                    }

                    if ($magicInfo['value'] == base64_encode($value)) {
                        fclose($fp);
                        $mimeType = $userapi->getMimetype(['subtypeId' => (int) $magicInfo['subtypeId']]);
                        if (!empty($mimeType)) {
                            return $mimeType;
                        }
                    }
                }
            }

            $parts = explode('.', $altFileName);
            if (is_array($parts) && count($parts)) {
                $extension = basename(end($parts));
                $typeInfo = $userapi->getExtension(['extensionName' => $extension]);
                if (is_array($typeInfo) && count($typeInfo)) {
                    $mimeType = $userapi->getMimetype(['subtypeId' => (int) $typeInfo['subtypeId']]);
                    return $mimeType;
                }
            }

            if (!rewind($fp)) {
                $msg = $this->ml('Unable to rewind to beginning of file: [#(1)]', $fileName);
                throw new Exception($msg);
            }

            if (!($value = @fread($fp, 256))) {
                $msg = $this->ml('Unable to read (256 bytes) from file: [#(1)]', $fileName);
                throw new Exception($msg);
            }

            // get rid of printable characters so we can
            // use ctype_print to check for printable characters
            // which, in a binary file, there shouldn't be any
            $value = str_replace(["\n","\r","\t"], '', $value);

            // if there are non-printable characters,
            // then the file is of application/octet-stream
            // Note that we use preg_match here to search for non-printable
            // characters - it's a "PHP Version Safe" work around for ctype_* problems.
            if (preg_match('/[^[:print:]]/', $value)) {
                $mime_type = 'application/octet-stream';
            } else {
                $mime_type = 'text/plain';
            }

            if ($fp) {
                fclose($fp);
            }

            return $mime_type;
        }
    }
}
