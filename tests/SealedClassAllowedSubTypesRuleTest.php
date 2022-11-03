<?php

declare(strict_types=1);

namespace JiriPudil\SealedClasses;

use PHPStan\Rules\Classes\AllowedSubTypesRule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<AllowedSubTypesRule>
 */
final class SealedClassAllowedSubTypesRuleTest extends RuleTestCase
{
	protected function getRule(): AllowedSubTypesRule
	{
		return new AllowedSubTypesRule();
	}

	public function testRule(): void
	{
		require __DIR__ . '/data/sealed-class.php';
		$this->analyse(
			[__DIR__ . '/data/sealed-class.php'],
			[
				['Type SealedClassDataset\\DisallowedDescendant is not allowed to be a subtype of SealedClassDataset\\SealedClass.', 13],
			],
		);

		require __DIR__ . '/data/sealed-interface.php';
		$this->analyse(
			[__DIR__ . '/data/sealed-interface.php'],
			[
				['Type SealedInterfaceDataset\\DisallowedInterfaceDescendant is not allowed to be a subtype of SealedInterfaceDataset\\SealedInterface.', 11],
				['Type SealedInterfaceDataset\\DisallowedImplementation is not allowed to be a subtype of SealedInterfaceDataset\\SealedInterface.', 13],
			],
		);
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [__DIR__ . '/../extension.neon'];
	}
}
