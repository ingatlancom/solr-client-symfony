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

use iCom\SolrClient\Query\Command\Commit;
use PHPUnit\Framework\TestCase;

/**
 * @covers \iCom\SolrClient\Query\Command\Commit
 */
final class CommitTest extends TestCase
{
    /** @test */
    public function it_is_empty_by_default(): void
    {
        $commit = Commit::create();

        self::assertSame('{}', $commit->toJson());
    }

    /** @test */
    public function it_has_a_name(): void
    {
        $commit = Commit::create();

        self::assertEquals('commit', $commit->getName());
    }

    /** @test */
    public function it_has_expunge_deletes_option(): void
    {
        $commit = new Commit();

        $new = $commit->enableExpungeDeletes();

        self::assertNotSame($commit, $new);
        self::assertSame('{"expungeDeletes":true}', $new->toJson());

        $new = $commit->disableExpungeDeletes();

        self::assertNotSame($commit, $new);
        self::assertSame('{"expungeDeletes":false}', $new->toJson());
    }

    /** @test */
    public function it_has_wait_searcher_option(): void
    {
        $commit = new Commit();

        $new = $commit->enableWaitSearcher();

        self::assertNotSame($commit, $new);
        self::assertSame('{"waitSearcher":true}', $new->toJson());

        $new = $commit->disableWaitSearcher();

        self::assertNotSame($commit, $new);
        self::assertSame('{"waitSearcher":false}', $new->toJson());
    }
}
