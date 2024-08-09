<?php

namespace Trax\ActivityProfiles\Repos\ActivityProfile;

use Trax\Framework\Repo\RepositoryInterface;
use Trax\ActivityProfiles\Repos\ActivityProfile\Actions\OneFunctions;

traxDeclareRepositoryClass('Trax\ActivityProfiles\Repos\ActivityProfile', 'activity-profiles');

class ActivityProfileRepository extends Repository implements RepositoryInterface
{
    use ActivityProfileFilters, OneFunctions;

    /**
     * The applicable domain.
     */
    protected $domain = 'activity-profiles';

    /**
     * The applicable scopes.
     */
    protected $scopes = ['store'];

    /**
     * Return the class of the factory.
     *
     * @return string
     */
    public function factoryClass()
    {
        return ActivityProfileFactory::class;
    }
}
