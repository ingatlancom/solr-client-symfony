# Solr Client

[![pipeline status][0]][1] [![coverage report][2]][3]

Simple [Solr][4] client using the JSON Request API available since Solr 5.1,
so this client only usable after that version.

## Install

    $ composer require icom/solr-client

## Usage

    <?php declare(strict_types=1);

    use iCom\SolrClient\SolrClient;

    require_once dirname(__DIR__).'/vendor/autoload.php';

    $client = SolrClient::create(['base_url' => 'http://127.0.0.1:8983/solr/core']);

    try {
        $result = $client->select('{"query": "*:*"}');

        // do something with the result
    } catch (\iCom\SolrClient\Exception\Exception $e) {
        // handle errors
    }


## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate. For details please consult with our [contribution][5] guide.


## License

[MIT][6]

[0]: https://github.com/ingatlancom/solr-client-symfony/workflows/CI/badge.svg
[1]: https://github.com/ingatlancom/solr-client-symfony/actions?workflow=CI
[2]: https://codecov.io/gh/ingatlancom/solr-client-symfony/branch/master/graph/badge.svg
[3]: https://codecov.io/gh/ingatlancom/solr-client-symfony
[4]: https://lucene.apache.org/solr/
[5]: CONTRIBUTING.md
[6]: https://choosealicense.com/licenses/mit/
