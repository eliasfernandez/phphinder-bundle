<?php

namespace PHPhinderBundle\Factory;

use PHPhinder\Index\DbalStorage;
use PHPhinder\Index\JsonStorage;
use PHPhinder\Index\RedisStorage;
use PHPhinder\Index\Storage;
use PHPhinder\Schema\Schema;

class StorageFactory
{
    public function __construct(
        private readonly string $storageType,
        private readonly string $name,
        private readonly string $projectDir,
    ) {}

    public function createStorage(Schema $schema): Storage
    {
        return match ($this->storageType) {
            'dbal' => new DbalStorage($this->name, $schema),
            'redis' => new RedisStorage($this->name, $schema),
            default => new JsonStorage($this->projectDir . DIRECTORY_SEPARATOR . $this->name, $schema)
        };
    }
}