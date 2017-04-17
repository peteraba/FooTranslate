FooTranslate
============

Library for making international applications in Opulence easy.

[![Build Status](https://travis-ci.org/peteraba/FooTranslate.svg?branch=master)](https://travis-ci.org/peteraba/FooTranslate)
[![License](https://poser.pugx.org/peteraba/foo-translate/license)](https://packagist.org/packages/peteraba/foo-translate)
[![composer.lock](https://poser.pugx.org/peteraba/foo-translate/composerlock)](https://packagist.org/packages/peteraba/foo-translate)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/peteraba/FooTranslate/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/peteraba/FooTranslate/?branch=master)

Setup
-----

Install the library via composer:

```
composer install peteraba/foo-translate
```

Add the bootstrapper to you application:
```php
# config/bootstrappers.php

return [
    // ...
    Foo\Translate\Bootstrapper\TranslatorBootstrapper::class,
];
```

Add your translations in `resources/lang` as it already exist for validation.

Add your default language to your `.env.app.php`:
```
Environment::setVar('DEAFULT_LANGUAGE', "hu");
```


Usage
-----

Files under `resources/lang/${DEFAULT_LANG}` will be loaded automatically. Values defined in the language files are
_namespaced_ by a `:` character, so the value *mainPageTitle* defined in `application.php` can be referenced as *application:mainPageTitle*

User classes can access the translator via loading from the [IOC Container](https://www.opulencephp.com/docs/1.0/ioc-container) as *ITranslator*.

In [Fortune](https://www.opulencephp.com/docs/1.0/view-fortune) you can call the helper *tr* to retrieve your translations.


Example
-------

**resources/lang/en/form.php**
```php
<?php

return [
    'createNewLabel' => 'Create new %s',
];
```

**src/Project/Form/Login.php**
```php
class Login
{
    /**
     * @param ITranslator $translator
     * @param string      $entityName
     */
    public function __construct(ITranslator $translator, string $entityName)
    {
        $this->translator = $translator;
        $this->entityName = $entityName;
    }

    /**
     * @return Button
     */
    public function createSaveButton(): Button
    {
        return new Button($this->translator->translate('form:createNewLabel', $this->entityName));
    }
}
```

**resources/views/forms/login.fortune.php**
```php
<button>{{ tr("form:createNewLabel", $entityName) }}</button>
```


Notes
-----

1. The library will default to use *en* as default language if one is not provided.
2. The bootrapper is not Lazy loaded, because international application usually need translations throughout the application.
3. At the moment translations are not cached, but that's a planned feature.

