<?php declare(strict_types=1);

namespace iCom\SolrClient\Tests\Query;

use iCom\SolrClient\Query\JsonQuery;
use PHPUnit\Framework\TestCase;

final class JsonQueryTest extends TestCase
{
    /** @test */
    function it_maintains_consistent_key_order(): void
    {
        $request1 = new JsonQuery();
        $request1
            ->query('*:*')
            ->filter(['field' => 'value'])
            ->facet(['field' => 'value'])
            ->limit(1)
            ->offset(2)
        ;

        $request2 = new JsonQuery();
        $request2
            ->facet(['field' => 'value'])
            ->filter(['field' => 'value'])
            ->offset(2)
            ->query('*:*')
            ->limit(1)
        ;

        $this->assertSame($request1->toJson(), $request2->toJson());
        $this->assertSame($request1->toArray(), $request2->toArray());
        $this->assertSame((string) $request1, (string) $request2);
        $this->assertSame(json_encode($request1), json_encode($request2));
    }

    /** @test */
    function it_throws_for_invalid_params(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('#^Invalid keys "foo" found valid keys are ".+?".$#');

        new JsonQuery(['foo' => 'bar', 'query' => '*:*']);
    }
}
