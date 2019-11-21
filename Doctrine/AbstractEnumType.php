<?php

namespace Fervo\EnumBundle\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

abstract class AbstractEnumType extends Type
{
    abstract public function getEnumClass();

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value == null) {
            return null;
        }

        $enumClass = $this->getEnumClass();

        // If the enumeration provides a casting method, apply it
        if (method_exists($enumClass, 'castValueIn')) {
            /** @var callable $castValueIn */
            $castValueIn = [$enumClass, 'castValueIn'];
            $value = $castValueIn($value);
        }

        return new $enumClass($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value == null) {
            return null;
        }

        $enumClass = $this->getEnumClass();

        // If the enumeration provides a casting method, apply it
        if (method_exists($enumClass, 'castValueOut')) {
            /** @var callable $castValueOut */
            $castValueOut = [$enumClass, 'castValueOut'];
            return $castValueOut($value->getValue());
        }

        return $value->getValue();
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform) {
        return true;
    }
}
