<?php declare(strict_types=1);

namespace iCom\SolrClient;

use iCom\SolrClient\Exception\CommunicationError;

interface Client
{
    /**
     * Runs a select query using the JSON Request API.
     *
     * Note that: Solr uses the Noggit JSON parser in its request API.
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
     * @param string $jsonBody JSON Query. @see https://lucene.apache.org/solr/guide/8_1/json-query-dsl.html
     *
     * @return array
     *
     * @throws CommunicationError When an error happens while calling the Solr server API.
     */
    public function select(string $jsonBody): array;
}
