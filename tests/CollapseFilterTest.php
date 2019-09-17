<?php declare(strict_types=1);

namespace iCom\SolrClient\Tests;

use iCom\SolrClient\CollapseFilter;
use PHPUnit\Framework\TestCase;

final class CollapseFilterTest extends TestCase
{
    /** @test */
    function it_throws_exception_on_invalid_null_policy(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CollapseFilter::create('id')->nullPolicy('policy');
    }

    /** @test */
    function multiple_sorts_is_not_allowed(): void
    {
        $this->expectException(\RuntimeException::class);

        CollapseFilter::create('id')->max('credit')->min('credit');
    }

    /**
     * @test
     * @dataProvider provider
     */
    function it_creates_filter_string(CollapseFilter $collapseFilter, string $expectedCollapse): void
    {
        $this->assertSame($expectedCollapse, (string) $collapseFilter);
    }

    public function provider(): iterable
    {
        yield 'field' => [
            CollapseFilter::create('id'),
            '{!collapse field=id}'
        ];

        yield 'min' => [
            CollapseFilter::create('id')->min('credit'),
            '{!collapse field=id min=credit}'
        ];

        yield 'max' => [
            CollapseFilter::create('id')->max('credit'),
            '{!collapse field=id max=credit}'
        ];

        yield 'sort' => [
            CollapseFilter::create('id')->sort(['credit desc', 'id desc']),
            "{!collapse field=id sort='credit desc,id desc'}"
        ];

        yield 'nullPolicy' => [
            CollapseFilter::create('id')->nullPolicy('ignore'),
            '{!collapse field=id nullPolicy=ignore}'
        ];

        yield 'hint' => [
            CollapseFilter::create('id')->hint(),
            '{!collapse field=id hint=top_fc}'
        ];

        yield 'size' => [
            CollapseFilter::create('id')->size(50000),
            '{!collapse field=id size=50000}'
        ];
    }
}
