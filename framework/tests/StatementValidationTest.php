<?php

namespace Trax\Framework\Tests;

use Tests\TestCase;
use Trax\Framework\Tests\Data\Statements;
use Trax\Framework\Xapi\Schema\Statement as StatementSchema;
use Trax\Framework\Xapi\Exceptions\XapiValidationException;

class StatementValidationTest extends TestCase
{
    public function testValid()
    {
        try {
            StatementSchema::validate(
                Statements::simple([], true)
            );
            $this->assertTrue(true);
        } catch (XapiValidationException $e) {
            $this->fail("XapiValidationException thrown");
        }
    }

    public function testNullProp()
    {
        $this->expectException(XapiValidationException::class);

        StatementSchema::validate(
            Statements::simple([
                'actor' => [
                    'name' => null,
                    'mbox' => 'mailto:agent@traxlrs.com',
                ]
            ], true)
        );
    }

    public function testSpecialIri()
    {
        try {
            StatementSchema::validate(
                Statements::simple([
                    'object' => [
                        'id' => 'ispring://traxlrs.com/activities/1',
                    ]
                ], true)
            );
            $this->assertTrue(true);
        } catch (XapiValidationException $e) {
            $this->fail("XapiValidationException thrown");
        }
    }

    public function testCmiInteractionWithoutCmiType()
    {
        try {
            StatementSchema::validate(
                Statements::simple([
                    'object' => [
                        'id' => 'http://traxlrs.com/xapi/activities/101',
                        'definition' => [
                            'type' => 'http://id.tincanapi.com/activitytype/resource',
                            'interactionType' => 'choice',
                            'correctResponsesPattern' => ['1'],
                            'choices' => [
                                [
                                    'id' => '1',
                                    'description' => ['en-US' => 'No']
                                ],
                                [
                                    'id' => '0',
                                    'description' => ['en-US' => 'Yes']
                                ]
                            ]
                        ]
                    ]
                    ], true)
            );
            $this->assertTrue(true);
        } catch (XapiValidationException $e) {
            $this->fail("XapiValidationException thrown");
        }
    }

    public function testMisc1()
    {
        try {
            StatementSchema::validate(
                Statements::simple([
                    'verb' => [
                        'id' => 'http://adlnet.gov/expapi/test/unicode/target/949585ff-720a-4e3e-ad49-7338ae75dff5',
                        'display' => [
                            "en-GB" => "attended",
                            "en-US" => "attended",
                            "ja-JP" => "出席した",
                            "ko-KR" => "참석",
                            "is-IS" => "sótti",
                            "ru-RU" => "участие",
                            "pa-IN" => "ਹਾਜ਼ਰ",
                            "sk-SK" => "zúčastnil",
                            "ar-EG" => "حضر",
                            "hy-AM" => "ներկա է գտնվել",
                            "kn-IN" => "ಹಾಜರಿದ್ದರು",
                        ]
                    ],
                    'object' => [
                        'objectType' => 'Activity',
                        'id' => 'http://www.example.com/unicode',
                        'definition' => [
                            'name' => [
                                'en' => 'Other',
                                'en-GB' => 'attended',
                                'en-US' => 'attended',
                                'ja-JP' => '出席した',
                                'ko-KR' => '참석',
                                'is-IS' => 'sótti',
                                'ru-RU' => 'участие',
                                'pa-IN' => 'ਹਾਜ਼ਰ',
                                'sk-SK' => 'zúčastnil',
                                'ar-EG' => 'حضر',
                                'hy-AM' => 'ներկա է գտնվել',
                                'kn-IN' => 'ಹಾಜರಿದ್ದರು'
                            ],
                            'description' => [
                                'en-US' => 'On this map, please mark Franklin, TN',
                                'en-GB' => 'On this map, please mark Franklin, TN'
                            ],
                            'type' => 'http://adlnet.gov/expapi/activities/cmi.interaction',
                            'moreInfo' => 'http://virtualmeeting.example.com/345256',
                            'interactionType' => 'other',
                            'correctResponsesPattern' => ['(35.937432,-86.868896)'],
                            'extensions' => [
                                'http://example.com/profiles/meetings/extension/location' => 'X:\\\\meetings\\\\minutes\\\\examplemeeting.one',
                                'http://example.com/profiles/meetings/extension/reporter' => [
                                    'name' => 'Thomas',
                                    'id' => 'http://openid.com/342'
                                ]
                            ]
                        ]
                    ]
                ], true)
            );
            $this->assertTrue(true);
        } catch (XapiValidationException $e) {
            $this->fail("XapiValidationException thrown");
        }
    }
}
