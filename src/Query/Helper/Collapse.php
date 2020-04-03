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
     * @psalm-var array{
     *      cache: null|'true'|'false',
     *      field: ?string,
     *      hint: ?string,
     *      max: ?string,
     *      min: ?string,
     *      nullPolicy: ?string,
     *      size: ?int,
     *      sort: ?string,
     * }
     */
    private $params = [
        'field' => null,
        'min' => null,
        'max' => null,
        'sort' => null,
        'nullPolicy' => null,
        'hint' => null,
        'size' => null,
        'cache' => null,
    ];

    public function __construct(string $field)
    {
        $this->params['field'] = $field;
    }

    public static function create(string $field): self
    {
        return new self($field);
    }

    public function min(string $expression): self
    {
        $this->assertSingleSort();

        $collapse = clone $this;
        $collapse->params['min'] = $expression;

        return $collapse;
    }

    public function max(string $expression): self
    {
        $this->assertSingleSort();

        $collapse = clone $this;
        $collapse->params['max'] = $expression;

        return $collapse;
    }

    public function sort(array $sort): self
    {
        $this->assertSingleSort();

        $collapse = clone $this;
        $collapse->params['sort'] = sprintf("'%s'", implode(',', $sort));

        return $collapse;
    }

    public function nullPolicy(string $nullPolicy): self
    {
        if (!\in_array($nullPolicy, $available = ['ignore', 'expand', 'collapse'], true)) {
            throw new \InvalidArgumentException(sprintf('Available null policies: %s!', implode(',', $available)));
        }

        $collapse = clone $this;
        $collapse->params['nullPolicy'] = $nullPolicy;

        return $collapse;
    }

    public function hint(): self
    {
        $collapse = clone $this;
        $collapse->params['hint'] = 'top_fc';

        return $collapse;
    }

    public function size(int $size): self
    {
        $collapse = clone $this;
        $collapse->params['size'] = $size;

        return $collapse;
    }

    public function cache(bool $cache): self
    {
        $collapse = clone $this;
        $collapse->params['cache'] = $cache ? 'true' : 'false';

        return $collapse;
    }

    public function toString(): string
    {
        return sprintf('{!collapse %s}', urldecode(http_build_query($this->params, '', ' ')));
    }

    private function assertSingleSort(): void
    {
        if (null !== $this->params['min'] || null !== $this->params['max'] || null !== $this->params['sort']) {
            throw new \RuntimeException('Multiple sort is not allowed.');
        }
    }
}
