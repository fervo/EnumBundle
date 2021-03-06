<?php

namespace Fervo\EnumBundle\DependencyInjection;

use Fervo\EnumBundle\FervoEnumBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class FervoEnumExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $cacheDir = $container->getParameter('kernel.cache_dir');
        $generatedDir = str_replace('%kernel.cache_dir%', $cacheDir, FervoEnumBundle::GENERATED_DIR);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $enumHandlerDef = $container->getDefinition('fervo_enum.jms_serializer.enum_handler');

        $enumTypeClasses = [];
        $formTypeClasses = [];
        $doctrineFormMap = [];
        $enumMap = [];
        foreach ($config['enums'] as $className => $classConfig) {
            $enumTypeClasses[$classConfig['doctrine_type']] = ['commented' => null, 'class' => $this->writeTypeClassFile($className, $classConfig, FervoEnumBundle::VENDOR_NAMESPACE, FervoEnumBundle::DOCTRINE_NAMESPACE, $generatedDir)];
            $formTypeClasses[$classConfig['form_type']] = ['class' => $this->writeFormTypeClassFile($className, $classConfig, FervoEnumBundle::VENDOR_NAMESPACE, FervoEnumBundle::FORM_NAMESPACE, $generatedDir)];
            $doctrineFormMap[$classConfig['form_type']] = $classConfig['doctrine_type'];
            $enumMap[$className] = $classConfig['form_type'];

            $enumHandlerDef->addTag('jms_serializer.handler', ['type' => $className, 'format' => 'json', 'method' => 'serializeEnumToJson']);
        }

        if ($config['fqcn_choice_label_prefix']) {
            @trigger_error('Using the default value (true) for fqcn_choice_label_prefix is deprecated. Please change it to false and check that your translations in choice types are still correct.', E_USER_DEPRECATED);
        }

        $container->setParameter('fervo_enum.doctrine_type_classes', $enumTypeClasses);
        $container->setParameter('fervo_enum.form_type_classes', $formTypeClasses);
        $container->setParameter('fervo_enum.doctrine_form_map', $doctrineFormMap);
        $container->setParameter('fervo_enum.enum_map', $enumMap);
        $container->setParameter('fervo_enum.fqcn_choice_label_prefix', $config['fqcn_choice_label_prefix']);
    }

    protected function writeTypeClassFile($className, $config, $vendorNamespace, $subNamespace, $dir)
    {
        $namespace = sprintf('%s\\%s', $vendorNamespace, $subNamespace);
        $typeClassName = 'Generated'.ucfirst($config['doctrine_type']).'Type';
        $classFile = $this->createTypeClass($className, $typeClassName, $config['doctrine_type'], $namespace);

        $doctrineDir = $dir.'/'.str_replace('\\', '/', $subNamespace);
        if (!is_dir($doctrineDir)) {
            mkdir($doctrineDir, 0755, true);
        }

        file_put_contents($doctrineDir.'/'.$typeClassName.'.php', $classFile);

        return $namespace.'\\'.$typeClassName;
    }

    protected function createTypeClass($className, $typeClassName, $typeName, $namespace)
    {
        $template = file_get_contents(__DIR__.'/../Resources/DoctrineType.php.template');
        $typeClass = strtr($template, [
            '{{namespace}}' => $namespace,
            '{{typeClassName}}' => $typeClassName,
            '{{typeClass}}' => $className,
            '{{typeName}}' => $typeName,
        ]);

        return $typeClass;
    }

    protected function writeFormTypeClassFile($enumFQCN, $config, $vendorNamespace, $subNamespace, $dir)
    {
        $namespace = sprintf('%s\\%s', $vendorNamespace, $subNamespace);
        $enumClass = substr($enumFQCN, strrpos($enumFQCN, '\\') +1);
        $typeClassName = $enumClass.'Type';

        $classFile = $this->createFormTypeClass($typeClassName, $namespace, $config['form_type'], $enumFQCN);

        $formDir = $dir.'/'.str_replace('\\', '/', $subNamespace);
        if (!is_dir($formDir)) {
            mkdir($formDir, 0755, true);
        }

        file_put_contents($formDir.'/'.$typeClassName.'.php', $classFile);

        return $namespace.'\\'.$typeClassName;
    }

    protected function createFormTypeClass($typeClassName, $namespace, $choiceLabelPrefix, $enumFQCN)
    {
        $template = file_get_contents(__DIR__.'/../Resources/FormType.php.template');
        $typeClass = strtr($template, [
            '{{namespace}}' => $namespace,
            '{{enumFQCN}}' => $enumFQCN,
            '{{typeClassName}}' => $typeClassName,
            '{{choiceLabelPrefix}}' => $choiceLabelPrefix,
        ]);

        return $typeClass;
    }
}
