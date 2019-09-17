<?php declare(strict_types=1);

namespace iCom\SolrClient\Query;

use Symfony\Component\HttpClient\Exception\InvalidArgumentException;

final class JsonQuery implements \JsonSerializable
{
    // to keep key order consistent
    /**
     * @var array<array-key, mixed>
     */
    private $body = [
        'query' => null,
        'filter' => null,
        'fields' => null,
        'facet' => null,
        'sort' => null,
        'offset' => null,
        'limit' => null,
        'params' => null,
    ];

    public function __construct(array $body = [])
    {
        if ($invalid = array_diff_key($body, $this->body)) {
            throw new \InvalidArgumentException(sprintf('Invalid keys "%s" found valid keys are "%s".', implode(', ', array_keys($invalid)), implode(', ', array_keys($this->body))));
        }

        $this->body = array_replace($this->body, $body);
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

    /**
     * @throws InvalidArgumentException When the value cannot be json-encoded.
     */
    private static function jsonEncode(array $value, int $flags = null, int $maxDepth = 512): string
    {
        $flags = $flags ?? (JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRESERVE_ZERO_FRACTION);

        try {
            $value = json_encode($value, $flags | (\PHP_VERSION_ID >= 70300 ? \JSON_THROW_ON_ERROR : 0), $maxDepth);
        } catch (\JsonException $e) {
            throw new InvalidArgumentException(sprintf('Invalid value for "json" option: %s.', $e->getMessage()));
        }

        if (\PHP_VERSION_ID < 70300 && JSON_ERROR_NONE !== json_last_error() && (false === $value || !($flags & JSON_PARTIAL_OUTPUT_ON_ERROR))) {
            throw new InvalidArgumentException(sprintf('Invalid value for "json" option: %s.', json_last_error_msg()));
        }

        return $value;
    }
}
