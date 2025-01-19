<?php

namespace  PHPhinderBundle\Message;

class SyncMessage
{
    public function __construct(
        public string $className,
        public string $id
    ) {
    }
}