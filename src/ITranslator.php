<?php

namespace Foo\Translate;

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