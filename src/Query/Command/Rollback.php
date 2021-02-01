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

namespace iCom\SolrClient\Query\Command;

use iCom\SolrClient\JsonHelper;
use iCom\SolrClient\Query\Command;

/**
 * @see https://lucene.apache.org/solr/guide/uploading-data-with-index-handlers.html#rollback-operations
 *
 * @psalm-immutable
 */
final class Rollback implements Command
{
    use JsonHelper;

    /**
     * @psalm-pure
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * @psalm-pure
     */
    public function toJson(): string
    {
        return self::jsonEncode([], JSON_FORCE_OBJECT);
    }

    /**
     * @psalm-pure
     */
    public function getName(): string
    {
        return 'rollback';
    }
}
