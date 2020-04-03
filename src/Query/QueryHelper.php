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

namespace iCom\SolrClient\Query;

/**
 * @psalm-immutable
 */
interface QueryHelper
{
    /**
     * Returns the string representation of the Query Helper.
     */
    public function toString(): string;
}
