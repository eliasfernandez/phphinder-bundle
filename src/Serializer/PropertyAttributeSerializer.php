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
                $field = $propertyAttributes->name ?? $property->getName();
                $serializedData[$field === 'id' ? '_id' : $field] = $value;
            }
        }

        foreach ($reflectionClass->getMethods() as $method) {
            $propertyAttributes = $method->getAttributes(Property::class);

            if (!empty($propertyAttributes)) {
                $arguments = $propertyAttributes[0]->getArguments();
                $value = $method->invoke($entity);
                $field = $arguments['name'] ?? $method->getName();
                $serializedData[$field === 'id' ? '_id' : $field] = $value;
            }
        }

        return $serializedData;
    }
}
