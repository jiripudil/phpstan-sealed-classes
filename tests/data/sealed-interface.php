<?php

namespace SealedInterfaceDataset;

use JiriPudil\SealedClasses\Sealed;

#[Sealed(permits: [AllowedInterfaceDescendant::class, AllowedImplementation::class])]
interface SealedInterface {}

interface AllowedInterfaceDescendant extends SealedInterface {}
interface DisallowedInterfaceDescendant extends SealedInterface {}
class AllowedImplementation implements SealedInterface {}
class DisallowedImplementation implements SealedInterface {}
class IndirectImplementation implements AllowedInterfaceDescendant {}
