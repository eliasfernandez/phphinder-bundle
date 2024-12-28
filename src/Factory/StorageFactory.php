<?php

namespace PHPhinderBundle\Factory;

use PHPhinder\Index\DbalStorage;
use PHPhinder\Index\JsonStorage;
use PHPhinder\Index\Storage;
use PHPhinder\Schema\Schema;

class StorageFactory
{
    public function __construct(
        private readonly string $storageType,
        private readonly string $name,
    ) {}

    public function createStorage(Schema $schema): Storage
    {
        if ($this->storageType === 'dbal') {
            return new DbalStorage($this->name, $schema);
        }

        return new JsonStorage($this->name, $schema);
    }
}