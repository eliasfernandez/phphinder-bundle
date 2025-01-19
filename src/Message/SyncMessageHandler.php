<?php

namespace PHPhinderBundle\Message;

use Doctrine\ORM\EntityManagerInterface;
use PHPhinderBundle\EventSubscriber\SyncEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncMessageHandler
{
    public function __construct(
        private readonly EntityManagerInterface $objectManager,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {

    }
    public function __invoke(SyncMessage $message)
    {
        $entity = $this->objectManager->find($message->className, $message->id);
        if (null === $entity) {
            throw new \LogicException(sprintf('No %s entity found with id %s ', $message->className, $message->id));
        }

        $this->eventDispatcher->dispatch(new SyncEvent($entity), SyncEvent::EVENT);
    }
}