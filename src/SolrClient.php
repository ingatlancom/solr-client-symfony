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
