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

/**
 * @psalm-immutable
 */
interface JsonQuery
{
    /**
     * Returns the JSON string representation of the query.
     *
     * Note that: Solr has his own JSON syntax which allows to have multiple keys.
     *            Therefore you can expect this to be an invalid JSON string.
     *
     * @see https://lucene.apache.org/solr/guide/8_3/uploading-data-with-index-handlers.html#sending-json-update-commands
     */
    public function toJson(): string;
}
