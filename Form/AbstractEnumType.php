<?php

namespace Fervo\EnumBundle\Form;

use MyCLabs\Enum\Enum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices_as_values' => true,
            'translation_domain' => 'enums',
            'choice_value' => function($enum) {
                if ($enum === null) {
                    return null;
                }
                
                return $enum->getValue();
            },
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
