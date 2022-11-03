<?php

declare(strict_types=1);

namespace JiriPudil\SealedClasses;

use PHPStan\Testing\TypeInferenceTestCase;

final class SealedClassTypeInferenceTest extends TypeInferenceTestCase
{
	public function dataFileAsserts(): iterable
	{
		require __DIR__ . '/data/allowed-subtypes-extension.php';
		yield from $this->gatherAssertTypes(__DIR__ . '/data/allowed-subtypes-extension.php');
	}

	/**
	 * @dataProvider dataFileAsserts
	 */
	public function testInference(string $assertType, string $file, mixed ...$args): void
	{
		$this->assertFileAsserts($assertType, $file, ...$args);
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [__DIR__ . '/../extension.neon'];
	}
}
