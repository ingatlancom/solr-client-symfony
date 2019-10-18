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

namespace iCom\SolrClient\Exception;

class CommunicationError extends \RuntimeException implements Exception
{
    public static function fromUpstreamException(\Throwable $e): self
    {
        return new self($e->getMessage(), $e->getCode(), $e);
    }
}
