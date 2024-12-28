<?php
namespace PHPhinderBundle\Schema\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Property
{
    public function __construct(public readonly int $flags)
    {}
}
