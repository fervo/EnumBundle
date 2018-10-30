<?php

namespace Fervo\EnumBundle\JMSSerializer;

use JMS\Serializer\Context;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use MyCLabs\Enum\Enum;

class EnumHandler
{
    public function serializeEnumToJson(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        if ($context->getDirection() == GraphNavigator::DIRECTION_SERIALIZATION) {
            if (!$data instanceof Enum) {
                throw new \UnexpectedValueException(sprintf('%s is not a valid enum', $data));
            }

            return $data->getValue();
        } elseif ($context->getDirection() == GraphNavigator::DIRECTION_DESERIALIZATION) {
            $enumClass = $type['name'];

            if (null === $data) {
                return null;
            }

            foreach ($enumClass::toArray() as $constant => $constantValue) {
                if ($data == $constantValue) {
                    return $enumClass::$constant();
                }
            }

            throw new \UnexpectedValueException(sprintf('%s is not a valid %s value', $data, $enumClass));
        }

        throw new \UnexpectedValueException("Invalid direction");
    }
}
