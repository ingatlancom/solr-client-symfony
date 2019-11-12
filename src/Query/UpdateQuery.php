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

use iCom\SolrClient\JsonQuery;
use iCom\SolrClient\Query\Command\Add;
use iCom\SolrClient\Query\Command\Commit;
use iCom\SolrClient\Query\Command\Delete;
use iCom\SolrClient\Query\Command\Optimize;

/**
 * Creates a JSON formatted update query.
 *
 * Note that: Solr has his own JSON syntax which allows to have multiple keys (multiple commands).
 *
 * @see https://lucene.apache.org/solr/guide/8_3/uploading-data-with-index-handlers.html#sending-json-update-commands
 */
final class UpdateQuery implements JsonQuery
{
    /** @var array|iterable|Command[] */
    private $commands = [];

    public function __construct(iterable $commands = [])
    {
        foreach ($commands as $command) {
            $this->addCommand($command);
        }
    }

    public static function create(array $commands = []): self
    {
        return new self($commands);
    }

    public function add(array $document, ?int $commitWithin = null, ?bool $overwrite = null): self
    {
        $add = Add::create($document);

        if (null !== $overwrite) {
            $add = $overwrite ? $add->enableOverWrite() : $add->disableOverWrite();
        }

        if (null !== $commitWithin) {
            $add = $add->commitWithin($commitWithin);
        }

        $this->addCommand($add);

        return $this;
    }

    public function commit(?bool $waitSearcher = null, ?bool $expungeDeletes = null): self
    {
        $commit = Commit::create();

        if (null !== $waitSearcher) {
            $commit = $waitSearcher ? $commit->enableWaitSearcher() : $commit->disableWaitSearcher();
        }

        if (null !== $expungeDeletes) {
            $commit = $expungeDeletes ? $commit->enableExpungeDeletes() : $commit->disableExpungeDeletes();
        }

        $this->addCommand($commit);

        return $this;
    }

    public function optimize(?bool $waitSearcher = null, ?int $maxSegments = null): self
    {
        $optimize = Optimize::create();

        if (null !== $waitSearcher) {
            $optimize = $waitSearcher ? $optimize->enableWaitSearcher() : $optimize->disableWaitSearcher();
        }

        if (null !== $maxSegments) {
            $optimize = $optimize->maxSegments($maxSegments);
        }

        $this->addCommand($optimize);

        return $this;
    }

    public function deleteByIds(array $ids): self
    {
        $this->addCommand(Delete::fromIds($ids));

        return $this;
    }

    public function deleteByQuery(SelectQuery $query): self
    {
        $this->addCommand(Delete::fromQuery($query));

        return $this;
    }

    public function toJson(): string
    {
        $commands = [];
        foreach ($this->commands as $command) {
            $commands[] = sprintf('"%s":%s', $command->getName(), $command->toJson());
        }

        return sprintf('{%s}', implode(',', $commands));
    }

    private function addCommand(Command $command): void
    {
        $this->commands[] = $command;
    }
}
