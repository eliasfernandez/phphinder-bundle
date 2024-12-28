<?php

namespace PHPhinderBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use PHPhinderBundle\Schema\SchemaGenerator;

#[AsDoctrineListener(event: Events::loadClassMetadata)]
class SchemaListener
{
    private SchemaGenerator $schemaGenerator;

    public function __construct(SchemaGenerator $schemaGenerator)
    {
        $this->schemaGenerator = $schemaGenerator;
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args): void
    {
        $classMetadata = $args->getClassMetadata();
        $this->schemaGenerator->generate($classMetadata->getName());
    }
}
