<?php declare(strict_types=1);

namespace iCom\SolrClient;

use iCom\SolrClient\Client\SymfonyClient;

class SolrClient
{
    public static function create(array $options): Client
    {
        if (class_exists(SymfonyClient::class)) {
            return new SymfonyClient($options);
        }

        throw new \LogicException('You need to install a client implementation.');
    }
}
