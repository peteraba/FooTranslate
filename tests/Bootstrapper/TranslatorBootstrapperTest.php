<?php

declare(strict_types = 1);

namespace Foo\Translate\Bootstrapper;

use Foo\Translate\Translator;
use Opulence\Cache\ICacheBridge;
use Opulence\Framework\Configuration\Config;
use Opulence\Ioc\Container;
use Opulence\Views\Compilers\Fortune\Transpiler;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class TranslatorBootstrapperTest extends TestCase
{
    /** @var TranslatorBootstrapper */
    protected $sut;

    /** @var Container|MockObject $containerMock */
    protected $containerMock;

    public function setUp()
    {
        $this->containerMock = $this->getMockBuilder(Container::class)
            ->setMethods(['hasBinding', 'bindInstance', 'resolve'])
            ->getMock();

        $this->sut = new TranslatorBootstrapper();
    }

    public function testRunRegistersViewFunction()
    {
        /** @var Transpiler|MockObject $transpilerMock */
        $transpilerMock = $this->getMockBuilder(Transpiler::class)
            ->disableOriginalConstructor()
            ->setMethods(['registerViewFunction'])
            ->getMock();

        $transpilerMock->expects($this->once())->method('registerViewFunction');

        /** @var Translator $translatorStub */
        $translatorStub = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut->run($transpilerMock, $translatorStub);
    }

    public function testRegisterBindingsBindsITranlator()
    {
        $this->containerMock->expects($this->any())->method('hasBinding')->willReturn(false);
        $this->containerMock->expects($this->once())->method('bindInstance');

        Config::set('paths', 'resources.lang', '');

        $this->sut->registerBindings($this->containerMock);
    }

    public function testRegisterBindingsBindsITranlatorWithCacheIfFound()
    {
        $cacheBridgeStub = $this->getMockBuilder(ICacheBridge::class)
            ->getMock();

        $this->containerMock->expects($this->any())->method('hasBinding')->willReturn(true);
        $this->containerMock->expects($this->once())->method('resolve')->willReturn($cacheBridgeStub);
        $this->containerMock->expects($this->once())->method('bindInstance');

        Config::set('paths', 'resources.lang', '');

        $this->sut->registerBindings($this->containerMock);
    }
}