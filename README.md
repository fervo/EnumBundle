# EnumBundle

## Getting started (a rough outline)

* Add the bundle as a dependency
* Add the bundle to your kernel
* Add the bundle config

## Bundle config example

```yaml
fervo_enum:
    enums:
        EnumExample\Action:
            doctrine_type: action # Type name used in doctrine annotations
            form_type: action # Type name used in Symfony Forms
```

## Enum class example

```php
namespace EnumExample;

use MyCLabs\Enum\Enum;

class Action extends Enum
{
    const VIEW = 'view';
    const EDIT = 'edit';
}
```

## Using Doctrine Types

```php
class Entity
{
    /**
     * @ORM\Type('action')
     */
    protected $action;
}
```

## Using JMSSerializer

```php
class Entity
{
    /**
     * @JMSSerializer\Type('EnumExample\Action')
     */
    protected $action;
}
```
