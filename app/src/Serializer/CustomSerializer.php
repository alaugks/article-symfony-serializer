<?php

namespace App\Serializer;

use App\Normalizer\MappingTableNormalizer;
use App\Reflection\CustomPropertyTypeExtractor;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class CustomSerializer
{
    private SerializerInterface $serializer;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader(null));

        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);

        $defaultContext = [];
        if ($parameterBag->has('serializer_default_context')) {
            $defaultContext = $parameterBag->get('serializer_default_context');
        }

        $propertyNormalizer = new PropertyNormalizer(
            classMetadataFactory: $classMetadataFactory, // Attribute
            nameConverter: $metadataAwareNameConverter, // PropertyName and SerializerName
            propertyTypeExtractor: new CustomPropertyTypeExtractor(),
            defaultContext: $defaultContext
        );

        $this->serializer = new Serializer(
            [
                new DateTimeNormalizer(), // For properties with \DateTimeInterface
                new MappingTableNormalizer(),
                $propertyNormalizer,
            ]
        );
    }

    /**
     * @return Serializer
     */
    public function getSerializer(): Serializer
    {
        return $this->serializer;
    }
}
