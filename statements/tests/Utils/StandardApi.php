<?php

namespace Trax\Statements\Tests\Utils;

use Trax\Framework\Tests\Utils\ServiceApi;
use Trax\Statements\Repos\Statement\StatementRepository;
use Trax\Statements\Repos\Attachment\AttachmentRepository;

class StandardApi extends ServiceApi
{
    protected $service = 'statements';

    protected $statements;
    
    protected $attachments;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->statements = app(StatementRepository::class);
        $this->attachments = app(AttachmentRepository::class);
    }
}
