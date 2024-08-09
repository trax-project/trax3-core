<?php

namespace Trax\Activities\Repos\Activity;

use Trax\Framework\Repo\RepositoryInterface;
use Trax\Activities\Repos\Activity\Actions\OneFunctions;
use Trax\Activities\Repos\Activity\Actions\FinalizeActivities;

traxDeclareRepositoryClass('Trax\Activities\Repos\Activity', 'activities');

class ActivityRepository extends Repository implements RepositoryInterface
{
    use ActivityFilters, OneFunctions, FinalizeActivities;

    /**
     * The applicable domain.
     */
    protected $domain = 'activities';

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
        return ActivityFactory::class;
    }
}
