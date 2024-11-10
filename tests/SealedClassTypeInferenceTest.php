<?php

declare(strict_types=1);

namespace JiriPudil\SealedClasses;

use PHPStan\Testing\TypeInferenceTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class SealedClassTypeInferenceTest extends TypeInferenceTestCase
{
	public static function dataFileAsserts(): iterable
	{
		require __DIR__ . '/data/allowed-subtypes-interface.php';
		yield from self::gatherAssertTypes(__DIR__ . '/data/allowed-subtypes-interface.php');

		require __DIR__ . '/data/allowed-subtypes-abstract-class.php';
		yield from self::gatherAssertTypes(__DIR__ . '/data/allowed-subtypes-abstract-class.php');

		require __DIR__ . '/data/allowed-subtypes-non-abstract-class.php';
		yield from self::gatherAssertTypes(__DIR__ . '/data/allowed-subtypes-non-abstract-class.php');
	}

	#[DataProvider('dataFileAsserts')]
	public function testInference(string $assertType, string $file, mixed ...$args): void
	{
		$this->assertFileAsserts($assertType, $file, ...$args);
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [__DIR__ . '/../extension.neon'];
	}
}
