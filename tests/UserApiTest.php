<?php

use Xaraya\Modules\TestHelper;
use Xaraya\Modules\Mime\UserApi;
use Xaraya\Modules\Mime\MimeTypeDetector;

final class UserApiTest extends TestHelper
{
    protected function setUp(): void
    {
        // file paths are relative to parent directory
        chdir(dirname(__DIR__));
    }

    protected function tearDown(): void {}

    public function testGetDetector(): void
    {
        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');

        $expected = MimeTypeDetector::class;
        $detector = $userapi->getDetector();
        $this->assertEquals($expected, $detector::class);
    }

    /**
     * Expected file results based on content & extension
     */
    public static function provideFileResults()
    {
        return [
            'php' => ['xarversion.php', 'text/x-php', 'application/x-httpd-php'],
            'png' => ['xarimages/image.png', 'image/png', 'image/png'],
            'xml' => ['xardata/adminmenu-dat.xml', 'text/html', 'application/xml'],
            'txt' => ['xardocs/license.txt', 'text/plain', 'text/plain'],
            'md' => ['README.md', 'text/plain', 'text/markdown'],
            'json' => ['composer.json', 'application/json', 'application/json'],
            'xt' => ['xartemplates/admin-main.xt', 'text/xml', null],
            'twig' => ['templates/admin/main.html.twig', 'text/plain', null],
        ];
    }

    /**
     * @dataProvider provideFileResults
     */
    public function testCheckFileType($path, $expectedMimeType, $expectedAlternative): void
    {
        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $mimeType = $userapi->checkFileType($path);
        if ($expectedMimeType == 'text/plain') {
            $expected = $expectedAlternative;
        } elseif (!file_exists($path)) {
            // if file does not exist, we should expect the alternative
            $expected = $expectedAlternative;
        } else {
            $expected = $expectedMimeType;
        }
        $this->assertEquals($expected, $mimeType);
    }

    /**
     * @dataProvider provideFileResults
     */
    public function testGetNewExtension($path, $mimeType, $alternative): void
    {
        // @todo unsupported extensions .xt and .*.twig
        $expected = pathinfo($path, PATHINFO_EXTENSION);
        $unknown = [
            'xt' => 'xml',
            'twig' => 'txt',
        ];
        if (!empty($unknown[$expected])) {
            $expected = $unknown[$expected];
        }

        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $extension = $userapi->getNewExtension($mimeType);
        // @todo unsupported extensions .xt and .*.twig + alternative .php mimeType
        if ($extension != $expected && !is_null($alternative)) {
            $extension = $userapi->getNewExtension($alternative);
        }
        $this->assertEquals($expected, $extension);
    }

    public function testGetMimeTypeList(): void
    {
        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');

        $expected = 'mime_types';
        $typelist = $userapi->getMimeTypeList();
        $this->assertEquals($expected, $typelist->name);

        $expected = 11;
        $mimetypes = $typelist->items;
        $this->assertCount($expected, $mimetypes);

        $expected = 'application';
        $first = reset($mimetypes);
        $this->assertEquals($expected, $first['name']);
    }

    public function testGetSubTypeList(): void
    {
        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');

        $expected = 'mime_subtypes';
        $subtypelist = $userapi->getSubTypeList();
        $this->assertEquals($expected, $subtypelist->name);

        $expected = 215;
        $subtypes = $subtypelist->items;
        $this->assertCount($expected, $subtypes);

        $expected = 'x-photo-cd-pack-file';
        $last = end($subtypes);
        $this->assertEquals($expected, $last['name']);
    }

    public function testGetExtensionList(): void
    {
        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');

        $expected = 'mime_extensions';
        $extensionlist = $userapi->getExtensionList();
        $this->assertEquals($expected, $extensionlist->name);

        $expected = 176;
        $extensions = $extensionlist->items;
        $this->assertCount($expected, $extensions);

        $expected = 'ice';
        $last = end($extensions);
        $this->assertEquals($expected, $last['name']);
    }

    public function testGetMagicList(): void
    {
        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');

        $expected = 'mime_magic';
        $magiclist = $userapi->getMagicList();
        $this->assertEquals($expected, $magiclist->name);

        $expected = 392;
        $magic = $magiclist->items;
        $this->assertCount($expected, $magic);

        $expected = 'UENEX0lQSQ==';
        $last = end($magic);
        $this->assertEquals($expected, $last['value']);
    }

    public function testGetTypeById(): void
    {
        $typeId = 4;
        $expected = [
            'typeId' => 4,
            'typeName' => 'font',
        ];

        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $result = $userapi->getType(['typeId' => $typeId]);

        $this->assertEquals($expected, $result);
    }

    public function testGetTypeByName(): void
    {
        $typeName = 'text';
        $expected = [
            'typeId' => 8,
            'typeName' => 'text',
        ];

        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $result = $userapi->getType(['typeName' => $typeName]);

        $this->assertEquals($expected, $result);
    }

    public function testGetSubtypeById(): void
    {
        $subtypeId = 66;
        $expected = [
            'subtypeId' => 66,
            'subtypeName' => 'x-httpd-php',
            'subtypeDesc' => '',
            'typeId' => 1,
            'typeName' => 'application',
        ];

        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $result = $userapi->getSubtype(['subtypeId' => $subtypeId]);

        $this->assertEquals($expected, $result);
    }

    public function testGetSubtypeByName(): void
    {
        $typeName = 'image';
        $subtypeName = 'gif';
        $expected = [
            'subtypeId' => 153,
            'subtypeName' => 'gif',
            'subtypeDesc' => '',
            'typeId' => 5,
            'typeName' => 'image',
        ];

        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $result = $userapi->getSubtype([
            'typeName' => $typeName,
            'subtypeName' => $subtypeName,
        ]);

        $this->assertEquals($expected, $result);
    }

    public function testGetExtensionById(): void
    {
        $extensionId = 90;
        $expected = [
            'subtypeId' => 106,
            'extensionId' => 90,
            'extensionName' => 'zip',
        ];

        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $result = $userapi->getExtension(['extensionId' => $extensionId]);

        $this->assertEquals($expected, $result);
    }

    public function testGetExtensionByName(): void
    {
        $extensionName = 'png';
        $expected = [
            'subtypeId' => 156,
            'extensionId' => 118,
            'extensionName' => 'png',
        ];

        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $result = $userapi->getExtension(['extensionName' => $extensionName]);

        $this->assertEquals($expected, $result);
    }

    public function testGetMagicById(): void
    {
        $magicId = 327;
        $expected = [
            'subtypeId' => 155,
            'magicId' => 327,
            'magicValue' => '/9g=',
            'magicOffset' => 0,
            'magicLength' => 2,
        ];

        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $result = $userapi->getMagic(['magicId' => $magicId]);

        $this->assertEquals($expected, $result);
    }

    public function testGetMagicByValue(): void
    {
        $magicValue = 'AAABug==';
        $expected = [
            'subtypeId' => 110,
            'magicId' => 385,
            'magicValue' => 'AAABug==',
            'magicOffset' => 0,
            'magicLength' => 4,
        ];

        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $result = $userapi->getMagic(['magicValue' => $magicValue]);

        $this->assertEquals($expected, $result);
    }

    public function testGetMimetypeById(): void
    {
        $subtypeId = 153;
        $expected = 'image/gif';

        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $result = $userapi->getMimetype(['subtypeId' => $subtypeId]);

        $this->assertEquals($expected, $result);
    }

    public function testGetMimetypeByName(): void
    {
        $subtypeName = 'jpeg';
        $expected = 'image/jpeg';

        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $result = $userapi->getMimetype(['subtypeName' => $subtypeName]);

        $this->assertEquals($expected, $result);
    }

    public function testGetRevMimetype(): void
    {
        $mimeType = 'audio/mpeg';
        $expected = [
            'typeId' => 2,
            'subtypeId' => 110,
        ];

        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $result = $userapi->getRevMimetype(['mimeType' => $mimeType]);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider provideFileResults
     */
    public function testAnalyzeFile($path, $mimeType, $alternative): void
    {
        $expected = $mimeType;

        if (!file_exists($path)) {
            $this->expectException(Exception::class);
            $expected = "Unable to retrieve mime type. File does not exist!";
            $this->expectExceptionMessage($expected);
        }

        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $result = $userapi->analyzeFile(['fileName' => $path]);

        if ($result != $expected && !empty($alternative)) {
            $expected = $alternative;
        }
        $this->assertEquals($expected, $result);

        $expected = [$mimeType, $alternative];

        $skipTest = 1;
        $result = $userapi->analyzeFile(['fileName' => $path, 'skipTest' => $skipTest]);
        $this->assertContains($result, $expected);

        // for .xt and .json files
        if (!in_array('text/plain', $expected)) {
            $expected[] = 'text/plain';
        }

        $skipTest = 2;
        $result = $userapi->analyzeFile(['fileName' => $path, 'skipTest' => $skipTest]);
        $this->assertContains($result, $expected);

        $skipTest = 3;
        $result = $userapi->analyzeFile(['fileName' => $path, 'skipTest' => $skipTest]);
        $this->assertContains($result, $expected);
    }

    public function testExtensionToMime(): void
    {
        $fileName = 'file.zip';
        $expected = 'application/zip';

        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $result = $userapi->extensionToMime(['fileName' => $fileName]);

        $this->assertEquals($expected, $result);
    }

    public function testMimeToExtension(): void
    {
        $mimeType = 'application/pdf';
        $expected = 'pdf';

        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $result = $userapi->mimeToExtension(['mime_type' => $mimeType]);

        $this->assertEquals($expected, $result);
    }

    public function testGetMimeImage(): void
    {
        $mimeType = 'audio/x-mp3';
        $baseurl = 'http://localhost/xaraya/code/modules/mime/xarimages/audio-x-mp3.png';
        $expected = $baseurl . 'code/modules/mime/xarimages/audio-x-mp3.png';

        xarServer::setBaseURL($baseurl);

        /** @var UserApi $userapi */
        $userapi = xarMod::userapi('mime');
        $result = $userapi->getMimeImage(['mimeType' => $mimeType]);

        $this->assertEquals($expected, $result);
    }
}
