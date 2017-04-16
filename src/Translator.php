<?php

namespace Foo\Translate;

class Translator implements ITranslator
{
    /** @var string */
    protected $lang;

    /** @var array */
    protected $translations = [];

    /**
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     */
    public function setLang(string $lang)
    {
        $this->lang = $lang;
    }

    /**
     * @param array  $translations
     * @param string $key
     * @param string $lang
     */
    public function setTranslations(array $translations, string $key = '', string $lang = 'en')
    {
        if ('' === $key) {
            $this->translations[$lang] = $translations;

            return;
        }

        $this->translations[$lang][$key] = $translations;
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
        $pathParts = explode(':', $key);

        $translations = &$this->translations[$this->lang];
        foreach ($pathParts as $pathPart) {
            if (!array_key_exists($pathPart, $translations)) {
                return "{{translation missing: $key}}";
            }

            $translations = &$translations[$pathPart];
        }

        if (!is_string($translations)) {
            return "{{translation is ambiguous: $key}}";
        }

        foreach ($args as $argKey => $argValue) {
            $argTranslation = $this->translateByArgs($argValue);

            if (substr($argTranslation, 0, 2) === '{{') {
                continue;
            }

            $args[$argKey] = $argTranslation;
        }

        return vsprintf($translations, $args);
    }
}