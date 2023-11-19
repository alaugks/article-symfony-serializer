# Create a custom Symfony Normalizer for mapping values

https://dev.to/elevado/create-a-custom-symfony-normalizer-for-mapping-values-4nc2

## Article Series: Mapping FieldValueIDs for the payload of the Emarsys API
* Create a custom Symfony Normalizer for mapping values ([Article](https://dev.to/elevado/create-a-custom-symfony-normalizer-for-mapping-values-4nc2) / [Repository](https://github.com/elevado/serializer-article))
    * Implementation with Symfony PropertyNormalizer ([Branch](https://github.com/elevado/serializer-article/tree/symfony-5.4-property-normalizer))
* Create a custom JMS Serializer handler for mapping value ([Article](https://dev.to/elevado/create-a-custom-jms-serializer-handler-for-mapping-values-670) / [Repository](https://github.com/elevado/serializer-article/tree/jms-serializer))
* Create a custom Jackson JsonSerializer und JsonDeserializer for mapping values ([Article](https://dev.to/elevado/create-a-custom-jackson-jsonserializer-und-jsondeserializer-for-mapping-values-48h7) / [Repository](https://github.com/elevado/jackson-article))
  gp

## Docker image

### Start docker image
```bash
docker compose -f docker-compose.yml up --build -d
```

### Run composer install
```bash
docker exec attribute_article composer install
```

### Run tests
```bash
docker exec attribute_article bin/phpunit
```

### Open bash

```bash
docker exec -it attribute_article bash
```

## Frontend

Open frontend: http://localhost:8080/
