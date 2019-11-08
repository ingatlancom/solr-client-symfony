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
use iCom\SolrClient\Query\SelectQuery;

/**
 * @see https://lucene.apache.org/solr/guide/8_3/uploading-data-with-index-handlers.html#delete-operations
 */
final class Delete implements Command
{
    use JsonHelper;

    private $value;

    public static function fromIds(array $ids): self
    {
        $delete = new self();
        $delete->value = $ids;

        return $delete;
    }

    public static function fromQuery(SelectQuery $query)
    {
        $delete = new self();
        $delete->value = $query;

        return $delete;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function toJson(): string
    {
        return self::jsonEncode($this->value, JSON_UNESCAPED_SLASHES);
    }

    public function getName(): string
    {
        return 'delete';
    }
}
