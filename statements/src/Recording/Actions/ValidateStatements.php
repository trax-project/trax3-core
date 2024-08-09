<?php

namespace Trax\Statements\Recording\Actions;

use Illuminate\Support\Collection;
use Trax\Framework\Xapi\Schema\Statement;
use Trax\Framework\Xapi\Exceptions\XapiConflictException;
use Trax\Framework\Xapi\Exceptions\XapiBadRequestException;
use Trax\Statements\Repos\Statement\StatementRepository;

trait ValidateStatements
{
    /**
     * Validate statements.
     *
     * @param  \Illuminate\Support\Collection  $statements
     * @param  array  $attachments
     * @return void
     */
    public function validateStatements(Collection $statements, array $attachments): void
    {
        // Standard validation.
        Statement::validateStatementsAndAttachments($statements->all(), $attachments);

        // Check potential conflicts.
        $this->validateAgainstExistingStatements($statements);
    }

    /**
     * Validate a POST request content.
     *
     * @param  \Illuminate\Support\Collection  $statements
     * @return void
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     * @throws \Trax\Framework\Xapi\Exceptions\XapiConflictException
     */
    protected function validateAgainstExistingStatements(Collection $statements): void
    {
        // Get the ids.
        $uuids = $statements->pluck('id')->filter();

        // Check duplicates.
        if ($uuids->unique()->count() < $uuids->count()) {
            throw new XapiConflictException("Some statements have the same ID in the batch of statements.");
        }

        // Identify statements to be voided..
        $uuids = $statements->where('verb.id', 'http://adlnet.gov/expapi/verbs/voided')->pluck('object.id');
        if ($uuids->isEmpty()) {
            return;
        }

        // Check if voided statements are also voiding statements.
        $voidedVoiding = app(StatementRepository::class)->addFilter([
            'voiding' => true,
        ])->in($uuids);

        if (!$voidedVoiding->isEmpty()) {
            throw new XapiBadRequestException('Voiding statements can not be voided.');
        }
    }
}
