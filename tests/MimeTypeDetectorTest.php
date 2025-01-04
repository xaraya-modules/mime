<?php

use PHPUnit\Framework\TestCase;
use Xaraya\Context\Context;
use Xaraya\Context\SessionContext;
use Xaraya\Modules\Mime\MimeTypeDetector;

//use Xaraya\Sessions\SessionHandler;

final class MimeTypeDetectorTest extends TestCase
{
    protected static $oldDir;

    public static function setUpBeforeClass(): void
    {
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

    public function testMimeTypeDetector(): void
    {
        $detector = new MimeTypeDetector();
        $expected = MimeTypeDetector::class;
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
    public function testCheckFileType(string $path, ?string $expectedContent, ?string $expectedExtension): void
    {
        $detector = new MimeTypeDetector();
        $mimeType = $detector->checkFileType($path);

        // careful about inconclusive mime types, see FinfoMimeTypeDetector::INCONCLUSIVE_MIME_TYPES
        if ($expectedContent == 'text/plain') {
            $this->assertEquals($expectedExtension, $mimeType);
        } else {
            $this->assertEquals($expectedContent, $mimeType);
        }
    }

    /**
     * @dataProvider provideFileResults
     */
    public function testGetMimeType(string $path, ?string $expectedContent, ?string $expectedExtension): void
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $detector = new MimeTypeDetector();
        // @todo unsupported extensions .xt and .*.twig
        $mimeType = $detector->getMimeType($extension);

        $this->assertEquals($expectedExtension, $mimeType);
    }

    /**
     * @dataProvider provideFileResults
     */
    public function testGetExtension(string $path, ?string $mimeTypeContent, ?string $mimeTypeExtension): void
    {
        $mimeType = $mimeTypeExtension ?? $mimeTypeContent;
        $detector = new MimeTypeDetector();
        $extension = $detector->getExtension($mimeType);

        // @todo unsupported extensions .xt and .*.twig
        $expected = pathinfo($path, PATHINFO_EXTENSION);
        $unknown = [
            'xt' => 'xml',
            'twig' => 'txt',
        ];
        if (!empty($unknown[$expected])) {
            $expected = $unknown[$expected];
        }
        $this->assertEquals($expected, $extension);
    }

    public function testGeneratedMaps(): void
    {
        $detector = new MimeTypeDetector();

        $expected = 1028;
        $this->assertCount($expected, $detector->getExtensionsForMimeTypesMap());

        $expected = 1240;
        $this->assertCount($expected, $detector->getMimeTypesForExtensionsMap());
    }

    /**
     * @dataProvider provideFileResults
     */
    public function testFinfoMimeTypeDetector(string $path, ?string $expectedContent, ?string $expectedExtension): void
    {
        $detector = new League\MimeTypeDetection\FinfoMimeTypeDetector();
        $contents = file_get_contents($path);

        // Detect by contents, fall back to detection by extension.
        $mimeType = $detector->detectMimeType($path, $contents);

        // careful about inconclusive mime types, see FinfoMimeTypeDetector::INCONCLUSIVE_MIME_TYPES
        if ($expectedContent == 'text/plain') {
            $this->assertEquals($expectedExtension, $mimeType);
        } else {
            $this->assertEquals($expectedContent, $mimeType);
        }
    }

    /**
     * @dataProvider provideFileResults
     */
    public function testFinfoMimeTypeDetectorBuffer(string $path, ?string $expectedContent, ?string $expectedExtension): void
    {
        $detector = new League\MimeTypeDetection\FinfoMimeTypeDetector();
        $contents = file_get_contents($path);

        // Detect by contents only, no extension fallback.
        $mimeType = $detector->detectMimeTypeFromBuffer($contents);
        $this->assertEquals($expectedContent, $mimeType);
    }

    /**
     * @dataProvider provideFileResults
     */
    public function testFinfoMimeTypeDetectorFile(string $path, ?string $expectedContent, ?string $expectedExtension): void
    {
        $detector = new League\MimeTypeDetection\FinfoMimeTypeDetector();
        //$contents = file_get_contents($path);

        // Detect by actual file, no extension fallback.
        $mimeType = $detector->detectMimeTypeFromFile($path);
        $this->assertEquals($expectedContent, $mimeType);
    }

    /**
     * @dataProvider provideFileResults
     */
    public function testFinfoMimeTypeDetectorPath(string $path, ?string $expectedContent, ?string $expectedExtension): void
    {
        $detector = new League\MimeTypeDetection\FinfoMimeTypeDetector();
        //$contents = file_get_contents($path);

        // Only detect by extension
        $mimeType = $detector->detectMimeTypeFromPath('dummy/' . $path);
        $this->assertEquals($expectedExtension, $mimeType);
    }

    /**
     * @dataProvider provideFileResults
     */
    public function testExtensionMimeTypeDetector(string $path, ?string $expectedContent, ?string $expectedExtension): void
    {
        $detector = new League\MimeTypeDetection\ExtensionMimeTypeDetector();
        $contents = file_get_contents($path);

        // Only detect by extension, ignores the file contents
        $mimeType = $detector->detectMimeType($path, $contents);
        $this->assertEquals($expectedExtension, $mimeType);

        // Always returns null
        $mimeType = $detector->detectMimeTypeFromBuffer($contents);
        $this->assertEquals(null, $mimeType);

        // Only detect by extension
        $mimeType = $detector->detectMimeTypeFromFile($path);
        $this->assertEquals($expectedExtension, $mimeType);

        // Only detect by extension
        $mimeType = $detector->detectMimeTypeFromPath('dummy/' . $path);
        $this->assertEquals($expectedExtension, $mimeType);
    }

    public function testExtensionToMimeTypeMap(): void
    {
        $map = new League\MimeTypeDetection\GeneratedExtensionToMimeTypeMap();

        // string mime-type or NULL
        $mimeType = $map->lookupMimeType('jpg');
        $expected = 'image/jpeg';
        $this->assertEquals($expected, $mimeType);

        // string | null
        $extension = $map->lookupExtension($mimeType);
        $expected = 'jpeg';
        $this->assertEquals($expected, $extension);

        // array<string>
        $allExtensions = $map->lookupAllExtensions($mimeType);
        $expected = ['jpeg', 'jpg', 'jpe'];
        $this->assertEquals($expected, $allExtensions);
    }
}
