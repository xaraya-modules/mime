<?php

use PHPUnit\Framework\TestCase;
use Xaraya\Context\Context;
use Xaraya\Context\SessionContext;
use Xaraya\Modules\Mime\UserApi;
use Xaraya\Modules\Mime\MimeTypeDetector;

//use Xaraya\Sessions\SessionHandler;

final class UserApiTest extends TestCase
{
    protected static $oldDir;

    public static function setUpBeforeClass(): void
    {
        // initialize bootstrap
        sys::init();
        // initialize caching - delay until we need results
        xarCache::init();
        // initialize loggers
        xarLog::init();
        // initialize database - delay until caching fails
        xarDatabase::init();
        // initialize modules
        //xarMod::init();
        // initialize users
        //xarUser::init();
        xarSession::setSessionClass(SessionContext::class);

        // file paths are relative to parent directory
        static::$oldDir = getcwd();
        chdir(dirname(__DIR__));
    }

    public static function tearDownAfterClass(): void
    {
        chdir(static::$oldDir);
    }

    protected function setUp(): void {}

    protected function tearDown(): void {}

    public function testGetDetector(): void
    {
        $expected = MimeTypeDetector::class;
        $detector = UserApi::getDetector();
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
    public function testCheckFileType($path, $expectedContent, $expectedExtension): void
    {
        $mimeType = UserApi::checkFileType($path);
        if ($expectedContent == 'text/plain') {
            $expected = $expectedExtension;
        } else {
            $expected = $expectedContent;
        }
        $this->assertEquals($expected, $mimeType);
    }

    /**
     * @dataProvider provideFileResults
     */
    public function testGetExtension($path, $mimeType, $alternative): void
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

        $extension = UserApi::getExtension($mimeType);
        // @todo unsupported extensions .xt and .*.twig + alternative .php mimeType
        if ($extension != $expected && !is_null($alternative)) {
            $extension = UserApi::getExtension($alternative);
        }
        $this->assertEquals($expected, $extension);
    }

    public function testGetMimeTypes(): void
    {
        $expected = 'mime_types';
        $typelist = UserApi::getMimeTypes();
        $this->assertEquals($expected, $typelist->name);

        $expected = 11;
        $mimetypes = $typelist->items;
        $this->assertCount($expected, $mimetypes);

        $expected = 'application';
        $first = reset($mimetypes);
        $this->assertEquals($expected, $first['name']);
    }

    public function testGetSubTypes(): void
    {
        $expected = 'mime_subtypes';
        $subtypelist = UserApi::getSubTypes();
        $this->assertEquals($expected, $subtypelist->name);

        $expected = 215;
        $subtypes = $subtypelist->items;
        $this->assertCount($expected, $subtypes);

        $expected = 'x-photo-cd-pack-file';
        $last = end($subtypes);
        $this->assertEquals($expected, $last['name']);
    }

    public function testGetExtensions(): void
    {
        $expected = 'mime_extensions';
        $extensionlist = UserApi::getExtensions();
        $this->assertEquals($expected, $extensionlist->name);

        $expected = 176;
        $extensions = $extensionlist->items;
        $this->assertCount($expected, $extensions);

        $expected = 'ice';
        $last = end($extensions);
        $this->assertEquals($expected, $last['name']);
    }

    public function testGetMagic(): void
    {
        $expected = 'mime_magic';
        $magiclist = UserApi::getMagic();
        $this->assertEquals($expected, $magiclist->name);

        $expected = 392;
        $magic = $magiclist->items;
        $this->assertCount($expected, $magic);

        $expected = 'UENEX0lQSQ==';
        $last = end($magic);
        $this->assertEquals($expected, $last['value']);
    }
}
