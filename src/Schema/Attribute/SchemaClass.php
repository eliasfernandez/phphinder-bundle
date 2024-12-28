<?php
namespace PHPhinderBundle\Schema\Attribute;

use Attribute;
use PHPhinder\Transformer\Transformer;

#[Attribute(Attribute::TARGET_CLASS)]
class SchemaClass
{
    /**
     * @param array<Transformer> $transformers
     */
    public function __construct(
        public readonly string $name,
        public readonly array $transformers
    ) {
    }
}
