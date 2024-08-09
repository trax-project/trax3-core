<?php

namespace Trax\Framework\Tests\Data;

use Trax\Framework\Tests\Utils\HasStaticFaker;
use Trax\Framework\Xapi\Helpers\Multipart;

class Statements
{
    use HasStaticFaker;

    public static function simple(array $data = [], bool $asObject = false)
    {
        self::initFaker();

        $statement = array_merge([
            'actor' => [
                'mbox' => 'mailto:' . self::$faker->email,
            ],
            'verb' => [
                'id' => self::$faker->url,
            ],
            'object' => [
                'id' => self::$faker->url,
            ],
        ], $data);

        return $asObject ? self::jsonObject($statement) : $statement;
    }

    public static function invalid(array $data = [], bool $asObject = false)
    {
        self::initFaker();

        $statement = array_merge([
            'actor' => [
                'mbox' => 'mailto:' . self::$faker->email,
            ],
            'verb' => [
                'id' => 'invalid IRI',
            ],
            'object' => [
                'id' => self::$faker->url,
            ],
        ], $data);

        return $asObject ? self::jsonObject($statement) : $statement;
    }

    public static function voided(string $uuid, array $data = [], bool $asObject = false)
    {
        self::initFaker();

        $statement = array_merge([
            'actor' => [
                'mbox' => 'mailto:' . self::$faker->email,
            ],
            'verb' => [
                'id' => 'http://adlnet.gov/expapi/verbs/voided',
            ],
            'object' => [
                'objectType' => 'StatementRef',
                'id' => $uuid,
            ],
        ], $data);

        return $asObject ? self::jsonObject($statement) : $statement;
    }

    public static function embeddedAttachment(array $data = [], bool $json = true): object
    {
        list($statementPart, $attachmentPart) = self::statementAndAttachmentParts($data, $json);

        return Multipart::contentAndBoundary([$statementPart, $attachmentPart], false);
    }

    public static function statementAndAttachmentParts(array $data = [], bool $json = true): array
    {
        self::initFaker();

        $attachment = $json ? json_encode(['email' => self::$faker->email]) : 'fe000104a46494600016494c450001010000';
        
        $attachmentPart = (object)[
            'sha2' => hash('sha256', $attachment),
            'content' => $attachment,
            'contentType' => $json ? 'application/json' : 'image/jpeg',
            'length' => mb_strlen($attachment, '8bit'),
        ];

        $statement = array_merge([
            'actor' => [
                'mbox' => 'mailto:' . self::$faker->email,
            ],
            'verb' => [
                'id' => self::$faker->url,
            ],
            'object' => [
                'id' => self::$faker->url,
            ],
            'attachments' => [
                [
                    'usageType' => self::$faker->url,
                    'display' => ['en' => 'Attachment'],
                    'contentType' => $json ? 'application/json' : 'image/jpeg',
                    'length' => mb_strlen($attachment, '8bit'),
                    'sha2' => $attachmentPart->sha2,
                ]
            ]
        ], $data);

        $statementPart = (object)[
            'content' => json_encode($statement),
            'contentType' => 'application/json',
        ];

        return [$statementPart, $attachmentPart];
    }

    protected static function jsonObject(array $data): object
    {
        return json_decode(json_encode($data));
    }
}
