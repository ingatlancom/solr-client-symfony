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

namespace iCom\SolrClient\Query;

use iCom\SolrClient\JsonHelper;
use iCom\SolrClient\JsonQuery;

/**
 * @psalm-immutable
 */
final class SelectQuery implements JsonQuery
{
    use JsonHelper;

    /**
     * @var array<string, string>
     */
    private $types = [
        // to keep key order consistent
        'query' => 'string',
        'filter' => 'array',
        'fields' => 'array',
        'facet' => 'array',
        'sort' => 'string',
        'offset' => 'int',
        'limit' => 'int',
        'params' => 'array',
    ];

    /**
     * @psalm-var array{
     *      query?: string,
     *      filter?: array,
     *      fields?: array,
     *      facet?: array,
     *      sort?: string,
     *      offset?: int,
     *      limit?: int,
     *      params?: array,
     * }
     */
    private $body;

    /**
     * @psalm-param array<string, string|array|int> $body
     */
    public function __construct(array $body = [])
    {
        if ([] !== $invalid = array_diff_key($body, $this->types)) {
            throw new \InvalidArgumentException(sprintf('Invalid keys "%s" found. Valid keys are "%s".', implode(', ', array_keys($invalid)), implode(', ', array_keys($this->types))));
        }

        foreach ($body as $key => $value) {
            if ($this->types[$key] !== $type = \get_debug_type($value)) {
                throw new \InvalidArgumentException(sprintf('Type of field "%s" should be "%s", "%s" given.', $key, $this->types[$key], $type));
            }
        }

        /** @psalm-suppress InvalidPropertyAssignmentValue */
        $this->body = array_replace(array_fill_keys(array_keys($this->types), null), $body);
    }

    /**
     * @psalm-param array<string, string|array|int> $body
     */
    public static function create(array $body = []): self
    {
        return new self($body);
    }

    public function query(string $query): self
    {
        $q = clone $this;
        $q->body['query'] = $query;

        return $q;
    }

    public function filter(array $filters): self
    {
        $q = clone $this;
        $q->body['filter'] = array_map(static function ($filter) use ($q): string { return $q->parseFilter($filter); }, $filters);

        return $q;
    }

    /**
     * @param mixed $filter
     */
    public function withFilter($filter): self
    {
        $q = clone $this;
        $q->body['filter'][] = $this->parseFilter($filter);

        return $q;
    }

    public function fields(array $fields): self
    {
        $q = clone $this;
        $q->body['fields'] = $fields;

        return $q;
    }

    public function sort(string $sort): self
    {
        $q = clone $this;
        $q->body['sort'] = $sort;

        return $q;
    }

    public function facet(array $facet): self
    {
        $q = clone $this;
        $q->body['facet'] = $facet;

        return $q;
    }

    public function params(array $params): self
    {
        $q = clone $this;
        $q->body['params'] = $params;

        return $q;
    }

    public function offset(int $offset): self
    {
        $q = clone $this;
        $q->body['offset'] = $offset;

        return $q;
    }

    public function limit(int $limit): self
    {
        $q = clone $this;
        $q->body['limit'] = $limit;

        return $q;
    }

    public function toJson(): string
    {
        return self::jsonEncode(new \ArrayObject(array_filter($this->body)), \JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param mixed $filter
     */
    private function parseFilter($filter): string
    {
        if ($filter instanceof QueryHelper) {
            return $filter->toString();
        }

        if (!\is_string($filter)) {
            throw new \InvalidArgumentException(sprintf('SelectQuery filter can accept only string or "%s", but "%s" given.', QueryHelper::class, \get_debug_type($filter)));
        }

        return $filter;
    }
}
