<?php

namespace App\Livewire;

use Livewire\Component;

class Notification extends Component
{
    public $message = '';
    public $type = 'success';
    public $show = false;

    protected $listeners = ['notify' => 'showNotification'];

    public function showNotification($message, $type = 'success')
    {
        $this->message = $message;
        $this->type = $type;
        $this->show = true;

        // Auto-hide after 5 seconds
        $this->dispatch('hide-notification')->self();
    }

    public function hide()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.notification');
    }
}