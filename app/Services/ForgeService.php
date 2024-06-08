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

        if ($response->successful()) {
            return $response->json()['servers'];
        }

        // Log the error if the response is not successful
        \Log::error('Failed to fetch servers from Forge API.', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return [];
    }

    public function fetchSites($serverId)
    {
        $response = Http::withToken($this->apiToken)->get("https://forge.laravel.com/api/v1/servers/{$serverId}/sites");

        if ($response->successful()) {
            return $response->json()['sites'];
        }

        // Log the error if the response is not successful
        \Log::error('Failed to fetch sites from Forge API.', [
            'serverId' => $serverId,
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return [];
    }
}
