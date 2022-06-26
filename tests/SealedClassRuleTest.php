<?php

declare(strict_types=1);

namespace JiriPudil\SealedClasses;

use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<SealedClassRule>
 */
final class SealedClassRuleTest extends RuleTestCase
{
	protected function getRule(): SealedClassRule
	{
		return new SealedClassRule();
	}

	public function testRule(): void
	{
		require __DIR__ . '/data/non-abstract-sealed-class.php';
		$this->analyse(
			[__DIR__ . '/data/non-abstract-sealed-class.php'],
			[
				['#[Sealed] class NonAbstractSealedClass must be abstract.', 5],
			],
		);

		require __DIR__ . '/data/sealed-class.php';
		$this->analyse(
			[__DIR__ . '/data/sealed-class.php'],
			[
				['Class DisallowedDescendant is not allowed to extend a #[Sealed] class SealedClass.', 11],
			],
		);

		require __DIR__ . '/data/sealed-interface.php';
		$this->analyse(
			[__DIR__ . '/data/sealed-interface.php'],
			[
				['Interface DisallowedInterfaceDescendant is not allowed to extend a #[Sealed] interface SealedInterface.', 9],
				['Class DisallowedImplementation is not allowed to implement a #[Sealed] interface SealedInterface.', 11],
			],
		);
	}
}
