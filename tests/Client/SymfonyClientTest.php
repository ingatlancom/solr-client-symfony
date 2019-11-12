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
use iCom\SolrClient\Query\Collapse;
use iCom\SolrClient\Query\SelectQuery;
use iCom\SolrClient\Query\UpdateQuery;
use iCom\SolrClient\SolrClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @covers \iCom\SolrClient\Client\SymfonyClient
 */
final class SymfonyClientTest extends TestCase
{
    /** @test */
    public function it_requires_a_base_url(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Config is missing the following keys: "base_url".');

        new SymfonyClient();
    }

    /** @test */
    public function it_makes_http_request_to_the_select_api(): void
    {
        /** @var MockObject&HttpClientInterface $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'http://127.0.0.1/select', $this->callback(static function (array $options): bool {
                return isset($options['body']) && '{"query": "*:*"}' === $options['body'];
            }))
        ;

        $client = new SymfonyClient(['base_url' => 'http://127.0.0.1'], $httpClient);
        $client->select('{"query": "*:*"}');
    }

    /** @test */
    public function it_converts_the_response_to_array(): void
    {
        $httpClient = new MockHttpClient(new MockResponse('{"message": "called!"}'));

        $client = new SymfonyClient(['base_url' => 'http://127.0.0.1'], $httpClient);
        $response = $client->select('{"query": "*:*"}');

        $this->assertEquals(['message' => 'called!'], $response);
    }

    /**
     * @test
     * @group integration
     * @dataProvider queryProvider
     */
    public function it_can_query_solr(\Closure $query, array $expected)
    {
        $response = SolrClient::create(['base_url' => getenv('SOLR_URL')])->select($query()->toJson());

        $this->assertSame($expected, $response['response']['docs']);
    }

    /**
     * @test
     * @group integration
     */
    public function it_can_update_solr()
    {
        $client = SolrClient::create(['base_url' => getenv('SOLR_URL')]);

        $deleteCommand = UpdateQuery::create()->deleteByIds(['33'])->commit();

        $client->update($deleteCommand->toSolrJson());

        $response = $client->select(SelectQuery::create()->query('id:33')->toJson());

        $this->assertEmpty($response['response']['docs']);

        $document = ['id' => 33, 'sample_bool' => false, 'sample_int' => 44];
        $client->update(UpdateQuery::create()->add($document)->commit()->toSolrJson());

        $response = $client->select(SelectQuery::create()->query('id:33')->fields(['id'])->toJson());

        $this->assertSame([['id' => '33']], $response['response']['docs']);

        $client->update($deleteCommand->toSolrJson());

        $response = $client->select(SelectQuery::create()->query('id:33')->toJson());

        $this->assertEmpty($response['response']['docs']);
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
                    ->filter([(string) Collapse::create('sample_int')])
                    ->fields(['id'])
                ;
            },
            'expected' => [['id' => '1'], ['id' => '3']],
        ];
    }
}
