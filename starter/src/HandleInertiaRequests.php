<?php

namespace Trax\Starter;

use Trax\Framework\Front\HandleInertiaRequests as FrameworkHandleInertiaRequests;

class HandleInertiaRequests extends FrameworkHandleInertiaRequests
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'trax-starter::app';
}
