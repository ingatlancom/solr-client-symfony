<?php

declare(strict_types=1);

namespace iCom\SolrClient\Query;

final class Collapse
{
    private $params = [
        'field' => null,
        'min' => null,
        'max' => null,
        'sort' => null,
        'nullPolicy' => null,
        'hint' => null,
        'size' => null,
    ];

    public function __construct(string $field)
    {
        $this->params['field'] = $field;
    }

    public function __toString(): string
    {
        return sprintf('{!collapse %s}', urldecode(http_build_query($this->params, '', ' ')));
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

    private function assertSingleSort(): void
    {
        if (null !== $this->params['min'] || null !== $this->params['max'] || null !== $this->params['sort']) {
            throw new \RuntimeException('Multiple sort is not allowed.');
        }
    }
}
