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
 * @see https://lucene.apache.org/solr/guide/8_3/uploading-data-with-index-handlers.html#commit-and-optimize-during-updates
 *
 * @psalm-immutable
 */
final class Optimize implements Command
{
    use JsonHelper;

    /**
     * @psalm-var array{maxSegments: ?int, waitSearcher: ?bool}
     */
    private array $options = [
        'waitSearcher' => null,
        'maxSegments' => null,
    ];

    /**
     * @psalm-pure
     */
    public static function create(): self
    {
        return new self();
    }

    public function enableWaitSearcher(): self
    {
        $commit = clone $this;
        $commit->options['waitSearcher'] = true;

        return $commit;
    }

    public function disableWaitSearcher(): self
    {
        $commit = clone $this;
        $commit->options['waitSearcher'] = false;

        return $commit;
    }

    public function maxSegments(int $maxSegments): self
    {
        $commit = clone $this;
        $commit->options['maxSegments'] = $maxSegments;

        return $commit;
    }

    public function toJson(): string
    {
        return self::jsonEncode(array_filter($this->options, static function ($option): bool { return null !== $option; }), JSON_FORCE_OBJECT);
    }

    public function getName(): string
    {
        return 'optimize';
    }
}
