<?php

namespace Trax\Statements;

use Trax\Framework\Database\Database;
use Trax\Statements\Repos\Statement\StatementRepository;
use Trax\Statements\Repos\Attachment\AttachmentRepository;
use Trax\Statements\Recording\StatementRecorder;

class StatementsDatabase extends Database
{
    /**
     * @var string
     */
    protected $service = 'statements';
            
    /**
     * @var string
     */
    protected static $connection = 'statements';
    
    /**
     * @var array
     */
    protected static $tables = [
        'xapi_statements' => ['type' => 'main'],
        'xapi_attachments' => ['type' => 'main'],
    ];

    /**
     * @var array
     */
    protected $storeTables = ['xapi_statements', 'xapi_attachments'];

    /**
     * @var bool
     */
    protected $nosqlSupported = true;

    /**
     * @var \Trax\Statements\Repos\Statement\StatementRepository
     */
    protected $xapi_statements;
    
    /**
     * @var \Trax\Statements\Repos\Attachment\AttachmentRepository
     */
    protected $xapi_attachments;
    
    /**
     * @return void
     */
    public function __construct()
    {
        $this->xapi_statements = app(StatementRepository::class);
        $this->xapi_attachments = app(AttachmentRepository::class);
        $this->mainRepository = $this->xapi_statements;
        $this->recorder = app(StatementRecorder::class);
    }

    /**
     * Export filters.
     *
     * @return array
     */
    protected function exportFilters(): array
    {
        return [
            ['voided' => false],
        ];
    }

    /**
     * Clear users data.
     *
     * @param  array  $users    each listed user is an agent string ID
     * @return void
     */
    public function clearUsers(array $users): void
    {
        $this->xapi_statements->clearAgents($users);
    }
}
