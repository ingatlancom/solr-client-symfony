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

namespace iCom\SolrClient\Tests\Query\Command;

use iCom\SolrClient\Query\Command\Delete;
use iCom\SolrClient\Query\SelectQuery;
use PHPUnit\Framework\TestCase;

/**
 * @covers \iCom\SolrClient\Query\Command\Delete
 *
 * @uses \iCom\SolrClient\Query\SelectQuery
 */
final class DeleteTest extends TestCase
{
    /** @test */
    public function it_can_hold_ids_or_json_query(): void
    {
        $deleteByIds = Delete::fromIds([1, 2, 3]);
        $deleteByQuery = Delete::fromQuery(SelectQuery::create()->query('id:1'));

        self::assertSame('[1,2,3]', $deleteByIds->toJson());
        self::assertSame('{"query":"id:1"}', $deleteByQuery->toJson());
    }

    /** @test */
    public function it_has_a_name(): void
    {
        $delete = Delete::fromIds([1, 2, 3]);

        self::assertSame('delete', $delete->getName());
    }
}
