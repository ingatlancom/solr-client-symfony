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
 * @psalm-immutable
 */
final class Collapse implements QueryHelper
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var ?string
     */
    private $min;

    /**
     * @var ?string
     */
    private $max;

    /**
     * @var ?string
     */
    private $sort;

    /**
     * @var ?string
     */
    private $nullPolicy;

    /**
     * @var ?string
     */
    private $hint;

    /**
     * @var ?int
     */
    private $size;

    /**
     * @var ?bool
     */
    private $cache;

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public static function create(string $field): self
    {
        return new self($field);
    }

    public function min(string $expression): self
    {
        $this->assertSingleSort();

        $collapse = clone $this;
        $collapse->min = $expression;

        return $collapse;
    }

    public function max(string $expression): self
    {
        $this->assertSingleSort();

        $collapse = clone $this;
        $collapse->max = $expression;

        return $collapse;
    }

    /**
     * @param array<string> $sort
     */
    public function sort(array $sort): self
    {
        $this->assertSingleSort();

        $collapse = clone $this;
        $collapse->sort = sprintf("'%s'", implode(',', $sort));

        return $collapse;
    }

    public function nullPolicy(string $nullPolicy): self
    {
        if (!\in_array($nullPolicy, $available = ['ignore', 'expand', 'collapse'], true)) {
            throw new \InvalidArgumentException(sprintf('Available null policies: %s!', implode(',', $available)));
        }

        $collapse = clone $this;
        $collapse->nullPolicy = $nullPolicy;

        return $collapse;
    }

    public function hint(): self
    {
        $collapse = clone $this;
        $collapse->hint = 'top_fc';

        return $collapse;
    }

    public function size(int $size): self
    {
        $collapse = clone $this;
        $collapse->size = $size;

        return $collapse;
    }

    public function cache(bool $cache): self
    {
        $collapse = clone $this;
        $collapse->cache = $cache;

        return $collapse;
    }

    public function toString(): string
    {
        $params = [
            'field' => $this->field,
            'min' => $this->min,
            'max' => $this->max,
            'sort' => $this->sort,
            'nullPolicy' => $this->nullPolicy,
            'hint' => $this->hint,
            'size' => $this->size,
            'cache' => null !== $this->cache ? var_export($this->cache, true) : null,
        ];

        return sprintf('{!collapse %s}', urldecode(http_build_query($params, '', ' ')));
    }

    private function assertSingleSort(): void
    {
        if (null !== $this->min || null !== $this->max || null !== $this->sort) {
            throw new \RuntimeException('Multiple sort is not allowed.');
        }
    }
}
