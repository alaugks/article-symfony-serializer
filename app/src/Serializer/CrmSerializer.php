<?php

namespace App\Serializer;

use App\Normalizer\MappingTableNormalizer;
use App\Reflection\PropertyTypeExtractor;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class CrmSerializer
{
    private SerializerInterface|NormalizerInterface|DenormalizerInterface $serializer;

    public function __construct(
        ParameterBagInterface $parameterBag
        // #[TaggedIterator('serializer.normalizer')] $taggedNormalizers
        // #[TaggedIterator('serializer.encoder')] $taggedEncoders
    )
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(null));

        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);

        //    $propertyTypeExtractor = new ReflectionExtractor(
        //        [], // overwrite ['add', 'remove', 'set']
        //        []  // overwrite ['get', 'is', 'has', 'can']
        //    );

        $propertyTypeExtractor = new PropertyInfoExtractor(
            typeExtractors: [new PropertyTypeExtractor()]
        );

        $defaultContext = [];
        if ($parameterBag->has('serializer_default_context')) {
            $defaultContext = $parameterBag->get('serializer_default_context');
        }

        $propertyNormalizer = new PropertyNormalizer(
            classMetadataFactory: $classMetadataFactory,
            nameConverter: $metadataAwareNameConverter,
            propertyTypeExtractor: $propertyTypeExtractor,
            defaultContext: $defaultContext
        );

        $this->serializer = new Serializer(
            [
                new DateTimeNormalizer(), // For properties with \DateTimeInterface
                new MappingTableNormalizer(),
                $propertyNormalizer
            ],
            [
                new JsonEncoder()
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
