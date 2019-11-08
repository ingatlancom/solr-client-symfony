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

interface Command
{
    /**
     * Returns the JSON representation of the command's value.
     *
     * @return string
     */
    public function toJson(): string;

    /**
     * Returns the name of the command.
     *
     * @return string
     */
    public function getName(): string;
}
