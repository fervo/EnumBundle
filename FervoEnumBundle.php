<?php

namespace Fervo\EnumBundle;

use Symfony\Component\ClassLoader\Psr4ClassLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FervoEnumBundle extends Bundle
{
    private $autoloader;

    const GENERATED_DIR = '%kernel.cache_dir%/fervoenumbundle/';
    const VENDOR_NAMESPACE = 'FervoEnumBundle';
    const DOCTRINE_NAMESPACE = 'Generated\Doctrine';
    const FORM_NAMESPACE = 'Generated\Form';

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

            $loader = new Psr4ClassLoader();
            $loader->addPrefix(self::VENDOR_NAMESPACE, $generatedDir);
            $loader->register();
        }
    }
}
