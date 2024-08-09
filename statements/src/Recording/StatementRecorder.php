<?php

namespace Trax\Statements\Recording;

use Trax\Framework\Context;
use Trax\Framework\Service\Facades\EventManager;
use Trax\Framework\Xapi\Helpers\Json;
use Trax\Framework\Database\DataRecorder;
use Trax\Statements\Repos\Statement\StatementRepository;
use Trax\Statements\Events\StatementsRecorded;
use Trax\Statements\Events\StatementsVoided;
use Trax\Statements\Events\AttachmentsRecorded;
use Trax\Statements\Recording\Actions\ValidateStatements;
use Trax\Statements\Recording\Actions\ManageStatementIds;
use Trax\Statements\Recording\Actions\TransformStatements;
use Trax\Statements\Recording\Actions\RecordAttachments;
use Trax\Statements\Recording\Actions\RecordStatements;
use Trax\Statements\Recording\Actions\VoidStatements;

class StatementRecorder implements DataRecorder
{
    use ValidateStatements, ManageStatementIds, TransformStatements, RecordAttachments, RecordStatements, VoidStatements;

    /**
     * Record statements and return their IDs.
     *
     * @param  array  $statements
     * @param  array  $attachments
     * @return array
     */
    public function record(array $statements, array $attachments = []): array
    {
        if (empty($statements)) {
            return [];
        }

        $pipeline = Context::pipeline();

        // Be sure to work on objects.
        // Arrays may be used when the function is called directly during seeding for example.
        $statements = collect($statements)->map(function ($statement) {
            return Json::object($statement);
        });

        // Validate the statements. This may include extra validation class.
        if (!empty($pipeline->validate_statements)) {
            $this->validateStatements($statements, $attachments);
        }

        // Manage statement IDs. This may include checking the conflicts.
        $statements = $this->manageStatementIds($statements, $pipeline->check_conflicts);

        // Transform statements.
        if (!empty($pipeline->transformation_class)) {
            $statements = $this->transformStatements($statements);
        }
        
        // Pseudonymize statements.
        // We no longer apply pseudonymization in real time. Use jobs instead.
        
        // Record attachments.
        $attachmentRecords = $pipeline->record_attachments
            ? $this->recordAttachments($attachments)
            : collect([]);

        // Record statements.
        $statementRecords = $this->recordStatements($statements);

        // Void statements.
        $voidedIds = collect();
        if ($pipeline->void_statements) {
            $voidedIds = $this->voidStatements($statements);
        }

        // Update "consistentThrough".
        app(StatementRepository::class)->updateConsistentThrough();

        // Dispatch events.
        if (!$statementRecords->isEmpty()) {
            EventManager::dispatch(StatementsRecorded::class, $statementRecords);
        }
        if (!$voidedIds->isEmpty()) {
            EventManager::dispatch(StatementsVoided::class, $voidedIds);
        }
        if (!$attachmentRecords->isEmpty()) {
            EventManager::dispatch(AttachmentsRecorded::class, $attachmentRecords);
        }

        return $statementRecords->pluck('id')->all();
    }
}
