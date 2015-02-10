<?php

namespace Fervo\EnumBundle\Doctrine;

use AppBundle\Enum\CommentStatus;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Types\Type;

abstract class AbstractEnumType extends Type
{
    abstract public function getEnumClass();

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $enumClass = $this->getEnumClass();
        $typeName = $this->getName();

        switch (true) {
            case $platform instanceof SqlitePlatform:
                return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
            case $platform instanceof MySqlPlatform:
                $values = $enumClass::toArray();
                $values = array_map([$platform, 'quoteStringLiteral'], $values);
                return 'ENUM('.implode(', ', $values).')';
            default:
                throw new Exception("No implementation of $typeName for current Doctrine platform");
        }
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
}
