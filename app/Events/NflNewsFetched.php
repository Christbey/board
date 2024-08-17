<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NflNewsFetched
{
    use Dispatchable, SerializesModels;

    public $newsItem;

    public function __construct(array $newsItem)
    {
        $this->newsItem = $newsItem;
    }
}
