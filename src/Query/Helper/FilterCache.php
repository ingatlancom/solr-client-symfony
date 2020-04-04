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

namespace iCom\SolrClient\Query\Helper;

/**
 * @psalm-immutable
 */
trait FilterCache
{
    /**
     * @var ?bool
     */
    private $cache;

    public function cache(bool $cache): self
    {
        $self = clone $this;
        $self->cache = $cache;

        return $self;
    }

    private function getCacheFields(): array
    {
        return [
            'cache' => null !== $this->cache ? var_export($this->cache, true) : null,
        ];
    }
}
