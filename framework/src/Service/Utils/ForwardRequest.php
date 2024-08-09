<?php

namespace Trax\Framework\Service\Utils;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Trax\Framework\Context;
use Trax\Framework\Xapi\Exceptions\XapiBadRequestException;

trait ForwardRequest
{
    /**
     * @var array Know errors.
     */
    protected $knownErrors = [
        'no-content' => 'Missing Content',
        'no-content-type' => 'Missing Content-Type',
    ];

    /**
     * Forward a given request to a specified endpoint.
     *
     * @param  string  $endpoint
     * @param  string  $method
     * @param  \Illuminate\Http\Request  $request
     * @param  bool  $appendQueryString
     * @return \Illuminate\Http\Response
     */
    protected function forwardRequest(string $endpoint, string $method, Request $request, bool $appendQueryString = true)
    {
        // We set the context in order to catch a potential exception with the logging system.
        Context::setEntryPoint('gateway');

        if ($appendQueryString) {
            $params = $request->query();
            $endpoint .= empty($params) ? '' : '?' . http_build_query($params);
        }

        if (strtoupper($method) != 'POST' && strtoupper($method) != 'PUT') {
            $response = $this->forwardNoContentRequest($endpoint, $method, $request);
        } elseif ($request->getContentTypeFormat() == 'form') {
            $response = $this->forwardFormRequest($endpoint, $method, $request);
        } else {
            $response = $this->forwardRawContentRequest($endpoint, $method, $request);
        }

        return response($response->body(), $response->status(), $response->headers());
    }

    /**
     * Forward a request without content.
     *
     * @param  string  $endpoint
     * @param  string  $method
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Client\Response
     */
    protected function forwardNoContentRequest(string $endpoint, string $method, Request $request)
    {
        // Remove a few headers because it make fail the request.
        $headers = $request->headers->all();
        unset($headers['content-type']);
        unset($headers['content-length']);

        // Add the context is a header.
        $headers['trax-context'] = Context::getAsHeader();

        return Http::withHeaders($headers)->$method($endpoint);
    }

    /**
     * Forward a request without content.
     *
     * @param  string  $endpoint
     * @param  string  $method
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Client\Response
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     */
    protected function forwardFormRequest(string $endpoint, string $method, Request $request)
    {
        // Check that we have a content. Requests without content will be rejected.
        $content = collect($request->all())->diffKeys(collect($request->query()))->filter()->toArray();
        if (empty($content)) {
            throw new XapiBadRequestException($this->knownErrors['no-content']);
        }

        $headers = $request->headers->all();

        // Add the context is a header.
        $headers['trax-context'] = Context::getAsHeader();

        return Http::asForm()->withHeaders(
            $headers
        )->$method(
            $endpoint,
            $content
        );
    }

    /**
     * Forward a raw content request, including JSON and MULTIPART.
     *
     * @param  string  $endpoint
     * @param  string  $method
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Client\Response
     *
     * @throw \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     */
    protected function forwardRawContentRequest(string $endpoint, string $method, Request $request)
    {
        // Check that we have a content type. Requests without content type will be rejected.
        $contentType = $request->headers->get('CONTENT_TYPE');
        if (empty($contentType)) {
            throw new XapiBadRequestException($this->knownErrors['no-content-type']);
        }

        // Remove the content type from the header because it is passed with the body.
        $headers = $request->headers->all();
        unset($headers['content-type']);
        
        // Add the context is a header.
        $headers['trax-context'] = Context::getAsHeader();

        return Http::withHeaders(
            $headers
        )->withBody(
            $request->getContent(),
            $contentType
        )->$method(
            $endpoint
        );
    }
}
