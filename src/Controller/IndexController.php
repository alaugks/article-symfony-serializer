<?php

namespace App\Controller;

use App\Dto\ContactDto;
use App\Normalizer\Value\BooleanValue;
use App\Normalizer\Value\StringValue;
use App\Service\CrmSerializerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    public function __construct(private CrmSerializerService $crmSerializerService) {}

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        $contactDto = new ContactDto();
        $contactDto->setSalutation(new StringValue('FEMALE'));
        $contactDto->setFirstname('Jane');
        $contactDto->setLastname('Doe');
        $contactDto->setEmail('jane.doe@example.com');
        $contactDto->setBirthdate(new \DateTime('1989-11-09'));
        $contactDto->setMarketingInformation(new BooleanValue(true));

        $array = $this->crmSerializerService->normalize($contactDto);
        /*
            Array
            (
                [1] => Jane
                [2] => Doe
                [3] => jane.doe@example.com
                [4] => 1989-11-09
                [46] => 2
                [100674] => 1
            )

        */

        $contactDtoFromArray = $this->crmSerializerService->denormalize($array);
        /*
            App\Dto\ContactDto Object
            (
                [firstname:App\Dto\ContactDto:private] => Jane
                [lastname:App\Dto\ContactDto:private] => Doe
                [email:App\Dto\ContactDto:private] => jane.doe@example.com
                [birthdate:App\Dto\ContactDto:private] => DateTimeImmutable Object
                    (
                        [date] => 1989-11-09 13:54:11.000000
                        [timezone_type] => 3
                        [timezone] => UTC
                    )

                [salutation:App\Dto\ContactDto:private] => App\Normalizer\Value\StringValue Object
                    (
                        [value:App\Normalizer\Value\StringValue:private] => FEMALE
                    )

                [marketingInformation:App\Dto\ContactDto:private] => App\Normalizer\Value\BooleanValue Object
                    (
                        [value:App\Normalizer\Value\BooleanValue:private] => 1
                    )

            )
        */

        $json = $this->crmSerializerService->serialize($contactDto);
        /*
            {
                "1": "Jane",
                "2": "Doe",
                "3": "jane.doe@example.com",
                "4": "1989-11-09",
                "46": "2",
                "100674": "1"
            }
        */

        $contactDtoFromJson = $this->crmSerializerService->deserialize($json);
        /*
            App\Dto\ContactDto Object
            (
                [firstname:App\Dto\ContactDto:private] => Jane
                [lastname:App\Dto\ContactDto:private] => Doe
                [email:App\Dto\ContactDto:private] => jane.doe@example.com
                [birthdate:App\Dto\ContactDto:private] => DateTimeImmutable Object
                    (
                        [date] => 1989-11-09 13:54:11.000000
                        [timezone_type] => 3
                        [timezone] => UTC
                    )

                [salutation:App\Dto\ContactDto:private] => App\Normalizer\Value\StringValue Object
                    (
                        [value:App\Normalizer\Value\StringValue:private] => FEMALE
                    )

                [marketingInformation:App\Dto\ContactDto:private] => App\Normalizer\Value\BooleanValue Object
                    (
                        [value:App\Normalizer\Value\BooleanValue:private] => 1
                    )

            )
        */

        return new Response(
            $this->renderView(
                'index/index.html.twig',
                [
                    'json' => json_encode(json_decode($json, true), JSON_PRETTY_PRINT),
                    'array' => print_r($array, true),
                    'object_from_json' => print_r($contactDtoFromJson, true),
                    'object_from_array' => print_r($contactDtoFromArray, true)
                ]
            ),
            Response::HTTP_OK
        );
    }
}
