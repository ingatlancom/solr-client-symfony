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

namespace iCom\SolrClient;

use iCom\SolrClient\Client\SymfonyClient;
use Symfony\Component\HttpClient\HttpClient;

final class SolrClient
{
    public static function create(array $options): Client
    {
        if (class_exists(HttpClient::class)) {
            return new SymfonyClient(HttpClient::create($options));
        }

        throw new \LogicException('You need to install an HTTP client implementation. Try running "composer require symfony/http-client".');
    }
}
