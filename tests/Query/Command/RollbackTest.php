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

use iCom\SolrClient\Query\Command\Rollback;
use PHPUnit\Framework\TestCase;

/**
 * @covers \iCom\SolrClient\Query\Command\Rollback
 */
final class RollbackTest extends TestCase
{
    /** @test */
    public function it_can_create_rollback_with_empty_options(): void
    {
        $rollback = Rollback::create();

        self::assertEquals('{}', $rollback->toJson());
    }
}
