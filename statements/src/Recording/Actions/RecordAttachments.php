<?php

namespace Trax\Statements\Recording\Actions;

use Illuminate\Support\Collection;
use Trax\Statements\Repos\Attachment\AttachmentRepository;

trait RecordAttachments
{
    /**
     * Record attachments.
     *
     * @param  array  $attachments
     * @param  bool  $dontReturnContent
     * @return \Illuminate\Support\Collection
     */
    public function recordAttachments(array $attachments, bool $dontReturnContent = true): Collection
    {
        if (empty($attachments)) {
            return collect();
        }

        $records = collect(
            app(AttachmentRepository::class)->upsert($attachments, 'prepareXapiAttachment')
        );
        
        if ($dontReturnContent) {
            $records = $records->map(function ($record) {
                unset($record['content']);
                return $record;
            });
        }

        return $records;
    }
}
