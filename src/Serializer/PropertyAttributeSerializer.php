<?php

namespace PHPhinderBundle\Serializer;

use PHPhinderBundle\Schema\Attribute\Property;
use ReflectionClass;

class PropertyAttributeSerializer
{
    public static function serialize(object $entity): array
    {
        $reflectionClass = new ReflectionClass($entity);
        $serializedData = [];

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyAttributes = $property->getAttributes(Property::class);

            if (!empty($propertyAttributes)) {
                $property->setAccessible(true);
                $value = $property->getValue($entity);
                $field = $property->getName();
                $serializedData[$field === 'id' ? '_id' : $field] = $value;
            }
        }
        return $serializedData;
    }
}
