<?php

namespace Trax\Framework\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Force UTF-8 output for Windows console
        if (PHP_OS_FAMILY === 'Windows' && function_exists('sapi_windows_cp_set')) {
            sapi_windows_cp_set(65001);
        }
    }
}
