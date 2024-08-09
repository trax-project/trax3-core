<?php

namespace Trax\Statements\Tests\Utils;

use Trax\Framework\Context;
use Trax\Statements\Recording\StatementRecorder;
use Trax\Statements\StatementsDatabase;
use Trax\Statements\Repos\Statement\StatementRepository;

trait RecordStatements
{
    protected function clearStatements(string $store = 'default')
    {
        Context::setStore($store);
        app(StatementsDatabase::class)->clear();
    }

    protected function createStatement($statement, $record = [], string $store = 'default')
    {
        $uuid = isset($statement['id']) ? $statement['id'] : traxUuid();
        $statement['id'] = $uuid;

        $this->createStatements([$statement], $store);

        if (empty($record)) {
            return;
        }

        app(StatementRepository::class)->update($uuid, $record);
    }

    protected function createStatements(array $statements, string $store = 'default')
    {
        Context::setStore($store);
        app(StatementRepository::class)->insert($statements, 'prepareXapiStatement');
    }

    protected function recordStatementsBatch(array $statements, string $store = 'default'): array
    {
        Context::setStore($store);
        return app(StatementRecorder::class)->record($statements);
    }

    protected function recordMultipartStatement($statement, array $attachments, string $store = 'default'): array
    {
        Context::setStore($store);
        return app(StatementRecorder::class)->record([$statement], $attachments);
    }
}
