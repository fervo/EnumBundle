<?php

namespace Fervo\EnumBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\Common\Proxy\Autoloader;

class FervoEnumBundle extends Bundle
{
    private $autoloader;

    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DependencyInjection\Compiler\AddDoctrineTypes());
    }

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        if ($this->container->hasParameter('fervo_enum.doctrine_types.namespace')) {
            $namespace = $this->container->getParameter('fervo_enum.doctrine_types.namespace');
            $dir = $this->container->getParameter('fervo_enum.doctrine_types.dir');

            $this->autoloader = Autoloader::register($dir, $namespace, null);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function shutdown()
    {
        if (null !== $this->autoloader) {
            spl_autoload_unregister($this->autoloader);
            $this->autoloader = null;
        }
    }

}
