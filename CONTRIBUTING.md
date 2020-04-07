Contributing
------------

Solr Client is an open source, community-driven project.

#### Install dependencies

```bash
docker-compose run --rm -u $(id -u):$(id -g) php composer install
```

#### Run unit tests

```bash
docker-compose run --rm -u $(id -u):$(id -g) php vendor/bin/phpunit
```

#### Run integration tests

Start Solr
```bash
docker-compose up -d solr
```
Create solr core
```bash
docker-compose exec solr solr create -c sample -d /sample
```
Load fixture data
```bash
cp $PWD/tests/Fixtures/sample.json $PWD/.docker/solr/data

docker-compose exec solr post -c sample data/sample.json
```
Run tests
```bash
docker-compose run --rm -u $(id -u):$(id -g) php vendor/bin/phpunit --group=integration
```
