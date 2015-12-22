<?php

namespace Fervo\EnumBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class UnregisterFormGuesser implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('doctrine')) {
            $container->removeDefinition('fervo_enum.form_guesser');
        }
    }
}
