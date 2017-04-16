<?php

namespace Foo\I18n;

interface ITranslator
{
    /**
     * @param string $key
     * @param array  ...$args
     *
     * @return string
     */
    public function translate(string $key, ...$args): string;

    /**
     * @param string $key
     * @param array  $args
     *
     * @return string
     */
    public function translateByArgs(string $key, array $args = []): string;
}