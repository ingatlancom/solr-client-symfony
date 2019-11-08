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

final class SelectQuery implements \JsonSerializable
{
    use JsonHelper;

    // to keep key order consistent
    private $types = [
        'query' => 'string',
        'filter' => 'array',
        'fields' => 'array',
        'facet' => 'array',
        'sort' => 'string',
        'offset' => 'integer',
        'limit' => 'integer',
        'params' => 'array',
    ];
    private $body;

    public function __construct(array $body = [])
    {
        if ($invalid = array_diff_key($body, $this->types)) {
            throw new \InvalidArgumentException(sprintf('Invalid keys "%s" found. Valid keys are "%s".', implode(', ', array_keys($invalid)), implode(', ', array_keys($this->types))));
        }

        foreach ($body as $key => $value) {
            if ($this->types[$key] !== $type = \gettype($value)) {
                throw new \InvalidArgumentException(sprintf('Type of field "%s" should be "%s", "%s" given.', $key, $this->types[$key], $type));
            }
        }

        $this->body = array_replace(array_fill_keys(array_keys($this->types), null), $body);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }

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

    public function filter(array $filter): self
    {
        $q = clone $this;
        $q->body['filter'] = $filter;

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
        return self::jsonEncode($this->toArray());
    }

    public function toArray(): array
    {
        return array_filter($this->body);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
