<?php

declare(strict_types = 1);

namespace Foo\Translate;

use Opulence\Cache\ArrayBridge;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class LoaderTest extends TestCase
{
    const DIR = 'exampleDir';

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

        $this->root = vfsStream::setup(static::DIR, null, $structure);
    }

    public function testLoadTranslationsReturnsEmptyArrayWithoutFilesAndWithoutCache()
    {
        $lang = 'hu';

        $sut = new Loader('', null);

        $actualResult = $sut->loadTranslations($lang);

        $this->assertSame([], $actualResult);
    }

    public function testLoadTranslationsReturnsEmptyArrayWithoutFilesWithEmptyCache()
    {
        $lang        = 'hu';
        $cacheBridge = new ArrayBridge();

        $sut = new Loader('', $cacheBridge);

        $actualResult = $sut->loadTranslations($lang);

        $this->assertSame([], $actualResult);
    }

    public function testLoadTranslationsCanLoadTranslationsFromFiles()
    {
        $lang = 'hu';

        $sut = new Loader(vfsStream::url(static::DIR), null);

        $actualResult = $sut->loadTranslations($lang);

        $this->assertEquals($this->expectedResult[$lang], $actualResult);
    }

    public function testLoadTranslationsCanLoadTranslationsFromCacheOncePopulated()
    {
        $lang        = 'hu';
        $cacheBridge = new ArrayBridge();

        $sut = new Loader(vfsStream::url(static::DIR), $cacheBridge);

        $sut->loadTranslations($lang);

        rmdir(vfsStream::url(static::DIR));

        $actualResult = $sut->loadTranslations($lang);

        $this->assertEquals($this->expectedResult[$lang], $actualResult);
    }
}
