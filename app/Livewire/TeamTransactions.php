<?php

namespace App\Livewire;

use Livewire\Component;

class TeamTransactions extends Component
{
    public $transactions;

    public function mount($transactions)
    {
        $this->transactions = $transactions;
    }

    public function render()
    {
        return view('livewire.team-transactions');
    }
}
