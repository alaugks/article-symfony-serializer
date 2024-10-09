# Create a custom Symfony Normalizer for mapping values 

## A part of article Series: Mapping FieldValueIDs for the payload of the Emarsys API

https://dev.to/elevado/create-a-custom-symfony-normalizer-for-mapping-values-4nc2

* Example with Serializer default service configuration ([Branch](https://github.com/alaugks/article-symfony-serializer))
* Example with PropertyNormalizer and custom PropertyTypeExtractor ([Branch](https://github.com/alaugks/article-symfony-serializer/tree/property-normalizer))

## Docker image

### Start docker image
```bash
docker compose -f docker-compose.yml up --build -d
```

### Run composer install
```bash
docker exec article_symfony_serializer composer install
```

### Run tests
```bash
docker exec article_symfony_serializer bin/phpunit
```

### Open bash

```bash
docker exec -it article_symfony_serializer bash
```

## Frontend

Open frontend: http://localhost:8080/
