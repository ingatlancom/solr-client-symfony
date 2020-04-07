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

use iCom\SolrClient\Query\QueryHelper;

/**
 * Uses the Terms Query Parser.
 *
 * May be more efficient in some cases than using
 * the Standard Query Parser to generate a boolean
 * query since the default implementation method avoids scoring.
 *
 * @see https://lucene.apache.org/solr/guide/8_4/other-parsers.html#term-query-parser
 *
 * @psalm-immutable
 */
final class Terms implements QueryHelper
{
    use Caching;

    /**
     * @var string
     */
    private $f;

    /**
     * @var ?string
     */
    private $method;

    /**
     * @var ?string
     */
    private $separator;

    /**
     * @psalm-var non-empty-array<array-key, mixed>
     */
    private $values;

    /**
     * @param array<mixed> $values
     */
    public function __construct(string $field, array $values)
    {
        if ('' === $field) {
            throw new \InvalidArgumentException('The "field" parameter can not be empty.');
        }

        if ([] === $values) {
            throw new \InvalidArgumentException('The "values" parameter can not be empty.');
        }

        $this->f = $field;
        $this->values = $values;
    }

    /**
     * @param array<mixed> $values
     */
    public static function create(string $field, array $values): self
    {
        return new self($field, $values);
    }

    public function separator(string $separator): self
    {
        $terms = clone $this;
        $terms->separator = $separator;

        return $terms;
    }

    public function method(string $method): self
    {
        if (!\in_array($method, $available = ['termsFilter', 'booleanQuery', 'automaton', 'docValuesTermsFilter'], true)) {
            throw new \InvalidArgumentException(sprintf('Available methods are: "%s"', implode('", "', $available)));
        }

        $terms = clone $this;
        $terms->method = $method;

        return $terms;
    }

    public function toString(): string
    {
        $params = [
            'f' => $this->f,
            'method' => $this->method,
            'separator' => null,
        ] + $this->getCacheFields();

        if (null !== $this->separator) {
            $params['separator'] = sprintf('"%s"', addslashes($this->separator));
        }

        return sprintf('{!terms %s}%s', urldecode(http_build_query($params, '', ' ')), implode($this->separator ?? ',', $this->values));
    }
}
