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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'enums',
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
                return StringUtil::fqcnToBlockPrefix($options['class']);
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
                    if (PHP_VERSION_ID < 70000) {
                        return $options['class']::values();
                    } else {
                        return $options['class']::values();
                    }
                });
            });
        }
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
