<?php

declare(strict_types=1);

namespace Foo\Translate;

use Opulence\Cache\ICacheBridge;

class Loader
{
    const CACHE_TEMPLATE = 'foo-translate-%s';

    const LIFETIME = 1000000;

    /** @var string */
    protected $directory;

    /** @var ICacheBridge */
    protected $cacheBridge;

    /**
     * Translator constructor.
     *
     * @param string            $directory
     * @param ICacheBridge|null $cacheBridge
     */
    public function __construct(string $directory, ICacheBridge $cacheBridge = null)
    {
        $this->directory   = $directory;
        $this->cacheBridge = $cacheBridge;
    }

    /**
     * @param string $lang
     *
     * @return array
     */
    public function loadTranslations(string $lang): array
    {
        $cacheKey = $this->getCacheKey($lang);

        if (null !== $this->cacheBridge && $this->cacheBridge->has($cacheKey)) {
            return $this->cacheBridge->get($cacheKey);
        }

        $translations = $this->loadTranslationsFromFiles($lang);

        if (null != $this->cacheBridge) {
            $this->cacheBridge->set($cacheKey, $translations, static::LIFETIME);
        }

        return $translations;
    }

    /**
     * @param string $lang
     *
     * @return string
     */
    protected function getCacheKey(string $lang): string
    {
        return sprintf(static::CACHE_TEMPLATE, $lang);
    }

    /**
     * @param string $lang
     *
     * @return array
     */
    protected function loadTranslationsFromFiles(string $lang): array
    {
        $dir = sprintf('%s/%s', $this->directory, $lang);

        if (!is_dir($dir)) {
            return [];
        }

        $translations = [];
        foreach (scandir($dir) as $file) {
            // Skip non-PHP files
            if (strlen($file) < 4 || substr($file, -4) !== '.php') {
                continue;
            }

            $content   = require $dir . '/' . $file;
            $namespace = substr($file, 0, -4);

            $translations[$namespace] = $content;
        }

        return $translations;
    }
}
