<?php

namespace Trax\Statements\Http\Controllers\Actions;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Trax\Framework\Xapi\Http\Requests\XapiStatementRequest;
use Trax\Framework\Xapi\Helpers\Multipart;
use Trax\Framework\Context;

trait BuildResponse
{
    /**
     * Get the 'more' URL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Trax\Framework\Xapi\Http\Requests\XapiStatementRequest  $xapiRequest
     * @param  \Illuminate\Support\Collection  $resources
     * @return string|false
     */
    protected function moreUrl(Request $request, XapiStatementRequest $xapiRequest, Collection $resources)
    {
        if ($resources->isEmpty()) {
            return false;
        }

        // Define the request params.
        $nav = $xapiRequest->param('ascending') == 'true' ? 'after' : 'before';
        $params = [
            $nav.'[stored]' => $resources->last()->stored,
        ];
        foreach (['agent', 'verb', 'activity', 'registration', 'related_activities', 'related_agents', 'since', 'until', 'limit', 'format', 'attachments', 'ascending'] as $name) {
            if ($xapiRequest->hasParam($name)) {
                $params[$name] = $xapiRequest->param($name);
            }
        }

        // Define the URL.
        return traxClientStoreUrl(
            Context::client(),
            Context::store(),
            false,
        ) . '/xapi/statements?' . http_build_query($params);
    }

    /**
     * Return the statements response.
     *
     * @param  object|array  $content
     * @param  \Illuminate\Support\Collection  $attachments
     * @return \Illuminate\Http\Response
     */
    protected function multipartResponse($content, Collection $attachments)
    {
        $attachments = $attachments->map(function ($attachment) {
            return (object)[
                'content' => $attachment->content,
                'contentType' => $attachment->content_type,
                'length' => $attachment->length,
                'sha2' => $attachment->id,
            ];
        })->all();

        array_unshift($attachments, (object)['content' => json_encode($content)]);

        $contentAndBoundary = Multipart::contentAndBoundary($attachments);

        return response($contentAndBoundary->content, 200)
            ->header('Content-Type', 'multipart/mixed; boundary="'.$contentAndBoundary->boundary.'"')
            // Don't set the content-length header because it causes a conflict in Octane.
            //->header('Content-Length', mb_strlen($contentAndBoundary->content, '8bit'))
            ;
    }
}
