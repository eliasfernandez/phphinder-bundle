<?php

namespace PHPhinderBundle\EventListener;

use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use PHPhinder\SearchEngine;
use PHPhinderBundle\Factory\StorageFactory;
use PHPhinderBundle\Schema\SchemaGenerator;
use PHPhinderBundle\Serializer\PropertyAttributeSerializer;

class EntityListener
{
    /** @var array<SearchEngine> */
    private array $searchEngines = [];

    public function __construct(
        private SchemaGenerator $schemaGenerator,
        private StorageFactory $factory
    ) {
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        //$this->syncEntity($args->getObject());
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        //$this->syncEntity($args->getObject());
    }

    private function syncEntity(object $entity): void
    {
        if (!$this->schemaGenerator->isSearchable($entity::class)) {
            return;
        }
        $schema = $this->schemaGenerator->generate($entity::class);

        if (!isset($this->searchEngines[$entity::class])) {
            $this->searchEngines[$entity::class] = new SearchEngine($this->factory->createStorage($schema));
        }
        $searchEngine = $this->searchEngines[$entity::class];

        $searchEngine->addDocument(array_map(
            fn ($value) => is_array($value) ? implode(', ', $value): $value, // patch to allow multi values
            PropertyAttributeSerializer::serialize($entity)
        ));
        $searchEngine->flush();
    }
}
