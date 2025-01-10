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
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * mime userapi import_mimelist function
 */
class ImportMimelistMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    public function __invoke(array $args = [])
    {
        extract($args);

        $descriptions = [];

        foreach ($mimeList as $mimeTypeText => $mimeInfo) {
            /*
                start off processing the mimetype and mimesubtype
                if niether of those exist, create them :)
            */
            $mimeType = explode('/', $mimeTypeText);

            $typeInfo = xarMod::apiFunc('mime', 'user', 'get_type', ['typeName' => $mimeType[0]]);
            if (!isset($typeInfo['typeId'])) {
                $typeId = xarMod::apiFunc('mime', 'user', 'add_type', ['typeName' => $mimeType[0]]);
            } else {
                $typeId = & $typeInfo['typeId'];
            }

            $subtypeInfo = xarMod::apiFunc('mime', 'user', 'get_subtype', ['subtypeName' => $mimeType[1]]);
            if (!isset($subtypeInfo['subtypeId'])) {
                $subtypeId = xarMod::apiFunc(
                    'mime',
                    'user',
                    'add_subtype',
                    [
                        'subtypeName'   => $mimeType[1],
                        'typeId'        => $typeId,
                        'subtypeDesc'   => ($mimeInfo['description'] ?? null),
                    ]
                );
            } else {
                $subtypeId = & $subtypeInfo['subtypeId'];
            }

            if (isset($mimeInfo['extensions']) && count($mimeInfo['extensions'])) {
                foreach ($mimeInfo['extensions'] as $extension) {
                    $extensionInfo = xarMod::apiFunc(
                        'mime',
                        'user',
                        'get_extension',
                        ['extensionName' => $extension]
                    );
                    if (!isset($extensionInfo['extensionId'])) {
                        $extensionId = xarMod::apiFunc(
                            'mime',
                            'user',
                            'add_extension',
                            ['subtypeId'     => $subtypeId,
                                'extensionName' => $extension, ]
                        );
                    } else {
                        $extensionId = $extensionInfo['extensionId'];
                    }
                }
            }

            if (isset($mimeInfo['needles']) && count($mimeInfo['needles'])) {
                foreach ($mimeInfo['needles'] as $magicNumber => $magicInfo) {
                    $info = xarMod::apiFunc(
                        'mime',
                        'user',
                        'get_magic',
                        ['magicValue' => $magicNumber]
                    );
                    if (!isset($info['magicId'])) {
                        $magicId = xarMod::apiFunc(
                            'mime',
                            'user',
                            'add_magic',
                            ['subtypeId'   => $subtypeId,
                                'magicValue'  => $magicNumber,
                                'magicOffset' => $magicInfo['offset'],
                                'magicLength' => $magicInfo['length'], ]
                        );
                    } else {
                        $magicId = $info['magicId'];
                    }
                }
            }
        }

        return true;
    }
}
