<?php

namespace App\Providers;

use App\Events\NbaOddsFetched;
use App\Events\NcaaOddsFetched;
use App\Events\NflOddsFetched;
use App\Events\MlbOddsFetched;
use App\Listeners\StoreNbaOdds;
use App\Listeners\StoreNcaaOdds;
use App\Listeners\StoreNflOdds;
use App\Listeners\StoreMlbOdds;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NflOddsFetched::class => [
            StoreNflOdds::class,
        ],
        MlbOddsFetched::class => [
            StoreMlbOdds::class,
        ],
        NbaOddsFetched::class => [
            StoreNbaOdds::class,
        ],

        NcaaOddsFetched::class => [
            StoreNcaaOdds::class,
        ],
        // other events...
    ];

    public function boot()
    {
        parent::boot();
    }
}