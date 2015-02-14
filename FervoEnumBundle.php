<?php

namespace Fervo\EnumBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\Common\Proxy\Autoloader;

class FervoEnumBundle extends Bundle
{
    private $autoloader;

    const GENERATED_NAMESPACE = 'Generated\Doctrine';
    const GENERATED_DIR = '%kernel.cache_dir%/enumtypes/';

    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $this->registerAutoloader($container);

        $container->addCompilerPass(new DependencyInjection\Compiler\AddDoctrineTypes());
    }

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $this->registerAutoloader($this->container);
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

    private function registerAutoloader($container)
    {
        if (!$this->autoloader) {
            $cacheDir = $container->getParameter('kernel.cache_dir');
            $generatedDir = str_replace('%kernel.cache_dir%', $cacheDir, self::GENERATED_DIR);

            $this->autoloader = Autoloader::register($generatedDir, self::GENERATED_NAMESPACE, null);
        }
    }
}
