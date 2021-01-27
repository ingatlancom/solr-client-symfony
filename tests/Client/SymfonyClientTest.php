<?php

declare(strict_types=1);

/*
 * This file is part of Solr Client Symfony package.
 *
 * (c) ingatlan.com Zrt. <fejlesztes@ingatlan.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace iCom\SolrClient\Tests\Client;

use iCom\SolrClient\Client\SymfonyClient;
use iCom\SolrClient\Query\Helper\Collapse;
use iCom\SolrClient\Query\Helper\Terms;
use iCom\SolrClient\Query\SelectQuery;
use iCom\SolrClient\Query\UpdateQuery;
use iCom\SolrClient\SolrClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @covers \iCom\SolrClient\Client\SymfonyClient
 *
 * @uses \iCom\SolrClient\JsonHelper
 * @uses \iCom\SolrClient\Query\SelectQuery
 * @uses \iCom\SolrClient\Query\Helper\Collapse
 * @uses \iCom\SolrClient\Query\Helper\Terms
 */
final class SymfonyClientTest extends TestCase
{
    /** @test */
    public function it_makes_http_request_to_the_select_api(): void
    {
        $callback = static function ($method, $url, $options): MockResponse {
            $headers = $options['request_headers'] ?? $options['headers'] ?? [];
            self::assertSame('GET', $method);
            self::assertSame('http://127.0.0.1/select', $url);
            self::assertContains('accept: application/json', array_map('strtolower', $headers));
            self::assertContains('content-type: application/json', array_map('strtolower', $headers));
            self::assertArrayHasKey('body', $options);
            self::assertSame('{"query": "*:*"}', $options['body']);

            return new MockResponse('{"message": "OK"}');
        };
        $httpClient = new MockHttpClient($callback, 'http://127.0.0.1');

        $client = new SymfonyClient($httpClient);
        $client->select('{"query": "*:*"}');
    }

    /** @test */
    public function it_makes_http_request_to_the_update_api(): void
    {
        $callback = static function ($method, $url, $options): MockResponse {
            $headers = $options['request_headers'] ?? $options['headers'] ?? [];
            self::assertSame('POST', $method);
            self::assertSame('http://127.0.0.1/update', $url);
            self::assertContains('accept: application/json', array_map('strtolower', $headers));
            self::assertArrayHasKey('body', $options);
            self::assertSame('{"add":{"doc":{"id":1}}}', $options['body']);

            return new MockResponse('{"message": "OK"}');
        };
        $httpClient = new MockHttpClient($callback, 'http://127.0.0.1');

        $client = new SymfonyClient($httpClient);
        $client->update('{"add":{"doc":{"id":1}}}');
    }

    /** @test */
    public function it_converts_the_response_to_array(): void
    {
        $httpClient = new MockHttpClient(new MockResponse('{"message": "called!"}'), 'http://127.0.0.1/');

        $client = new SymfonyClient($httpClient);
        $response = $client->select('{"query": "*:*"}');

        self::assertEquals(['message' => 'called!'], $response);
    }

    /** @test */
    public function it_accepts_object(): void
    {
        $httpClient = new MockHttpClient(new MockResponse('{"message": "called!"}'), 'http://127.0.0.1/');

        $client = new SymfonyClient($httpClient);
        $response = $client->select(SelectQuery::create()->query('*:*'));

        self::assertEquals(['message' => 'called!'], $response);
    }

    /** @test */
    public function it_throws_exception_for_wrong_body_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches(sprintf('#^Client can accept only string or ".+", but "%s" given.$#', preg_quote(Collapse::class, '#')));

        $client = new SymfonyClient(new MockHttpClient());
        $client->select(Collapse::create('id'));
    }

    /**
     * @test
     * @group integration
     * @dataProvider queryProvider
     */
    public function it_can_query_solr(\Closure $query, array $expected): void
    {
        $response = SolrClient::create(['base_uri' => getenv('SOLR_URL')])->select($query());

        self::assertSame($expected, $response['response']['docs']);
    }

    /**
     * @test
     * @group integration
     */
    public function it_can_update_solr(): void
    {
        $client = SolrClient::create(['base_uri' => getenv('SOLR_URL')]);

        $deleteQuery = UpdateQuery::create()->deleteByIds(['33'])->commit();

        $client->update($deleteQuery);

        $response = $client->select(SelectQuery::create()->query('id:33'));

        self::assertEmpty($response['response']['docs']);

        $document = ['id' => 33, 'sample_bool' => false, 'sample_int' => 44];
        $client->update(UpdateQuery::create()->add($document)->commit());

        $response = $client->select(SelectQuery::create()->query('id:33')->fields(['id']));

        self::assertSame([['id' => '33']], $response['response']['docs']);

        $client->update($deleteQuery);

        $response = $client->select(SelectQuery::create()->query('id:33'));

        self::assertEmpty($response['response']['docs']);
    }

    /**
     * @test
     * @group integration
     */
    public function it_can_rollback_uncommitted_changes(): void
    {
        $client = SolrClient::create(['base_uri' => getenv('SOLR_URL')]);

        $document = ['id' => 55, 'sample_bool' => false, 'sample_int' => 66];
        $client->update(UpdateQuery::create()->add($document));
        $client->update(UpdateQuery::create()->rollback());
        $client->update(UpdateQuery::create()->commit());

        $response = $client->select(SelectQuery::create()->query('id:55')->fields(['id']));

        self::assertEmpty($response['response']['docs']);
    }

    public function queryProvider(): iterable
    {
        yield 'it selects single document id' => [
            'query' => static function (): SelectQuery {
                return SelectQuery::create()
                    ->query('id:1')
                    ->fields(['id'])
                ;
            },
            'expected' => [['id' => '1']],
        ];

        yield 'it selects multiple document id' => [
            'query' => static function (): SelectQuery {
                return SelectQuery::create()
                    ->query('sample_bool:true')
                    ->fields(['id'])
                ;
            },
            'expected' => [['id' => '1'], ['id' => '3']],
        ];

        yield 'it selects all document ids' => [
            'query' => static function (): SelectQuery {
                return SelectQuery::create()
                    ->query('*:*')
                    ->fields(['id'])
                ;
            },
            'expected' => [['id' => '1'], ['id' => '2'], ['id' => '3']],
        ];

        yield 'it sorts documents' => [
            'query' => static function (): SelectQuery {
                return SelectQuery::create()
                    ->query('*:*')
                    ->sort('id desc')
                    ->fields(['id'])
                ;
            },
            'expected' => [['id' => '3'], ['id' => '2'], ['id' => '1']],
        ];

        yield 'it can limit documents' => [
            'query' => static function (): SelectQuery {
                return SelectQuery::create()
                    ->query('*:*')
                    ->limit(2)
                    ->fields(['id'])
                ;
            },
            'expected' => [['id' => '1'], ['id' => '2']],
        ];

        yield 'it can collapse documents' => [
            'query' => static function (): SelectQuery {
                return SelectQuery::create()
                    ->query('*:*')
                    ->filter([Collapse::create('sample_int')->cache(false)])
                    ->fields(['id'])
                ;
            },
            'expected' => [['id' => '1'], ['id' => '3']],
        ];

        yield 'it can search with terms query' => [
            'query' => static function (): SelectQuery {
                return SelectQuery::create()
                    ->query('*:*')
                    ->filter([Terms::create('id', [1, 3])->separator('"')->cache(false)])
                    ->fields(['id'])
                    ;
            },
            'expected' => [['id' => '1'], ['id' => '3']],
        ];
    }
}
