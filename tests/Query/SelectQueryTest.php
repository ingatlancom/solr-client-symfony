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

namespace iCom\SolrClient\Tests\Query;

use iCom\SolrClient\Query\Helper\Collapse;
use iCom\SolrClient\Query\Helper\Terms;
use iCom\SolrClient\Query\SelectQuery;
use PHPUnit\Framework\TestCase;

/**
 * @covers \iCom\SolrClient\Query\SelectQuery
 *
 * @uses \iCom\SolrClient\Query\Helper\Collapse
 * @uses \iCom\SolrClient\Query\Helper\Terms
 */
final class SelectQueryTest extends TestCase
{
    /** @test */
    public function it_is_empty_default(): void
    {
        $select = new SelectQuery();

        $this->assertSame('{}', $select->toJson());
    }

    /** @test */
    public function it_has_a_query_option(): void
    {
        $select = new SelectQuery();
        $new = $select->query('*:*');

        $this->assertNotSame($select, $new);
        $this->assertSame('{"query":"*:*"}', $new->toJson());
    }

    /** @test */
    public function it_has_a_filter_option_for_multiple_filters(): void
    {
        $select = new SelectQuery();
        $new = $select->filter(['field' => 'value']);

        $this->assertNotSame($select, $new);
        $this->assertSame('{"filter":{"field":"value"}}', $new->toJson());
    }

    /** @test */
    public function it_has_a_filter_option_for_filter_expressions(): void
    {
        $select = new SelectQuery();
        $new = $select->withFilter('id:1');

        $this->assertNotSame($select, $new);
        $this->assertSame('{"filter":["id:1"]}', $new->toJson());
    }

    /** @test */
    public function it_has_a_fields_option(): void
    {
        $select = new SelectQuery();
        $new = $select->fields(['field1', 'field2']);

        $this->assertNotSame($select, $new);
        $this->assertSame('{"fields":["field1","field2"]}', $new->toJson());
    }

    /** @test */
    public function it_has_a_sort_option(): void
    {
        $select = new SelectQuery();
        $new = $select->sort('id desc');

        $this->assertNotSame($select, $new);
        $this->assertSame('{"sort":"id desc"}', $new->toJson());
    }

    /** @test */
    public function it_has_a_facet_option(): void
    {
        $select = new SelectQuery();
        $new = $select->facet(['categories' => ['type' => 'terms', 'field' => 'cat']]);

        $this->assertNotSame($select, $new);
        $this->assertSame('{"facet":{"categories":{"type":"terms","field":"cat"}}}', $new->toJson());
    }

    /** @test */
    public function it_has_a_params_option(): void
    {
        $select = new SelectQuery();
        $new = $select->params(['debug' => true]);

        $this->assertNotSame($select, $new);
        $this->assertSame('{"params":{"debug":true}}', $new->toJson());
    }

    /** @test */
    public function it_has_a_offset_option(): void
    {
        $select = new SelectQuery();
        $new = $select->offset(10);

        $this->assertNotSame($select, $new);
        $this->assertSame('{"offset":10}', $new->toJson());
    }

    /** @test */
    public function it_has_a_limit_option(): void
    {
        $select = new SelectQuery();
        $new = $select->limit(10);

        $this->assertNotSame($select, $new);
        $this->assertSame('{"limit":10}', $new->toJson());
    }

    /** @test */
    public function it_throws_for_invalid_params(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#^Invalid keys "foo" found. Valid keys are "query.+?".$#');

        new SelectQuery(['foo' => 'bar', 'query' => '*:*']);
    }

    /** @test */
    public function it_throws_for_invalid_field_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Type of field "query" should be "string", "array" given.');

        new SelectQuery(['query' => []]);
    }

    /** @test */
    public function it_throws_for_invalid_json(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value for "json" option: Type is not supported.');

        (new SelectQuery(['fields' => [opendir(__DIR__)]]))->toJson();
    }

    /** @test */
    public function it_maintains_consistent_key_order(): void
    {
        $request1 = (new SelectQuery())
            ->query('*:*')
            ->filter(['field' => 'value'])
            ->facet(['field' => 'value'])
            ->limit(1)
            ->offset(2)
        ;

        $request2 = (new SelectQuery())
            ->facet(['field' => 'value'])
            ->filter(['field' => 'value'])
            ->offset(2)
            ->query('*:*')
            ->limit(1)
        ;

        $this->assertSame($request1->toJson(), $request2->toJson());
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

        $query = SelectQuery::create()
            ->query($body['query'])
            ->filter($body['filter'])
            ->fields($body['fields'])
            ->facet($body['facet'])
            ->sort($body['sort'])
            ->offset($body['offset'])
            ->limit($body['limit'])
            ->params($body['params'])
        ;

        $this->assertSame($body, \json_decode($query->toJson(), true));
    }

    /** @test */
    public function it_removes_keys_from_body_with_null_value(): void
    {
        $query = new SelectQuery(['query' => '*:*', 'sort' => 'id asc', 'offset' => 3, 'limit' => 10]);

        $this->assertStringNotContainsString('filter', $query->toJson());
        $this->assertStringNotContainsString('fields', $query->toJson());
        $this->assertStringNotContainsString('facet', $query->toJson());
        $this->assertStringNotContainsString('params', $query->toJson());
    }

    /** @test */
    public function it_can_append_multiple_filters(): void
    {
        $query = (new SelectQuery())->filter(['id:1'])->withFilter('id:2')->withFilter('id:3');

        $this->assertStringContainsString('id:1', $query->toJson());
        $this->assertStringContainsString('id:2', $query->toJson());
        $this->assertStringContainsString('id:3', $query->toJson());
    }

    /** @test */
    public function it_throws_for_invalid_filter(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        SelectQuery::create()->withFilter(new \stdClass());
    }

    /** @test */
    public function it_accepts_query_helper_filter(): void
    {
        $query = (new SelectQuery())->filter(['id:1', Collapse::create('collapse_field')])->withFilter(Terms::create('terms_field', [1, 2]));

        $this->assertSame('{"filter":["id:1","{!collapse field=collapse_field}","{!terms f=terms_field}1,2"]}', $query->toJson());
    }
}
