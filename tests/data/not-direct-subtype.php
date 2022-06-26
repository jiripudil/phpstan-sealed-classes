<?php

use JiriPudil\SealedClasses\Sealed;

#[Sealed(permits: [DirectSubtype::class, NotADirectSubtype::class, NotADirectSubtypeEither::class])]
abstract class AbstractSealedClass {}

class DirectSubtype extends AbstractSealedClass {}
class NotADirectSubtype extends DirectSubtype {}

class NotADirectSubtypeEither {}
