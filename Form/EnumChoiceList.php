<?php

namespace Fervo\EnumBundle\Form;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

class EnumChoiceList extends ChoiceList
{
    protected $typeName;

    public function __construct($enumClass, $typeName)
    {
        $this->typeName = $typeName;

        $choices = [];
        foreach ($enumClass::toArray() as $constant => $value) {
            $choices[$value] = $enumClass::$constant();
        }

        parent::__construct($choices, [], []);
    }

    public function getValuesForChoices(array $choices)
    {
        $choices = $this->fixChoices($choices);
        $values = array();

        foreach ($choices as $i => $givenChoice) {
            foreach ($this->choices as $j => $choice) {
                if ($choice == $givenChoice) {
                    $values[$i] = $this->values[$j];
                    unset($choices[$i]);

                    if (0 === count($choices)) {
                        break 2;
                    }
                }
            }
        }

        return $values;
    }

    protected function initialize($choices, array $labels, array $preferredChoices)
    {
        $labels = $this->createLabels($choices);

        parent::initialize($choices, $labels, $preferredChoices);
    }

    protected function createLabels($choices)
    {
        $labels = [];

        foreach ($choices as $i => $choice) {
            $labels[$i] = sprintf('%s.%s', $this->typeName, $choice->getValue());
        }

        return $labels;
    }
}
