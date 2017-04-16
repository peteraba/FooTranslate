<?php

namespace Foo\I18n\Bootstrapper;

use Foo\I18n\ITranslator;
use Foo\I18n\Translator;
use Opulence\Framework\Configuration\Config;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Views\Compilers\Fortune\ITranspiler;

class I18nBootstrapper extends Bootstrapper
{
    const DEFAULT_LANGUAGE = 'DEFAULT_LANGUAGE';

    public function run(ITranspiler $transpiler, Translator $translator)
    {
        $transpiler->registerViewFunction(
            'tr',
            function (string $key, ...$args) use ($translator) {
                return $translator->translateByArgs($key, $args);
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $translator = new Translator();

        $lang = getenv(static::DEFAULT_LANGUAGE);
        $dir  = sprintf('%s/%s/', Config::get('paths', 'resources.lang'), $lang);

        foreach (scandir($dir) as $file) {
            // Skip non-PHP files
            if (strlen($file) < 4 || substr($file, -4) !== '.php') {
                continue;
            }

            $content = require $dir . $file;
            $translator->setTranslations($content, substr($file, 0, -4), $lang);
        }

        $translator->setLang($lang);

        $container->bindInstance(ITranslator::class, $translator);
    }
}