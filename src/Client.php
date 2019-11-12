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

use iCom\SolrClient\Exception\CommunicationError;

interface Client
{
    /**
     * Runs a select query using the JSON Request API.
     *
     * Note that: Solr uses the Noggit JSON parser in its request API.
     *
     * @see https://github.com/yonik/noggit
     *
     * Noggit is capable of more relaxed JSON parsing, and allows a number
     * of deviations from the JSON standard:
     *  - bare words can be left unquoted
     *  - single line comments can be inserted using either // or #
     *  - Multi-line ("C style") comments can be inserted using \/* and *\/
     *  - strings can be single-quoted
     *  - special characters can be backslash-escaped
     *  - trailing (extra) commas are silently ignored (e.g., [9,4,3,])
     *  - nbsp (non-break space, \u00a0) is treated as whitespace.
     *
     * @param string|JsonQuery $jsonQuery JSON Query. @see https://lucene.apache.org/solr/guide/8_3/json-query-dsl.html
     *
     * @return array
     *
     * @throws CommunicationError When an error happens while calling the Solr server API.
     */
    public function select($jsonQuery): array;

    /**
     * Runs a JSON formatted update query.
     *
     * Note that: JSON formatted updates can take 3 basic forms. You can
     *  - add a single JSON document
     *  - add multiple JSON document
     *  - or send JSON update commands
     *
     * @param string|JsonQuery $jsonQuery @see https://lucene.apache.org/solr/guide/8_3/uploading-data-with-index-handlers.html#json-formatted-index-updates
     *
     * @return array
     *
     * @throws CommunicationError When an error happens while calling the Solr server API.
     */
    public function update($jsonQuery): array;
}
