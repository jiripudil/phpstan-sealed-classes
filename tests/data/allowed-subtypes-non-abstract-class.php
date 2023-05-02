<?php

namespace AllowedSubTypesExtensionDataset\NonAbstractClass;

use JiriPudil\SealedClasses\Sealed;
use function PHPStan\Testing\assertType;

#[Sealed(permits: [FirstDescendant::class, SecondDescendant::class, ThirdDescendant::class])]
class SealedClass {}

final class FirstDescendant extends SealedClass {}
final class SecondDescendant extends SealedClass {}
final class ThirdDescendant extends SealedClass {}

function foo(SealedClass $sealedClass): void
{
	if ($sealedClass instanceof FirstDescendant) {
		return;
	}

	assertType('AllowedSubTypesExtensionDataset\\NonAbstractClass\\SealedClass~AllowedSubTypesExtensionDataset\\NonAbstractClass\\FirstDescendant', $sealedClass);

	if ($sealedClass instanceof SecondDescendant) {
		return;
	}

	assertType('AllowedSubTypesExtensionDataset\\NonAbstractClass\\SealedClass~AllowedSubTypesExtensionDataset\\NonAbstractClass\\FirstDescendant|AllowedSubTypesExtensionDataset\\NonAbstractClass\\SecondDescendant', $sealedClass);

	if ($sealedClass instanceof ThirdDescendant) {
		return;
	}

	assertType('AllowedSubTypesExtensionDataset\\NonAbstractClass\\SealedClass', $sealedClass);
}
