<?php

declare(strict_types=1);

namespace JiriPudil\SealedClasses;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function count;
use function reset;
use function sprintf;

/**
 * @implements Rule<InClassNode>
 */
final class SealedClassRule implements Rule
{
	public function __construct(
		private readonly ReflectionProvider $reflectionProvider,
	)
	{
	}

	public function getNodeType(): string
	{
		return InClassNode::class;
	}

	/**
	 * @param InClassNode $node
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		$messages = [];

		$classReflection = $node->getClassReflection();
		$className = $classReflection->getName();

		$sealedAttributes = $classReflection->getNativeReflection()->getAttributes(Sealed::class);
		if (count($sealedAttributes) === 0) {
			return $messages;
		}

		if ( ! $classReflection->isClass() && ! $classReflection->isInterface()) {
			$messages[] = RuleErrorBuilder::message(
				'#[Sealed] can only be used over a class or an interface.'
			)
				->identifier('sealedClass.invalidTarget')
				->build();
			return $messages;
		}

		$sealedAttribute = reset($sealedAttributes);
		$permittedClassNames = SealedClassUtils::extractPermittedDescendants($sealedAttribute, $scope);
		foreach ($permittedClassNames as $permittedClassName) {
			if ( ! $this->reflectionProvider->hasClass($permittedClassName)) {
				// ignore, will be reported elsewhere
				continue;
			}

			$permittedClass = $this->reflectionProvider->getClass($permittedClassName);

			if ($permittedClass->getParentClass()?->getName() === $className) {
				continue;
			}

			foreach ($permittedClass->getImmediateInterfaces() as $immediateInterface) {
				if ($immediateInterface->getName() === $className) {
					continue 2;
				}
			}

			$messages[] = RuleErrorBuilder::message(sprintf(
				'Type %s is not a direct subtype of #[Sealed] %s %s.',
				$permittedClassName,
				$classReflection->isClass() ? 'class' : 'interface',
				$classReflection->getName(),
			))
				->identifier('sealedClass.notDirectSubtype')
				->build();
		}

		return $messages;
	}
}
