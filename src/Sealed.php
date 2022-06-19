<?php

declare(strict_types=1);

namespace JiriPudil\SealedClasses;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Sealed
{
	/**
	 * @param class-string[] $permits
	 */
	public function __construct(
		public array $permits
	) {}
}
