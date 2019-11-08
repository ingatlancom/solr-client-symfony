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
    public function it_can_toggle_options(): void
    {
        $commit = new Commit();

        $this->assertSame('{}', $commit->toJson());

        $commit = $commit->enableExpungeDeletes()->enableWaitSearcher();

        $this->assertEquals('{"waitSearcher":true,"expungeDeletes":true}', $commit->toJson());

        $commit = $commit->disableExpungeDeletes()->disableWaitSearcher();

        $this->assertEquals('{"waitSearcher":false,"expungeDeletes":false}', $commit->toJson());
    }
}
