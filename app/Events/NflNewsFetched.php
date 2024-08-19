<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NflNewsFetched
{
    use Dispatchable, SerializesModels;

    public array $newsItem;

    public function __construct(array $newsItem)
    {
        $this->newsItem = $newsItem;
    }
}
