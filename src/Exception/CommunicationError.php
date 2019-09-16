<?php declare(strict_types=1);

namespace iCom\SolrClient\Exception;

class CommunicationError extends \RuntimeException implements Exception
{
    public static function fromUpstreamException(\Throwable $e): self
    {
        return  new self($e->getMessage(), $e->getCode(), $e);
    }
}
