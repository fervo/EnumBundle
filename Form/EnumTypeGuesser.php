<?php

namespace Fervo\EnumBundle\Form;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Mapping\MappingException as LegacyMappingException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\MappingException;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

class EnumTypeGuesser implements FormTypeGuesserInterface
{
    protected $registry;
    protected $doctrineFormMap;

    private $cache = array();

    public function __construct(ManagerRegistry $registry = null, array $doctrineFormMap)
    {
        $this->registry = $registry;
        $this->doctrineFormMap = $doctrineFormMap;
    }

    public function guessType($class, $property)
    {
        if (!$this->registry) {
            return;
        }

        if (!$ret = $this->getMetadata($class)) {
            return;
        }

        list($metadata, $name) = $ret;

        $doctrineType = $metadata->getTypeOfField($property);

        if (isset($this->doctrineFormMap[$doctrineType])) {
            return new TypeGuess($this->doctrineFormMap[$doctrineType]['class'], array(), Guess::HIGH_CONFIDENCE);
        }
    }

    public function guessRequired($class, $property)
    {

    }

    public function guessMaxLength($class, $property)
    {

    }

    public function guessPattern($class, $property)
    {

    }

    protected function getMetadata($class)
    {
        // normalize class name
        $class = ClassUtils::getRealClass(ltrim($class, '\\'));

        if (array_key_exists($class, $this->cache)) {
            return $this->cache[$class];
        }

        $this->cache[$class] = null;
        foreach ($this->registry->getManagers() as $name => $em) {
            try {
                return $this->cache[$class] = array($em->getClassMetadata($class), $name);
            } catch (MappingException $e) {
                // not an entity or mapped super class
            } catch (LegacyMappingException $e) {
                // not an entity or mapped super class, using Doctrine ORM 2.2
            }
        }
    }
}
