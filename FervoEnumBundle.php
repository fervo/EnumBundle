<?php

namespace Fervo\EnumBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

class FervoEnumBundle extends Bundle
{
    private $autoloaderRegistered = false;

    const GENERATED_DIR = '%kernel.cache_dir%/fervoenumbundle';
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

    private function registerAutoloader(ContainerInterface $container)
    {
        if (!$this->autoloaderRegistered) {
            if (Kernel::VERSION_ID < 30300) {
                $projectDir = $container->getParameter('kernel.root_dir').'/..';
            } else {
                $projectDir = $container->getParameter('kernel.project_dir');
            }
            $cacheDir = $container->getParameter('kernel.cache_dir');
            $generatedDir = str_replace('%kernel.cache_dir%', $cacheDir, self::GENERATED_DIR);

            /** @var \Composer\Autoload\ClassLoader $loader */
            $loader = require $projectDir.'/vendor/autoload.php';
            $loader->addPsr4(self::VENDOR_NAMESPACE.'\\', $generatedDir);

            $this->autoloaderRegistered = true;
        }
    }
}
