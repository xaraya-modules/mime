<?php

use Xaraya\Modules\TestHelper;
use Xaraya\Modules\Mime\AdminGui;
use Xaraya\Modules\Mime\AdminGui\ViewMethod;

final class AdminGuiTest extends TestHelper
{
    protected function setUp(): void {}

    protected function tearDown(): void {}

    public function testAdminGui(): void
    {
        $expected = AdminGui::class;
        $admingui = xarMod::getModule('mime')->admingui();
        $this->assertEquals($expected, $admingui::class);
    }

    public function testCallView(): void
    {
        $context = $this->createContext(['source' => __METHOD__]);
        /** @var AdminGui $admingui */
        $admingui = $this->createMockWithAccess('mime', AdminGui::class, 1);
        $admingui->setContext($context);

        // use __call() here
        $args = ['hello' => 'world'];
        $data = $admingui->view($args);

        // This is no longer the case with core services without core trait
        // this will return null because it's trying to find something like
        // MockObject_AdminGui_62c03933\ViewMethod as method class to __call
        //$expected = null;
        //$this->assertEquals($expected, $data);

        $expected = array_merge($args, [
            'objectname' => 'mime_types',
            'object' => 'DataObjectList(...)',
            'options' => ['...'],
            'context' => $context,
        ]);
        $this->assertEquals(array_keys($expected), array_keys($data));

        $expected = $context;
        $this->assertEquals($expected, $admingui->getContext());
    }

    public function testViewMethod(): void
    {
        $context = $this->createContext(['source' => __METHOD__]);
        /** @var ViewMethod $viewmethod */
        $viewmethod = $this->createMockWithAccess('mime', ViewMethod::class, 1);
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
