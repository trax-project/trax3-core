<?php

namespace Trax\Statements\Repos\Attachment;

use Trax\Framework\Repo\ModelFactory;
use Trax\Framework\Context;
use Trax\Framework\Xapi\Helpers\Json;

class AttachmentFactory extends ModelFactory
{
    /**
     * Return the model class.
     *
     * @return string
     */
    public function modelClass(): string
    {
        return Attachment::class;
    }

    /**
     * Return the unique columns used to upsert data.
     *
     * @return array
     */
    public function uniqueColumns(): array
    {
        return ['id', 'store'];
    }

    /**
     * Prepare data before recording (used for bulk insert).
     *
     * @param  array  $data
     * @return array
     */
    public function prepare(array $data)
    {
        // Feature not supported for this model.
    }

    /**
     * Prepare data before recording (used for bulk insert).
     * This function differs from `prepare` because it take an xAPI attachment in parameter.
     *
     * @param  string|object|array  $statement
     * @return array
     */
    public function prepareXapiAttachment($attachment)
    {
        // Be sure to work with objects.
        $attachment = Json::object($attachment);

        // Return the record.
        return [
            'id' => $attachment->sha2,
            'content' => $attachment->content,
            'content_type' => $attachment->contentType,
            'length' => $attachment->length,
            'store' => Context::store(),
            'client' => Context::client(),
            'stored' => traxIsoNow(),
        ];
    }
}
