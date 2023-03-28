<?php

namespace App\Tests;

use App\Dto\ContactDto;
use App\Service\CrmSerializerService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CrmSerializerServiceTest extends KernelTestCase
{
    private CrmSerializerService $serializer;

    public array $contactDto = [
        "1" => "Jane",
        "2" => "Doe",
        "3" =>"jane.doe@example.com",
        "4" => "1989-11-09",
        "46" =>"2",
        "100674" => "1"
    ];

    public function setUp(): void
    {
        self::bootKernel();
        $this->serializer = self::getContainer()->get(CrmSerializerService::class);
    }

    public function testNormalize()
    {
        $contactDto = new ContactDto();
        $contactDto->setSalutation('FEMALE');
        $contactDto->setFirstname('Jane');
        $contactDto->setLastname('Doe');
        $contactDto->setBirthdate(new DateTimeImmutable('1989-11-09'));
        $contactDto->setEmail('jane.doe@example.com');
        $contactDto->setMarketingInformation(true);

        $array = $this->serializer->normalize($contactDto);

        $this->assertEquals(
            $this->contactDto,
            $array
        );
    }

    public function testSerializer()
    {
        $contactDto = new ContactDto();
        $contactDto->setSalutation('FEMALE');
        $contactDto->setFirstname('Jane');
        $contactDto->setLastname('Doe');
        $contactDto->setBirthdate(new DateTimeImmutable('1989-11-09'));
        $contactDto->setEmail('jane.doe@example.com');
        $contactDto->setMarketingInformation(true);

        $jsonContent = $this->serializer->serialize($contactDto);

        $this->assertJsonStringEqualsJsonString(
            json_encode($this->contactDto),
            $jsonContent
        );
    }

    public function testNormalizeSkipNullValue()
    {
        $contactDto = new ContactDto();
        $contactDto->setSalutation('FEMALE');
        $contactDto->setFirstname('Jane');
        $contactDto->setEmail('jane.doe@example.com');
        $contactDto->setMarketingInformation(true);

        $this->assertEquals(
            (function () {
                $array = $this->contactDto;
                unset($array['2']);
                unset($array['4']);
                return $array;
            })(),
            $this->serializer->normalize($contactDto)
        );
    }

    public function testSerializeSkipNullValue()
    {
        $contactDto = new ContactDto();
        $contactDto->setSalutation('FEMALE');
        $contactDto->setFirstname('Jane');
        $contactDto->setEmail('jane.doe@example.com');
        $contactDto->setMarketingInformation(true);

        $this->assertJsonStringEqualsJsonString(
            json_encode((function () {
                $array = $this->contactDto;
                unset($array['2']);
                unset($array['4']);
                return $array;
            })()),
            $this->serializer->serialize($contactDto)
        );
    }

    public function testDenormalize()
    {
        /**
         * @var $contactDto ContactDto
         */
        $contactDto = $this->serializer->denormalize($this->contactDto);

        $this->assertSame('FEMALE', $contactDto->getSalutation());
        $this->assertSame('Jane', $contactDto->getFirstname());
        $this->assertSame('Doe', $contactDto->getLastname());
        $this->assertSame('1989-11-09', $contactDto->getBirthdate()->format('1989-11-09'));
        $this->assertSame('jane.doe@example.com', $contactDto->getEmail());
        $this->assertTrue($contactDto->isMarketingInformation());
    }

    public function testDeserialize()
    {
        /**
         * @var $contactDto ContactDto
         */
        $contactDto = $this->serializer->deserialize(json_encode($this->contactDto));

        $this->assertSame('FEMALE', $contactDto->getSalutation());
        $this->assertSame('Jane', $contactDto->getFirstname());
        $this->assertSame('Doe', $contactDto->getLastname());
        $this->assertSame('1989-11-09', $contactDto->getBirthdate()->format('1989-11-09'));
        $this->assertSame('jane.doe@example.com', $contactDto->getEmail());
        $this->assertTrue($contactDto->isMarketingInformation());
    }

    public function testDenormalizeOnNull()
    {
        /**
         * @var $contactDto ContactDto
         */
        $contactDto = $this->serializer->denormalize([]);

        $this->assertNull($contactDto->getSalutation());
        $this->assertNull($contactDto->getFirstname());
        $this->assertNull($contactDto->getLastname());
        $this->assertNull($contactDto->getBirthdate());
        $this->assertNull($contactDto->getEmail());
        $this->assertNull($contactDto->isMarketingInformation());
    }

    public function testDeserializeOnNull()
    {
        /**
         * @var $contactDto ContactDto
         */
        $contactDto = $this->serializer->deserialize('{}');

        $this->assertNull($contactDto->getSalutation());
        $this->assertNull($contactDto->getFirstname());
        $this->assertNull($contactDto->getLastname());
        $this->assertNull($contactDto->getBirthdate());
        $this->assertNull($contactDto->getEmail());
        $this->assertNull($contactDto->isMarketingInformation());
    }
}
