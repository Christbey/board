<?php
namespace App\Livewire;


use Livewire\Component;


class Modal extends Component
{
    public $showModal = false;

    protected $listeners = ['openModal', 'closeModal'];

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.modal');
    }
}