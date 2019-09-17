<?php declare(strict_types=1);

namespace iCom\SolrClient\Tests\Query;

use iCom\SolrClient\Query\Collapse;
use PHPUnit\Framework\TestCase;

final class CollapseTest extends TestCase
{
    /** @test */
    function it_throws_exception_on_invalid_null_policy(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Collapse::create('id')->nullPolicy('policy');
    }

    /** @test */
    function multiple_sorts_is_not_allowed(): void
    {
        $this->expectException(\RuntimeException::class);

        Collapse::create('id')->max('credit')->min('credit');
    }

    /**
     * @test
     * @dataProvider provider
     */
    function it_creates_filter_string(Collapse $collapse, string $expectedCollapse): void
    {
        $this->assertSame($expectedCollapse, (string) $collapse);
    }

    public function provider(): iterable
    {
        yield 'field' => [
            Collapse::create('id'),
            '{!collapse field=id}'
        ];

        yield 'min' => [
            Collapse::create('id')->min('credit'),
            '{!collapse field=id min=credit}'
        ];

        yield 'max' => [
            Collapse::create('id')->max('credit'),
            '{!collapse field=id max=credit}'
        ];

        yield 'sort' => [
            Collapse::create('id')->sort(['credit desc', 'id desc']),
            "{!collapse field=id sort='credit desc,id desc'}"
        ];

        yield 'nullPolicy' => [
            Collapse::create('id')->nullPolicy('ignore'),
            '{!collapse field=id nullPolicy=ignore}'
        ];

        yield 'hint' => [
            Collapse::create('id')->hint(),
            '{!collapse field=id hint=top_fc}'
        ];

        yield 'size' => [
            Collapse::create('id')->size(50000),
            '{!collapse field=id size=50000}'
        ];
    }
}
