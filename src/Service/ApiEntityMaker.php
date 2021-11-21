<?php

namespace App\Service;

use App\Attributes\ApiEntity;
use App\Attributes\ApiProperty;
use App\Exception\EntityMakerException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\VarExporter\Instantiator;

class ApiEntityMaker
{
    /**
     * @throws ReflectionException
     * @throws EntityMakerException
     */
    private function getReflectionClass($objectOrClass): ReflectionClass
    {
        $reflection = new ReflectionClass($objectOrClass);
        $classAttributes = $reflection->getAttributes(ApiEntity::class);
        if (!$classAttributes) {
            throw new EntityMakerException('Requested class is missing the ApiEntity attribute');
        }

        return $reflection;
    }

    public function mapApiEntityToLocalEntity($remoteEntity, &$localEntity = null): void
    {
        if (null === $localEntity) {
            $localEntity = Instantiator::instantiate($remoteEntity::class);
        }
        $privateProperties = $this->getApiFieldAndValues($remoteEntity, $remoteEntity);

        $localReflector = $this->getReflectionClass($localEntity);
        foreach ($privateProperties as $propertyName => $propertyValue) {
            $localProperty = $localReflector->getProperty($propertyName);
            $localProperty->setAccessible(true);
            $localProperty->setValue($localEntity, $propertyValue);
        }
    }

    public function createEntityFromApiPayload(string $className, array $payload): mixed
    {
        $privateProperties = $this->getApiFieldAndValues($className, $payload);

        return Instantiator::instantiate($className, [], [$className => $privateProperties]);
    }

    private function getApiFieldAndValues(object|string $objectOrClass, object|array $payload): array
    {
        $mappings = $this->getPropertyToArrayKeyMapping($objectOrClass);
        if (is_array($payload)) {
            return $this->mapFromArray($mappings, $payload);
        }

        return $this->mapFromObject($mappings, $payload);
    }

    private function mapFromObject(array $mappings, object $payload): array
    {
        $payloadReflector = $this->getReflectionClass($payload);

        $privateProperties = [];

        foreach ($mappings as $propertyName => $arrayKeyName) {
            $property = $payloadReflector->getProperty($propertyName);
            $property->setAccessible(true);
            $privateProperties[$propertyName] = $property->getValue($payload);
        }

        return $privateProperties;
    }

    private function mapFromArray(array $mappings, array $payload): array
    {
        $privateProperties = [];
        foreach ($mappings as $propertyName => $arrayKeyName) {
            $privateProperties[$propertyName] = $payload[$arrayKeyName];
        }

        return $privateProperties;
    }

    private function getPropertyToArrayKeyMapping(object|string $objectOrClass): array
    {
        $reflection = $this->getReflectionClass($objectOrClass);
        $properties = $reflection->getProperties();

        // Create an array of private properties because that's what Symfony's hydrator wants
        $privateProperties = [];
        foreach ($properties as $property) {

            // We're only looking for properties that have our custom attribute on them
            $attributes = $property->getAttributes(ApiProperty::class);
            if (!$attributes) {
                continue;
            }

            if (1 !== count($attributes)) {
                throw new EntityMakerException('The ApiProperty attribute can only be used once on each property');
            }

            /** @var ApiProperty $apiProperty */
            $apiProperty = $attributes[0]->newInstance();

            // The attribute supports customizing the key in the array to use, but defaults to the property's name
            $arrayKeyName = $apiProperty->getApiArrayKeyName() ?? $property->getName();

            $privateProperties[$property->getName()] = $arrayKeyName;
        }

        return $privateProperties;
    }
}