<?php

namespace Trax\Framework\Xapi\Http\Response;

trait DocumentResponse
{
    /**
     * Get the response.
     *
     * @param  string  $content
     * @param  string  $contentType
     * @param  bool  $etag
     * @return \Illuminate\Http\Response
     */
    protected function documentResponse(string $content, string $contentType = 'application/json', bool $etag = true)
    {
        if ($contentType == 'application/json') {
            $response = response()->json(json_decode($content));
        } else {
            $response = response($content, 200)
                        ->header('Content-Type', $contentType)
                        // Don't set the content-length header because it causes a conflict in Octane.
                        //->header('Content-Length', mb_strlen($content, '8bit'))
                        ;
        }
        return $etag
            ? $response->header('ETag', '"'.sha1($content).'"')
            : $response;
    }
}
