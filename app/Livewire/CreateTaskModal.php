<?php

namespace App\Livewire;

use Livewire\Component;

class CreateTaskModal extends Component
{
public $showModal = false;

protected $listeners = ['openCreateTaskModal'];

public function openCreateTaskModal()
{
$this->showModal = true;
}

public function closeModal()
{
$this->showModal = false;
}

public function render()
{
return view('livewire.create-task-modal');
}
}