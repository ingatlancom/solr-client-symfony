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

namespace iCom\SolrClient;

use Symfony\Component\HttpClient\Exception\InvalidArgumentException;

/**
 * @internal
 *
 * @psalm-immutable
 */
trait JsonHelper
{
    /**
     * @param mixed $value
     *
     * @throws InvalidArgumentException When the value cannot be json-encoded.
     *
     * @psalm-pure
     */
    private static function jsonEncode($value, int $flags = null, int $maxDepth = 512): string
    {
        $flags = $flags ?? (JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRESERVE_ZERO_FRACTION);

        try {
            $value = json_encode($value, $flags | (\PHP_VERSION_ID >= 70300 ? \JSON_THROW_ON_ERROR : 0), $maxDepth);
        } catch (\JsonException $e) {
            throw new InvalidArgumentException(sprintf('Invalid value for "json" option: %s.', $e->getMessage()));
        }

        if (\PHP_VERSION_ID < 70300 && JSON_ERROR_NONE !== json_last_error() && false === $value) {
            throw new InvalidArgumentException(sprintf('Invalid value for "json" option: %s.', json_last_error_msg()));
        }

        return $value;
    }
}
