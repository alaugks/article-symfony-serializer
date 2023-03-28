<?php

namespace App\Service;

use App\Dto\ContactDto;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class CrmSerializerService
{
    private Serializer $serializer; // Only Symfony Serializer

    public function __construct(SerializerInterface $serializer) {
        $this->serializer = $serializer;
    }

    public function normalize(ContactDto $contactDto): array
    {
        return $this->serializer->normalize($contactDto);
    }

    public function denormalize(array $data): ContactDto
    {
        return $this->serializer->denormalize($data, ContactDto::class);
    }

    public function serialize(ContactDto $contactDto): string
    {
        return $this->serializer->serialize($contactDto, 'json');
    }

    public function deserialize(string $json): ContactDto
    {
        return $this->serializer->deserialize($json, ContactDto::class, 'json');
    }
}
