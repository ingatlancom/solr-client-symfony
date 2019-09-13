<?php declare(strict_types=1);

namespace iCom\SolrClient;

final class CollapseFilter
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

    public static function create(string $field): self
    {
        return new self($field);
    }

    public function withMin(string $expression): self
    {
        if (null !== $this->params['sort'] || null !== $this->params['max']) {
            throw new \RuntimeException('Multiple sort is not allowed.');
        }

        $collapse = clone $this;
        $collapse->params['min'] = $expression;

        return $collapse;
    }

    public function withMax(string $expression): self
    {
        if (null !== $this->params['min'] || null !== $this->params['sort']) {
            throw new \RuntimeException('Multiple sort is not allowed.');
        }

        $collapse = clone $this;
        $collapse->params['max'] = $expression;

        return $collapse;
    }

    public function withSort(array $sort): self
    {
        if (null !== $this->params['min'] || null !== $this->params['max']) {
            throw new \RuntimeException('Multiple sort is not allowed.');
        }

        $collapse = clone $this;
        $collapse->params['sort'] = sprintf("'%s'", implode(',', $sort));

        return $collapse;
    }

    public function withNullPolicy(string $nullPolicy): self
    {
        if (!\in_array($nullPolicy, $available = ['ignore', 'expand', 'collapse'])) {
            throw new \InvalidArgumentException(sprintf('Available null policies: %s!', implode(',', $available)));
        }

        $collapse = clone $this;
        $collapse->params['nullPolicy'] = $nullPolicy;

        return $collapse;
    }

    public function withHint(): self
    {
        $collapse = clone $this;
        $collapse->params['hint'] = 'top_fc';

        return $collapse;
    }

    public function withSize(int $size): self
    {
        $collapse = clone $this;
        $collapse->params['size'] = $size;

        return $collapse;
    }

    public function __toString(): string
    {
        return sprintf('{!collapse %s}', urldecode(http_build_query(array_filter($this->params), '', ' ')));
    }
}
