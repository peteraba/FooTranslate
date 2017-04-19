<?php

declare(strict_types = 1);

namespace Foo\Translate;

use PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase
{
    /** @var Translator */
    protected $sut;

    /** @var array */
    protected $translations = [
        'en' => [
            'application' => [
                'joe'     => 'Joe',
                'charles' => 'Charles: %s %d',
            ],
        ],
        'hu' => [
            'application' => [
                'joe'     => 'József',
                'charles' => 'Károly: %s %d',
            ],
        ],
    ];

    /** @var Loader|\PHPUnit_Framework_MockObject_MockObject */
    protected $loaderStub;

    public function setUp()
    {
        $this->loaderStub = $this->getMockBuilder(Loader::class)
            ->disableOriginalConstructor()
            ->setMethods(['loadTranslations'])
            ->getMock();

        $this->sut = new Translator($this->loaderStub);

        $this->sut->setTranslations($this->translations);
    }

    /**
     * @return array
     */
    public function translateDataProvider()
    {
        return [
            'en-simple'                    => [
                'en',
                'application:joe',
                [],
                'Joe',
            ],
            'en-with-arguments'            => [
                'en',
                'application:charles',
                ['foo', 6],
                'Charles: foo 6',
            ],
            'hu-simple'                    => [
                'hu',
                'application:joe',
                [],
                'József',
            ],
            'hu-with-arguments'            => [
                'hu',
                'application:charles',
                ['foo', 6],
                'Károly: foo 6',
            ],
            'hu-with-argument-translation' => [
                'hu',
                'application:charles',
                ['{{application:joe}}', 42],
                'Károly: József 42',
            ],
        ];
    }

    /**
     * @dataProvider translateDataProvider
     *
     * @param string $lang
     * @param string $key
     * @param array  $args
     * @param string $expectedResult
     */
    public function testTranslateCanTranslate(string $lang, string $key, array $args, string $expectedResult)
    {
        $this->sut->setLang($lang);

        array_unshift($args, $key);

        $actualResult = call_user_func_array([$this->sut, 'translate'], $args);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @dataProvider translateDataProvider
     *
     * @param string $lang
     * @param string $key
     * @param array  $args
     * @param string $expectedResult
     */
    public function testTranslateByArgs(string $lang, string $key, array $args, string $expectedResult)
    {
        $this->sut->setLang($lang);

        $actualResult = $this->sut->translateByArgs($key, $args);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testTranslateByArgsHandlesMissingLanguageGracefully()
    {
        $lang           = 'foo';
        $key            = 'application:joe';
        $args           = [];
        $expectedResult = '{{language is missing: foo}}';

        $this->sut->setLang($lang);

        $actualResult = $this->sut->translateByArgs($key, $args);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testTranslateByArgsHandlesMissingTranslationGracefully()
    {
        $lang           = 'en';
        $key            = 'application:bill';
        $args           = [];
        $expectedResult = '{{translation is missing: application:bill}}';

        $this->sut->setLang($lang);

        $actualResult = $this->sut->translateByArgs($key, $args);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testTranslateByArgsHandlesAmbiguousTranslationGracefully()
    {
        $lang           = 'en';
        $key            = 'application';
        $args           = [];
        $expectedResult = '{{translation is ambiguous: application}}';

        $this->sut->setLang($lang);

        $actualResult = $this->sut->translateByArgs($key, $args);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testTranslateByArgsHandlesInsufficientArgumentsGracefully()
    {
        $lang           = 'en';
        $key            = 'application:charles';
        $args           = [];
        $expectedResult = '{{translation argument list failure: application:charles}}';

        $this->sut->setLang($lang);

        $actualResult = $this->sut->translateByArgs($key, $args);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testSetTranslationsReplacesAllTranslationsByDefault()
    {
        $foo = ['en' => ['foo' => ['bar' => 'BAR']]];

        $this->sut->setTranslations($foo);

        $this->assertEquals($foo['en'], $this->sut->getTranslations('en'));
    }

    public function testSetTranslationsReplacesOneKeyOfOneLanguageIfBothAreDefined()
    {
        $foo = ['foo' => ['bar' => 'BAR']];

        $this->sut->setTranslations($foo, 'application', 'hu');

        $this->assertEquals($foo, $this->sut->getTranslations('hu')['application']);
    }

    public function testSetTranslationsCanReplaceTranslationsForALanguageCompletely()
    {
        $foo = ['foo' => ['bar' => 'BAR']];

        $this->sut->setTranslations($foo, null, 'hu');

        $this->assertEquals($foo, $this->sut->getTranslations('hu'));
    }

    public function testSetTranslationsUsesEnglishByDefault()
    {
        $foo = ['foo' => ['bar' => 'BAR']];

        $this->sut->setTranslations($foo, 'application');

        $this->assertEquals($foo, $this->sut->getTranslations('en')['application']);
    }

    public function testSetTranslationsUsesCurrentLangIfDefined()
    {
        $foo = ['foo' => ['bar' => 'BAR']];

        $this->sut->setLang('hu');
        $this->sut->setTranslations($foo, 'application');

        $this->assertEquals($foo, $this->sut->getTranslations('hu')['application']);
    }

    public function testGetTranslationsLoadsTranslationsIfNotFound()
    {
        $foo = ['foo' => ['bar' => 'BAR']];

        $this->loaderStub
            ->expects($this->atLeastOnce())
            ->method('loadTranslations')
            ->willReturn($foo);

        $actualResult = $this->sut->getTranslations('pl');

        $this->assertEquals($foo, $actualResult);
    }
}