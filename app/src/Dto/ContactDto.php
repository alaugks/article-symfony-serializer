<?php

namespace App\Dto;

use App\Normalizer\MappingTableNormalizer;
use App\Normalizer\Value\BooleanValue;
use App\Normalizer\Value\StringValue;
use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class ContactDto
{
    public const SALUTATIONS = [
        '1' => 'MALE',
        '2' => 'FEMALE',
        '6' => 'DIVERS'
    ];

    public const MARKETING_INFORMATION = [
        '1' => true,
        '2' => false
    ];

    #[SerializedName('1')]
    private ?string $firstname = null;

    #[SerializedName('2')]
    private ?string $lastname = null;

    #[SerializedName('3')]
    private ?string $email = null;

    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[SerializedName('4')]
    private ?DateTimeInterface $birthdate = null;

    //#[Context([MappingTableNormalizer::TABLE => self::SALUTATIONS])]
    #[Context([MappingTableNormalizer::TABLE => ['1' => 'MALE', '2' => 'FEMALE', '6' => 'DIVERS']])]
    #[SerializedName('46')]
    private ?StringValue $salutation = null;

    //#[Context([MappingTableNormalizer::TABLE => self::MARKETING_INFORMATION])]
    #[Context([MappingTableNormalizer::TABLE => ['1' => true, '2' => false]])]
    #[SerializedName('100674')]
    private ?BooleanValue $marketingInformation = null;

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getBirthdate(): ?DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?DateTimeInterface $birthdate): void
    {
        $this->birthdate = $birthdate;
    }

    public function getSalutation(): ?StringValue
    {
        return $this->salutation;
    }

    public function setSalutation(?StringValue $salutation): void
    {
        $this->salutation = $salutation;
    }

    public function isMarketingInformation(): ?BooleanValue
    {
        return $this->marketingInformation;
    }

    public function setMarketingInformation(?BooleanValue $marketingInformation): void
    {
        $this->marketingInformation = $marketingInformation;
    }
}
