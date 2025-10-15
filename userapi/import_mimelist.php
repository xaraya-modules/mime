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

sys::import('xaraya.modules.method');

/**
 * mime userapi import_mimelist function
 * @extends MethodClass<UserApi>
 */
class ImportMimelistMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Summary of __invoke
     * @param array<mixed> $args
     * @return bool
     * @see UserApi::importMimelist()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        /** @var UserApi $userapi */
        $userapi = $this->userapi();

        $descriptions = [];

        foreach ($mimeList as $mimeTypeText => $mimeInfo) {
            /*
                start off processing the mimetype and mimesubtype
                if niether of those exist, create them :)
            */
            $mimeType = explode('/', $mimeTypeText);

            $typeInfo = $userapi->getType(['typeName' => $mimeType[0]]);
            if (!isset($typeInfo['typeId'])) {
                $typeId = $userapi->addType(['typeName' => $mimeType[0]]);
            } else {
                $typeId = $typeInfo['typeId'];
            }

            $subtypeInfo = $userapi->getSubtype(['subtypeName' => $mimeType[1]]);
            if (!isset($subtypeInfo['subtypeId'])) {
                $subtypeId = $userapi->addSubtype([
                    'subtypeName'   => $mimeType[1],
                    'typeId'        => $typeId,
                    'subtypeDesc'   => ($mimeInfo['description'] ?? null),
                ]);
            } else {
                $subtypeId = $subtypeInfo['subtypeId'];
            }

            if (isset($mimeInfo['extensions']) && count($mimeInfo['extensions'])) {
                foreach ($mimeInfo['extensions'] as $extension) {
                    $extensionInfo = $userapi->getExtension(['extensionName' => $extension]);
                    if (!isset($extensionInfo['extensionId'])) {
                        $extensionId = $userapi->addExtension([
                            'subtypeId'     => $subtypeId,
                            'extensionName' => $extension,
                        ]);
                    } else {
                        $extensionId = $extensionInfo['extensionId'];
                    }
                }
            }

            if (isset($mimeInfo['needles']) && count($mimeInfo['needles'])) {
                foreach ($mimeInfo['needles'] as $magicNumber => $magicInfo) {
                    $info = $userapi->getMagic(['magicValue' => $magicNumber]);
                    if (!isset($info['magicId'])) {
                        $magicId = $userapi->addMagic([
                            'subtypeId'   => $subtypeId,
                            'magicValue'  => $magicNumber,
                            'magicOffset' => $magicInfo['offset'],
                            'magicLength' => $magicInfo['length'],
                        ]);
                    } else {
                        $magicId = $info['magicId'];
                    }
                }
            }
        }

        return true;
    }
}
