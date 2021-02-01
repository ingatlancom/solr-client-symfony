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

namespace iCom\SolrClient\Client;

use iCom\SolrClient\Client;
use iCom\SolrClient\Exception\CommunicationError;
use iCom\SolrClient\JsonQuery;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SymfonyClient implements Client
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function select($jsonBody): array
    {
        return $this->send('GET', 'select', $jsonBody);
    }

    public function update($jsonBody): array
    {
        return $this->send('POST', 'update', $jsonBody);
    }

    /**
     * @param mixed $body
     */
    private function send(string $method, string $url, $body = null): array
    {
        $options = ['headers' => [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]];

        if (null !== $body) {
            $options['body'] = $this->getBody($body);
        }

        try {
            $response = $this->httpClient->request($method, $url, $options);

            return $response->toArray();
        } catch (ExceptionInterface $e) {
            throw CommunicationError::fromUpstreamException($e);
        }
    }

    /**
     * @param mixed $body
     */
    private function getBody($body): string
    {
        if ($body instanceof JsonQuery) {
            return $body->toJson();
        }

        if (!\is_string($body) || '{' !== $body[0]) {
            throw new \InvalidArgumentException(sprintf('Client can accept only string or "%s", but "%s" given.', JsonQuery::class, \get_debug_type($body)));
        }

        return $body;
    }
}
