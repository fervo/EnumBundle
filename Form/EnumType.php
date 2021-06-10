<?php

namespace Fervo\EnumBundle\Form;

use MyCLabs\Enum\Enum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnumType extends AbstractType
{
    private $enumMap;
    private $fqcnChoicePrefix;

    public function __construct(array $enumMap, bool $fqcnChoicePrefix)
    {
        $this->enumMap = $enumMap;
        $this->fqcnChoicePrefix = $fqcnChoicePrefix;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choice_translation_domain' => 'enums',
            'choice_value' => function (Enum $enum = null) {
                if ($enum === null) {
                    return null;
                }

                return $enum->getValue();
            },
            'choice_label' => function (Options $options) {
                return function (Enum $value) use ($options) {
                    return sprintf('%s.%s', $options['choice_label_prefix'], $value->getValue());
                };
            },
            'choice_label_prefix' => function (Options $options) {
                // BC compatibility layer, to be removed in 3.0
                if ($this->fqcnChoicePrefix) {
                    return StringUtil::fqcnToBlockPrefix($options['class']);
                }

                if (!isset($this->enumMap[$options['class']])) {
                    throw new \LogicException(sprintf('No prefix found for class %s', $options['class']));
                }

                return $this->enumMap[$options['class']];
            },
        ]);
        $resolver->setRequired(['class']);
        $resolver->setAllowedValues('class', function ($value) {
            return is_subclass_of($value, Enum::class);
        });
        if (Kernel::VERSION_ID < 30000) {
            $resolver->setDefault('choices_as_values', true);
        }
        if (Kernel::VERSION_ID < 30200) {
            $resolver->setNormalizer('choices', function (OptionsResolver $resolver) {
                return $resolver['class']::values();
            });
        } else {
            $resolver->setDefault('choice_loader', function (Options $options) {
                return new CallbackChoiceLoader(function () use ($options) {
                    return $options['class']::values();
                });
            });
        }
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
