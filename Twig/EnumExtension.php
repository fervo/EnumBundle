<?php

namespace Fervo\EnumBundle\Twig;

use MyCLabs\Enum\Enum;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

class EnumExtension extends AbstractExtension
{
    protected $translator;
    protected $enumMap;

    public function __construct(TranslatorInterface $translator, array $enumMap)
    {
        $this->translator = $translator;
        $this->enumMap = $enumMap;
    }

    public function getName()
    {
        return 'enum';
    }

    public function getTests()
    {
        return [
            new TwigTest('enum', [$this, 'isEnum']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('enum_trans', [$this, 'getEnumTranslation']),
        ];
    }

    public function isEnum(Enum $left, $rightEnumClass, $rightEnumConst)
    {
        return $left == new $rightEnumClass(constant("$rightEnumClass::$rightEnumConst"));
    }

    public function getEnumTranslation(Enum $enum)
    {
        if (isset($this->enumMap[get_class($enum)])) {
            $enumType = $this->enumMap[get_class($enum)];
            return $this->translator->trans(sprintf("%s.%s", $enumType, $enum->getValue()), [], 'enums');
        }

        return $enum->getValue();
    }
}
