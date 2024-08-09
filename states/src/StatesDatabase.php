<?php

namespace Trax\States;

use Trax\Framework\Database\Database;
use Trax\States\Repos\State\StateRepository;
use Trax\States\Recording\StateRecorder;

class StatesDatabase extends Database
{
    /**
     * @var string
     */
    protected $service = 'states';
        
    /**
     * @var string
     */
    protected static $connection = 'states';
    
    /**
     * @var array
     */
    protected static $tables = [
        'xapi_states' => ['type' => 'main'],
    ];

    /**
     * @var array
     */
    protected $storeTables = ['xapi_states'];

    /**
     * @var bool
     */
    protected $nosqlSupported = true;

    /**
     * @var \Trax\States\Repos\State\StateRepository
     */
    protected $xapi_states;
    
    /**
     * @return void
     */
    public function __construct()
    {
        $this->xapi_states = app(StateRepository::class);
        $this->mainRepository = $this->xapi_states;
        $this->recorder = app(StateRecorder::class);
    }

    /**
     * Clear users data.
     *
     * @param  array  $users    each listed user is an agent string ID
     * @return void
     */
    public function clearUsers(array $users): void
    {
        $this->xapi_states->clearAgents($users);
    }
}
