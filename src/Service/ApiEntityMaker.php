<?php

namespace App\Service;

use App\Attributes\ApiEntity;
use App\Attributes\ApiProperty;
use App\DTO\ApiPropertyNameAndType;
use App\Exception\EntityMakerException;
use DateTimeInterface;
use Doctrine\Persistence\ObjectManager;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use Symfony\Component\VarExporter\Instantiator;

class ApiEntityMaker
{
    public function __construct(private readonly ObjectManager $objectManager)
    {
    }

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
            //$localProperty->setAccessible(true);
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

    private function transformToEntity(ApiPropertyNameAndType $propertyInfo, mixed $value): ?object
    {
        $repository = $this->objectManager->getRepository($propertyInfo->entityClass);

        // TODO: I'm not sure if this can actually return null
        if (!$repository) {
            throw new EntityMakerException(sprintf('Could not find repository for entity of type %1$s', $propertyInfo->entityClass));
        }

        return $repository->find($value);
    }

    private function transformToDate(ApiPropertyNameAndType $propertyInfo, mixed $value): ?DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }
        $ret = \DateTimeImmutable::createFromFormat('Y-m-d', $value);
        if (false === $ret) {
            throw new EntityMakerException(sprintf('Could not transform %1$s to a date', $value));
        }

        return $ret;
    }

    private function transformToDateTime(ApiPropertyNameAndType $propertyInfo, mixed $value): ?DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        $ret = \DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $value);
        if (false === $ret) {
            throw new EntityMakerException(sprintf('Could not transform %1$s to a datetime', $value));
        }

        return $ret;
    }

    private function maybeTransformValueBeforeSetting(ApiPropertyNameAndType $propertyInfo, mixed $value): mixed
    {
        return match ($propertyInfo->type) {
            ApiProperty::PROPERTY_TYPE_STRING => (string)$value,
            ApiProperty::PROPERTY_TYPE_BOOLEAN => (bool)$value,
            ApiProperty::PROPERTY_TYPE_DEFAULT => $value,
            ApiProperty::PROPERTY_TYPE_DATE => $this->transformToDate($propertyInfo, $value),
            ApiProperty::PROPERTY_TYPE_DATETIME => $this->transformToDateTime($propertyInfo, $value),
            ApiProperty::PROPERTY_TYPE_ENTITY => $this->transformToEntity($propertyInfo, $value),
            default => throw new EntityMakerException(sprintf('Unhandled transformation type: %1$s', $propertyInfo->type))
        };
    }

    /**
     * @param ApiPropertyNameAndType[] $mappings
     * @param object $payload
     * @return array
     * @throws EntityMakerException
     * @throws ReflectionException
     */
    private function mapFromObject(array $mappings, object $payload): array
    {
        $payloadReflector = $this->getReflectionClass($payload);

        $privateProperties = [];

        foreach ($mappings as $propertyName => $propertyNameAndType) {
            $property = $payloadReflector->getProperty($propertyName);
            $property->setAccessible(true);
            $privateProperties[$propertyName] = $this->maybeTransformValueBeforeSetting($propertyNameAndType, $property->getValue($payload));
        }

        return $privateProperties;
    }

    /**
     * @param ApiPropertyNameAndType[] $mappings
     * @param array $payload
     * @return array
     * @throws EntityMakerException
     */
    private function mapFromArray(array $mappings, array $payload): array
    {
        $privateProperties = [];
        foreach ($mappings as $propertyName => $propertyNameAndType) {
            $arrayKeyName = $propertyNameAndType->name;
            if (str_contains($arrayKeyName, '.')) {
                $parts = explode('.', $arrayKeyName);
                if (count($parts) > 2) {
                    throw new EntityMakerException('An Api property can only go one extra property deep when using array syntax');
                }
                $privateProperties[$propertyName] = $this->maybeTransformValueBeforeSetting($propertyNameAndType, $payload[$parts[0]][$parts[1]]);
            } else {
                $privateProperties[$propertyName] = $this->maybeTransformValueBeforeSetting($propertyNameAndType, $payload[$arrayKeyName]);
            }
        }

        return $privateProperties;
    }

    /**
     * @param object|string $objectOrClass
     * @return ApiPropertyNameAndType[]
     * @throws EntityMakerException
     * @throws ReflectionException
     */
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

            $nameAndType = new ApiPropertyNameAndType($arrayKeyName, $apiProperty->getApiPropertyType());
            if ((ApiProperty::PROPERTY_TYPE_ENTITY === $apiProperty->getApiPropertyType()) && ($property->getType() instanceof ReflectionNamedType)) {
                $nameAndType->setEntityClass($property->getType()->getName());
            }

            $privateProperties[$property->getName()] = $nameAndType;
        }

        return $privateProperties;
    }
}