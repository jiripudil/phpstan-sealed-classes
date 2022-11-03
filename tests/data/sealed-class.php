<?php

namespace SealedClassDataset;

use JiriPudil\SealedClasses\Sealed;

#[Sealed(permits: [AllowedDescendant::class])]
abstract class SealedClass {}

class AllowedDescendant extends SealedClass {}
final class IndirectDescendant extends AllowedDescendant {}

final class DisallowedDescendant extends SealedClass {}
