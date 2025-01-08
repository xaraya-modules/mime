<?php

use PHPUnit\Framework\TestCase;
use Xaraya\Context\Context;
use Xaraya\Context\SessionContext;
use Xaraya\Modules\Mime\AdminGui;
use Xaraya\Modules\Mime\AdminGui\ViewMethod;
use Xaraya\Modules\Mime\MimeTypeDetector;

//use Xaraya\Sessions\SessionHandler;

final class AdminGuiTest extends TestCase
{
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
    }

    public static function tearDownAfterClass(): void {}

    protected function setUp(): void {}

    protected function tearDown(): void {}

    public function testAdminGui(): void
    {
        $expected = AdminGui::class;
        $admingui = xarMod::getModule('mime')->getAdminGUI();
        $this->assertEquals($expected, $admingui::class);
    }

    public function testMain(): void
    {
        $context = null;
        $admingui = xarMod::getModule('mime')->getAdminGUI();
        $admingui->setContext($context);

        $args = ['hello' => 'world'];
        $data = $admingui->main($args);

        $expected = array_merge($args, [
            'context' => $context,
        ]);
        $this->assertEquals($expected, $data);
    }

    protected function createMockClassWithAccess(string $modName, string $className, int $count = 1): object
    {
        //$gui = xarMod::getModule($modName)->getGUI();
        //$gui = xarMod::getModule($modName)->getAdminGUI();
        $gui = $this->getMockBuilder($className)
            ->setConstructorArgs([$modName])
            ->onlyMethods(['checkAccess'])
            ->getMock();
        // override checkAccess() method to return true + check if called $count times
        $constraint = $this->exactly($count);
        $gui->expects($constraint)
            ->method('checkAccess')
            ->willReturn(true);
        return $gui;
    }

    public function testCallView(): void
    {
        $context = null;
        /** @var AdminGui $admingui */
        $admingui = $this->createMockClassWithAccess('mime', AdminGui::class, 0);
        $admingui->setContext($context);

        // use __call() here
        $args = ['hello' => 'world'];
        $data = $admingui->view($args);

        // will return null because we didn't override the MethodClass->checkAccess() here
        $expected = null;
        $this->assertEquals($expected, $data);
    }

    public function testViewMethod(): void
    {
        $context = null;
        /** @var ViewMethod $viewmethod */
        $viewmethod = $this->createMockClassWithAccess('mime', ViewMethod::class, 1);
        $viewmethod->setContext($context);

        // use __invoke() here
        $args = ['hello' => 'world'];
        $data = $viewmethod($args);

        $expected = array_merge($args, [
            'objectname' => 'mime_types',
            'object' => 'DataObjectList(...)',
            'options' => ['...'],
            'context' => $context,
        ]);
        $this->assertEquals(array_keys($expected), array_keys($data));

        $expected = 'mime_types';
        $this->assertEquals($expected, $data['objectname']);
        $this->assertEquals($expected, $data['object']->name);

        $expected = 11;
        $this->assertCount($expected, $data['object']->items);

        $expected = 4;
        $this->assertCount($expected, $data['options']);

        $expected = $context;
        $this->assertEquals($expected, $data['context']);
    }
}
