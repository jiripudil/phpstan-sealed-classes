<?php

namespace NonAbstractSealedClassDataset;

use JiriPudil\SealedClasses\Sealed;

#[Sealed(permits: [AllowedButInvalidDescendant::class])]
class NonAbstractSealedClass {}

class AllowedButInvalidDescendant extends NonAbstractSealedClass {}
