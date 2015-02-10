<?php

namespace Fervo\EnumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EnumType extends AbstractType
{
    protected $enumClass;
    protected $name;

    public function __construct($enumClass, $name)
    {
        $this->enumClass = $enumClass;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'enums',
            'choice_list' => new EnumChoiceList($this->enumClass, $this->name),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
