<?php

namespace Trax\Framework\Xapi\Schema\Traits;

use Trax\Framework\Xapi\Schema\Traits\IsValid;
use Trax\Framework\Xapi\Schema\Parsing\StatementSchema;
use Trax\Framework\Xapi\Schema\Parsing\Parser;
use Trax\Framework\Xapi\Exceptions\XapiValidationException;
use Trax\Framework\Xapi\Exceptions\XapiBadRequestException;

trait ValidateStatements
{
    use IsValid;

    /**
     * Validate a statement and throw an exception with a list of errors.
     *
     * @param  mixed  $data
     * @return array
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiValidationException
     */
    public static function validate($data)
    {
        $schema = new StatementSchema();
        $parser = new Parser($schema);
        $errors = $parser->validate($data, 'statement');
        if (!empty($errors)) {
            throw new XapiValidationException('This statement is not valid.', $data, $errors);
        }
    }

    /**
     * Validate a one or many statements with a list of attachments.
     *
     * @param  mixed  $statements
     * @param  mixed  $attachments
     * @return array
     */
    public static function validateStatementsAndAttachments($statements, $attachments)
    {
        self::validateStatements($statements);
        self::validateAttachments($statements, $attachments);
    }

    /**
     * Validate Statements.
     *
     * @param  object|array  $statements
     * @return void
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     */
    public static function validateStatements($statements): void
    {
        $statements = is_array($statements) ? collect($statements) : collect([$statements]);

        // Validate each statement individually.
        $statements->each(function ($statement) {
            self::validate($statement);
        });
    }
    
    /**
     * Validate attachments.
     *
     * @param  object|array  $statements
     * @param  array  $attachments
     * @param  bool  $globalCheck  Perform the global checks (on a batch or unique statement)
     * @return  array  The attachments that are referenced by the statements and only them.
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     */
    public static function validateAttachments($statements, array $attachments = [], $globalCheck = true): array
    {
        $usedAttachments = [];
        if (is_array($statements)) {
            // Statements batch.
            foreach ($statements as $statement) {
                $justUsedAttachments = self::validateAttachments($statement, $attachments, false);
                $usedAttachments = array_merge($usedAttachments, $justUsedAttachments);
            }
        } else {
            // Single statement.
            $statement = $statements;

            // No attachment.
            if (!isset($statement->attachments)) {
                return $usedAttachments;
            }

            foreach ($statement->attachments as $attachment) {
                // Location.
                $mustBeRaw = $attachment->usageType == 'http://adlnet.gov/expapi/attachments/signature'
                    || !isset($attachment->fileUrl);

                // Check that a matching raw attachment exists.
                if ($mustBeRaw && !isset($attachments[$attachment->sha2])) {
                    throw new XapiBadRequestException("Some raw attachments are missing.");
                }
                
                // This is a remote attachment, skip it.
                if (!isset($attachments[$attachment->sha2])) {
                    continue;
                }
                
                // Check content type.
                $rawAttachment = $attachments[$attachment->sha2];
                if (isset($rawAttachment->contentType) && $rawAttachment->contentType != $attachment->contentType) {
                    throw new XapiBadRequestException("The Content-Type of a raw attachment is incorrect.");
                }
                
                // Check content lenght.
                if (isset($rawAttachment->length) && $rawAttachment->length != $attachment->length) {
                    throw new XapiBadRequestException("The Content-Length of a raw attachment is incorrect.");
                }
                
                // Check signed statements
                self::validateSignedAttachment($attachment, $rawAttachment, $statements);
                
                // Check that content length does not exceed the platform config.
                // TBD !!! (MongoDB/MySQL limit, server limit...)
                
                // Remember that this attachment as been used.
                $usedAttachments[$attachment->sha2] = true;
            }
        }
                
        // Some attachments have not been used by the statements.
        if ($globalCheck && count($usedAttachments) < count($attachments)) {
            throw new XapiBadRequestException("Some attachments are not referenced in the statements.");
        }
            
        return $usedAttachments;
    }

    /**
     * Validate a signed attachment.
     *
     * @param  object  $jsonAttachment
     * @param  object  $rawAttachment
     * @param  object|array  $statements
     * @return  void
     */
    public static function validateSignedAttachment(object $jsonAttachment, object $rawAttachment, $statements): void
    {
        // Not a signed statement.
        if ($jsonAttachment->usageType != 'http://adlnet.gov/expapi/attachments/signature') {
            return;
        }
        
        // Content type.
        if ($jsonAttachment->contentType != 'application/octet-stream') {
            throw new XapiBadRequestException("The Content-Type of a signed attachment is incorrect.");
        }
        
        // Decompose.
        $parts = explode('.', $rawAttachment->content);
        if (count($parts) != 3) {
            throw new XapiBadRequestException("The format of a signed attachment is incorrect.");
        }

        // Header.
        $header = json_decode(base64_decode($parts[0]));
        if (!$header) {
            throw new XapiBadRequestException("The header of a signed attachment is incorrect.");
        }
        
        // Encryption.
        if (!isset($header->alg) || !in_array($header->alg, ['RS256', 'RS384', 'RS512'])) {
            throw new XapiBadRequestException("The encryption algorythm of a signed attachment is incorrect.");
        }
        
        // Payload.
        $payload = json_decode(base64_decode($parts[1]));
        if (!$payload) {
            throw new XapiBadRequestException("The payload of a signed attachment is incorrect.");
        }
        
        // Remove JWT data on payload.
        // xAPI Launch adds an 'iat' prop. I don't know if it is normal. But it makes the comparison fail.
        unset($payload->iat);

        // Compare statements
        if (!self::compare($statements, $payload)) {
            throw new XapiBadRequestException("A signed attachment is incorrect.");
        }
    }
}
