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
    public function it_throws_exception_on_invalid_null_policy(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Collapse::create('id')->nullPolicy('policy');
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
     * @test
     * @dataProvider provider
     */
    public function it_creates_filter_string(\Closure $collapse, string $expectedCollapse): void
    {
        $this->assertSame($expectedCollapse, $collapse()->toString());
    }

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

    public function provider(): iterable
    {
        yield 'field' => [
            static function (): Collapse { return Collapse::create('id'); },
            '{!collapse field=id}',
        ];

        yield 'min' => [
            static function (): Collapse { return Collapse::create('id')->min('field_name'); },
            '{!collapse field=id min=field_name}',
        ];

        yield 'max' => [
            static function (): Collapse { return Collapse::create('id')->max('field_name'); },
            '{!collapse field=id max=field_name}',
        ];

        yield 'sort' => [
            static function (): Collapse { return Collapse::create('id')->sort(['field_name desc', 'id desc']); },
            "{!collapse field=id sort='field_name desc,id desc'}",
        ];

        yield 'nullPolicy' => [
            static function (): Collapse { return Collapse::create('id')->nullPolicy('ignore'); },
            '{!collapse field=id nullPolicy=ignore}',
        ];

        yield 'hint' => [
            static function (): Collapse { return Collapse::create('id')->hint(); },
            '{!collapse field=id hint=top_fc}',
        ];

        yield 'size' => [
            static function (): Collapse { return Collapse::create('id')->size(50000); },
            '{!collapse field=id size=50000}',
        ];

        yield 'cache-true' => [
            static function (): Collapse { return Collapse::create('id')->cache(true); },
            '{!collapse field=id cache=true}',
        ];

        yield 'cache-false' => [
            static function (): Collapse { return Collapse::create('id')->cache(false); },
            '{!collapse field=id cache=false}',
        ];
    }
}
