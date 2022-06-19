<?php

use JiriPudil\SealedClasses\Sealed;

#[Sealed(permits: [AllowedDescendant::class])]
abstract class SealedClass {}

final class AllowedDescendant extends SealedClass {}

final class DisallowedDescendant extends SealedClass {}
