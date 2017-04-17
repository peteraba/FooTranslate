<?php

declare(strict_types=1);

namespace Foo\Translate;

class Translator implements ITranslator
{
    const DEFAULT_LANGUAGE = 'DEFAULT_LANGUAGE';

    /** @var Loader */
    protected $loader;

    /** @var string */
    protected $lang;

    /** @var array */
    protected $translations = [];

    /**
     * Translator constructor.
     *
     * @param Loader $loader
     */
    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param string
     *
     * @return array
     */
    public function getTranslations(string $lang): array
    {
        if (!array_key_exists($lang, $this->translations)) {
            $this->translations[$lang] = $this->loader->loadTranslations($lang);
        }

        if (empty($this->translations[$lang])) {
            throw new Exception("{{language is missing: $lang}}");
        }

        return $this->translations[$lang];
    }


    /**
     * @param array  $translations
     * @param string $key
     * @param string $lang
     */
    public function setTranslations(array $translations, string $key = null, string $lang = null)
    {
        if (null === $lang && null === $key) {
            $this->translations = $translations;

            return;
        } elseif (null === $lang) {
            $lang = 'en';
        }

        if (null === $key) {
            $this->translations[$lang] = $translations;

            return;
        }

        $this->translations[$lang][$key] = $translations;
    }

    /**
     * @return string
     */
    public function getLang(): string
    {
        if (null === $this->lang) {
            $this->lang = getenv(static::DEFAULT_LANGUAGE);
        }

        return $this->lang;
    }

    /**
     * @param string $lang
     *
     * @return Translator
     */
    public function setLang(string $lang): Translator
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @param string $key
     * @param array  ...$args
     *
     * @return string
     */
    public function translate(string $key, ...$args): string
    {
        return $this->translateByArgs($key, $args);
    }

    /**
     * @param string $key
     * @param array  $args
     *
     * @return string
     */
    public function translateByArgs(string $key, array $args = []): string
    {
        try {
            $translation = $this->findTranslation($key);
            $args        = $this->translateArguments($args);
            $translated  = $this->execute($translation, $args, $key);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $translated;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function findTranslation(string $key): string
    {
        $pathParts = explode(':', $key);

        $translations = $this->getTranslations($this->getLang());
        foreach ($pathParts as $pathPart) {
            if (!array_key_exists($pathPart, $translations)) {
                throw new Exception("{{translation is missing: $key}}");
            }

            $translations = &$translations[$pathPart];
        }

        if (!is_string($translations)) {
            throw new Exception("{{translation is ambiguous: $key}}");
        }

        return $translations;
    }

    /**
     * @param array $args
     *
     * @return array
     */
    private function translateArguments(array $args): array
    {
        foreach ($args as $argKey => $argValue) {
            if (!is_string($argValue)) {
                continue;
            }

            if (!preg_match('/{{(.+)}}/Ums', $argValue, $match)) {
                continue;
            }

            $args[$argKey] = $this->translateByArgs($match[1]);
        }

        return $args;
    }

    /**
     * @param string $translation
     * @param array  $args
     * @param string $key
     *
     * @return string
     */
    private function execute(string $translation, array $args, string $key): string
    {
        $result = @vsprintf($translation, $args);

        if (!is_string($result)) {
            throw new Exception("{{translation argument list failure: $key}}");
        }

        return $result;
    }
}