<?php
namespace PHPhinderBundle;

use PHPhinderBundle\DependencyInjection\PHPhinderExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PHPhinderBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new PHPhinderExtension();
        }

        return $this->extension;
    }
}
