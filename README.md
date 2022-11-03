# Sealed classes with PHPStan

This extension adds support for sealed classes and interfaces to PHPStan.

## Installation

To use this extension, require it via Composer

```shell
composer require --dev jiripudil/phpstan-sealed-classes
```

If you are using [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer), this extension's configuration will be automatically enabled.

Otherwise, you need to include it explicitly in your `phpstan.neon`:

```neon
includes:
    - vendor/jiripudil/phpstan-sealed-classes/extension.neon
```


## Usage

Sealed classes and interfaces allow developers to restrict class hierarchies: a sealed class can only be subclassed by classes that are explicitly permitted to do so. The same applies to sealed interfaces and their implementations. In a way, sealed classes are similar to enumerations, with an important distinction: while enums are singletons, a subclass of a sealed class can have _multiple_ instances, each with its own state.

You can seal an abstract class or an interface by attributing it as `#[Sealed]`. The attribute accepts a list of permitted descendants or implementations:

```php
<?php

use JiriPudil\SealedClasses\Sealed;

#[Sealed(permits: [AllowedImplementation::class, AnotherImplementation::class])]
interface SealedInterface {}

class AllowedImplementation implements SealedInterface {}
class AnotherImplementation implements SealedInterface {}
class DisallowedImplementation implements SealedInterface {}
```

While the first two classes will be allowed, PHPStan will report an error for the third:

```
------ ----------------------------------------------------------------------------------
 Line   sealed-interface.php
------ ----------------------------------------------------------------------------------
 10     Type DisallowedImplementation is not allowed to be a subtype of SealedInterface.
------ ----------------------------------------------------------------------------------
```

Note that the restrictions do not apply to indirect subclasses. If a direct subclass of a sealed class is not sealed itself, it can be further extended without raising any errors. This code is perfectly fine:

```php
<?php

use JiriPudil\SealedClasses\Sealed;

#[Sealed(permits: [AllowedDescendant::class])]
abstract class SealedClass {}

class AllowedDescendant extends SealedClass {}
class IndirectDescendant extends AllowedDescendant {}
```
