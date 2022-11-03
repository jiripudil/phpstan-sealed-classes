<?php

namespace NotDirectSubtypeDataset;

use JiriPudil\SealedClasses\Sealed;

#[Sealed(permits: [DirectSubtype::class, NotADirectSubtype::class, NotADirectSubtypeEither::class])]
abstract class AbstractSealedClass {}

class DirectSubtype extends AbstractSealedClass {}
class NotADirectSubtype extends DirectSubtype {}

class NotADirectSubtypeEither {}

#[Sealed(permits: [DirectExtension::class, IndirectExtension::class, DirectImplementation::class, IndirectImplementation::class, AlsoIndirectImplementation::class])]
interface SealedInterface {}

interface DirectExtension extends SealedInterface {}
interface IndirectExtension extends DirectExtension {}

class DirectImplementation implements SealedInterface {}
class IndirectImplementation extends DirectImplementation {}
class AlsoIndirectImplementation implements DirectExtension {}
