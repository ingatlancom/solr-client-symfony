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

namespace iCom\SolrClient\Tests\Query\Helper;

use iCom\SolrClient\Query\Helper\Collapse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \iCom\SolrClient\Query\Helper\Collapse
 */
final class CollapseTest extends TestCase
{
    /** @test */
    public function it_throws_exception_for_invalid_null_policy(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Collapse::create('id')->nullPolicy('invalid');
    }

    /** @test */
    public function it_can_be_created_for_a_field(): void
    {
        $collapse = Collapse::create('id');

        self::assertSame('{!collapse field=id}', $collapse->toString());
    }

    /** @test */
    public function it_has_a_min_option(): void
    {
        $collapse = Collapse::create('id');
        $new = $collapse->min('field_name');

        self::assertNotSame($new, $collapse);
        self::assertSame('{!collapse field=id min=field_name}', $new->toString());
    }

    /** @test */
    public function it_has_a_max_option(): void
    {
        $collapse = Collapse::create('id');
        $new = $collapse->max('field_name');

        self::assertNotSame($new, $collapse);
        self::assertSame('{!collapse field=id max=field_name}', $new->toString());
    }

    /** @test */
    public function it_has_a_sort_option(): void
    {
        $collapse = Collapse::create('id');
        $new = $collapse->sort(['field_name desc', 'id desc']);

        self::assertNotSame($new, $collapse);
        self::assertSame("{!collapse field=id sort='field_name desc,id desc'}", $new->toString());
    }

    /** @test */
    public function it_has_a_null_policy_option(): void
    {
        $collapse = Collapse::create('id');
        $new = $collapse->nullPolicy('ignore');

        self::assertNotSame($new, $collapse);
        self::assertSame('{!collapse field=id nullPolicy=ignore}', $new->toString());
    }

    /** @test */
    public function it_has_a_hint_option(): void
    {
        $collapse = Collapse::create('id');
        $new = $collapse->hint();

        self::assertNotSame($new, $collapse);
        self::assertSame('{!collapse field=id hint=top_fc}', $new->toString());
    }

    /** @test */
    public function it_has_a_size_option(): void
    {
        $collapse = Collapse::create('id');
        $new = $collapse->size(50000);

        self::assertNotSame($new, $collapse);
        self::assertSame('{!collapse field=id size=50000}', $new->toString());
    }

    /** @test */
    public function it_has_a_cache_option(): void
    {
        $collapse = Collapse::create('id');
        $new = $collapse->cache(true);

        self::assertNotSame($new, $collapse);
        self::assertSame('{!collapse field=id cache=true}', $new->toString());

        $new = $collapse->cache(false);
        self::assertSame('{!collapse field=id cache=false}', $new->toString());

        $new = $collapse->cache(true, 10);
        self::assertSame('{!collapse field=id cache=true cost=10}', $new->toString());
    }

    /**
     * @test
     * @dataProvider multipleSortProvider
     */
    public function it_throws_if_multiple_sort_is_configured(\Closure $multiSort): void
    {
        $this->expectException(\RuntimeException::class);

        $multiSort();
    }

    /**
     * @return iterable<string, array>
     */
    public function multipleSortProvider(): iterable
    {
        yield 'min with sort' => [static function (): Collapse {
            return Collapse::create('id')->min('field_name')->sort(['field_name desc']);
        }];

        yield 'min with max' => [static function (): Collapse {
            return Collapse::create('id')->min('field_name')->max('field_name');
        }];

        yield 'max with min' => [static function (): Collapse {
            return Collapse::create('id')->max('field_name')->min('field_name');
        }];

        yield 'max with sort' => [static function (): Collapse {
            return Collapse::create('id')->max('field_name')->sort(['field_name desc']);
        }];

        yield 'sort with min' => [static function (): Collapse {
            return Collapse::create('id')->sort(['field_name desc'])->min('field_name');
        }];

        yield 'sort with max' => [static function (): Collapse {
            return Collapse::create('id')->sort(['field_name desc'])->max('field_name');
        }];
    }
}
