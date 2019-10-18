<?php

declare(strict_types=1);

namespace iCom\SolrClient\Tests\Query;

use iCom\SolrClient\Query\JsonQuery;
use PHPUnit\Framework\TestCase;

/**
 * @covers \iCom\SolrClient\Query\JsonQuery
 */
final class JsonQueryTest extends TestCase
{
    /** @test */
    public function it_maintains_consistent_key_order(): void
    {
        $request1 = (new JsonQuery())
            ->query('*:*')
            ->filter(['field' => 'value'])
            ->facet(['field' => 'value'])
            ->limit(1)
            ->offset(2)
        ;

        $request2 = (new JsonQuery())
            ->facet(['field' => 'value'])
            ->filter(['field' => 'value'])
            ->offset(2)
            ->query('*:*')
            ->limit(1)
        ;

        $expected = ['query' => '*:*', 'filter' => ['field' => 'value'], 'facet' => ['field' => 'value'], 'offset' => 2, 'limit' => 1];
        $this->assertSame($expected, $request1->toArray());
        $this->assertSame($request1->toJson(), $request2->toJson());
        $this->assertSame($request1->toArray(), $request2->toArray());
        $this->assertSame((string) $request1, (string) $request2);
        $this->assertSame(json_encode($request1), json_encode($request2));
    }

    /** @test */
    public function it_throws_for_invalid_params(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('#^Invalid keys "foo" found. Valid keys are "query.+?".$#');

        new JsonQuery(['foo' => 'bar', 'query' => '*:*']);
    }

    /** @test */
    public function it_throws_for_invalid_field_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Type of field "query" should be "string", "array" given.');

        new JsonQuery(['query' => []]);
    }

    /** @test */
    public function it_throws_for_invalid_json(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value for "json" option: Type is not supported.');

        (new JsonQuery(['fields' => [opendir(__DIR__)]]))->toJson();
    }

    /** @test */
    public function it_is_chainable(): void
    {
        $body = [
            'query' => '*:*',
            'filter' => ['-id:1'],
            'fields' => ['id'],
            'facet' => ['categories' => ['type' => 'terms', 'field' => 'cat']],
            'sort' => 'id asc',
            'offset' => 2,
            'limit' => 5,
            'params' => ['debug' => 'true'],
        ];

        $query = JsonQuery::create()
            ->query($body['query'])
            ->filter($body['filter'])
            ->fields($body['fields'])
            ->facet($body['facet'])
            ->sort($body['sort'])
            ->offset($body['offset'])
            ->limit($body['limit'])
            ->params($body['params']);

        $this->assertSame($body, $query->toArray());
    }

    /** @test */
    public function it_removes_keys_from_body_with_null_value(): void
    {
        $query = new JsonQuery(['query' => '*:*', 'sort' => 'id asc', 'offset' => 3, 'limit' => 10]);

        $this->assertArrayNotHasKey('filter', $query->toArray());
        $this->assertArrayNotHasKey('fields', $query->toArray());
        $this->assertArrayNotHasKey('facet', $query->toArray());
        $this->assertArrayNotHasKey('params', $query->toArray());
    }
}
