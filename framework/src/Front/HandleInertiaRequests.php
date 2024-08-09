<?php

namespace Trax\Framework\Front;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use Trax\Framework\Service\Config;

abstract class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    // protected $rootView = 'trax-starter::app';           MUST BE DEFINED

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(

            parent::share($request),
            
            ['ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            }],

            Config::authData(),
            Config::storesData(),
        );
    }
}
