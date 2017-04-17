<?php

declare(strict_types=1);

namespace Foo\Translate\Bootstrapper;

use Foo\Translate\ITranslator;
use Foo\Translate\Translator;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Views\Compilers\Fortune\ITranspiler;

class TranslatorBootstrapper extends Bootstrapper
{
    /**
     * @param ITranspiler $transpiler
     * @param Translator  $translator
     */
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
     * @param IContainer $container
     */
    public function registerBindings(IContainer $container)
    {
        $translator = new Translator();

        $container->bindInstance(ITranslator::class, $translator);
    }
}