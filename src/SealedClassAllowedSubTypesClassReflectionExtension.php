<?php

declare(strict_types=1);

namespace JiriPudil\SealedClasses;

use PHPStan\Analyser\ScopeContext;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\Reflection\AllowedSubTypesClassReflectionExtension;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use function assert;
use function count;
use function reset;

final class SealedClassAllowedSubTypesClassReflectionExtension implements AllowedSubTypesClassReflectionExtension
{
	public function __construct(
		private readonly ScopeFactory $scopeFactory,
	)
	{
	}

	public function supports(ClassReflection $classReflection): bool
	{
		$sealedAttributes = $classReflection->getNativeReflection()->getAttributes(Sealed::class);
		return count($sealedAttributes) > 0;
	}

	public function getAllowedSubTypes(ClassReflection $classReflection): array
	{
		$sealedAttributes = $classReflection->getNativeReflection()->getAttributes(Sealed::class);
		assert(count($sealedAttributes) > 0);

		$fileName = $classReflection->getFileName();
		if ($fileName === null) {
			return [];
		}

		$sealedAttribute = reset($sealedAttributes);
		$scope = $this->scopeFactory->create(ScopeContext::create($fileName));
		$permittedClassNames = SealedClassUtils::extractPermittedDescendants($sealedAttribute, $scope);

		$allowedSubTypes = [];
		foreach ($permittedClassNames as $permittedClassName) {
			$allowedSubTypes[] = new ObjectType($permittedClassName);
		}

		if ($classReflection->isClass() && ! $classReflection->isAbstract()) {
			$allowedSubTypes[] = new ObjectType($classReflection->getName());
		}

		return $allowedSubTypes;
	}
}
