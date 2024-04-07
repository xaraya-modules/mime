<?php

use PHPUnit\Framework\TestCase;
use Xaraya\Context\Context;
use Xaraya\Context\SessionContext;
use Xaraya\Modules\Mime\MimeTypeDetector;

//use Xaraya\Sessions\SessionHandler;

final class MimeTypeDetectorTest extends TestCase
{
    public static function noSetUpBeforeClass(): void
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
    }

    public static function tearDownAfterClass(): void {}

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
    public static function expectedFileResults()
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
     * @dataProvider expectedFileResults
     */
    public function testAnalyzeFile(string $path, ?string $expectedContent, ?string $expectedExtension): void
    {
        $detector = new MimeTypeDetector();
        $mimeType = $detector->analyzeFile($path);

        // careful about inconclusive mime types, see FinfoMimeTypeDetector::INCONCLUSIVE_MIME_TYPES
        if ($expectedContent == 'text/plain') {
            $this->assertEquals($expectedExtension, $mimeType);
        } else {
            $this->assertEquals($expectedContent, $mimeType);
        }
    }

    /**
     * @dataProvider expectedFileResults
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
     * @dataProvider expectedFileResults
     */
    public function testGetExtension(string $path, ?string $expectedContent, ?string $expectedExtension): void
    {
        $mimeType = $expectedExtension ?? $expectedContent;
        $detector = new MimeTypeDetector();
        $extension = $detector->getExtension($mimeType);

        // @todo unsupported extensions .xt and .*.twig
        $unknown = [
            'xt' => 'xml',
            'twig' => 'txt',
        ];
        $expected = pathinfo($path, PATHINFO_EXTENSION);
        if (!empty($unknown[$expected])) {
            $expected = $unknown[$expected];
        }
        $this->assertEquals($expected, $extension);
    }

    /**
     * @dataProvider expectedFileResults
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
     * @dataProvider expectedFileResults
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
     * @dataProvider expectedFileResults
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
     * @dataProvider expectedFileResults
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
     * @dataProvider expectedFileResults
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
