<?php

declare(strict_types=1);

namespace JiriPudil\SealedClasses;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function array_values;
use function assert;
use function in_array;
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

		$parentClass = $classReflection->getParentClass();
		$parents = array_values($classReflection->getImmediateInterfaces());
		if ($parentClass !== null) {
			$parents[] = $parentClass;
		}

		$messages = [];

		foreach ($parents as $parentReflection) {
			$sealedAttributes = $parentReflection->getNativeReflection()->getAttributes(Sealed::class);
			foreach ($sealedAttributes as $sealedAttribute) {
				$sealed = $sealedAttribute->newInstance();
				assert($sealed instanceof Sealed);

				if ( ! in_array($className, $sealed->permits, true)) {
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
		}

		$sealedAttributes = $classReflection->getNativeReflection()->getAttributes(Sealed::class);
		foreach ($sealedAttributes as $sealedAttribute) {
			$sealed = $sealedAttribute->newInstance();
			assert($sealed instanceof Sealed);

			if ( ! $classReflection->isClass() && ! $classReflection->isInterface()) {
				$messages[] = RuleErrorBuilder::message(
					'#[Sealed] can only be used over an abstract class or an interface.'
				)->build();
				continue;
			}

			if ($classReflection->isClass() && ! $classReflection->isAbstract()) {
				$messages[] = RuleErrorBuilder::message(sprintf(
					'#[Sealed] class %s must be abstract.',
					$className,
				))->build();
			}

			if ($sealed->permits === []) {
				$messages[] = RuleErrorBuilder::message(sprintf(
					'#[Sealed] %s %s does not permit any descendant.',
					$classReflection->isClass() ? 'class' : 'interface',
					$className,
				))->build();
			}
		}

		return $messages;
	}
}
