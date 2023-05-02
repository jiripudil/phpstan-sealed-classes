<?php

namespace AllowedSubTypesExtensionDataset\AbstractClass;

use JiriPudil\SealedClasses\Sealed;
use function PHPStan\Testing\assertType;

#[Sealed(permits: [FirstDescendant::class, SecondDescendant::class, ThirdDescendant::class])]
abstract class SealedClass {}

final class FirstDescendant extends SealedClass {}
final class SecondDescendant extends SealedClass {}
final class ThirdDescendant extends SealedClass {}

function foo(SealedClass $sealedClass): void
{
	if ($sealedClass instanceof FirstDescendant) {
		return;
	}

	assertType('AllowedSubTypesExtensionDataset\\AbstractClass\\SealedClass~AllowedSubTypesExtensionDataset\\AbstractClass\\FirstDescendant', $sealedClass);

	if ($sealedClass instanceof SecondDescendant) {
		return;
	}

	assertType('AllowedSubTypesExtensionDataset\\AbstractClass\\ThirdDescendant', $sealedClass);
}
