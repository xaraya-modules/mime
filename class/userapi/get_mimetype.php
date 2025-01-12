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
use xarDB;
use sys;
use Exception;

sys::import('xaraya.modules.method');

/**
 * mime userapi get_mimetype function
 * @extends MethodClass<UserApi>
 */
class GetMimetypeMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get the name of a mime type
     * @author Carl P. Corliss
     * @access public
     * @param array<mixed> $args
     *      integer    subtypeId   the subtypeID of the mime subtype to lookup (optional)
     *      integer    subtypeName the Name of the mime sub type to lookup (optional)
     * @deprecated 1.5.0 use league/mime-type-detection instead
     * @return string A string of typeName/subtypeName or an empty string
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($subtypeId) && !isset($subtypeName)) {
            $msg = xarML('No (usable) parameter to work with (#(1)::#(2)::#(3))', 'mime', 'userapi', 'get_subtype');
            throw new Exception($msg);
        }
        $userapi = $this->getParent();

        // No need to duplicate the database query here.
        $subtypes = $userapi->getallSubtypes($args);

        if (empty($subtypes)) {
            // No matches.
            return '';
        }

        // Pick first match here
        $subtypeInfo = reset($subtypes);

        return $subtypeInfo['typeName'] . '/' . $subtypeInfo['subtypeName'];
    }
}
