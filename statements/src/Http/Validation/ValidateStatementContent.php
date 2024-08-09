<?php

namespace Trax\Statements\Http\Validation;

use Illuminate\Http\Request;
use Trax\Framework\Xapi\Http\Validation\AcceptJsonRequests;
use Trax\Framework\Xapi\Http\Validation\AcceptMultipartRequests;
use Trax\Framework\Xapi\Exceptions\XapiBadRequestException;

trait ValidateStatementContent
{
    use AcceptJsonRequests, AcceptMultipartRequests;

    /**
     * Validate a PUT request content.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     */
    protected function validatePutRequestContent(Request $request)
    {
        list($statements, $attachments) = $this->validatePostRequestContent($request);

        if (is_array($statements)) {
            throw new XapiBadRequestException('Can not PUT a batch of statements. The POST method should be used.');
        }
        
        if (isset($statements->id) && $statements->id != $request->input('statementId')) {
            throw new XapiBadRequestException('The id of the statement to PUT does not match with the given statementId param.');
        }
        
        return [$statements, $attachments];
    }

    /**
     * Validate a POST request content.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function validatePostRequestContent(Request $request)
    {
        return $this->validateRequestContent($request);
    }

    /**
     * Validate a request content and return the statements and attachments in an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function validateRequestContent(Request $request): array
    {
        if ($parts = $this->validateMultipartRequest($request)) {
            return $this->validateStatementMultiparts($parts);
        } else {
            return [$this->validateJsonRequest($request), []];
        }
    }

    /**
     * Validate statement multiparts and return the statements and attachments in an array.
     *
     * @param  array  $parts
     * @return array
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     */
    protected function validateStatementMultiparts(array $parts): array
    {
        // Content-Type.
        $statement = array_shift($parts);
        if (!isset($statement->contentType) || $statement->contentType != 'application/json') {
            throw new XapiBadRequestException('Invalid Content-Type in multipart request.');
        }
        
        // JSON validity.
        if (!$statement = json_decode($statement->content)) {
            throw new XapiBadRequestException('Invalid JSON content in multipart request.');
        }
        
        // Check attachments.
        foreach ($parts as &$attachment) {
            //
            // Attachment hash.
            if (!isset($attachment->sha2)) {
                throw new XapiBadRequestException('Missing X-Experience-API-Hash in multipart request.');
            }
            
            // Attachment encoding.
            if (!isset($attachment->encoding)) {
                throw new XapiBadRequestException('Missing Content-Transfer-Encoding in multipart request.');
            }
            
            // Attachment binary encoding.
            if ($attachment->encoding != 'binary') {
                throw new XapiBadRequestException('None binary Content-Transfer-Encoding in multipart request.');
            }

            // Get the definition from the statement.
            $definition = collect($statement->attachments)->first(function ($def) use ($attachment) {
                return $def->sha2 == $attachment->sha2;
            });
            
            // No matching hash.
            if (is_null($definition)) {
                throw new XapiBadRequestException('No matching attachment found in the statement JSON.');
            }

            // Assign content type if missing.
            if (!isset($attachment->contentType)) {
                $attachment->contentType = $definition->contentType;
            }

            // Assign length if missing.
            if (!isset($attachment->length)) {
                $attachment->length = $definition->length;
            }
        }

        return [$statement, $parts];
    }
}
