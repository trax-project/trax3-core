<?php

namespace Trax\Framework\Auth\Env;

use Trax\Framework\Auth\Store;

class EnvStore implements Store
{
    /**
     * @var int
     */
    public $id = 1;

    /**
     * @var string
     */
    public $slug = 'default';

    /**
     * @var string
     */
    public $name = 'Default store';
}
