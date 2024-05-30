<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ForgeService
{
protected $apiToken;

public function __construct()
{
$this->apiToken = config('services.forge.token');
}

public function fetchServers()
{
$response = Http::withToken($this->apiToken)->get('https://forge.laravel.com/api/v1/servers');
return $response->json()['servers'];
}

public function fetchSites($serverId)
{
$response = Http::withToken($this->apiToken)->get("https://forge.laravel.com/api/v1/servers/{$serverId}/sites");
return $response->json()['sites'];
}
}
