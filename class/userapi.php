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
use DataObjectFactory;
use DataObjectList;
use sys;

sys::import('modules.dynamicdata.class.traits.userapi');

/**
 * Class to handle the Mime User API (static for now)
**/
class UserApi implements UserApiInterface
{
    use UserApiTrait;

    public static string $moduleName = 'mime';
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

    /**
     * Summary of checkFileType
     * @param string $filePath
     * @return string|null
     */
    public static function checkFileType($filePath)
    {
        return static::getDetector()->checkFileType($filePath);
    }

    /**
     * Summary of getExtension
     * @param string $mimeType
     * @return string|null
     */
    public static function getExtension($mimeType)
    {
        return static::getDetector()->getExtension($mimeType);
    }

    /**
     * Summary of getMimeTypes
     * @param array<string, mixed> $args
     * @param mixed $context
     * @return DataObjectList|null
     */
    public static function getMimeTypes($args = [], $context = null)
    {
        $objectlist = DataObjectFactory::getObjectList(['name' => 'mime_types'], $context);
        if (!empty($args['where'])) {
            DataObjectFactory::applyObjectFilters($objectlist, $args['where']);
            // count the items
            //$objectlist->countItems();
        }
        $objectlist->getItems();
        return $objectlist;
    }

    /**
     * Summary of getSubTypes
     * @param array<string, mixed> $args
     * @param mixed $context
     * @return DataObjectList|null
     */
    public static function getSubTypes($args = [], $context = null)
    {
        $objectlist = DataObjectFactory::getObjectList(['name' => 'mime_subtypes'], $context);
        if (!empty($args['where'])) {
            DataObjectFactory::applyObjectFilters($objectlist, $args['where']);
            // count the items
            //$objectlist->countItems();
        }
        $objectlist->getItems();
        return $objectlist;
    }

    /**
     * Summary of getExtensions
     * @param array<string, mixed> $args
     * @param mixed $context
     * @return DataObjectList|null
     */
    public static function getExtensions($args = [], $context = null)
    {
        $objectlist = DataObjectFactory::getObjectList(['name' => 'mime_extensions'], $context);
        if (!empty($args['where'])) {
            DataObjectFactory::applyObjectFilters($objectlist, $args['where']);
            // count the items
            //$objectlist->countItems();
        }
        $objectlist->getItems();
        return $objectlist;
    }

    /**
     * Summary of getMagic
     * @param array<string, mixed> $args
     * @param mixed $context
     * @return DataObjectList|null
     */
    public static function getMagic($args = [], $context = null)
    {
        $objectlist = DataObjectFactory::getObjectList(['name' => 'mime_magic'], $context);
        if (!empty($args['where'])) {
            DataObjectFactory::applyObjectFilters($objectlist, $args['where']);
            // count the items
            //$objectlist->countItems();
        }
        $objectlist->getItems();
        return $objectlist;
    }
}
