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
use iCom\SolrClient\Query\JsonQuery;
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
        $url = sprintf('http://%s:%s/solr/sample', getenv('SOLR_HOST') ?: 'solr', getenv('SOLR_PORT') ?: 8983);

        $response = SolrClient::create(['base_url' => $url])->select($query()->toJson());

        $this->assertSame($expected, $response['response']['docs']);
    }

    public function queryProvider(): iterable
    {
        yield 'it selects single document id' => [
            'query' => static function (): JsonQuery {
                return JsonQuery::create()
                                ->query('id:1')
                                ->fields(['id']);
            },
            'expected' => [['id' => '1']],
        ];

        yield 'it selects multiple document id' => [
            'query' => static function (): JsonQuery {
                return JsonQuery::create()
                                ->query('sample_bool:true')
                                ->fields(['id']);
            },
            'expected' => [['id' => '1'], ['id' => '3']],
        ];

        yield 'it selects all document ids' => [
            'query' => static function (): JsonQuery {
                return JsonQuery::create()
                                ->query('*:*')
                                ->fields(['id']);
            },
            'expected' => [['id' => '1'], ['id' => '2'], ['id' => '3']],
        ];

        yield 'it sorts documents' => [
            'query' => static function (): JsonQuery {
                return JsonQuery::create()
                                ->query('*:*')
                                ->sort('id desc')
                                ->fields(['id']);
            },
            'expected' => [['id' => '3'], ['id' => '2'], ['id' => '1']],
        ];

        yield 'it can limit documents' => [
            'query' => static function (): JsonQuery {
                return JsonQuery::create()
                                ->query('*:*')
                                ->limit(2)
                                ->fields(['id']);
            },
            'expected' => [['id' => '1'], ['id' => '2']],
        ];

        yield 'it can collapse documents' => [
            'query' => static function (): JsonQuery {
                return JsonQuery::create()
                                ->query('*:*')
                                ->filter([(string) Collapse::create('sample_int')])
                                ->fields(['id']);
            },
            'expected' => [['id' => '1'], ['id' => '3']],
        ];
    }
}
