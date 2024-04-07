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

use Xaraya\DataObject\Traits\UserApiInterface;
use Xaraya\DataObject\Traits\UserApiTrait;
use sys;

sys::import('modules.dynamicdata.class.traits.userapi');

/**
 * Class to handle the Mime User API (static for now)
**/
class UserApi implements UserApiInterface
{
    use UserApiTrait;

    protected static string $moduleName = 'mime';
    protected static int $moduleId = 999;
    protected static int $itemtype = 0;
    protected static MimeTypeDetector $detector;

    /**
     * Get mime type detector
     * @uses \sys::autoload()
     */
    public static function getDetector()
    {
        if (!isset(static::$detector)) {
            sys::autoload();
            static::$detector = new MimeTypeDetector();
        }
        return static::$detector;
    }
}
