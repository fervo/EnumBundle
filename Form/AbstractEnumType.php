<?php

namespace Fervo\EnumBundle\Form;

@trigger_error('The '.__NAMESPACE__.'\AbstractEnumType class is deprecated since version 2.1 and will be removed in 3.0. Use '.__NAMESPACE__.'\EnumType instead.', E_USER_DEPRECATED);

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'enums',
            'choice_value' => function($enum) {
                if ($enum === null) {
                    return null;
                }
                
                return $enum->getValue();
            },
        ]);
        if (Kernel::VERSION_ID < 30000) {
            $resolver->setDefault('choices_as_values', true);
        }
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
