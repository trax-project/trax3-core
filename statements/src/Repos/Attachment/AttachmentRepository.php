<?php

namespace Trax\Statements\Repos\Attachment;

use Trax\Framework\Repo\RepositoryInterface;
use Trax\Framework\Auth\Client;

traxDeclareRepositoryClass('Trax\Statements\Repos\Attachment', 'statements');

class AttachmentRepository extends Repository implements RepositoryInterface
{
    /**
     * The applicable domain.
     */
    protected $domain = 'statements';

    /**
     * The applicable scopes.
     */
    protected $scopes = ['store', 'client'];

    /**
     * Return the class of the factory.
     *
     * @return string
     */
    public function factoryClass()
    {
        return AttachmentFactory::class;
    }
}
