<?php

declare(strict_types=1);

namespace Foo\Translate;

use Opulence\Cache\ArrayBridge;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class LoaderTest extends TestCase
{
    const DIR = '/exampleDir';

    /**
     * @var array
     */
    protected $expectedResult = [
        'en' => [
            'foo' => [
                'table' => 'table',
                'body'  => 'body',
            ],
            'bar' => [
                'plus'  => 'plus',
                'minus' => 'minus',
            ],
        ],
        'hu' => [
            'foo' => [
                'table' => 'tábla',
                'body'  => 'test',
            ],
            'bar' => [
                'plus'  => 'plusz',
                'minus' => 'minusz',
            ],
        ],
    ];

    public function setUp()
    {
        $structure = [
            'en' => [
                'foo.php'     => '<?php return ["table" => "table", "body" => "body"];',
                'bar.php'     => '<?php return ["plus" => "plus", "minus" => "minus"];',
                'skipped.txt' => 'whatever',
            ],
            'hu' => [
                'foo.php'     => '<?php return ["table" => "tábla", "body" => "test"];',
                'bar.php'     => '<?php return ["plus" => "plusz", "minus" => "minusz"];',
                'skipped.txt' => 'kiterdekel',
            ],
        ];
        vfsStream::setup('root', null, $structure);
    }

    public function testLoadTranslationsWithoutCacheWithoutFiles()
    {
        $lang = 'hu';

        $sut = new Loader(static::DIR, null);

        $actualResult = $sut->loadTranslations($lang);

        $this->assertSame([], $actualResult);
    }

    public function testLoadTranslationsWithCacheWithoutFiles()
    {
        $lang            = 'hu';
        $cacheBridgeStub = new ArrayBridge();

        $sut = new Loader(static::DIR, $cacheBridgeStub);

        $actualResult = $sut->loadTranslations($lang);

        $this->assertSame([], $actualResult);
    }

    public function testLoadTranslationsWithoutCacheWithFiles()
    {
        $this->markTestSkipped();

        $lang = 'hu';

        $sut = new Loader('', null);

        $actualResult = $sut->loadTranslations($lang);

        $this->assertSame($this->expectedResult[$lang], $actualResult);
    }

    public function testLoadTranslationsWithCacheWithFiles()
    {
        $this->markTestSkipped();

        $lang            = 'hu';
        $cacheBridgeStub = new ArrayBridge();

        $sut = new Loader('', $cacheBridgeStub);

        $actualResult = $sut->loadTranslations($lang);

        $this->assertSame($this->expectedResult[$lang], $actualResult);
    }
}
