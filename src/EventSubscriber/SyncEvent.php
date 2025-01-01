<?php

namespace PHPhinderBundle\EventSubscriber;


class SyncEvent
{
    public const EVENT= 'sync.event';
    public function __construct(public object $object)
    {
    }
}