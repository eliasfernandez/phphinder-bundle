<?php

namespace PHPhinderBundle\EventListener;

use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use PHPhinder\SearchEngine;
use PHPhinderBundle\EventSubscriber\SyncEvent;
use PHPhinderBundle\Factory\StorageFactory;
use PHPhinderBundle\Message\SyncMessage;
use PHPhinderBundle\Schema\SchemaGenerator;
use PHPhinderBundle\Serializer\PropertyAttributeSerializer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class EntityListener
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly bool $autoSync,
        private readonly bool $syncInBackground,
        private readonly MessageBusInterface $messenger
    ) {
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $this->syncEntity($args->getObject());
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $this->syncEntity($args->getObject());
    }

    private function syncEntity(object $entity): void
    {
        if ($this->autoSync) {
            $this->eventDispatcher->dispatch(new SyncEvent($entity), SyncEvent::EVENT);
            return;
        }

        if ($this->syncInBackground) {
            $this->messenger->dispatch(new SyncMessage($entity::class, $entity->getId()));
        }
    }
}
