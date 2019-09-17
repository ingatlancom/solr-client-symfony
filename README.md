# Solr Client

Egyszerű [Solr][solr] kliens.

## Telepítés

    $ composer require icom/solr-client

## Használat

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

[solr]: https://lucene.apache.org/solr/
