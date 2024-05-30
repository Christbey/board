<?php

// app/Http/Livewire/ForgeServers.php
namespace App\Livewire;

use Livewire\Component;
use App\Services\ForgeService;

class ForgeServers extends Component
{
public $servers = [];

public function mount(ForgeService $forgeService)
{
$this->servers = $forgeService->fetchServers();
}

public function render()
{
return view('livewire.forge-servers', ['servers' => $this->servers]);
}
}
