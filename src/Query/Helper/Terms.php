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

namespace iCom\SolrClient\Query\Helper;

/**
 * Uses the Terms Query Parser.
 *
 * May be more efficient in some cases than using
 * the Standard Query Parser to generate a boolean
 * query since the default implementation method avoids scoring.
 *
 * @see https://lucene.apache.org/solr/guide/8_4/other-parsers.html#term-query-parser
 */
final class Terms
{
    private $params = [
        'f' => null,
        'method' => null,
        'separator' => null,
        'cache' => null,
    ];

    private $values;

    public function __construct(string $field, array $values)
    {
        if ('' === $field) {
            throw new \InvalidArgumentException('The "field" parameter can not be empty.');
        }

        if ([] === $values) {
            throw new \InvalidArgumentException('The "values" parameter can not be empty.');
        }

        $this->params['f'] = $field;
        $this->values = $values;
    }

    public function __toString(): string
    {
        $params = $this->params;

        if (null !== $params['separator']) {
            $params['separator'] = sprintf('"%s"', addslashes($separator = $params['separator']));
        }

        return sprintf('{!terms %s}%s', urldecode(http_build_query($params, '', ' ')), implode($separator ?? ',', $this->values));
    }

    public static function create(string $field, array $values): self
    {
        return new self($field, $values);
    }

    public function separator(string $separator): self
    {
        $terms = clone $this;
        $terms->params['separator'] = $separator;

        return $terms;
    }

    public function method(string $method): self
    {
        if (!\in_array($method, $available = ['termsFilter', 'booleanQuery', 'automaton', 'docValuesTermsFilter'], true)) {
            throw new \InvalidArgumentException(sprintf('Available methods are: "%s"', implode('", "', $available)));
        }

        $terms = clone $this;
        $terms->params['method'] = $method;

        return $terms;
    }

    public function cache(bool $cache): self
    {
        $terms = clone $this;
        $terms->params['cache'] = $cache ? 'true' : 'false';

        return $terms;
    }
}
