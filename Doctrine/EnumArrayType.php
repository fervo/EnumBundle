<?php

namespace Fervo\EnumBundle\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EnumArrayType extends Type
{
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value == null) {
            return null;
        }

        $data = json_decode($value, true);

        if (count($data['values']) == 0) {
            return [];
        }

        $enumClass = $data['class'];
        $values = array_map(function($enumValue) use ($enumClass) {
            return new $enumClass($enumValue);
        }, array_values($data['values']));

        return $values;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value == null) {
            return null;
        }

        if ($value == []) {
            return json_encode(['values' => []]);
        }

        $struct = [
            'class' => get_class($value[0]),
            'values' => array_map(function($enumInstance) {
                return $enumInstance->getValue();
            }, array_values($value)),
        ];

        return json_encode($struct);
    }

    public function getName()
    {
        return 'enumarray';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform) {
        return true;
    }
}
