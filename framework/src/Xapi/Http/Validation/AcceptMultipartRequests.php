<?php

namespace Trax\Framework\Xapi\Http\Validation;

use Illuminate\Http\Request;
use Trax\Framework\Xapi\Helpers\Multipart;
use Trax\Framework\Xapi\Http\Requests\HttpRequest;
use Trax\Framework\Xapi\Exceptions\XapiBadRequestException;

trait AcceptMultipartRequests
{
    /**
     * Validate an multipart request.
     *
     * @param \Illuminate\Http\Request  $request;
     * @return  array|false  Return the multipart content, or false if it is not a multipart.
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     */
    protected function validateMultipartRequest(Request $request)
    {
        // No content type.
        if (!HttpRequest::hasHeader($request, 'Content-Type')) {
            throw new XapiBadRequestException('Missing Content-Type in request.');
        }
        
        // Check header.
        if (!HttpRequest::hasType($request, 'multipart/mixed')) {
            return false;
        }
       
        // Invalid content.
        $parts = $this->multiparts($request);
        if (empty($parts)) {
            throw new XapiBadRequestException('Invalid content in multipart request.');
        }

        return $parts;
    }

    /**
     * Return the parts of a multipart request.
     *
     * @param  \Illuminate\Http\Request  $request;
     * @return  array
     */
    public function multiparts(Request $request)
    {
        return Multipart::parts(
            HttpRequest::content($request),
            Multipart::boundary(HttpRequest::header($request, 'Content-Type'))
        );
    }
}
