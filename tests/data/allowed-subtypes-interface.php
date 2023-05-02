<?php

namespace AllowedSubTypesExtensionDataset\Interface_;

use JiriPudil\SealedClasses\Sealed;
use function PHPStan\Testing\assertType;

#[Sealed(permits: [FirstDescendant::class, SecondDescendant::class, ThirdDescendant::class])]
interface SealedInterface {}

final class FirstDescendant implements SealedInterface {}
final class SecondDescendant implements SealedInterface {}
final class ThirdDescendant implements SealedInterface {}

function foo(SealedInterface $sealedClass): void
{
	if ($sealedClass instanceof FirstDescendant) {
		return;
	}

	assertType('AllowedSubTypesExtensionDataset\\Interface_\\SealedInterface~AllowedSubTypesExtensionDataset\\Interface_\\FirstDescendant', $sealedClass);

	if ($sealedClass instanceof SecondDescendant) {
		return;
	}

	assertType('AllowedSubTypesExtensionDataset\\Interface_\\ThirdDescendant', $sealedClass);
}
