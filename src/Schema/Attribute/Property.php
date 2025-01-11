<?php
namespace PHPhinderBundle\Schema\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::TARGET_METHOD)]
class Property
{
    public function __construct(public readonly int $flags, public readonly ?string $name = null)
    {}
}
