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
 * @see https://lucene.apache.org/solr/guide/8_3/uploading-data-with-index-handlers.html#adding-documents
 *
 * @psalm-immutable
 */
final class Add implements Command
{
    use JsonHelper;

    /**
     * @psalm-var array{commitWithin: ?int, doc: ?array, overwrite: ?bool}
     */
    private $options = [
        'doc' => null,
        'commitWithin' => null,
        'overwrite' => null,
    ];

    public function __construct(array $document)
    {
        $this->options['doc'] = $document;
    }

    /**
     * @psalm-pure
     */
    public static function create(array $document): self
    {
        return new self($document);
    }

    public function commitWithin(int $commitWithin): self
    {
        $add = clone $this;
        $add->options['commitWithin'] = $commitWithin;

        return $add;
    }

    public function enableOverWrite(): self
    {
        $add = clone $this;
        $add->options['overwrite'] = true;

        return $add;
    }

    public function disableOverWrite(): self
    {
        $add = clone $this;
        $add->options['overwrite'] = false;

        return $add;
    }

    public function toJson(): string
    {
        return self::jsonEncode(array_filter($this->options, static function ($option) { return null !== $option; }));
    }

    public function getName(): string
    {
        return 'add';
    }
}
