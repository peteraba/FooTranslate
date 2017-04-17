<?php

declare(strict_types=1);

namespace Foo\Translate\Bootstrapper;

use Foo\Translate\ITranslator;
use Foo\Translate\Translator;
use Opulence\Ioc\Container;
use Opulence\Views\Compilers\Fortune\Transpiler;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class TranslatorBootstrapperTest extends TestCase
{
    /** @var TranslatorBootstrapper */
    protected $sut;

    public function setUp()
    {
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
        $translatorStub = $this->getMockBuilder(Translator::class)->getMock();

        $this->sut->run($transpilerMock, $translatorStub);
    }

    public function testRegisterBindingsBindsITranlator()
    {
        /** @var Container|MockObject $containerMock */
        $containerMock = $this->getMockBuilder(Container::class)
            ->setMethods(['bindInstance'])
            ->getMock();

        $containerMock->expects($this->once())->method('bindInstance')->with(ITranslator::class, new Translator);

        $this->sut->registerBindings($containerMock);
    }
}