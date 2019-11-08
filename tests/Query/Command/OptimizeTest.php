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
    public function it_can_toggle_options(): void
    {
        $optimize = new Optimize();

        $this->assertEquals('{}', $optimize->toJson());

        $optimize = $optimize->enableMaxSegments()->enableWaitSearcher();

        $this->assertEquals('{"waitSearcher":true,"maxSegments":true}', $optimize->toJson());

        $optimize = $optimize->disableMaxSegments()->disableWaitSearcher();

        $this->assertEquals('{"waitSearcher":false,"maxSegments":false}', $optimize->toJson());
    }
}
