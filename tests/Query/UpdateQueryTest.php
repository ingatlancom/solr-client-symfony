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

use iCom\SolrClient\Query\Command\Add;
use iCom\SolrClient\Query\SelectQuery;
use iCom\SolrClient\Query\UpdateQuery;
use PHPUnit\Framework\TestCase;

/**
 * @covers \iCom\SolrClient\Query\UpdateQuery
 *
 * @uses \iCom\SolrClient\Query\SelectQuery
 * @uses \iCom\SolrClient\Query\Command\Add
 * @uses \iCom\SolrClient\Query\Command\Commit
 * @uses \iCom\SolrClient\Query\Command\Optimize
 * @uses \iCom\SolrClient\Query\Command\Delete
 */
final class UpdateQueryTest extends TestCase
{
    /** @test */
    public function it_is_possible_to_have_multiple_commands_with_same_key(): void
    {
        $commands = UpdateQuery::create([new Add(['id' => 1])])
            ->add(['id' => 2], 1000, false)
            ->add(['id' => 3], 500)
            ->deleteByIds(['1', '2', '3'])
            ->deleteByQuery(SelectQuery::create()->query('id:"1"'))
        ;

        self::assertSame('{"add":{"doc":{"id":1}},"add":{"doc":{"id":2},"commitWithin":1000,"overwrite":false},"add":{"doc":{"id":3},"commitWithin":500},"delete":["1","2","3"],"delete":{"query":"id:\"1\""}}', $commands->toJson());
    }

    /** @test */
    public function it_can_add_a_commit_command(): void
    {
        self::assertSame('{"commit":{}}', UpdateQuery::create()->commit()->toJson());
        self::assertSame('{"commit":{"waitSearcher":true}}', UpdateQuery::create()->commit(true)->toJson());
        self::assertSame('{"commit":{"expungeDeletes":true}}', UpdateQuery::create()->commit(null, true)->toJson());
    }

    /** @test */
    public function it_can_add_an_optimize_command(): void
    {
        self::assertSame('{"optimize":{}}', UpdateQuery::create()->optimize()->toJson());
        self::assertSame('{"optimize":{"waitSearcher":true}}', UpdateQuery::create()->optimize(true)->toJson());
        self::assertSame('{"optimize":{"maxSegments":1}}', UpdateQuery::create()->optimize(null, 1)->toJson());
    }
}
