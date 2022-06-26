<?php

declare(strict_types=1);

namespace JiriPudil\SealedClasses;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\BetterReflection\Reflection\Adapter\FakeReflectionAttribute;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionAttribute;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use function array_values;
use function count;
use function in_array;
use function reset;
use function sprintf;

/**
 * @implements Rule<InClassNode>
 */
final class SealedClassRule implements Rule
{
	public function getNodeType(): string
	{
		return InClassNode::class;
	}

	/**
	 * @param InClassNode $node
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		$classReflection = $node->getClassReflection();
		$className = $classReflection->getName();

		$parents = array_values($classReflection->getImmediateInterfaces());
		$parentClass = $classReflection->getParentClass();
		if ($parentClass !== null) {
			$parents[] = $parentClass;
		}

		$messages = [];

		foreach ($parents as $parentReflection) {
			$sealedAttributes = $parentReflection->getNativeReflection()->getAttributes(Sealed::class);
			if (count($sealedAttributes) === 0) {
				continue;
			}

			$sealedAttribute = reset($sealedAttributes);
			$permittedClassNames = $this->extractPermittedDescendants($sealedAttribute, $scope);
			if ( ! in_array($className, $permittedClassNames, true)) {
				$messages[] = RuleErrorBuilder::message(sprintf(
					'%s %s is not allowed to %s a #[Sealed] %s %s.',
					$classReflection->isClass() ? 'Class' : 'Interface',
					$className,
					$classReflection->isClass() && $parentReflection->isInterface() ? 'implement' : 'extend',
					$parentReflection->isClass() ? 'class' : 'interface',
					$parentReflection->getName(),
				))->build();
			}
		}

		$sealedAttributes = $classReflection->getNativeReflection()->getAttributes(Sealed::class);
		if (count($sealedAttributes) === 0) {
			return $messages;
		}

		if ( ! $classReflection->isClass() && ! $classReflection->isInterface()) {
			$messages[] = RuleErrorBuilder::message(
				'#[Sealed] can only be used over an abstract class or an interface.'
			)->build();
			return $messages;
		}

		if ($classReflection->isClass() && ! $classReflection->isAbstract()) {
			$messages[] = RuleErrorBuilder::message(sprintf(
				'#[Sealed] class %s must be abstract.',
				$className,
			))->build();
		}

		return $messages;
	}

	/**
	 * @return class-string[]
	 */
	private function extractPermittedDescendants(
		FakeReflectionAttribute|ReflectionAttribute $sealedAttribute,
		Scope $scope,
	): array
	{
		$sealed = $sealedAttribute->getArgumentsExpressions();
		if ( ! \array_key_exists(0, $sealed) && ! \array_key_exists('permits', $sealed)) {
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
