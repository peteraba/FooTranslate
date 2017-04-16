<?php

namespace Foo\I18n;

use PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase
{
    /** @var Translator */
    protected $sut;

    /** @var array */
    protected $translations = [
        'en' => [
            'joe'     => 'Joe',
            'charles' => 'Charles: %s %d'
        ],
        'hu' => [
            'joe'     => 'József',
            'charles' => 'Károly: %s %d'
        ],
    ];

    public function setUp()
    {
        $this->sut = new Translator();

        $this->sut->setTranslations($this->translations['en'], '','en');
        $this->sut->setTranslations($this->translations['hu'], '', 'hu');
    }

    /**
     * @return array
     */
    public function translateDataProvider()
    {
        return [
            'en-simple' => [
                'en',
                'joe',
                [],
                'Joe',
            ],
            'en-with-arguments' => [
                'en',
                'charles',
                ['foo', 6],
                'Charles: foo 6',
            ],
            'hu-simple' => [
                'hu',
                'joe',
                [],
                'József'
            ],
            'hu-with-arguments' => [
                'hu',
                'charles',
                ['foo', 6],
                'Károly: foo 6'
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
    public function testTranslate(string $lang, string $key, array $args, string $expectedResult)
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
}