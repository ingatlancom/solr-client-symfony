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

use iCom\SolrClient\Query\Command\Optimize;
use PHPUnit\Framework\TestCase;

/**
 * @covers \iCom\SolrClient\Query\Command\Optimize
 */
final class OptimizeTest extends TestCase
{
    /** @test */
    public function it_is_empty_by_default(): void
    {
        $optimize = Optimize::create();

        $this->assertEquals('{}', $optimize->toJson());
    }

    /** @test */
    public function it_has_a_name(): void
    {
        $optimize = new Optimize();

        $this->assertEquals('optimize', $optimize->getName());
    }

    /** @test */
    public function its_has_a_wait_searcher_option(): void
    {
        $optimize = new Optimize();

        $new = $optimize->enableWaitSearcher();

        $this->assertNotSame($optimize, $new);
        $this->assertEquals('{"waitSearcher":true}', $new->toJson());

        $new = $optimize->disableWaitSearcher();

        $this->assertNotSame($optimize, $new);
        $this->assertEquals('{"waitSearcher":false}', $new->toJson());
    }

    /** @test */
    public function it_has_a_max_segments_option(): void
    {
        $optimize = new Optimize();

        $new = $optimize->maxSegments(1024);

        $this->assertNotSame($optimize, $new);
        $this->assertEquals('{"maxSegments":1024}', $new->toJson());
    }
}
