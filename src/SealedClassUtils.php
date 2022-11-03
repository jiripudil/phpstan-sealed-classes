<?php

declare(strict_types=1);

namespace JiriPudil\SealedClasses;

use PHPStan\Analyser\Scope;
use PHPStan\BetterReflection\Reflection\Adapter\FakeReflectionAttribute;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionAttribute;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use function array_key_exists;

final class SealedClassUtils
{
	private function __construct()
	{
	}

	/**
	 * @return class-string[]
	 */
	public static function extractPermittedDescendants(
		FakeReflectionAttribute|ReflectionAttribute $sealedAttribute,
		Scope $scope,
	): array
	{
		$sealed = $sealedAttribute->getArgumentsExpressions();
		if ( ! array_key_exists(0, $sealed) && ! array_key_exists('permits', $sealed)) {
			return [];
		}

		$permitsType = $scope->getType($sealed[0] ?? $sealed['permits']);

		if ( ! $permitsType instanceof ConstantArrayType) {
			return [];
		}

		$permittedClassNames = [];
		foreach ($permitsType->getValueTypes() as $valueType) {
			if ( ! $valueType instanceof ConstantStringType || ! $valueType->isClassString()) {
				continue;
			}

			/** @var class-string $value */
			$value = $valueType->getValue();
			$permittedClassNames[] = $value;
		}

		return $permittedClassNames;
	}
}
