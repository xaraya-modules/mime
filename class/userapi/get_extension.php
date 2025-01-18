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
 * mime userapi get_extension function
 * @extends MethodClass<UserApi>
 */
class GetExtensionMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get the name of an extension
     * @param array<mixed> $args
     * @var integer   $extensionId       the ID of the extension to lookup   (optional)
     * @var string    $extensionName     the Name of the extension to lookup (optional)
     * @uses UserApi::getExtensionList()
     * @return array An array of (subtypeId, extension) or an empty array
     * @see UserApi::getExtension()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($extensionId) && !isset($extensionName)) {
            $msg = $this->ml('No (usable) parameter to work with (#(1)::#(2)::#(3))', 'mime', 'userapi', 'get_extension');
            throw new Exception($msg);
        }
        $userapi = $this->getParent();

        // apply where clauses if relevant
        if (isset($extensionId)) {
            $args['where'] = [
                'id' => (int) $extensionId,
            ];
            unset($args['extensionId']);
        } else {
            $args['where'] = [
                'name' => strtolower($extensionName),
            ];
            unset($args['extensionName']);
        }
        $objectlist = $userapi->getExtensionList($args);

        $item = reset($objectlist->items);
        if (empty($item)) {
            return [];
        }
        return [
            'subtypeId'     => (int) $item['subtype'],
            'extensionId'   => (int) $item['id'],
            'extensionName' => (string) $item['name'],
        ];
    }
}
