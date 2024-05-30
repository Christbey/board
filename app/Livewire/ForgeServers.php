<?php
namespace App\Livewire;


use Livewire\Component;
use App\Services\ForgeService;

class ForgeServers extends Component
{
public $servers = [];
public $selectedServer = null;
public $sites = [];

protected $listeners = ['fetchSites'];

public function mount(ForgeService $forgeService)
{
$this->servers = $forgeService->fetchServers();
}

public function fetchSites($serverId)
{
$this->selectedServer = $serverId;
$forgeService = new ForgeService();
$this->sites = $forgeService->fetchSites($serverId);
}

public function render()
{
return view('livewire.forge-servers', [
'servers' => $this->servers,
'sites' => $this->sites
]);
}
}
