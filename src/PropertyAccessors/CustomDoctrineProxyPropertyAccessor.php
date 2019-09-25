<?php

namespace App\PropertyAccessors;

use AutoMapperPlus\PropertyAccessor\PropertyAccessor;
use Doctrine\ORM\Proxy\Proxy;

class CustomDoctrineProxyPropertyAccessor extends PropertyAccessor
{
    public function getProperty($object, string $propertyName)
    {
        // This is the magical part :)
        if ($object instanceof Proxy) {
            $object->__load();
        }

        return parent::getProperty($object, $propertyName);
    }
}