<?php

namespace Trax\Framework\Auth\Env;

use Illuminate\Contracts\Auth\Authenticatable;
use Trax\Framework\Auth\User;

class EnvUser implements Authenticatable, User
{
    /**
     * @var int
     */
    public $id = 99999999;

    /**
     * @var string
     */
    public $firstname = 'Super';

    /**
     * @var string
     */
    public $lastname = 'Admin';

    /**
     * @var string
     */
    public $fullname = 'Super Admin';

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $agent = null;

    /**
     * @var string
     */
    public $role = 'admin';

    /**
     * @var array
     */
    public $stores = [];

    /**
     * @var array
     */
    public $store_ids = [];

    /**
     * @var array
     */
    public $store_slugs = [];

    /**
     * @return void
     */
    public function __construct()
    {
        $this->email = config('trax.auth.admin.email');
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     * Get the name of the password attribute for the user.
     *
     * @return string
     */
    public function getAuthPasswordName()
    {
        return 'password';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return config('trax.auth.admin.password');
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return 'not supported';
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // Not supported.
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'not supported';
    }

    /**
     * Get the user capabilities.
     *
     * @return array
     */
    public function capabilities(): array
    {
        return [];
    }
}
