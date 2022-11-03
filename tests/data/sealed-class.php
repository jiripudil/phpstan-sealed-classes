<?php

namespace SealedClassDataset;

use JiriPudil\SealedClasses\Sealed;

#[Sealed(permits: [AllowedDescendant::class, ResealedClass::class])]
abstract class SealedClass {}

class AllowedDescendant extends SealedClass {}
final class IndirectDescendant extends AllowedDescendant {}

final class DisallowedDescendant extends SealedClass {}

#[Sealed(permits: [AllowedResealedDescendant::class])]
abstract class ResealedClass extends SealedClass {}

class AllowedResealedDescendant extends ResealedClass {}
final class IndirectResealedDescendant extends AllowedResealedDescendant {}

final class DisallowedResealedDescendant extends ResealedClass {}
