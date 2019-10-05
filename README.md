# Enum Bundle

Provides a [MyCLabs\Enum][myclabs-enum-homepage] integration with Doctrine for your Symfony projects.

## Installation

### Step 1: Download the Bundle

    $ composer require fervo/enum-bundle "^2.0"

### Step 2: Enable the Bundle

    <?php

    // app/AppKernel.php

    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...

                new Fervo\EnumBundle\FervoEnumBundle(),
            );

            // ...
        }

        // ...
    }

### Step 3: Configure your enum

    fervo_enum:
        enums:
            AppBundle\Enum\Gender:
                doctrine_type: gender # Type name used in doctrine annotations
                form_type: gender # Used in translation keys

### Step 4: Create your enum

    <?php

    namespace AppBundle\Enum\Gender;

    use MyCLabs\Enum\Enum;

    class Gender extends Enum
    {
        const MALE = 'male';
        const FEMALE = 'female';
    }

### Step 5: Use the enum in a doctrine entity

    <?php

    namespace AppBundle\Entity;

    use AppBundle\Enum\Gender;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity()
     */
    class Person
    {
        // ...

        /**
         * @ORM\Column(type="gender")
         */
        protected $gender;

        // ...

        public function getGender()
        {
            return $this->gender;
        }

        public function setGender(Gender $gender)
        {
            $this->gender = $gender;
        }

        // ...
    }

### Step 6: Use the enum in [Symfony forms][symfony-forms-homepage]

The bundle auto-generates a corresponding form type for each configured enum. The FQCN for the form type is on the format `FervoEnumBundle\Generated\Form\{{enum class name}}Type`. So with the enum class in the example above, it could be used in a form type in the following way.

    <?php

    namespace AppBundle\Form\Type;

    use FervoEnumBundle\Generated\Form\GenderType;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;

    class EmployeeType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
            	// ...
                ->add('gender', GenderType::class)
                // ...
            ;
        }
    }

If the underlying object of the form type is a doctrine mapped entity, the type can also be guessed by the framework. But it is a good practice to always specify the FQCN in form types.

Or you can use `EnumType` with configured options:

    <?php

    namespace AppBundle\Form\Type;

    use AppBundle\Enum\Gender;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;

    class EmployeeType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
            	// ...
                ->add('gender', EnumType::class, [
                    'class' => Gender::class,
                    'choice_label_prefix' => 'gender', // optional
                ])
                // ...
            ;
        }
    }

### Step 7: Specify translations for the enum values

The form type looks by default for the translation of the enum values in the `enums` translation domain. The translation keys are on the format `{{configured form_type name}}.{{enum constant value}}`. So going with the example the translation keys would be `gender.male` and `gender.female`.

## Additional functionality

### Use the enum with [Symfony @ParamConverter][symfony-paramconver-homepage]

    <?php

    namespace AppBundle\Controller;

    use AppBundle\Enum\Gender;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    class EmployeeController extends Controller
    {
        /**
         * @ParamConverter("gender")
         */
        public function indexAction(Gender $gender)
        {
            // ...
        }
    }

### Use the enum with [JMS\Serializer][jms-serializer-homepage]

    <?php

    namespace AppBundle\Entity;

    use AppBundle\Enum\Gender;
    use JMS\Serializer\Annotation as JMS;

    class Person
    {
        // ...

        /**
         * @JMS\Type("gender")
         */
        protected $gender;

        // ...

        public function getGender()
        {
            return $this->gender;
        }

        public function setGender(Gender $gender)
        {
            $this->gender = $gender;
        }

        // ...
    }

### Customize value casting

In case the values of your enumeration are not strings, you can use the two magic function `castValueIn` and `castValueOut` to support non-string values:

```php
<?php

namespace App\Enum;

use MyCLabs\Enum\Enum;

class Status extends Enum {
    public const SUCCESS = 1;
    public const ERROR = 2;
    
    public static function castValueIn($value) {
        return (int) $value;
    }
}
```

[myclabs-enum-homepage]: https://github.com/myclabs/php-enum
[jms-serializer-homepage]: http://jmsyst.com/libs/serializer
[symfony-forms-homepage]: http://symfony.com/doc/current/book/forms.html
[symfony-paramconver-homepage]: http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html
