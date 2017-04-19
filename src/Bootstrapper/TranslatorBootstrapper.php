<?php

declare(strict_types = 1);

namespace Foo\Translate\Bootstrapper;

use Foo\Translate\ITranslator;
use Foo\Translate\Loader;
use Foo\Translate\Translator;
use Opulence\Cache\ICacheBridge;
use Opulence\Framework\Configuration\Config;
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
        $cacheBridge = null;
        if ($container->hasBinding(ICacheBridge::class)) {
            $cacheBridge = $container->resolve(ICacheBridge::class);
        }

        $dir = Config::get('paths', 'resources.lang');

        $loader = new Loader($dir, $cacheBridge);

        $translator = new Translator($loader);

        $container->bindInstance(ITranslator::class, $translator);
    }
}