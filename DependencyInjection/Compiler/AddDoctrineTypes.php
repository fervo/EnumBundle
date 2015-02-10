<?php

namespace Fervo\EnumBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AddDoctrineTypes implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('doctrine.dbal.connection_factory')) {
            return;
        }

        $types = $container->getParameter('doctrine.dbal.connection_factory.types');
        $enumTypes = $container->getParameter('fervo_enum.doctrine_type_classes');

        $allTypes = array_merge($types, $enumTypes);
        $container->setParameter('fervo_enum.all_types', $allTypes);

        $container->findDefinition('doctrine.dbal.connection_factory')
            ->replaceArgument(0, '%fervo_enum.all_types%');
    }
}
