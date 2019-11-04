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

        return new $enumClass($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value == null) {
            return null;
        }

        return $value->getValue();
    }
    
    public function requiresSQLCommentHint(AbstractPlatform $platform) : bool
    {
        return true;
    }
}
