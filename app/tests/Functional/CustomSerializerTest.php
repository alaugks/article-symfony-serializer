<?php

namespace App\Tests\Functional;

use App\Dto\ContactDto;
use App\Serializer\CustomSerializer;
use App\Service\CustomSerializerService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\Serializer;

/**
 * @SuppressWarnings("php:S1192")
 */
class CustomSerializerTest extends KernelTestCase
{
    private Serializer $serializer;

    public array $contactDto = [
        "1" => "Jane",
        "2" => "Doe",
        "3" => "jane.doe@example.com",
        "4" => "1989-11-09",
        "46" =>"2",
        "100674" => "1"
    ];

    public function setUp(): void
    {
        self::bootKernel();

        $this->serializer = (new CustomSerializer(self::getContainer()->getParameterBag()))->getSerializer();
    }

    public function testNormalize()
    {
        $contactDto = new ContactDto();
        $contactDto->setSalutation('FEMALE');
        $contactDto->setFirstname('Jane');
        $contactDto->setLastname('Doe');
        $contactDto->setBirthdate(new DateTime('1989-11-09'));
        $contactDto->setEmail('jane.doe@example.com');
        $contactDto->setMarketingInformation(true);

        $array = $this->serializer->normalize($contactDto);

        $this->assertEquals(
            $this->contactDto,
            $array
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

    public function testDenormalize()
    {
        /**
         * @var $contactDto ContactDto
         */
        $contactDto = $this->serializer->denormalize($this->contactDto,  ContactDto::class);

        $this->assertSame('FEMALE', $contactDto->getSalutation());
        $this->assertSame('Jane', $contactDto->getFirstname());
        $this->assertSame('Doe', $contactDto->getLastname());
        $this->assertSame('1989-11-09', $contactDto->getBirthdate()->format('1989-11-09'));
        $this->assertSame('jane.doe@example.com', $contactDto->getEmail());
        $this->assertTrue($contactDto->isMarketingInformation());
    }
}
