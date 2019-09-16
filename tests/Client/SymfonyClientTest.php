<?php declare(strict_types=1);

namespace iCom\SolrClient\Tests\Client;

use iCom\SolrClient\Client\SymfonyClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SymfonyClientTest extends TestCase
{
    /** @test */
    function it_requires_a_base_url(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Config is missing the following keys: "base_url".');

        new SymfonyClient();
    }

    /** @test */
    function it_makes_http_request_to_the_select_api(): void
    {
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
    function it_converts_the_response_to_array(): void
    {
        $httpClient = new MockHttpClient(new MockResponse('{"message": "called!"}'));

        $client = new SymfonyClient(['base_url' => 'http://127.0.0.1'], $httpClient);
        $response = $client->select('{"query": "*:*"}');

        $this->assertEquals(['message' => 'called!'], $response);
    }
}
