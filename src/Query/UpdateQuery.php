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
final class UpdateQuery
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

    public function add(Add $add): self
    {
        $this->addCommand($add);

        return $this;
    }

    public function commit(Commit $commit): self
    {
        $this->addCommand($commit);

        return $this;
    }

    public function optimize(Optimize $optimize): self
    {
        $this->addCommand($optimize);

        return $this;
    }

    public function delete(Delete $delete): self
    {
        $this->addCommand($delete);

        return $this;
    }

    public function toSolrJson(): string
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
