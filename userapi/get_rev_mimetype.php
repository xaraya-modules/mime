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
use DataObjectFactory;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * mime userapi get_rev_mimetype function
 * @extends MethodClass<UserApi>
 */
class GetRevMimetypeMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get the typeId and subtypeId for a named mimeType (ie: application/octet-stream)
     * @author Carl P. Corliss
     * @access public
     * @param array<mixed> $args
     * @var string|int $mimeType the mime type we want to lookup id's for
     * @return array An array of (typeId, subtypeId) or an empty array
     * @see UserApi::getRevMimetype()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (empty($mimeType)) {
            // if not found return 0 for the id of both type / subtype
            return ['typeId' => 0, 'subtypeId' => 0];
        }
        if (is_numeric($mimeType)) {
            // Do a lookup
            $types = $this->data()->getObject(['name' => 'mime_types']);
            $types->getItem(['itemid' => $mimeType]);
            $mimeType = $types->properties['name']->value;
        }
        /** @var UserApi $userapi */
        $userapi = $this->userapi();

        $mimeType = explode('/', $mimeType);

        $typeInfo = $userapi->getType(['typeName' => $mimeType[0]]);
        if (!isset($typeInfo['typeId'])) {
            // if not found return 0 for the id of both type / subtype
            return ['typeId' => 0, 'subtypeId' => 0];
        } else {
            $typeId = $typeInfo['typeId'];
        }

        // Pick exact match here
        $subtypeInfo = $userapi->getSubtype([
            'typeName' => $mimeType[0],
            'subtypeName' => $mimeType[1],
        ]);

        if (!isset($subtypeInfo['subtypeId'])) {
            // if not found return 0 for the subtypeId
            return ['typeId' => (int) $typeId, 'subtypeId' => 0];
        } else {
            return ['typeId' => (int) $typeId, 'subtypeId' => (int) $subtypeInfo['subtypeId']];
        }
    }
}
