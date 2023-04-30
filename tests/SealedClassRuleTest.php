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
		return new SealedClassRule(
			self::createReflectionProvider(),
		);
	}

	public function testRule(): void
	{
		require __DIR__ . '/data/non-abstract-sealed-class.php';
		$this->analyse(
			[__DIR__ . '/data/non-abstract-sealed-class.php'],
			[
				['#[Sealed] class NonAbstractSealedClassDataset\\NonAbstractSealedClass must be abstract.', 7],
			],
		);

		require __DIR__ . '/data/not-direct-subtype.php';
		$this->analyse(
			[__DIR__ . '/data/not-direct-subtype.php'],
			[
				['Type NotDirectSubtypeDataset\\NotADirectSubtype is not a direct subtype of #[Sealed] class NotDirectSubtypeDataset\\AbstractSealedClass.', 7],
				['Type NotDirectSubtypeDataset\\NotADirectSubtypeEither is not a direct subtype of #[Sealed] class NotDirectSubtypeDataset\\AbstractSealedClass.', 7],
				['Type NotDirectSubtypeDataset\\IndirectExtension is not a direct subtype of #[Sealed] interface NotDirectSubtypeDataset\\SealedInterface.', 15],
				['Type NotDirectSubtypeDataset\\IndirectImplementation is not a direct subtype of #[Sealed] interface NotDirectSubtypeDataset\\SealedInterface.', 15],
				['Type NotDirectSubtypeDataset\\AlsoIndirectImplementation is not a direct subtype of #[Sealed] interface NotDirectSubtypeDataset\\SealedInterface.', 15],
			],
		);
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [__DIR__ . '/../extension.neon'];
	}
}
