<?php

namespace PHPhinderBundle\EventSubscriber;

use PHPhinder\SearchEngine;
use PHPhinderBundle\Factory\StorageFactory;
use PHPhinderBundle\Schema\SchemaGenerator;
use PHPhinderBundle\Serializer\PropertyAttributeSerializer;

class SyncEventSubscriber
{

    /** @var array<SearchEngine> */
    private array $searchEngines = [];

    public function __construct(
        private SchemaGenerator $schemaGenerator,
        private StorageFactory $factory
    ) {
    }

    public function onSyncEvent(SyncEvent $event): void
    {
        $entity = $event->object;
        if (!$this->schemaGenerator->isSearchable($entity::class)) {
            return;
        }
        $schema = $this->schemaGenerator->generate($entity::class);

        if (!isset($this->searchEngines[$entity::class])) {
            $this->searchEngines[$entity::class] = new SearchEngine($this->factory->createStorage($schema));
        }
        $searchEngine = $this->searchEngines[$entity::class];

        $searchEngine->addDocument(array_filter(array_map(
            fn ($value) => is_array($value) ? implode(', ', $value) : $value, // patch to allow multi values
            PropertyAttributeSerializer::serialize($entity)
        )));
        $searchEngine->flush();
    }
}