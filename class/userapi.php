<?php

/**
 * @package modules\mime
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
 *
 * @author mikespub <mikespub@xaraya.com>
**/

namespace Xaraya\Modules\Mime;

use Xaraya\DataObject\Traits\UserApiInterface;
use Xaraya\DataObject\Traits\UserApiTrait;
use Xaraya\DataObject\Traits\UserApiClass;
use DataObjectFactory;
use DataObjectList;
use sys;

sys::import('modules.dynamicdata.class.traits.userapi');

/**
 * Class to handle the Mime User API
 *
 * @method mixed addExtension(array $args) Get the name of a mime type
 * array{subtypeId: int, extensionName: string}
 * @method mixed addMagic(array $args = []) Get the magic number(s) for a particular mime subtype
 * array{subtypeId?: int}
 * @method mixed addSubtype(array $args) Get the name of a mime type
 * array{typeId: int, subtypeName: string, subtypeDesc: string}
 * @method mixed addType(array $args = []) Get all mime types
 * array{typeName?: int}
 * @method mixed analyzeFile(array $args) Uses variants of the UNIX "file" command to attempt to determine the - MIME type of an unknown file.
 * array{fileName: string, altFileName?: string, skipTest?: int}
 * @method mixed arraySearchR(array $args) Search an array recursivly - This function will search an array recursivly  till it finds what it is looking for. An array - within an array within an array within array is all good. It returns an array containing the - index names from the outermost index to the innermost, all the way up to the needle, or FALSE - if the needle was not found, example:
 * array{needle: string, haystack: array}
 * @method mixed extensionToMime(array $args) Tries to guess the mime type based on the file fileName.
 * array{fileName: string}
 * @method mixed getExtension(array $args = []) Get the name of an extension
 * array{extensionId?: int, extensionName?: string}
 * @method mixed getMagic(array $args = []) Get the magic number(s) for a particular mime subtype
 * array{magicId?: int, magicValue?: string}
 * @method mixed getMimeImage(array $args) Retrieves the name of the image file to use for a given mimetype.
 * array{mimeType: string, fileSuffix: string, defaultBase: string}
 * @method mixed getMimetype(array $args = []) Get the name of a mime type
 * array{subtypeId?: int, subtypeName?: int}
 * @method mixed getRevMimetype(array $args) Get the typeId and subtypeId for a named mimeType (ie: application/octet-stream)
 * array{mimeType: string|int}
 * @method mixed getSubtype(array $args) Get the name of a mime subtype
 * array{subtypeId?: int, subtypeName?: string, typeName: string, mimeName: string}
 * @method mixed getType(array $args = []) Get the name of a mime type
 * array{typeId?: int, typeName?: string}
 * @method mixed getallExtensions(array $args) Get the name of a mime type
 * array{subtypeId: int}
 * @method mixed getallMagic(array $args = []) Get the magic number(s) for a particular mime subtype
 * array{subtypeId?: int}
 * @method mixed getallSubtypes(array $args) Get details for mime subtypes
 * array{typeId: int, subtypeId: int, subtypeName: string, typeName: string, mimeName: string}
 * @method mixed getallTypes(array $args = []) Get all mime types
 * array{state?: int}
 * @method mixed importMimelist(array $args = [])
 * @method mixed mimeToExtension(array $args) Attempt to convert a MIME type to a file extension.
 * array{mime_type: string}
 * @extends UserApiClass<Module>
 */
class UserApi extends UserApiClass
{
    protected static MimeTypeDetector $detector;

    /**
     * Get mime type detector
     * @uses \sys::autoload()
     */
    public function getDetector()
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
    public function checkFileType($filePath)
    {
        return static::getDetector()->checkFileType($filePath);
    }

    /**
     * Summary of getNewExtension
     * @param string $mimeType
     * @return string|null
     */
    public function getNewExtension($mimeType)
    {
        return static::getDetector()->getExtension($mimeType);
    }

    /**
     * Summary of getMimeTypeList
     * @param array<string, mixed> $args
     * @return DataObjectList|null
     */
    public function getMimeTypeList($args = [])
    {
        $objectlist = DataObjectFactory::getObjectList(['name' => 'mime_types'], $this->context);
        if (!empty($args['where'])) {
            DataObjectFactory::applyObjectFilters($objectlist, $args['where']);
            // count the items
            //$objectlist->countItems();
        }
        $objectlist->getItems();
        return $objectlist;
    }

    /**
     * Summary of getSubTypeList
     * @param array<string, mixed> $args
     * @return DataObjectList|null
     */
    public function getSubTypeList($args = [])
    {
        $objectlist = DataObjectFactory::getObjectList(['name' => 'mime_subtypes'], $this->context);
        if (!empty($args['where'])) {
            DataObjectFactory::applyObjectFilters($objectlist, $args['where']);
            // count the items
            //$objectlist->countItems();
        }
        $objectlist->getItems();
        return $objectlist;
    }

    /**
     * Summary of getExtensionList
     * @param array<string, mixed> $args
     * @return DataObjectList|null
     */
    public function getExtensionList($args = [])
    {
        $objectlist = DataObjectFactory::getObjectList(['name' => 'mime_extensions'], $this->context);
        if (!empty($args['where'])) {
            DataObjectFactory::applyObjectFilters($objectlist, $args['where']);
            // count the items
            //$objectlist->countItems();
        }
        $objectlist->getItems();
        return $objectlist;
    }

    /**
     * Summary of getMagicList
     * @param array<string, mixed> $args
     * @return DataObjectList|null
     */
    public function getMagicList($args = [])
    {
        $objectlist = DataObjectFactory::getObjectList(['name' => 'mime_magic'], $this->context);
        if (!empty($args['where'])) {
            DataObjectFactory::applyObjectFilters($objectlist, $args['where']);
            // count the items
            //$objectlist->countItems();
        }
        $objectlist->getItems();
        return $objectlist;
    }
}
